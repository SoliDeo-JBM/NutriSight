<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\SbfpParticipant;
use App\Services\AuditLogger;
use App\Services\SchoolYearManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentViewController extends Controller
{
    public function index(Request $request)
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $query = Student::with(['enrollments' => function ($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)
                ->with('sbfpParticipant.nutritionMeasurements');
        }])
            ->whereHas('enrollments', function ($q) use ($activeSyId) {
                $q->where('school_year_id', $activeSyId);
            });

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $searchTerm = mb_strlen($search) === 1 ? strtolower($search) . '%' : '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(CAST(lrn AS TEXT)) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(first_name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(middle_name) LIKE ?', [$searchTerm]);
            });
        }

        if ($request->filled('grade_level')) {
            $gradeLevel = $request->input('grade_level');
            $query->whereHas('enrollments', function ($q) use ($activeSyId, $gradeLevel) {
                $q->where('school_year_id', $activeSyId)->where('grade_level', $gradeLevel);
            });
        }

        if ($request->filled('section')) {
            $section = $request->input('section');
            $query->whereHas('enrollments', function ($q) use ($activeSyId, $section) {
                $q->where('school_year_id', $activeSyId)->where('section', $section);
            });
        }

        if ($request->filled('sex')) {
            $query->where('sex', $request->input('sex'));
        }

        if ($request->filled('bmi_category')) {
            $query->whereHas('enrollments', function ($enrollmentQuery) use ($activeSyId, $request) {
                $enrollmentQuery->where('school_year_id', $activeSyId)
                    ->whereHas('sbfpParticipant.nutritionMeasurements', function ($measurementQuery) use ($request) {
                        $measurementQuery->where('measurement_period', 'baseline')
                            ->where('bmi_category', $request->input('bmi_category'));
                    });
            });
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

        $gradeLevels = Enrollment::where('school_year_id', $activeSyId)->whereNotNull('grade_level')->where('grade_level', '<=', 6)->distinct()->orderBy('grade_level')->pluck('grade_level');
        $sections = Enrollment::where('school_year_id', $activeSyId)->whereNotNull('section')->distinct()->pluck('section');
        $sexes = ['Male', 'Female'];
        $bmiCategories = ['Severely Wasted', 'Wasted', 'Normal', 'Overweight', 'Obese'];
        $sortOptions = [
            'latest' => 'Latest to Oldest',
            'oldest' => 'Oldest to Latest',
            'name_az' => 'Name (A-Z)',
            'name_za' => 'Name (Z-A)',
            'lrn_asc' => 'LRN / ID (Ascending)',
            'lrn_desc' => 'LRN / ID (Descending)',
        ];

        $routePrefix = auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';

        return view('admin.students.index', compact('students', 'gradeLevels', 'sections', 'sexes', 'bmiCategories', 'sortOptions', 'routePrefix'));
    }

    public function sbfpIndex(Request $request)
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $query = Student::with(['enrollments' => function ($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)
                ->with('sbfpParticipant.nutritionMeasurements');
        }])
            ->whereHas('enrollments', function ($q) use ($activeSyId) {
                $q->where('school_year_id', $activeSyId);
            })
            ->whereHas('enrollments', function ($q) use ($activeSyId) {
                $q->where('school_year_id', $activeSyId)
                    ->whereHas('sbfpParticipant', function ($participantQuery) {
                        $participantQuery->where(function ($sub) {
                            $sub->where('parent_consent', '!=', 'disapproved')
                                ->orWhereNull('parent_consent');
                        })->whereHas('nutritionMeasurements', function ($measurementQuery) {
                            $measurementQuery->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                        });
                    });
            });

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $searchTerm = mb_strlen($search) === 1 ? strtolower($search) . '%' : '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(CAST(lrn AS TEXT)) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(first_name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(middle_name) LIKE ?', [$searchTerm]);
            });
        }

        if ($request->filled('grade_level')) {
            $gradeLevel = $request->input('grade_level');
            $query->whereHas('enrollments', function ($q) use ($activeSyId, $gradeLevel) {
                $q->where('school_year_id', $activeSyId)->where('grade_level', $gradeLevel);
            });
        }

        if ($request->filled('section')) {
            $section = $request->input('section');
            $query->whereHas('enrollments', function ($q) use ($activeSyId, $section) {
                $q->where('school_year_id', $activeSyId)->where('section', $section);
            });
        }

        if ($request->filled('sex')) {
            $query->where('sex', $request->input('sex'));
        }

        $students = $query->paginate(15)->withQueryString();

        $gradeLevels = Enrollment::where('school_year_id', $activeSyId)->whereNotNull('grade_level')->where('grade_level', '<=', 6)->distinct()->orderBy('grade_level')->pluck('grade_level');
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

        $routePrefix = auth()->user()->role === 'super_admin' ? 'super-admin' : 'admin';

        return view('admin.students.sbfp', compact('students', 'gradeLevels', 'sections', 'sexes', 'sortOptions', 'routePrefix'));
    }

    public function uploadProfileImages(Request $request)
    {
        $request->validate([
            'profiles' => ['required', 'array'],
            'profiles.*' => ['nullable', 'image', 'max:4096'],
        ]);

        $updated = 0;

        DB::transaction(function () use ($request, &$updated) {
            foreach ($request->file('profiles', []) as $participantId => $file) {
                if (!$file) {
                    continue;
                }

                $participant = SbfpParticipant::find($participantId);
                if (!$participant) {
                    continue;
                }

                $path = $file->store('sbfp-profiles', 'r2');
                $url = Storage::disk('r2')->url($path);

                $participant->update(['profile_image_url' => $url]);
                $updated++;
            }
        });

        AuditLogger::log('Updated', 'SBFP Participants', "Uploaded {$updated} profile image(s) for SBFP participants.");

        return back()->with('success', "Uploaded {$updated} profile image(s).");
    }
}
