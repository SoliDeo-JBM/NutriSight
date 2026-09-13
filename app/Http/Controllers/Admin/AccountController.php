<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $targetRole = $currentUser->isSuperAdmin() ? 'admin' : 'encoder';

        $query = User::where('role', $targetRole);

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $searchTerm = mb_strlen($search) === 1 ? strtolower($search) . '%' : '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(CAST(deped_id AS TEXT)) LIKE ?', [$searchTerm]);
            });
        }

        if ($request->filled('grade_level')) {
            $query->where('advisory_grade_level', $request->input('grade_level'));
        }

        if ($request->filled('position')) {
            $query->where('position', $request->input('position'));
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
                $query->orderBy('advisory_grade_level', 'asc');
                break;
            case 'date_newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $advisers = $query->paginate(15)->withQueryString();

        $gradeLevels = User::where('role', $targetRole)
            ->whereNotNull('advisory_grade_level')
            ->distinct()
            ->pluck('advisory_grade_level');

        $positions = User::where('role', $targetRole)
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position');

        $sexes = ['Male', 'Female'];

        return view('admin.accounts.index', compact('advisers', 'gradeLevels', 'positions', 'sexes'));
    }

    public function create()
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isSuperAdmin();
        $positions = [
            'Teacher I',
            'Teacher II',
            'Teacher III',
            'Master Teacher I',
            'Master Teacher II'
        ];
        $gradeLevels = [0, 1, 2, 3, 4, 5, 6];
        $sexes = ['Male', 'Female'];

        return view('admin.accounts.create', compact('positions', 'gradeLevels', 'sexes', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser->isSuperAdmin();
        $targetRole = $isSuperAdmin ? 'admin' : 'encoder';
        $redirectRoute = $isSuperAdmin ? 'super-admin.accounts.index' : 'admin.accounts.index';

        $validated = $request->validate([
            'deped_id' => 'required|string|unique:users,deped_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'sex' => 'required|in:Male,Female',
            'birthdate' => 'required|date|before:today',
            'position' => 'required|string',
            'advisory_grade_level' => $isSuperAdmin ? 'nullable' : 'required|integer',
            'advisory_section' => $isSuperAdmin ? 'nullable|string|max:255' : 'required|string|max:255',
        ]);

        User::create([
            'deped_id' => $validated['deped_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'sex' => $validated['sex'],
            'birthdate' => $validated['birthdate'],
            'position' => $validated['position'],
            'advisory_grade_level' => $isSuperAdmin ? null : (int)$validated['advisory_grade_level'],
            'advisory_section' => $isSuperAdmin ? null : ucfirst(strtolower($validated['advisory_section'])),
            'role' => $targetRole,
            'is_active' => true,
        ]);

        \App\Services\AuditLogger::log('Created', 'Accounts', 'Created new ' . $targetRole . ' account for ' . $validated['name']);

        return redirect()->route($redirectRoute)->with('success', 'Account created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();
        $expectedRole = $currentUser->isSuperAdmin() ? User::ROLE_ADMIN : User::ROLE_ENCODER;

        abort_unless($user->role === $expectedRole, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sex' => ['nullable', 'in:Male,Female'],
            'birthdate' => ['nullable', 'date'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'deped_id' => ['nullable', 'string', 'max:255', Rule::unique('users', 'deped_id')->ignore($user->id)],
            'position' => ['nullable', 'string', 'max:255'],
        ]);

        if ($user->isEncoder()) {
            $validated = array_merge($validated, $request->validate([
                'advisory_grade_level' => ['nullable', 'integer', 'between:0,6'],
                'advisory_section' => ['nullable', 'string', 'max:255'],
            ]));

            if (array_key_exists('advisory_section', $validated) && $validated['advisory_section'] !== null) {
                $validated['advisory_section'] = ucfirst(strtolower($validated['advisory_section']));
            }
        }

        $user->update($validated);

        \App\Services\AuditLogger::log('Updated', 'Accounts', 'Updated account profile for ' . $user->name);

        $redirectRoute = $currentUser->isSuperAdmin() ? 'super-admin.accounts.index' : 'admin.accounts.index';
        return redirect()->route($redirectRoute)->with('success', 'Account updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        $currentUser = auth()->user();
        if ($currentUser->isSuperAdmin() && $user->role !== 'admin') {
            abort(403);
        }
        if ($currentUser->isAdmin() && $user->role !== 'encoder') {
            abort(403);
        }

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

        $user->delete();

        \App\Services\AuditLogger::log('Deleted', 'Accounts', 'Deleted user account ' . $user->name);

        $redirectRoute = $currentUser->isSuperAdmin() ? 'super-admin.accounts.index' : 'admin.accounts.index';
        return redirect()->route($redirectRoute)->with('success', 'Account deleted successfully.');
    }
}
