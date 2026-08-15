<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAssessment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin()
    {
        return view('dashboards.super-admin');
    }

    public function admin()
    {
        // Get all SBFP students (is_permitted = true)
        $sbfpStudents = Student::where('is_permitted', true)
            ->with(['section', 'assessments' => function($query) {
                $query->orderBy('assessment_date', 'desc');
            }])
            ->get();
        
        // Organize assessments by term for each student
        $sbfpStudents->each(function($student) {
            $student->termProgress = $this->groupAssessmentsByTerm($student->assessments);
        });
        
        return view('dashboards.admin', compact('sbfpStudents'));
    }

    public function encoder()
    {
        $totalStudents = Student::count();
        $totalSbfp = Student::where('is_permitted', true)->count();
        
        // Attendance chart data (last 7 days)
        $attendanceDates = [];
        $attendanceCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $attendanceDates[] = Carbon::parse($date)->format('M d');
            $attendanceCounts[] = \App\Models\AttendanceLog::where('date', $date)->where('status', 'present')->count();
        }

        return view('dashboards.encoder', compact('totalStudents', 'totalSbfp', 'attendanceDates', 'attendanceCounts'));
    }

private function groupAssessmentsByTerm($assessments)
{
    $terms = [
        'Term 1' => [],
        'Term 2' => [],
        'Term 3' => []
    ];
    
    foreach ($assessments as $assessment) {
        $month = $assessment->assessment_date->month;
        
        if ($month == 1) {
            $terms['Term 1'][] = $assessment;
        } elseif ($month == 2) {
            $terms['Term 2'][] = $assessment;
        } else {
            $terms['Term 3'][] = $assessment;
        }
    }
    
    return $terms;
}
}