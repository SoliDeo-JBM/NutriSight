<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use App\Models\Enrollment;
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
        
        $activeStudents = Student::whereHas('enrollments', function($q) use ($activeSy) {
            $q->where('school_year_id', $activeSy?->id);
        })->get();

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
            $query = Student::with(['enrollments' => function($q) use ($sourceSyId) {
                $q->where('school_year_id', $sourceSyId);
            }])->whereHas('enrollments', function($q) use ($sourceSyId) {
                $q->where('school_year_id', $sourceSyId);
            });

            if ($request->filled('search')) {
                $search = trim($request->input('search'));
                $searchTerm = mb_strlen($search) === 1 ? strtolower($search) . '%' : '%' . strtolower($search) . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(lrn) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(first_name) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(last_name) LIKE ?', [$searchTerm])
                      ->orWhereRaw('LOWER(middle_name) LIKE ?', [$searchTerm]);
                });
            }

            if ($request->filled('grade_level')) {
                $gradeLevel = $request->input('grade_level');
                $query->whereHas('enrollments', function($q) use ($sourceSyId, $gradeLevel) {
                    $q->where('school_year_id', $sourceSyId)->where('grade_level', $gradeLevel);
                });
            }

            if ($request->filled('section')) {
                $section = $request->input('section');
                $query->whereHas('enrollments', function($q) use ($sourceSyId, $section) {
                    $q->where('school_year_id', $sourceSyId)->where('section', $section);
                });
            }

            if ($request->filled('sex')) {
                $query->where('sex', $request->input('sex'));
            }

            $sourceStudents = $query->get();

            $gradeLevels = Enrollment::where('school_year_id', $sourceSyId)->whereNotNull('grade_level')->distinct()->pluck('grade_level');
            $sectionsList = Enrollment::where('school_year_id', $sourceSyId)->whereNotNull('section')->distinct()->pluck('section');
        }

        $sections = Enrollment::where('school_year_id', $activeSy?->id)->select('grade_level', 'section')->distinct()->get();
        $rolePrefix = auth()->user()->isSuperAdmin() ? 'super-admin' : 'admin';

        return view('admin.students.promote', compact('activeSy', 'allSy', 'activeStudents', 'sourceSyId', 'sourceStudents', 'sections', 'gradeLevels', 'sectionsList', 'sexes', 'sortOptions', 'rolePrefix'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'grade_level' => 'required|integer',
            'section' => 'required|string',
        ]);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $promotedCount = 0;

        foreach ($validated['student_ids'] as $id) {
            $sourceStudent = Student::find($id);
            if (!$sourceStudent) {
                continue;
            }

            $existing = Enrollment::where('student_id', $sourceStudent->id)
                ->where('school_year_id', $activeSyId)
                ->first();

            if (!$existing) {
                Enrollment::create([
                    'student_id' => $sourceStudent->id,
                    'school_year_id' => $activeSyId,
                    'grade_level' => (int)$validated['grade_level'],
                    'section' => ucfirst(strtolower($validated['section'])),
                    'status' => 'enrolled',
                ]);
                $promotedCount++;
            }
        }

        AuditLogger::log('created', 'Student Promotion', 'Promoted / enrolled ' . $promotedCount . ' students into active school year (' . $validated['section'] . ')');

        $rolePrefix = auth()->user()->isSuperAdmin() ? 'super-admin' : 'admin';

        return redirect()->route($rolePrefix . '.students.promote')
            ->with('success', 'Successfully promoted/enrolled ' . $promotedCount . ' students into the active school year.');
    }
}
