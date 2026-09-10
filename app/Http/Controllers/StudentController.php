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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class StudentController extends Controller
{
    protected NutriCalculationService $nutriService;

    public function __construct(NutriCalculationService $nutriService)
    {
        $this->nutriService = $nutriService;
    }

    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $query = Student::with([
            'enrollments' => function ($q) use ($activeSyId) {
                $q->where('school_year_id', $activeSyId)->with(['sbfpParticipant.nutritionMeasurements']);
            }
        ])->whereHas('enrollments', function ($q) use ($activeSyId, $user) {
            $q->where('school_year_id', $activeSyId);
            if ($user && $user->isEncoder()) {
                if ($user->advisory_grade_level === null || $user->advisory_section === null) {
                    $q->whereRaw('1 = 0');
                } else {
                    $q->where('grade_level', $user->advisory_grade_level)
                      ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($user->advisory_section))]);
                }
            }
        });

        // Search by name or LRN
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
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $query = Student::with(['enrollments' => function ($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)
                ->with('sbfpParticipant.nutritionMeasurements');
            }])
            ->whereHas('enrollments', function ($q) use ($activeSyId, $user) {
                $q->where('school_year_id', $activeSyId);
                if ($user && $user->isEncoder()) {
                    if ($user->advisory_grade_level === null || $user->advisory_section === null) {
                        $q->whereRaw('1 = 0');
                    } else {
                        $q->where('grade_level', $user->advisory_grade_level)
                            ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($user->advisory_section))]);
                    }
                }
            })
            ->whereHas('enrollments', function ($q) use ($activeSyId, $user) {
                $q->where('school_year_id', $activeSyId);
                if ($user && $user->isEncoder()) {
                    $q->where('grade_level', $user->advisory_grade_level)
                        ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim((string) $user->advisory_section))]);
                }
                $q->whereHas('sbfpParticipant', function ($participantQuery) {
                    $participantQuery->whereHas('nutritionMeasurements', function ($sub) {
                        $sub->whereIn('measurement_period', ['baseline', 'Baseline', 'Term 1'])
                            ->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                    });
                });
            });

        // Search by name or LRN
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
            $query->whereHas('enrollments.sbfpParticipant', function ($q) use ($approvalStatus) {
                $q->where('parent_consent', $approvalStatus);
            });
        }

        $students = $query->paginate(15)->withQueryString();
        $sexes = ['Male', 'Female'];
        $bmiCategories = ['Severely Wasted', 'Wasted'];
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
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        return view('students.create', compact('user'));
    }

    public function edit(Student $student)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $enrollment = $student->enrollments()
            ->where('school_year_id', SchoolYearManager::activeSchoolYearId())
            ->with('sbfpParticipant.nutritionMeasurements')
            ->first();

        if (!$enrollment || ($user->isEncoder() && ($enrollment->grade_level != $user->advisory_grade_level || strtolower(trim($enrollment->section)) !== strtolower(trim($user->advisory_section))))) {
            abort(404);
        }

        $measurement = $enrollment->sbfpParticipant?->nutritionMeasurements
            ->first(fn ($item) => in_array(strtolower($item->measurement_period), ['baseline', 'term 1'], true));

        return view('students.create', compact('user', 'student', 'enrollment', 'measurement'));
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
            'guardian_contact' => $validated['guardian_contact'],
            'guardian_email' => $validated['guardian_email'] ?? null,
            'address' => $validated['address'],
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'school_year_id' => SchoolYearManager::activeSchoolYearId(),
            'grade_level' => (int) $validated['grade_level'],
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

    public function update(Request $request, Student $student)
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $student->enrollments()
            ->where('school_year_id', $activeSyId)
            ->with('sbfpParticipant')
            ->first();

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$enrollment || ($user->isEncoder() && ($enrollment->grade_level != $user->advisory_grade_level || strtolower(trim($enrollment->section)) !== strtolower(trim($user->advisory_section))))) {
            abort(404);
        }

        $validated = $request->validate([
            'lrn' => 'required|unique:students,lrn,' . $student->id,
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

        DB::transaction(function () use ($student, $enrollment, $validated, $metrics) {
            $student->update([
                'lrn' => $validated['lrn'],
                'last_name' => $validated['last_name'],
                'first_name' => $validated['first_name'],
                'name_extension' => $validated['name_extension'] ?? null,
                'middle_name' => $validated['middle_name'] ?? null,
                'sex' => $validated['sex'],
                'birth_date' => $validated['birth_date'],
                'guardian_name' => $validated['guardian_name'],
                'guardian_contact' => $validated['guardian_contact'],
                'guardian_email' => $validated['guardian_email'] ?? null,
                'address' => $validated['address'],
            ]);

            $enrollment->update([
                'grade_level' => (int) $validated['grade_level'],
                'section' => ucfirst(strtolower($validated['section'])),
            ]);

            $measurement = $enrollment->sbfpParticipant?->nutritionMeasurements()
                ->whereIn('measurement_period', ['baseline', 'Baseline', 'Term 1'])
                ->first();

            if ($measurement) {
                $measurement->update([
                    'weight' => $validated['weight'],
                    'height' => $validated['height'],
                    'bmi' => $metrics['bmi'],
                    'bmi_category' => $metrics['category'],
                    'measurement_period' => 'baseline',
                ]);
            }
        });

        AuditLogger::log('Updated', 'Students', 'Updated student ' . $student->first_name . ' ' . $student->last_name);

        return redirect()->route('encoder.students.index')->with('success', 'Student updated successfully.');
    }

    public function storeAssessment(Request $request, Student $student)
    {
        $validated = $request->validate([
            'measurement_period' => 'required|in:midline,endline,mid,end',
            'weight' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
        ]);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $student->enrollments()->where('school_year_id', $activeSyId)->first();
        if (!$enrollment || !$enrollment->sbfpParticipant) {
            return back()->withErrors(['error' => 'Student is not an SBFP participant for the active school year.']);
        }

        $participant = $enrollment->sbfpParticipant;
        $metrics = $this->nutriService->calculateBMI($validated['weight'], $validated['height']);

        $periodMap = [
            'mid' => 'midline',
            'end' => 'endline',
        ];
        $measurementPeriod = $periodMap[$validated['measurement_period']] ?? $validated['measurement_period'];

        $existing = NutritionMeasurement::where('sbfp_participant_id', $participant->id)
            ->where(function ($q) use ($measurementPeriod) {
                $q->whereIn('measurement_period', [$measurementPeriod, ucfirst($measurementPeriod)]);
            })
            ->first();

        if ($existing) {
            $existing->update([
                'weight' => $validated['weight'],
                'height' => $validated['height'],
                'bmi' => $metrics['bmi'],
                'bmi_category' => $metrics['category'],
                'hfa' => 'Normal',
            ]);
        } else {
            NutritionMeasurement::create([
                'sbfp_participant_id' => $participant->id,
                'measurement_period' => $measurementPeriod,
                'weight' => $validated['weight'],
                'height' => $validated['height'],
                'bmi' => $metrics['bmi'],
                'bmi_category' => $metrics['category'],
                'hfa' => 'Normal',
                'remarks' => ucfirst($measurementPeriod) . ' progress assessment',
            ]);
        }

        AuditLogger::log('Updated', 'Assessments', 'Recorded term progress for student ' . $student->first_name . ' ' . $student->last_name);
        return back()->with('success', 'Term progress recorded successfully.');
    }

    public function storeBulkAssessments(Request $request)
    {
        $validated = $request->validate([
            'measurement_period' => 'required|in:midline,endline',
            'measurements' => 'required|array',
            'measurements.*.student_id' => 'required|integer',
            'measurements.*.weight' => 'required|numeric|min:0.1',
            'measurements.*.height' => 'required|numeric|min:0.1',
        ]);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $user = Auth::user();
        $saved = 0;

        DB::transaction(function () use ($validated, $activeSyId, $user, &$saved) {
            foreach ($validated['measurements'] as $entry) {
                $student = Student::find($entry['student_id']);
                $enrollment = $student?->enrollments()
                    ->where('school_year_id', $activeSyId)
                    ->with('sbfpParticipant')
                    ->first();

                if (!$enrollment || !$enrollment->sbfpParticipant || ($user->isEncoder() && (
                    (string) $enrollment->grade_level !== (string) $user->advisory_grade_level ||
                    strtolower(trim($enrollment->section)) !== strtolower(trim((string) $user->advisory_section))
                ))) {
                    continue;
                }

                $metrics = $this->nutriService->calculateBMI($entry['weight'], $entry['height']);
                $alreadyExists = $enrollment->sbfpParticipant->nutritionMeasurements()
                    ->whereIn('measurement_period', [$validated['measurement_period'], ucfirst($validated['measurement_period'])])
                    ->exists();

                if ($alreadyExists) {
                    throw ValidationException::withMessages([
                        'measurement_period' => ucfirst($validated['measurement_period']) . ' already exists for ' . $student->first_name . ' ' . $student->last_name . '. Use Edit Period instead.',
                    ]);
                }

                $attributes = [
                    'weight' => $entry['weight'],
                    'height' => $entry['height'],
                    'bmi' => $metrics['bmi'],
                    'bmi_category' => $metrics['category'],
                    'hfa' => 'Normal',
                ];

                $enrollment->sbfpParticipant->nutritionMeasurements()->create($attributes + [
                    'measurement_period' => $validated['measurement_period'],
                    'remarks' => ucfirst($validated['measurement_period']) . ' progress assessment',
                ]);

                $saved++;
            }
        });

        return back()->with('success', $saved . ' period measurement(s) saved successfully.');
    }

    public function updateBulkAssessments(Request $request)
    {
        $validated = $request->validate([
            'measurement_period' => 'required|in:baseline,midline,endline',
            'measurements' => 'required|array',
            'measurements.*.student_id' => 'required|integer',
            'measurements.*.weight' => 'nullable|numeric|min:0.1',
            'measurements.*.height' => 'nullable|numeric|min:0.1',
        ]);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $user = Auth::user();
        $updated = 0;

        DB::transaction(function () use ($validated, $activeSyId, $user, &$updated) {
            foreach ($validated['measurements'] as $entry) {
                if (($entry['weight'] ?? null) === null && ($entry['height'] ?? null) === null) {
                    continue;
                }

                if (($entry['weight'] ?? null) === null || ($entry['height'] ?? null) === null) {
                    throw ValidationException::withMessages([
                        'measurements' => 'Each edited student must have both weight and height.',
                    ]);
                }

                $student = Student::find($entry['student_id']);
                $enrollment = $student?->enrollments()
                    ->where('school_year_id', $activeSyId)
                    ->with('sbfpParticipant')
                    ->first();

                if (!$enrollment || !$enrollment->sbfpParticipant || ($user->isEncoder() && (
                    (string) $enrollment->grade_level !== (string) $user->advisory_grade_level ||
                    strtolower(trim($enrollment->section)) !== strtolower(trim((string) $user->advisory_section))
                ))) {
                    throw ValidationException::withMessages([
                        'measurements' => 'One or more students are outside your advisory list.',
                    ]);
                }

                $measurement = $enrollment->sbfpParticipant->nutritionMeasurements()
                    ->whereIn('measurement_period', [
                        $validated['measurement_period'],
                        ucfirst($validated['measurement_period']),
                        $validated['measurement_period'] === 'baseline' ? 'Term 1' : '',
                    ])
                    ->first();

                if (!$measurement) {
                    throw ValidationException::withMessages([
                        'measurement_period' => ucfirst($validated['measurement_period']) . ' does not exist for ' . $student->first_name . ' ' . $student->last_name . '. Use Add Period first.',
                    ]);
                }

                $metrics = $this->nutriService->calculateBMI($entry['weight'], $entry['height']);
                $measurement->update([
                    'measurement_period' => $validated['measurement_period'],
                    'weight' => $entry['weight'],
                    'height' => $entry['height'],
                    'bmi' => $metrics['bmi'],
                    'bmi_category' => $metrics['category'],
                    'hfa' => 'Normal',
                ]);
                $updated++;
            }

            if ($updated === 0) {
                throw ValidationException::withMessages([
                    'measurements' => 'Enter at least one complete student measurement to edit.',
                ]);
            }
        });

        return back()->with('success', $updated . ' period measurement(s) updated successfully.');
    }

    public function updatePeriod(Request $request, Student $student)
    {
        $validated = $request->validate([
            'measurement_period' => 'required|in:baseline,midline,endline',
            'weight' => 'required|numeric|min:0.1',
            'height' => 'required|numeric|min:0.1',
        ]);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $user = Auth::user();
        $enrollment = $student->enrollments()
            ->where('school_year_id', $activeSyId)
            ->with('sbfpParticipant')
            ->first();

        if (!$enrollment || !$enrollment->sbfpParticipant || ($user->isEncoder() && (
            (string) $enrollment->grade_level !== (string) $user->advisory_grade_level ||
            strtolower(trim($enrollment->section)) !== strtolower(trim((string) $user->advisory_section))
        ))) {
            abort(404);
        }

        $measurement = $enrollment->sbfpParticipant->nutritionMeasurements()
            ->whereIn('measurement_period', [$validated['measurement_period'], ucfirst($validated['measurement_period']), $validated['measurement_period'] === 'baseline' ? 'Term 1' : ''])
            ->first();

        if (!$measurement) {
            return back()->withErrors(['measurement_period' => 'That period does not exist for this student yet. Use Add Period first.']);
        }

        $metrics = $this->nutriService->calculateBMI($validated['weight'], $validated['height']);
        $measurement->update([
            'measurement_period' => $validated['measurement_period'],
            'weight' => $validated['weight'],
            'height' => $validated['height'],
            'bmi' => $metrics['bmi'],
            'bmi_category' => $metrics['category'],
            'hfa' => 'Normal',
        ]);

        return back()->with('success', ucfirst($validated['measurement_period']) . ' measurement updated successfully.');
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
        /** @var Enrollment|null $enrollment */
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
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $query = Student::with(['enrollments.sbfpParticipant.nutritionMeasurements'])
            ->whereHas('enrollments', function ($q) use ($activeSyId, $user) {
                $q->where('school_year_id', $activeSyId);
                if ($user && $user->isEncoder()) {
                    $q->where('grade_level', $user->advisory_grade_level)
                        ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim((string) $user->advisory_section))]);
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
