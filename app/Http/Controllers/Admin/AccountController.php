<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYearUserRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Services\SchoolYearManager;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $targetRole = $this->resolveTargetRole($request, $currentUser);

        $query = User::where('role', $targetRole);
        $activeSchoolYearId = SchoolYearManager::activeSchoolYearId();

        if ($activeSchoolYearId) {
            $query->whereHas('schoolYearUserRecords', function ($recordQuery) use ($activeSchoolYearId, $targetRole) {
                $recordQuery->where('school_year_id', $activeSchoolYearId)->where('role', $targetRole);
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $searchTerm = mb_strlen($search) === 1 ? strtolower($search) . '%' : '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($searchTerm, $activeSchoolYearId) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereHas('schoolYearUserRecords', function ($recordQuery) use ($searchTerm, $activeSchoolYearId) {
                        $recordQuery->where('school_year_id', $activeSchoolYearId)
                            ->whereRaw('LOWER(CAST(deped_id AS TEXT)) LIKE ?', [$searchTerm]);
                    });
            });
        }

        if ($request->filled('grade_level')) {
            $query->whereHas('schoolYearUserRecords', function ($recordQuery) use ($activeSchoolYearId, $request) {
                $recordQuery->where('school_year_id', $activeSchoolYearId)
                    ->where('advisory_grade_level', $request->input('grade_level'));
            });
        }

        if ($request->filled('position')) {
            $query->whereHas('schoolYearUserRecords', function ($recordQuery) use ($activeSchoolYearId, $request) {
                $recordQuery->where('school_year_id', $activeSchoolYearId)
                    ->where('position', $request->input('position'));
            });
        }

        if ($request->filled('sex')) {
            $query->where('sex', $request->input('sex'));
        }

        $sortBy = $request->input('sort_by', 'name_asc');
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'grade_level_asc':
                $query->orderBy(
                    SchoolYearUserRecord::select('advisory_grade_level')
                        ->whereColumn('user_id', 'users.id')
                        ->where('school_year_id', $activeSchoolYearId),
                    'asc'
                );
                break;
            case 'date_newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $advisers = $query->paginate(15)->withQueryString();

        $gradeLevels = User::where('users.role', $targetRole)
            ->whereHas('schoolYearUserRecords', fn($recordQuery) => $recordQuery
                ->where('school_year_id', $activeSchoolYearId)
                ->whereNotNull('advisory_grade_level'))
            ->distinct()
            ->join('school_year_user_records', 'users.id', '=', 'school_year_user_records.user_id')
            ->where('school_year_user_records.school_year_id', $activeSchoolYearId)
            ->pluck('school_year_user_records.advisory_grade_level');

        $positions = User::where('users.role', $targetRole)
            ->join('school_year_user_records', 'users.id', '=', 'school_year_user_records.user_id')
            ->where('school_year_user_records.school_year_id', $activeSchoolYearId)
            ->whereNotNull('school_year_user_records.position')
            ->distinct()
            ->pluck('school_year_user_records.position');

        $sexes = ['Male', 'Female'];

        return view('admin.accounts.index', compact('advisers', 'gradeLevels', 'positions', 'sexes', 'targetRole'));
    }

    public function create(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->isSuperAdmin();
        $targetRole = $this->resolveTargetRole($request, $currentUser);
        $positions = [
            'Teacher I',
            'Teacher II',
            'Teacher III',
            'Master Teacher I',
            'Master Teacher II'
        ];
        $gradeLevels = [0, 1, 2, 3, 4, 5, 6, 7];
        $sexes = ['Male', 'Female'];

        return view('admin.accounts.create', compact('positions', 'gradeLevels', 'sexes', 'isSuperAdmin', 'targetRole'));
    }

    public function store(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $targetRole = $this->resolveTargetRole($request, $currentUser, true);
        $isEncoder = $targetRole === User::ROLE_ENCODER;

        $validated = $request->validate([
            'deped_id' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9-]+$/', 'unique:users,deped_id'],
            'first_name' => ['required_without:name', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'last_name' => ['required_without:name', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'name_extension' => ['nullable', 'string', 'max:50', 'regex:/^[\pL\s.\'-]+$/u'],
            'name' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'sex' => 'required|in:Male,Female',
            'birthdate' => 'required|date|before:today',
            'position' => ['required', 'string', 'max:255', 'regex:/^[\pL\pN\s.\'\/-]+$/u'],
            'advisory_grade_level' => $isEncoder ? 'required|integer|between:0,7' : 'nullable|integer|between:0,7',
            'advisory_section' => $isEncoder ? ['required', 'string', 'max:100', 'regex:/^[\pL\pN][\pL\pN .\'-]*$/u'] : ['nullable', 'string', 'max:100', 'regex:/^[\pL\pN][\pL\pN .\'-]*$/u'],
        ]);

        $user = User::create([
            'deped_id' => $validated['deped_id'],
            'name' => User::composeName($validated['first_name'] ?? null, $validated['middle_name'] ?? null, $validated['last_name'] ?? null, $validated['name_extension'] ?? null) ?: $validated['name'],
            'first_name' => $validated['first_name'] ?? null,
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'name_extension' => $validated['name_extension'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'sex' => $validated['sex'],
            'birthdate' => $validated['birthdate'],
            'position' => $validated['position'],
            'advisory_grade_level' => $isEncoder ? (int) $validated['advisory_grade_level'] : null,
            'advisory_section' => $isEncoder ? ucfirst(strtolower($validated['advisory_section'])) : null,
            'role' => $targetRole,
            'is_active' => true,
        ]);
        $user->syncSchoolYearUserRecord(SchoolYearManager::activeSchoolYearId());

        \App\Services\AuditLogger::log('Created', 'Accounts', 'Created new ' . $targetRole . ' account for ' . $user->name);

        return $this->redirectToAccountIndex($currentUser, $targetRole)->with('success', 'Account created successfully.');
    }

    public function update(Request $request, User $user)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($this->canManageAccount($currentUser, $user), 403);

        if (!$user->is_active) {
            return back()->withErrors(['account' => 'Archived accounts can only be restored.']);
        }

        $validated = $request->validate([
            'first_name' => ['required_without:name', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'middle_name' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'last_name' => ['required_without:name', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'name_extension' => ['nullable', 'string', 'max:50', 'regex:/^[\pL\s.\'-]+$/u'],
            'name' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s.\'-]+$/u'],
            'sex' => ['nullable', 'in:Male,Female'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'deped_id' => ['nullable', 'string', 'max:50', 'regex:/^[A-Za-z0-9-]+$/', Rule::unique('users', 'deped_id')->ignore($user->id)],
            'position' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\pN\s.\'\/-]+$/u'],
        ]);

        if ($user->isEncoder()) {
            $validated = array_merge($validated, $request->validate([
                'advisory_grade_level' => ['nullable', 'integer', 'between:0,7'],
                'advisory_section' => ['nullable', 'string', 'max:100', 'regex:/^[\pL\pN][\pL\pN .\'-]*$/u'],
            ]));

            if (array_key_exists('advisory_section', $validated) && $validated['advisory_section'] !== null) {
                $validated['advisory_section'] = ucfirst(strtolower($validated['advisory_section']));
            }
        }

        $user->update([
            'name' => User::composeName($validated['first_name'] ?? null, $validated['middle_name'] ?? null, $validated['last_name'] ?? null, $validated['name_extension'] ?? null) ?: $validated['name'],
            'first_name' => $validated['first_name'] ?? null,
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'name_extension' => $validated['name_extension'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'birthdate' => $validated['birthdate'] ?? null,
            'email' => $validated['email'],
            'deped_id' => $validated['deped_id'] ?? null,
            'position' => $validated['position'] ?? null,
            'advisory_grade_level' => $validated['advisory_grade_level'] ?? null,
            'advisory_section' => $validated['advisory_section'] ?? null,
        ]);

        $assignment = $user->currentSchoolYearUserRecord() ?? $user->syncSchoolYearUserRecord();
        if ($assignment) {
            $assignment->update([
                'deped_id' => $validated['deped_id'] ?? null,
                'position' => $validated['position'] ?? null,
                'advisory_grade_level' => $validated['advisory_grade_level'] ?? null,
                'advisory_section' => $validated['advisory_section'] ?? null,
            ]);
        } else {
            $user->update([
                'deped_id' => $validated['deped_id'] ?? null,
                'position' => $validated['position'] ?? null,
                'advisory_grade_level' => $validated['advisory_grade_level'] ?? null,
                'advisory_section' => $validated['advisory_section'] ?? null,
            ]);
        }

        \App\Services\AuditLogger::log('Updated', 'Accounts', 'Updated account profile for ' . $user->name);

        return $this->redirectToAccountIndex($currentUser, $user->role)->with('success', 'Account updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($this->canManageAccount($currentUser, $user), 403);

        if ($currentUser->id === $user->id) {
            return back()->withErrors(['delete_error' => 'You cannot delete your own account.']);
        }

        $request->validate([
            'password' => ['required', function ($attribute, $value, $fail) use ($currentUser) {
                if (!Hash::check($value, $currentUser->password)) {
                    $fail('The password you entered is incorrect.');
                }
            }],
        ]);

        $user->update(['is_active' => false]);

        \App\Services\AuditLogger::log('Archived', 'Accounts', 'Archived user account ' . $user->name);

        return $this->redirectToAccountIndex($currentUser, $user->role)->with('success', 'Account archived and access disabled.');
    }

    public function restore(Request $request, User $user)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        abort_unless($this->canManageAccount($currentUser, $user), 403);

        $request->validate([
            'password' => ['required', function ($attribute, $value, $fail) use ($currentUser) {
                if (!Hash::check($value, $currentUser->password)) {
                    $fail('The password you entered is incorrect.');
                }
            }],
        ]);

        $user->update(['is_active' => true]);

        \App\Services\AuditLogger::log('Restored', 'Accounts', 'Restored user account ' . $user->name);

        return $this->redirectToAccountIndex($currentUser, $user->role)->with('success', 'Account restored successfully.');
    }

    private function resolveTargetRole(Request $request, User $currentUser, bool $fromInput = false): string
    {
        if ($currentUser->isAdmin()) {
            return User::ROLE_ENCODER;
        }

        $role = $fromInput
            ? $request->input('target_role', User::ROLE_ADMIN)
            : $request->query('role', User::ROLE_ADMIN);

        abort_unless(in_array($role, [User::ROLE_ADMIN, User::ROLE_ENCODER], true), 403);

        return $role;
    }

    private function canManageAccount(User $actor, User $target): bool
    {
        if ($actor->isSuperAdmin()) {
            return in_array($target->role, [User::ROLE_ADMIN, User::ROLE_ENCODER], true);
        }

        return $actor->isAdmin() && $target->role === User::ROLE_ENCODER;
    }

    private function redirectToAccountIndex(User $actor, string $targetRole): RedirectResponse
    {
        if ($actor->isSuperAdmin()) {
            return redirect()->route('super-admin.accounts.index', ['role' => $targetRole]);
        }

        return redirect()->route('admin.accounts.index');
    }
}
