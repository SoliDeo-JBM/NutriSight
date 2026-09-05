<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\SchoolYearManager;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function admin()
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $students = Student::with(['enrollments' => function($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)->with(['sbfpParticipant.nutritionMeasurements' => function($sub) {
                $sub->orderBy('created_at', 'desc');
            }]);
        }])->whereHas('enrollments', function($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)->whereHas('sbfpParticipant', function($sub) {
                $sub->where('parent_consent', 'approved');
            });
        })->get();
        
        $sbfpStudents = $students->map(function($student) use ($activeSyId) {
            $enrollment = $student->enrollments->where('school_year_id', $activeSyId)->first();
            $participant = $enrollment?->sbfpParticipant;
            $measurements = $participant ? $participant->nutritionMeasurements : collect();
            $student->grade_level = $enrollment?->grade_level;
            $student->section = $enrollment?->section;
            $student->quarterlyProgress = $this->groupMeasurementsByQuarter($measurements);
            return $student;
        });
        
        return view('dashboards.reports.admin', compact('sbfpStudents'));
    }
    
    private function groupMeasurementsByQuarter($measurements)
    {
        $quarters = [
            '1st Quarter' => [],
            '2nd Quarter' => [],
            '3rd Quarter' => [],
            '4th Quarter' => []
        ];
        
        foreach ($measurements as $m) {
            $month = $m->created_at->month;
            if ($month >= 1 && $month <= 3) {
                $quarters['1st Quarter'][] = $m;
            } elseif ($month >= 4 && $month <= 6) {
                $quarters['2nd Quarter'][] = $m;
            } elseif ($month >= 7 && $month <= 9) {
                $quarters['3rd Quarter'][] = $m;
            } else {
                $quarters['4th Quarter'][] = $m;
            }
        }
        
        return $quarters;
    }
}
