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
   * Display a listing of all adviser accounts.
   */
  public function index(Request $request)
  {
    $query = User::where('role', 'encoder');

    // Search by name or DepEd ID
    if ($request->filled('search')) {
      $search = $request->input('search');
      $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('deped_id', 'like', "%{$search}%");
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
    $gradeLevels = User::where('role', 'encoder')
      ->whereNotNull('advisory_grade_level')
      ->distinct('advisory_grade_level')
      ->pluck('advisory_grade_level');

    $positions = User::where('role', 'encoder')
      ->whereNotNull('position')
      ->distinct('position')
      ->pluck('position');

    $sexes = ['Male', 'Female'];

    return view('admin.accounts.index', compact('advisers', 'gradeLevels', 'positions', 'sexes'));
  }

  /**
   * Show the form for creating a new adviser account.
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
   * Store a newly created adviser account in storage.
   */
  public function store(Request $request)
  {
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

    // Create the user with encoder role
    User::create([
      'deped_id' => $validated['deped_id'],
      'name' => $validated['name'],
      'email' => $validated['email'],
      'password' => Hash::make($validated['password']),
      'sex' => $validated['sex'],
      'birthdate' => $validated['birthdate'],
      'position' => $validated['position'],
      'advisory_grade_level' => $validated['advisory_grade_level'],
      'advisory_section' => $validated['advisory_section'],
      'role' => 'encoder',
    ]);

    return redirect()->route('admin.accounts.index')->with('success', 'Adviser account created successfully.');
  }
}
