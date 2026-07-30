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
        
        // Exclude disapproved students from attendance roster
        $sbfpStudents = Student::with('nutritionalRecords')
            ->get()
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

        $student = Student::where('student_number', $request->student_number)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Check if student is disapproved for SBFP
        if ($student->parent_approval_status === 'disapproved') {
            return response()->json(['error' => 'Student is disapproved for SBFP.'], 403);
        }

        // Automatically permit/include student in SBFP if scanned for attendance and not already disapproved
        if (!$student->is_permitted) {
            $student->update([
                'is_permitted' => true,
                'parent_approval_status' => 'approved'
            ]);
        }

        AttendanceLog::updateOrCreate(
            [
                'student_id' => $student->id,
                'date' => now()->toDateString(),
            ],
            [
                'status' => 'present'
            ]
        );

        return response()->json(['success' => 'Attendance logged for ' . $student->first_name . ' ' . $student->last_name]);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,tardy'
        ]);

        AttendanceLog::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'date' => $request->date,
            ],
            [
                'status' => $request->status
            ]
        );

        return back()->with('success', 'Attendance updated.');
    }
}
