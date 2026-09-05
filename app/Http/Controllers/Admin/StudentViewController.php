<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Services\SchoolYearManager;
use Illuminate\Http\Request;

class StudentViewController extends Controller
{
    public function index(Request $request)
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $query = Student::with(['enrollments.sbfpParticipant.nutritionMeasurements'])
            ->whereHas('enrollments', function($q) use ($activeSyId) {
                $q->where('school_year_id', $activeSyId);
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
            $query->whereHas('enrollments', function($q) use ($activeSyId, $gradeLevel) {
                $q->where('school_year_id', $activeSyId)->where('grade_level', $gradeLevel);
            });
        }

        if ($request->filled('section')) {
            $section = $request->input('section');
            $query->whereHas('enrollments', function($q) use ($activeSyId, $section) {
                $q->where('school_year_id', $activeSyId)->where('section', $section);
            });
        }

        if ($request->filled('sex')) {
            $query->where('sex', $request->input('sex'));
        }

        $sort = $request->input('sort', 'latest');
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
                $query->orderBy('lrn', 'asc');
                break;
            case 'lrn_desc':
                $query->orderBy('lrn', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $students = $query->paginate(15)->withQueryString();

        $gradeLevels = Enrollment::where('school_year_id', $activeSyId)->whereNotNull('grade_level')->distinct()->pluck('grade_level');
        $sections = Enrollment::where('school_year_id', $activeSyId)->whereNotNull('section')->distinct()->pluck('section');
        $sexes = ['Male', 'Female'];
        $sortOptions = [
            'latest' => 'Latest to Oldest',
            'oldest' => 'Oldest to Latest',
            'name_az' => 'Name (A-Z)',
            'name_za' => 'Name (Z-A)',
            'lrn_asc' => 'LRN / ID (Ascending)',
            'lrn_desc' => 'LRN / ID (Descending)',
        ];

        return view('admin.students.index', compact('students', 'gradeLevels', 'sections', 'sexes', 'sortOptions'));
    }

    public function sbfpIndex(Request $request)
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $query = Student::with(['enrollments.sbfpParticipant.nutritionMeasurements'])
            ->whereHas('enrollments', function($q) use ($activeSyId) {
                $q->where('school_year_id', $activeSyId);
            })
            ->whereHas('enrollments.sbfpParticipant', function($q) {
                $q->where(function($sub) {
                    $sub->where('parent_consent', '!=', 'disapproved')
                        ->orWhereNull('parent_consent');
                })->where(function($sub) {
                    $sub->whereHas('nutritionMeasurements', function($m) {
                        $m->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                    });
                });
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
            $query->whereHas('enrollments', function($q) use ($activeSyId, $gradeLevel) {
                $q->where('school_year_id', $activeSyId)->where('grade_level', $gradeLevel);
            });
        }

        if ($request->filled('section')) {
            $section = $request->input('section');
            $query->whereHas('enrollments', function($q) use ($activeSyId, $section) {
                $q->where('school_year_id', $activeSyId)->where('section', $section);
            });
        }

        if ($request->filled('sex')) {
            $query->where('sex', $request->input('sex'));
        }

        if ($request->filled('approval_status')) {
            $approvalStatus = $request->input('approval_status');
            $query->whereHas('enrollments.sbfpParticipant', function($q) use ($approvalStatus) {
                $q->where('parent_consent', $approvalStatus);
            });
        }

        $students = $query->paginate(15)->withQueryString();

        $gradeLevels = Enrollment::where('school_year_id', $activeSyId)->whereNotNull('grade_level')->distinct()->pluck('grade_level');
        $sections = Enrollment::where('school_year_id', $activeSyId)->whereNotNull('section')->distinct()->pluck('section');
        $sexes = ['Male', 'Female'];
        $approvalStatuses = [
            'approved' => 'Approved',
            'disapproved' => 'Disapproved'
        ];
        $sortOptions = [
            'latest' => 'Latest to Oldest',
            'oldest' => 'Oldest to Latest',
            'name_az' => 'Name (A-Z)',
            'name_za' => 'Name (Z-A)',
            'lrn_asc' => 'LRN / ID (Ascending)',
            'lrn_desc' => 'LRN / ID (Descending)',
        ];

        return view('admin.students.sbfp', compact('students', 'gradeLevels', 'sections', 'sexes', 'approvalStatuses', 'sortOptions'));
    }
}
