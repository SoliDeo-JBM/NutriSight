<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAssessment;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function admin()
    {
        // Get all SBFP students (is_permitted = true)
        $sbfpStudents = Student::where('is_permitted', true)
            ->with(['section', 'assessments' => function($query) {
                $query->orderBy('assessment_date', 'desc');
            }])
            ->get();
        
        // Organize assessments by quarter for each student
        $sbfpStudents->each(function($student) {
            $student->quarterlyProgress = $this->groupAssessmentsByQuarter($student->assessments);
        });
        
        return view('dashboards.reports.admin', compact('sbfpStudents'));
    }
    
    private function groupAssessmentsByQuarter($assessments)
    {
        $quarters = [
            '1st Quarter' => [],
            '2nd Quarter' => [],
            '3rd Quarter' => [],
            '4th Quarter' => []
        ];
        
        foreach ($assessments as $assessment) {
            $month = $assessment->assessment_date->month;
            
            if ($month >= 1 && $month <= 3) {
                $quarters['1st Quarter'][] = $assessment;
            } elseif ($month >= 4 && $month <= 6) {
                $quarters['2nd Quarter'][] = $assessment;
            } elseif ($month >= 7 && $month <= 9) {
                $quarters['3rd Quarter'][] = $assessment;
            } else {
                $quarters['4th Quarter'][] = $assessment;
            }
        }
        
        return $quarters;
    }
}