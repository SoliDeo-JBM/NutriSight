<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAttendanceRecord;
use App\Services\SchoolYearManager;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', Carbon::today()->year);
        $month = $request->input('month', Carbon::today()->month);
        
        $defaultDate = Carbon::create($year, $month, 1)->toDateString();
        $date = $request->input('date', $defaultDate);
        
        $user = auth()->user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $studentQuery = Student::with(['enrollments.sbfpParticipant.nutritionMeasurements'])
            ->whereHas('enrollments', function($q) use ($activeSyId, $user) {
                $q->where('school_year_id', $activeSyId);
                if ($user && $user->isEncoder()) {
                    $q->where('grade_level', $user->advisory_grade_level)
                      ->where('section', $user->advisory_section);
                }
            });

        $sbfpStudents = $studentQuery->get()->filter(function ($student) use ($activeSyId) {
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
                'error' => 'Student not found in active school year',
                'student_name' => null,
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
            'recorded_by_user_id' => auth()->id(),
            'attendance_date' => $today,
            'status' => 'present',
        ]);

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
        $request->validate([
            'sbfp_participant_id' => 'required|exists:sbfp_participants,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,tardy'
        ]);

        StudentAttendanceRecord::updateOrCreate(
            [
                'sbfp_participant_id' => $request->sbfp_participant_id,
                'attendance_date' => $request->date,
            ],
            [
                'recorded_by_user_id' => auth()->id(),
                'status' => $request->status
            ]
        );

        AuditLogger::log('Updated', 'Attendance', 'Updated attendance status for participant ID ' . $request->sbfp_participant_id . ' on ' . $request->date);

        return back()->with('success', 'Attendance updated.');
    }
}
