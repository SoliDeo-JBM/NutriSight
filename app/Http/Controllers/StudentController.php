<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Enrollment;
use App\Models\SbfpParticipant;
use App\Models\NutritionMeasurement;
use App\Services\NutriCalculationService;
use App\Services\SchoolYearManager;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentController extends Controller
{
    protected $nutriService;

    public function __construct(NutriCalculationService $nutriService)
    {
        $this->nutriService = $nutriService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $query = Student::with(['enrollments' => function($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)->with(['sbfpParticipant.nutritionMeasurements']);
        }])->whereHas('enrollments', function($q) use ($activeSyId, $user) {
            $q->where('school_year_id', $activeSyId);
            if ($user && $user->isEncoder()) {
                $q->where('grade_level', $user->advisory_grade_level)
                  ->where('section', $user->advisory_section);
            }
        });

        // Search by name or LRN
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

        // Filter by sex
        if ($request->filled('sex')) {
            $query->where('sex', $request->input('sex'));
        }

        // Filter by BMI category
        if ($request->filled('bmi_category')) {
            $bmiCategory = $request->input('bmi_category');
            $query->whereHas('enrollments.sbfpParticipant.nutritionMeasurements', function ($q) use ($bmiCategory) {
                $q->where('bmi_category', $bmiCategory);
            });
        }

        // Sorting
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

        return view('students.index', compact('students', 'sexes', 'bmiCategories', 'sortOptions'));
    }

    public function sbfpIndex(Request $request)
    {
        $user = auth()->user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $query = Student::with(['enrollments.sbfpParticipant.nutritionMeasurements'])
            ->whereHas('enrollments', function($q) use ($activeSyId, $user) {
                $q->where('school_year_id', $activeSyId);
                if ($user && $user->isEncoder()) {
                    $q->where('grade_level', $user->advisory_grade_level)
                      ->where('section', $user->advisory_section);
                }
            })
            ->whereHas('enrollments.sbfpParticipant', function($q) {
                $q->where('parent_consent', 'approved')
                  ->orWhereHas('nutritionMeasurements', function($sub) {
                      $sub->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                  });
            });

        // Search by name or LRN
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

        if ($request->filled('sex')) {
            $query->where('sex', $request->input('sex'));
        }

        if ($request->filled('bmi_category')) {
            $bmiCategory = $request->input('bmi_category');
            $query->whereHas('enrollments.sbfpParticipant.nutritionMeasurements', function ($q) use ($bmiCategory) {
                $q->where('bmi_category', $bmiCategory);
            });
        }

        if ($request->filled('approval_status')) {
            $approvalStatus = $request->input('approval_status');
            $query->whereHas('enrollments.sbfpParticipant', function($q) use ($approvalStatus) {
                $q->where('parent_consent', $approvalStatus);
            });
        }

        $students = $query->paginate(15)->withQueryString();
        $sexes = ['Male', 'Female'];
        $bmiCategories = ['Severely Wasted', 'Wasted', 'Normal', 'Overweight', 'Obese'];
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

        return view('students.sbfp', compact('students', 'sexes', 'bmiCategories', 'approvalStatuses', 'sortOptions'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lrn' => 'required|unique:students,lrn',
            'last_name' => 'required',
            'first_name' => 'required',
            'name_extension' => 'nullable',
            'middle_name' => 'nullable',
            'birth_date' => 'required|date',
            'sex' => 'required',
            'grade_level' => 'required|integer',
            'section' => 'required|string',
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'guardian_name' => 'required',
            'guardian_contact' => 'required',
            'guardian_email' => 'nullable|email',
            'address' => 'required',
        ]);

        $metrics = $this->nutriService->calculateBMI($validated['weight'], $validated['height']);
        $isWasted = in_array($metrics['category'], ['Severely Wasted', 'Wasted']);

        $student = Student::create([
            'lrn' => $validated['lrn'],
            'last_name' => $validated['last_name'],
            'first_name' => $validated['first_name'],
            'name_extension' => $validated['name_extension'] ?? null,
            'middle_name' => $validated['middle_name'] ?? null,
            'sex' => $validated['sex'],
            'birth_date' => $validated['birth_date'],
            'guardian_name' => $validated['guardian_name'],
            'guardian_email' => $validated['guardian_email'] ?? null,
            'address' => $validated['address'],
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'school_year_id' => SchoolYearManager::activeSchoolYearId(),
            'grade_level' => (int)$validated['grade_level'],
            'section' => ucfirst(strtolower($validated['section'])),
            'status' => 'enrolled',
        ]);

        $participant = SbfpParticipant::create([
            'enrollment_id' => $enrollment->id,
            'parent_consent' => $isWasted ? 'approved' : 'pending',
        ]);

        NutritionMeasurement::create([
            'sbfp_participant_id' => $participant->id,
            'height' => $validated['height'],
            'weight' => $validated['weight'],
            'bmi' => $metrics['bmi'],
            'bmi_category' => $metrics['category'],
            'hfa' => 'Normal',
            'measurement_period' => 'baseline',
            'remarks' => 'Initial encoder entry',
        ]);

        AuditLogger::log('Created', 'Students', 'Added student ' . $student->first_name . ' ' . $student->last_name);

        return redirect()->route('encoder.students.index')->with('success', 'Student added successfully.');
    }

    public function updateApproval(Request $request, Student $student)
    {
        $validated = $request->validate([
            'parent_consent' => 'required|in:approved,disapproved',
            'disapproval_reason' => 'nullable|string',
        ]);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $student->enrollments()->where('school_year_id', $activeSyId)->first();

        if ($enrollment && $enrollment->sbfpParticipant) {
            $enrollment->sbfpParticipant->update([
                'parent_consent' => $validated['parent_consent'],
                'disapproval_reason' => $validated['parent_consent'] === 'disapproved' ? ($validated['disapproval_reason'] ?? null) : null,
            ]);
        }

        AuditLogger::log('Updated', 'SBFP Approval', 'Updated parent consent for student ' . $student->first_name . ' ' . $student->last_name . ' to ' . $validated['parent_consent']);

        return back()->with('success', 'Parent consent updated.');
    }

    public function destroy(Student $student)
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $student->enrollments()->where('school_year_id', $activeSyId)->first();
        if ($enrollment) {
            $enrollment->delete();
        }
        AuditLogger::log('Archived', 'Students', 'Archived student enrollment for ' . $student->first_name . ' ' . $student->last_name);
        return back()->with('success', 'Student enrollment archived.');
    }

    public function generateIdCard(Student $student)
    {
        return view('students.id-card', compact('student'));
    }

    public function printBatch()
    {
        $user = auth()->user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        
        $query = Student::with(['enrollments.sbfpParticipant.nutritionMeasurements'])
            ->whereHas('enrollments', function($q) use ($activeSyId, $user) {
                $q->where('school_year_id', $activeSyId);
                if ($user && $user->isEncoder()) {
                    $q->where('grade_level', $user->advisory_grade_level)
                      ->where('section', $user->advisory_section);
                }
            });

        $students = $query->get()->filter(function ($student) use ($activeSyId) {
            $enrollment = $student->enrollments->where('school_year_id', $activeSyId)->first();
            if (!$enrollment || !$enrollment->sbfpParticipant) {
                return false;
            }
            $participant = $enrollment->sbfpParticipant;
            if ($participant->parent_consent === 'disapproved') {
                return false;
            }
            $latestMeasurement = $participant->nutritionMeasurements()->latest()->first();
            $isWasted = $latestMeasurement && in_array($latestMeasurement->bmi_category, ['Wasted', 'Severely Wasted']);
            return $participant->parent_consent === 'approved' || $isWasted;
        });

        return view('students.print-batch', compact('students'));
    }

    public function emailFeedingNotice(Request $request, Student $student)
    {
        $validated = $request->validate([
            'meal' => 'required|string',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if (!$student->guardian_email) {
            return back()->withErrors(['email' => 'This student does not have a guardian email address recorded.']);
        }

        \Illuminate\Support\Facades\Mail::to($student->guardian_email)->send(
            new \App\Mail\FeedingDayNotice($student, $validated['meal'], $validated['date'], $validated['notes'])
        );

        AuditLogger::log('Created', 'Email', 'Sent feeding day email notice to guardian of ' . $student->first_name . ' ' . $student->last_name);

        return back()->with('success', 'Feeding day notice email sent successfully to ' . $student->guardian_email);
    }
}
