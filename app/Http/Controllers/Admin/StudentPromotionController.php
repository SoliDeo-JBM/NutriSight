<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Section;
use App\Models\Student;
use App\Services\AuditLogger;
use App\Services\SchoolYearManager;
use Illuminate\Http\Request;

class StudentPromotionController extends Controller
{
    public function index(Request $request)
    {
        $activeSy = SchoolYearManager::activeSchoolYear();
        $allSy = SchoolYearManager::allSchoolYears();
        
        // Students in active school year
        $activeStudents = Student::with('section')
            ->where('school_year_id', $activeSy?->id)
            ->get();

        // Previous school years for promotion source
        $sourceSyId = $request->input('source_school_year_id');
        $sourceStudents = collect();
        $gradeLevels = [];
        $sectionsList = [];
        $sexes = ['Male', 'Female'];
        $sortOptions = [
            'name_az' => 'Name (A-Z)',
            'name_za' => 'Name (Z-A)',
            'lrn_asc' => 'LRN / ID (Ascending)',
            'lrn_desc' => 'LRN / ID (Descending)',
            'latest' => 'Latest to Oldest',
            'oldest' => 'Oldest to Latest',
        ];

        if ($sourceSyId) {
            $query = Student::with('section')->where('school_year_id', $sourceSyId);

            // Search by name or LRN
            if ($request->filled('search')) {
                $search = trim($request->input('search'));
                if (mb_strlen($search) === 1) {
                    $searchTerm = strtolower($search) . '%';
                } else {
                    $searchTerm = '%' . strtolower($search) . '%';
                }
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(student_number) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(first_name) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(last_name) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(middle_name) LIKE ?', [$searchTerm]);
                });
            }

            // Grade level filter
            if ($request->filled('grade_level')) {
                $query->where('grade_level', $request->input('grade_level'));
            }

            // Section filter
            if ($request->filled('section')) {
                $query->where('section', $request->input('section'));
            }

            // Sex filter
            if ($request->filled('sex')) {
                $query->where('gender', $request->input('sex'));
            }

            // Sorting
            $sort = $request->input('sort', 'name_az');
            switch ($sort) {
                case 'name_az':
                    $query->orderBy('last_name', 'asc')->orderBy('first_name', 'asc');
                    break;
                case 'name_za':
                    $query->orderBy('last_name', 'desc')->orderBy('first_name', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'lrn_asc':
                    $query->orderBy('student_number', 'asc');
                    break;
                case 'lrn_desc':
                    $query->orderBy('student_number', 'desc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $sourceStudents = $query->get();

            $gradeLevels = Student::where('school_year_id', $sourceSyId)->whereNotNull('grade_level')->distinct()->pluck('grade_level');
            $sectionsList = Student::where('school_year_id', $sourceSyId)->whereNotNull('section')->distinct()->pluck('section');
        }

        $sections = Section::where('school_year_id', $activeSy?->id)->get();
        $rolePrefix = auth()->user()->isSuperAdmin() ? 'super-admin' : 'admin';

        return view('admin.students.promote', compact('activeSy', 'allSy', 'activeStudents', 'sourceSyId', 'sourceStudents', 'sections', 'gradeLevels', 'sectionsList', 'sexes', 'sortOptions', 'rolePrefix'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'grade_level' => 'required|string',
            'section_id' => 'required|exists:sections,id',
        ]);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $section = Section::find($validated['section_id']);
        $promotedCount = 0;

        foreach ($validated['student_ids'] as $id) {
            $sourceStudent = Student::find($id);
            if (!$sourceStudent) {
                continue;
            }

            // Check if already enrolled in active school year by LRN (student_number)
            $existing = Student::where('student_number', $sourceStudent->student_number)
                ->where('school_year_id', $activeSyId)
                ->first();

            if (!$existing) {
                $newStudent = Student::create([
                    'school_year_id' => $activeSyId,
                    'student_number' => $sourceStudent->student_number,
                    'first_name' => $sourceStudent->first_name,
                    'last_name' => $sourceStudent->last_name,
                    'name_extension' => $sourceStudent->name_extension,
                    'middle_name' => $sourceStudent->middle_name,
                    'birth_date' => $sourceStudent->birth_date,
                    'gender' => $sourceStudent->gender,
                    'grade_level' => $validated['grade_level'],
                    'section' => $section?->name ?? '',
                    'section_id' => $section?->id,
                    'guardian_name' => $sourceStudent->guardian_name,
                    'guardian_contact' => $sourceStudent->guardian_contact,
                    'guardian_email' => $sourceStudent->guardian_email,
                    'address' => $sourceStudent->address,
                    'is_permitted' => false,
                    'parent_approval_status' => null,
                ]);

                $promotedCount++;
            }
        }

        AuditLogger::log('created', 'Student Promotion', 'Promoted / enrolled ' . $promotedCount . ' students into active school year (' . ($section?->name ?? '') . ')');

        $rolePrefix = auth()->user()->isSuperAdmin() ? 'super-admin' : 'admin';

        return redirect()->route($rolePrefix . '.students.promote')
            ->with('success', 'Successfully promoted/enrolled ' . $promotedCount . ' students into the active school year.');
    }
}
