<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AttendanceLog;
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
        
        // Exclude disapproved students from attendance roster and scope to encoder's advisory section/grade level
        $user = auth()->user();
        $activeSyId = \App\Services\SchoolYearManager::activeSchoolYearId();
        $studentQuery = Student::with('nutritionalRecords')->where('school_year_id', $activeSyId);
        if ($user && $user->isEncoder()) {
            $activeSectionIds = $user->activeSections()->pluck('id');
            $studentQuery->whereIn('section_id', $activeSectionIds);
        }
        $sbfpStudents = $studentQuery->get()
            ->filter(function ($student) {
                if ($student->parent_approval_status === 'disapproved') {
                    return false;
                }
                $latestRecord = $student->nutritionalRecords()->latest()->first();
                $isWasted = $latestRecord && in_array($latestRecord->bmi_category, ['Wasted', 'Severely Wasted']);
                return $student->is_permitted || $isWasted;
            });
        
        $attendanceLogs = AttendanceLog::where('date', $date)->get()->keyBy('student_id');

        $loggedDates = AttendanceLog::select('date')
            ->distinct()
            ->pluck('date')
            ->toArray();

        return view('attendance.index', compact('sbfpStudents', 'attendanceLogs', 'date', 'loggedDates'));
    }

    public function scan(Request $request)
    {
        $request->validate(['student_number' => 'required']);

        $activeSyId = \App\Services\SchoolYearManager::activeSchoolYearId();
        $student = Student::where('student_number', $request->student_number)
            ->where('school_year_id', $activeSyId)
            ->first();

        if (!$student) {
            return response()->json([
                'error' => 'Student not found in active school year',
                'student_name' => null,
                'grade_level' => null,
                'section' => null
            ], 404);
        }

        $studentName = $student->first_name . ' ' . $student->last_name;
        $gradeLevel = $student->grade_level;
        $section = $student->section;

        // Check if student is disapproved for SBFP
        if ($student->parent_approval_status === 'disapproved') {
            return response()->json([
                'error' => 'Student is disapproved for SBFP.',
                'student_name' => $studentName,
                'grade_level' => $gradeLevel,
                'section' => $section
            ], 403);
        }

        // Check if attendance already recorded today
        $existingLog = AttendanceLog::where('student_id', $student->id)
            ->where('date', now()->toDateString())
            ->where('school_year_id', $activeSyId)
            ->first();

        if ($existingLog) {
            return response()->json([
                'error' => 'Attendance already recorded for today.',
                'student_name' => $studentName,
                'grade_level' => $gradeLevel,
                'section' => $section
            ], 409);
        }

        // Automatically permit/include student in SBFP if scanned for attendance and not already disapproved
        if (!$student->is_permitted) {
            $student->update([
                'is_permitted' => true,
                'parent_approval_status' => 'approved'
            ]);
        }

        AttendanceLog::create([
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'school_year_id' => $activeSyId,
            'status' => 'present'
        ]);

        \App\Services\AuditLogger::log('Created', 'Attendance', 'Scanned QR attendance for student ' . $studentName);

        return response()->json([
            'success' => 'Attendance logged successfully.',
            'student_name' => $studentName,
            'grade_level' => $gradeLevel,
            'section' => $section
        ]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,tardy'
        ]);

        $activeSyId = \App\Services\SchoolYearManager::activeSchoolYearId();

        AttendanceLog::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'date' => $request->date,
                'school_year_id' => $activeSyId,
            ],
            [
                'status' => $request->status
            ]
        );

        \App\Services\AuditLogger::log('Updated', 'Attendance', 'Updated attendance status for student ID ' . $request->student_id . ' on ' . $request->date);

        return back()->with('success', 'Attendance updated.');
    }
}
