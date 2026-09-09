<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAttendanceRecord;
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
        $year = $request->input('year', Carbon::today()->year);
        $month = $request->input('month', Carbon::today()->month);
        
        $defaultDate = Carbon::create($year, $month, 1)->toDateString();
        $date = $request->input('date', $defaultDate);
        
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $studentQuery = Student::with([
                'enrollments' => function ($q) use ($activeSyId) {
                    $q->where('school_year_id', $activeSyId)
                        ->with('sbfpParticipant.nutritionMeasurements');
                },
            ])
            ->whereHas('enrollments', function($q) use ($activeSyId, $user) {
                $q->where('school_year_id', $activeSyId);
                if ($user && $user->isEncoder() && $user->advisory_grade_level && $user->advisory_section) {
                    $q->where('grade_level', $user->advisory_grade_level)
                      ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($user->advisory_section))]);
                }
            });

        $sbfpStudents = $studentQuery->get()->filter(function ($student) use ($activeSyId, $date) {
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

            if ($participant->parent_consent === 'disapproved') {
                return false;
            }
            $latestMeasurement = $participant->nutritionMeasurements()->latest()->first();
            $isWasted = $latestMeasurement && in_array($latestMeasurement->bmi_category, ['Wasted', 'Severely Wasted']);
            return $participant->parent_consent === 'approved' || $isWasted;
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

        return view('attendance.index', compact('sbfpStudents', 'attendanceLogs', 'date', 'loggedDates'));
    }

    public function scan(Request $request)
    {
        $request->validate(['lrn' => 'required']);

        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $student = Student::with(['enrollments' => function($q) use ($activeSyId) {
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

        if ($participant->parent_consent === 'disapproved') {
            return response()->json([
                'error' => 'Student is disapproved for SBFP.',
                'student_name' => $studentName,
                'grade_level' => $enrollment->grade_level,
                'section' => $enrollment->section
            ], 403);
        }

        $today = now()->toDateString();
        $existingLog = StudentAttendanceRecord::where('sbfp_participant_id', $participant->id)
            ->where('attendance_date', $today)
            ->first();

        if ($existingLog) {
            return response()->json([
                'error' => 'Attendance already recorded for today.',
                'student_name' => $studentName,
                'grade_level' => $enrollment->grade_level,
                'section' => $enrollment->section
            ], 409);
        }

        if ($participant->parent_consent !== 'approved') {
            $participant->update(['parent_consent' => 'approved']);
        }

        StudentAttendanceRecord::create([
            'sbfp_participant_id' => $participant->id,
            'recorded_by_user_id' => Auth::id(),
            'attendance_date' => $today,
            'status' => 'present',
        ]);

        $this->sendAttendanceNotice($student, $today);

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
            'status' => 'required|in:present,absent,tardy'
        ]);

        $participant = SbfpParticipant::with('enrollment.student')->findOrFail($validated['sbfp_participant_id']);
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

        if (in_array($validated['status'], ['present', 'tardy'], true)
            && (!$existingRecord || !in_array($existingRecord->status, ['present', 'tardy'], true))) {
            $this->sendAttendanceNotice($participant->enrollment->student, $validated['date']);
        }

        AuditLogger::log('Updated', 'Attendance', 'Updated attendance status for participant ID ' . $validated['sbfp_participant_id'] . ' on ' . $validated['date']);

        return back()->with('success', 'Attendance updated.');
    }

    private function sendAttendanceNotice(Student $student, string $date): void
    {
        if (!$student->guardian_email) {
            return;
        }

        try {
            Mail::to($student->guardian_email)->send(new FeedingDayNotice(
                $student,
                'SBFP feeding program meal',
                Carbon::parse($date)->toDateString(),
                'Your child attended the feeding program today.'
            ));
        } catch (Throwable $exception) {
            Log::warning('Automatic SBFP attendance email failed.', [
                'student_id' => $student->id,
                'attendance_date' => $date,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
