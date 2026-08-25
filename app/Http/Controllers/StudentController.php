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

    public function index()
    {
        $students = Student::with(['section', 'nutritionalRecords'])->get();
        return view('students.index', compact('students'));
    }

    public function sbfpIndex()
    {
        // Only include students who are explicitly permitted or Wasted/Severely Wasted, AND NOT disapproved by parent
        $students = Student::with(['section', 'nutritionalRecords'])
            ->get()
            ->filter(function ($student) {
                if ($student->parent_approval_status === 'disapproved') {
                    return false;
                }
                $latestRecord = $student->nutritionalRecords()->latest()->first();
                $isWasted = $latestRecord && in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted']);
                return $student->is_permitted || $isWasted;
            });

        return view('students.sbfp', compact('students'));
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

        return redirect()->route('students.index')->with('success', 'Student added successfully.');
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

        return back()->with('success', 'Parent approval status updated.');
    }

    public function destroy(Student $student)
    {
        $student->delete(); 
        return back()->with('success', 'Student archived.');
    }

    public function generateIdCard(Student $student)
    {
        return view('students.id-card', compact('student'));
    }

    public function printBatch()
    {
        $students = Student::with('nutritionalRecords')
            ->get()
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
}
