<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
                  ->orWhereRaw('LOWER(deped_id) LIKE ?', [$searchTerm]);
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

        $advisers = $query->paginate(15);

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
        $positions = [
            'Teacher I', 'Teacher II', 'Teacher III', 'Master Teacher I', 'Master Teacher II'
        ];
        $gradeLevels = [0, 1, 2, 3, 4, 5, 6];
        $sexes = ['Male', 'Female'];

        return view('admin.accounts.create', compact('positions', 'gradeLevels', 'sexes'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        $targetRole = $currentUser->isSuperAdmin() ? 'admin' : 'encoder';
        $redirectRoute = $currentUser->isSuperAdmin() ? 'super-admin.accounts.index' : 'admin.accounts.index';

        $validated = $request->validate([
            'deped_id' => 'required|string|unique:users,deped_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'sex' => 'required|in:Male,Female',
            'birthdate' => 'required|date|before:today',
            'position' => 'required|string',
            'advisory_grade_level' => 'required|integer',
            'advisory_section' => 'required|string|max:255',
        ]);

        User::create([
            'deped_id' => $validated['deped_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'sex' => $validated['sex'],
            'birthdate' => $validated['birthdate'],
            'position' => $validated['position'],
            'advisory_grade_level' => (int)$validated['advisory_grade_level'],
            'advisory_section' => ucfirst(strtolower($validated['advisory_section'])),
            'role' => $targetRole,
            'is_active' => true,
        ]);

        \App\Services\AuditLogger::log('Created', 'Accounts', 'Created new ' . $targetRole . ' account for ' . $validated['name']);

        return redirect()->route($redirectRoute)->with('success', 'Account created successfully.');
    }

    public function toggleStatus(User $user)
    {
        $currentUser = auth()->user();
        if ($currentUser->isSuperAdmin() && $user->role !== 'admin') {
            abort(403);
        }
        if ($currentUser->isAdmin() && $user->role !== 'encoder') {
            abort(403);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        \App\Services\AuditLogger::log('Updated', 'Accounts', 'Toggled active status for user ' . $user->name);

        return back()->with('success', 'Account status updated successfully.');
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
