<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAttendanceRecord;
use App\Models\MealPlan;
use App\Mail\FeedingDayNotice;
use App\Models\SbfpParticipant;
use App\Services\SchoolYearManager;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Throwable;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        return $this->attendanceIndex($request, true, 'encoder');
    }

    public function adminIndex(Request $request)
    {
        $routePrefix = Auth::user()->role === 'super_admin' ? 'super-admin' : 'admin';

        return $this->attendanceIndex($request, false, $routePrefix);
    }

    private function attendanceIndex(Request $request, bool $encoderScope, string $routePrefix)
    {
        $year = $request->input('year', Carbon::today()->year);
        $month = $request->input('month', Carbon::today()->month);

        $defaultDate = Carbon::today();
        $date = $request->filled('date')
            ? Carbon::parse($request->input('date'))->toDateString()
            : Carbon::create($year, $month, min($defaultDate->day, Carbon::create($year, $month)->daysInMonth))->toDateString();

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $studentQuery = Student::with([
            'enrollments' => function ($q) use ($activeSyId) {
                $q->where('school_year_id', $activeSyId)
                    ->with('sbfpParticipant.nutritionMeasurements');
            },
        ])
            ->whereHas('enrollments', function ($q) use ($activeSyId, $user, $encoderScope) {
                $q->where('school_year_id', $activeSyId);
                if ($encoderScope && $user && $user->isEncoder() && $user->advisory_grade_level && $user->advisory_section) {
                    $q->where('grade_level', $user->advisory_grade_level)
                        ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($user->advisory_section))]);
                }
            });

        if (!$encoderScope) {
            $studentQuery->whereHas('enrollments', function ($enrollmentQuery) use ($activeSyId) {
                $enrollmentQuery->where('school_year_id', $activeSyId)
                    ->whereHas('sbfpParticipant', function ($participantQuery) {
                        $participantQuery->where('parent_consent', 'approved')
                            ->whereHas('nutritionMeasurements', function ($measurementQuery) {
                                $measurementQuery->where('measurement_period', 'baseline')
                                    ->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                            });
                    });
            })->whereHas('enrollments', function ($query) use ($activeSyId, $request) {
                $query->where('school_year_id', $activeSyId)
                    ->when($request->filled('grade_level'), fn($q) => $q->where('grade_level', $request->input('grade_level')))
                    ->when($request->filled('section'), fn($q) => $q->where('section', $request->input('section')));
            });
        }

        $sbfpStudents = $studentQuery->get()->filter(function ($student) use ($activeSyId, $date, $encoderScope) {
            $enrollment = $student->enrollments->where('school_year_id', $activeSyId)->first();
            if (!$enrollment || !$enrollment->sbfpParticipant) {
                return false;
            }
            $participant = $enrollment->sbfpParticipant;

            // If attendance record exists for this date, ALWAYS include them (e.g. from QR scan)
            $hasAttendance = StudentAttendanceRecord::where('attendance_date', $date)
                ->where('sbfp_participant_id', $participant->id)
                ->exists();
            if ($hasAttendance) {
                return true;
            }

            if (!$encoderScope) {
                return $participant->parent_consent === 'approved';
            }

            if ($participant->parent_consent === 'disapproved') {
                return false;
            }
            return $participant->parent_consent === 'approved';
        });

        // Get attendance logs for the date keyed by sbfp_participant_id
        $participantIds = $sbfpStudents->pluck('enrollments')->flatten()->pluck('sbfpParticipant.id')->filter();
        $attendanceLogs = StudentAttendanceRecord::where('attendance_date', $date)
            ->whereIn('sbfp_participant_id', $participantIds)
            ->get()
            ->keyBy('sbfp_participant_id');

        $loggedDates = StudentAttendanceRecord::select('attendance_date')
            ->distinct()
            ->pluck('attendance_date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $gradeLevels = $encoderScope ? collect() : \App\Models\Enrollment::where('school_year_id', $activeSyId)->distinct()->orderBy('grade_level')->pluck('grade_level');
        $sections = $encoderScope ? collect() : \App\Models\Enrollment::where('school_year_id', $activeSyId)->distinct()->orderBy('section')->pluck('section');

        return view('attendance.index', compact('sbfpStudents', 'attendanceLogs', 'date', 'loggedDates', 'routePrefix', 'gradeLevels', 'sections'));
    }

    public function scan(Request $request)
    {
        $request->validate(['lrn' => 'required']);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $student = Student::with(['enrollments' => function ($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)->with('sbfpParticipant');
        }])->where('lrn', $request->lrn)->first();

        if (!$student) {
            return response()->json([
                'error' => 'Invalid QR Code: Student not found in active school year.',
                'student_name' => 'Invalid QR Code',
                'grade_level' => null,
                'section' => null
            ], 404);
        }

        $enrollment = $student->enrollments->where('school_year_id', $activeSyId)->first();
        if (!$enrollment || !$enrollment->sbfpParticipant) {
            return response()->json([
                'error' => 'Student is not an SBFP participant in the active school year.',
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'grade_level' => $enrollment?->grade_level,
                'section' => $enrollment?->section
            ], 404);
        }

        $participant = $enrollment->sbfpParticipant;
        $studentName = $student->first_name . ' ' . $student->last_name;

        if ($participant->parent_consent !== 'approved') {
            return response()->json([
                'error' => 'Parent approval is required before recording attendance.',
                'student_name' => $studentName,
                'grade_level' => $enrollment->grade_level,
                'section' => $enrollment->section
            ], 403);
        }

        $today = now()->toDateString();
        $meals = MealPlan::whereDate('meal_date', $today)->pluck('meal_name');
        if ($meals->isEmpty()) {
            return response()->json([
                'error' => 'Add meal first before taking attendance.',
                'student_name' => $studentName,
                'grade_level' => $enrollment->grade_level,
                'section' => $enrollment->section
            ], 422);
        }

        $existingLog = StudentAttendanceRecord::where('sbfp_participant_id', $participant->id)
            ->where('attendance_date', $today)
            ->first();

        if ($existingLog) {
            return response()->json([
                'error' => 'Attendance already recorded for today. No additional email was sent.',
                'student_name' => $studentName,
                'grade_level' => $enrollment->grade_level,
                'section' => $enrollment->section
            ], 409);
        }

        StudentAttendanceRecord::create([
            'sbfp_participant_id' => $participant->id,
            'recorded_by_user_id' => Auth::id(),
            'attendance_date' => $today,
            'status' => 'present',
        ]);

        $this->sendAttendanceNotice($student, $today, $meals->implode(', '));

        AuditLogger::log('Created', 'Attendance', 'Scanned QR attendance for student ' . $studentName);

        return response()->json([
            'success' => 'Attendance logged successfully.',
            'student_name' => $studentName,
            'grade_level' => $enrollment->grade_level,
            'section' => $enrollment->section
        ]);
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'sbfp_participant_id' => 'required|exists:sbfp_participants,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent'
        ]);

        $participant = SbfpParticipant::with('enrollment.student')->findOrFail($validated['sbfp_participant_id']);
        if (
            $validated['status'] === 'present'
            && !MealPlan::whereDate('meal_date', $validated['date'])->exists()
        ) {
            return back()->with('error', 'Add meal first before recording present attendance.');
        }

        $existingRecord = StudentAttendanceRecord::where('sbfp_participant_id', $participant->id)
            ->whereDate('attendance_date', $validated['date'])
            ->first();

        StudentAttendanceRecord::updateOrCreate(
            [
                'sbfp_participant_id' => $validated['sbfp_participant_id'],
                'attendance_date' => $validated['date'],
            ],
            [
                'recorded_by_user_id' => Auth::id(),
                'status' => $validated['status']
            ]
        );

        if (
            $validated['status'] === 'present'
            && (!$existingRecord || $existingRecord->status !== 'present')
        ) {
            $meal = MealPlan::whereDate('meal_date', $validated['date'])->pluck('meal_name')->implode(', ');
            $this->sendAttendanceNotice($participant->enrollment->student, $validated['date'], $meal);
        }

        AuditLogger::log('Updated', 'Attendance', 'Updated attendance status for participant ID ' . $validated['sbfp_participant_id'] . ' on ' . $validated['date']);

        return back()->with('success', 'Attendance updated.');
    }

    private function sendAttendanceNotice(Student $student, string $date, string $meal): void
    {
        if (!$student->guardian_email) {
            Log::info('Automatic SBFP attendance email skipped because guardian email is missing.', [
                'student_id' => $student->id,
                'attendance_date' => $date,
            ]);
            return;
        }

        try {
            Mail::to($student->guardian_email)->send(new FeedingDayNotice(
                $student,
                $meal,
                Carbon::parse($date)->toDateString(),
                null
            ));
            Log::info('Automatic SBFP attendance email accepted by SMTP transport.', [
                'student_id' => $student->id,
                'attendance_date' => $date,
                'recipient' => $student->guardian_email,
            ]);
        } catch (Throwable $exception) {
            Log::warning('Automatic SBFP attendance email failed.', [
                'student_id' => $student->id,
                'attendance_date' => $date,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
