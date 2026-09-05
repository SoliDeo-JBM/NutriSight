<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
  /**
   * Display a listing of accounts.
   */
  public function index(Request $request)
  {
    $currentUser = auth()->user();
    $targetRole = $currentUser->isSuperAdmin() ? 'admin' : 'encoder';

    $query = User::where('role', $targetRole);

    // Search by name or DepEd ID (case-insensitive, starts-with for 1 char, partial for multi-char)
    if ($request->filled('search')) {
      $search = trim($request->input('search'));
      if (mb_strlen($search) === 1) {
        $searchTerm = strtolower($search) . '%';
      } else {
        $searchTerm = '%' . strtolower($search) . '%';
      }
      $query->where(function ($q) use ($searchTerm) {
        $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
          ->orWhereRaw('LOWER(deped_id) LIKE ?', [$searchTerm]);
      });
    }

    // Filter by advisory grade level
    if ($request->filled('grade_level')) {
      $query->where('advisory_grade_level', $request->input('grade_level'));
    }

    // Filter by position
    if ($request->filled('position')) {
      $query->where('position', $request->input('position'));
    }

    // Filter by sex
    if ($request->filled('sex')) {
      $query->where('sex', $request->input('sex'));
    }

    // Sort options
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

    // Get filter options
    $gradeLevels = User::where('role', $targetRole)
      ->whereNotNull('advisory_grade_level')
      ->distinct('advisory_grade_level')
      ->pluck('advisory_grade_level');

    $positions = User::where('role', $targetRole)
      ->whereNotNull('position')
      ->distinct('position')
      ->pluck('position');

    $sexes = ['Male', 'Female'];

    return view('admin.accounts.index', compact('advisers', 'gradeLevels', 'positions', 'sexes'));
  }

  /**
   * Show the form for creating a new account.
   */
  public function create()
  {
    $positions = [
      'Teacher I',
      'Teacher II',
      'Teacher III',
      'Master Teacher I',
      'Master Teacher II'
    ];

    $gradeLevels = [
      'Kinder',
      'Grade 1',
      'Grade 2',
      'Grade 3',
      'Grade 4',
      'Grade 5',
      'Grade 6'
    ];

    $sexes = ['Male', 'Female'];

    return view('admin.accounts.create', compact('positions', 'gradeLevels', 'sexes'));
  }

  /**
   * Store a newly created account in storage.
   */
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
      'position' => 'required|in:Teacher I,Teacher II,Teacher III,Master Teacher I,Master Teacher II',
      'advisory_grade_level' => 'required|in:Kinder,Grade 1,Grade 2,Grade 3,Grade 4,Grade 5,Grade 6',
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
      'advisory_grade_level' => $validated['advisory_grade_level'],
      'advisory_section' => ucfirst(strtolower($validated['advisory_section'])),
      'role' => $targetRole,
      'is_active' => true,
    ]);

    \App\Services\AuditLogger::log('Created', 'Accounts', 'Created new ' . $targetRole . ' account for ' . $validated['name']);

    return redirect()->route($redirectRoute)->with('success', 'Account created successfully.');
  }

  /**
   * Toggle account active status.
   */
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

  /**
   * Soft delete account with level 2 security password verification.
   */
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

    $user->delete(); // Soft delete

    \App\Services\AuditLogger::log('Deleted', 'Accounts', 'Deleted user account ' . $user->name);

    $redirectRoute = $currentUser->isSuperAdmin() ? 'super-admin.accounts.index' : 'admin.accounts.index';
    return redirect()->route($redirectRoute)->with('success', 'Account deleted successfully.');
  }
}
