<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Section;
use App\Models\NutritionalRecord;
use App\Services\NutriCalculationService;
use Illuminate\Http\Request;

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
        $activeSyId = \App\Services\SchoolYearManager::activeSchoolYearId();
        $query = Student::with(['section', 'nutritionalRecords'])->where('school_year_id', $activeSyId);

        if ($user && $user->isEncoder()) {
            $activeSectionIds = $user->activeSections()->pluck('id');
            $query->whereIn('section_id', $activeSectionIds);
        }

        // Search by name or student number (LRN)
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

        // Filter by sex
        if ($request->filled('sex')) {
            $query->where('gender', $request->input('sex'));
        }

        // Filter by BMI category
        if ($request->filled('bmi_category')) {
            $bmiCategory = $request->input('bmi_category');
            $query->whereHas('nutritionalRecords', function ($q) use ($bmiCategory) {
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
        $activeSyId = \App\Services\SchoolYearManager::activeSchoolYearId();
        $query = Student::with(['section', 'nutritionalRecords', 'assessments'])
            ->where('school_year_id', $activeSyId)
            ->where(function ($q) {
                $q->where('is_permitted', true)
                  ->orWhereHas('nutritionalRecords', function ($sub) {
                      $sub->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                  });
            });

        if ($user && $user->isEncoder()) {
            $activeSectionIds = $user->activeSections()->pluck('id');
            $query->whereIn('section_id', $activeSectionIds);
        }

        // Search by name or student number (LRN)
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

        // Filter by sex
        if ($request->filled('sex')) {
            $query->where('gender', $request->input('sex'));
        }

        // Filter by BMI category
        if ($request->filled('bmi_category')) {
            $bmiCategory = $request->input('bmi_category');
            $query->whereHas('nutritionalRecords', function ($q) use ($bmiCategory) {
                $q->where('bmi_category', $bmiCategory);
            });
        }

        // Filter by approval status
        if ($request->filled('approval_status')) {
            $approvalStatus = $request->input('approval_status');
            if ($approvalStatus === 'approved') {
                $query->where('parent_approval_status', 'approved');
            } elseif ($approvalStatus === 'disapproved') {
                $query->where('parent_approval_status', 'disapproved');
            }
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
        $sections = Section::all();
        return view('students.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_number' => 'required|unique:students,student_number',
            'last_name' => 'required',
            'first_name' => 'required',
            'name_extension' => 'nullable',
            'middle_name' => 'nullable',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'grade_level' => 'required',
            'section' => 'required',
            'weight' => 'required|numeric',
            'height' => 'required|numeric',
            'guardian_name' => 'required',
            'guardian_contact' => 'required',
            'guardian_email' => 'nullable|email',
            'address' => 'required',
            'section_id' => 'required|exists:sections,id',
        ]);

        // Calculate BMI & Category
        $metrics = $this->nutriService->calculateBMI($validated['weight'], $validated['height']);
        
        // Auto permit if Severely Wasted or Wasted
        $isWasted = in_array($metrics['category'], ['Severely Wasted', 'Wasted']);

        $student = Student::create([
            'school_year_id' => \App\Services\SchoolYearManager::activeSchoolYearId(),
            'student_number' => $validated['student_number'],
            'last_name' => $validated['last_name'],
            'first_name' => $validated['first_name'],
            'name_extension' => $validated['name_extension'] ?? null,
            'middle_name' => $validated['middle_name'] ?? null,
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'grade_level' => $validated['grade_level'],
            'section' => $validated['section'],
            'guardian_name' => $validated['guardian_name'],
            'guardian_contact' => $validated['guardian_contact'],
            'guardian_email' => $validated['guardian_email'] ?? null,
            'address' => $validated['address'],
            'is_permitted' => $isWasted,
            'parent_approval_status' => $isWasted ? 'approved' : null,
            'section_id' => $validated['section_id'],
        ]);

        \App\Services\AuditLogger::log('Created', 'Students', 'Added advisory student ' . $student->first_name . ' ' . $student->last_name);

        NutritionalRecord::create([
            'student_id' => $student->id,
            'type' => 'baseline',
            'weight' => $validated['weight'],
            'height' => $validated['height'],
            'bmi' => $metrics['bmi'],
            'bmi_category' => $metrics['category'],
            'height_for_age' => 'Normal',
            'remarks' => 'Initial encoder entry'
        ]);

        $heightInMeters = $validated['height'] / 100;
\App\Models\StudentAssessment::create([
    'student_id' => $student->id,
    'assessed_by_user_id' => auth()->id(),
    'assessment_date' => \Carbon\Carbon::createFromFormat('Y-m-d', date('Y') . '-01-01'),
    'weight_kg' => $validated['weight'],
    'height_m' => round($heightInMeters, 2),
    'bmi' => $metrics['bmi'],
    'nutritional_status' => $this->getNutritionalStatus($metrics['bmi']),
]);

        return redirect()->route('encoder.students.index')->with('success', 'Student added successfully.');
    }

public function storeAssessment(Request $request, Student $student)
{
    $validated = $request->validate([
        'term' => 'required|in:1,2,3',
        'weight_kg' => 'required|numeric|min:0',
        'height_cm' => 'required|numeric|min:0',
    ]);

    // Convert height from cm to meters for storage
    $heightM = $validated['height_cm'] / 100;
    $bmi = $validated['weight_kg'] / ($heightM ** 2);
    $nutritionalStatus = $this->getNutritionalStatus($bmi);
    
    // Use fixed dates for each term (month identifies the term)
    $termDates = [
        1 => date('Y') . '-01-01',  // Term 1
        2 => date('Y') . '-02-01',  // Term 2
        3 => date('Y') . '-03-01',  // Term 3
    ];
    
    $assessmentDate = \Carbon\Carbon::createFromFormat('Y-m-d', $termDates[$validated['term']]);

    // Check if assessment already exists for this term
    $existingAssessment = \App\Models\StudentAssessment::where('student_id', $student->id)
        ->whereMonth('assessment_date', $validated['term'])
        ->latest('assessment_date')
        ->first();

    if ($existingAssessment) {
        // Update existing assessment
        $existingAssessment->update([
            'assessed_by_user_id' => auth()->id(),
            'weight_kg' => $validated['weight_kg'],
            'height_m' => round($heightM, 2),
            'bmi' => round($bmi, 2),
            'nutritional_status' => $nutritionalStatus,
        ]);
    } else {
        // Create new assessment
        \App\Models\StudentAssessment::create([
            'student_id' => $student->id,
            'assessed_by_user_id' => auth()->id(),
            'assessment_date' => $assessmentDate,
            'weight_kg' => $validated['weight_kg'],
            'height_m' => round($heightM, 2),
            'bmi' => round($bmi, 2),
            'nutritional_status' => $nutritionalStatus,
        ]);
    }

    \App\Services\AuditLogger::log('Updated', 'Assessments', 'Recorded term progress for student ' . $student->first_name . ' ' . $student->last_name);
    return back()->with('success', 'Term progress recorded successfully.');
}

private function getNutritionalStatus($bmi)
{
    if ($bmi < 16) return 'Severely Wasted';
    if ($bmi < 18.5) return 'Wasted';
    if ($bmi < 25) return 'Normal';
    if ($bmi < 30) return 'Overweight';
    return 'Obese';
}

    public function updateApproval(Request $request, Student $student)
    {
        $validated = $request->validate([
            'parent_approval_status' => 'required|in:approved,disapproved',
            'disapproval_reason' => 'nullable|in:unwilling,medical_condition',
            'medical_condition_notes' => 'nullable|string',
        ]);

        $student->update([
            'parent_approval_status' => $validated['parent_approval_status'],
            'disapproval_reason' => $validated['parent_approval_status'] === 'disapproved' ? $validated['disapproval_reason'] : null,
            'medical_condition_notes' => ($validated['parent_approval_status'] === 'disapproved' && $validated['disapproval_reason'] === 'medical_condition') ? $validated['medical_condition_notes'] : null,
            'is_permitted' => $validated['parent_approval_status'] === 'approved'
        ]);

        \App\Services\AuditLogger::log('Updated', 'SBFP Approval', 'Updated parent approval status for student ' . $student->first_name . ' ' . $student->last_name . ' to ' . $validated['parent_approval_status']);

        return back()->with('success', 'Parent approval status updated.');
    }

    public function destroy(Student $student)
    {
        $student->delete(); 
        \App\Services\AuditLogger::log('Archived', 'Students', 'Archived student ' . $student->first_name . ' ' . $student->last_name);
        return back()->with('success', 'Student archived.');
    }

    public function generateIdCard(Student $student)
    {
        return view('students.id-card', compact('student'));
    }

    public function printBatch()
    {
        $user = auth()->user();
        $query = Student::with('nutritionalRecords');
        if ($user && $user->isEncoder()) {
            $activeSectionIds = $user->activeSections()->pluck('id');
            $query->whereIn('section_id', $activeSectionIds);
        }
        $students = $query->get()
            ->filter(function ($student) {
                if ($student->parent_approval_status === 'disapproved') {
                    return false;
                }
                $latestRecord = $student->nutritionalRecords()->latest()->first();
                $isWasted = $latestRecord && in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted']);
                return $student->is_permitted || $isWasted;
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

        \App\Services\AuditLogger::log('Created', 'Email', 'Sent feeding day email notice to guardian of ' . $student->first_name . ' ' . $student->last_name);

        return back()->with('success', 'Feeding day notice email sent successfully to ' . $student->guardian_email);
    }
}
