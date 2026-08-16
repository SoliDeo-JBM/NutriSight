<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAssessment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin(Request $request)
    {
        $selectedTerm = $request->get('term', 'all');

        // Get all SBFP students for super admin analytics overview
        $sbfpStudents = Student::where('is_permitted', true)
            ->with(['section', 'assessments' => function($query) {
                $query->orderBy('assessment_date', 'desc');
            }])
            ->get();
        
        $sbfpStudents->each(function($student) {
            $student->termProgress = $this->groupAssessmentsByTerm($student->assessments);
        });

        $bmiDistribution = [
            'Normal' => 0,
            'Wasted' => 0,
            'Severely Wasted' => 0,
            'Overweight' => 0,
            'Obese' => 0,
        ];

        foreach ($sbfpStudents as $student) {
            if ($selectedTerm === 'all') {
                $latest = $student->assessments->first();
                if ($latest && isset($bmiDistribution[$latest->nutritional_status])) {
                    $bmiDistribution[$latest->nutritional_status]++;
                } elseif ($latest) {
                    $bmiDistribution[$latest->nutritional_status] = 1;
                }
            } else {
                $termAssessments = $student->termProgress[$selectedTerm] ?? [];
                if (!empty($termAssessments)) {
                    $assessment = $termAssessments[0];
                    if (isset($bmiDistribution[$assessment->nutritional_status])) {
                        $bmiDistribution[$assessment->nutritional_status]++;
                    } else {
                        $bmiDistribution[$assessment->nutritional_status] = 1;
                    }
                }
            }
        }

        $termAverages = ['Term 1' => 0, 'Term 2' => 0, 'Term 3' => 0];
        $termCounts = ['Term 1' => 0, 'Term 2' => 0, 'Term 3' => 0];

        foreach ($sbfpStudents as $student) {
            foreach (['Term 1', 'Term 2', 'Term 3'] as $term) {
                if (!empty($student->termProgress[$term])) {
                    $sumTermBmi = collect($student->termProgress[$term])->sum('bmi');
                    $countTermBmi = count($student->termProgress[$term]);
                    $termAverages[$term] += $sumTermBmi;
                    $termCounts[$term] += $countTermBmi;
                }
            }
        }

        $termBmiChartLabels = ['Term 1', 'Term 2', 'Term 3'];
        $termBmiChartData = [];
        foreach (['Term 1', 'Term 2', 'Term 3'] as $term) {
            $termBmiChartData[] = $termCounts[$term] > 0 ? round($termAverages[$term] / $termCounts[$term], 2) : 0;
        }

        $malnourishedTerm1Count = 0;
        $recoveredCount = 0;

        foreach ($sbfpStudents as $student) {
            $t1Assessments = $student->termProgress['Term 1'] ?? [];
            if (!empty($t1Assessments)) {
                $t1Status = $t1Assessments[0]->nutritional_status;
                if (in_array($t1Status, ['Wasted', 'Severely Wasted'])) {
                    $malnourishedTerm1Count++;
                    $latestStatus = null;
                    foreach (['Term 3', 'Term 2', 'Term 1'] as $t) {
                        if (!empty($student->termProgress[$t])) {
                            $latestStatus = $student->termProgress[$t][0]->nutritional_status;
                            break;
                        }
                    }
                    if ($latestStatus === 'Normal') {
                        $recoveredCount++;
                    }
                }
            }
        }

        $recoveryRate = $malnourishedTerm1Count > 0 ? round(($recoveredCount / $malnourishedTerm1Count) * 100, 1) : 0;

        // 4. Section-wise Attendance Rate
        $sections = \App\Models\Section::with('students.attendanceLogs')->get();
        $sectionAttendanceLabels = [];
        $sectionAttendanceRates = [];

        foreach ($sections as $section) {
            $totalLogs = 0;
            $presentLogs = 0;
            foreach ($section->students as $student) {
                foreach ($student->attendanceLogs as $log) {
                    $totalLogs++;
                    if ($log->status === 'present') {
                        $presentLogs++;
                    }
                }
            }
            $rate = $totalLogs > 0 ? round(($presentLogs / $totalLogs) * 100, 1) : 0;
            $sectionAttendanceLabels[] = $section->name;
            $sectionAttendanceRates[] = $rate;
        }

        return view('dashboards.super-admin', compact('sbfpStudents', 'bmiDistribution', 'termBmiChartLabels', 'termBmiChartData', 'malnourishedTerm1Count', 'recoveredCount', 'recoveryRate', 'selectedTerm', 'sectionAttendanceLabels', 'sectionAttendanceRates'));
    }

    public function admin(Request $request)
    {
        $selectedTerm = $request->get('term', 'all');

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

        // 1. BMI Distribution (Filtered by selected term)
        $bmiDistribution = [
            'Normal' => 0,
            'Wasted' => 0,
            'Severely Wasted' => 0,
            'Overweight' => 0,
            'Obese' => 0,
        ];

        foreach ($sbfpStudents as $student) {
            if ($selectedTerm === 'all') {
                $latest = $student->assessments->first();
                if ($latest && isset($bmiDistribution[$latest->nutritional_status])) {
                    $bmiDistribution[$latest->nutritional_status]++;
                } elseif ($latest) {
                    $bmiDistribution[$latest->nutritional_status] = 1;
                }
            } else {
                $termAssessments = $student->termProgress[$selectedTerm] ?? [];
                if (!empty($termAssessments)) {
                    $assessment = $termAssessments[0];
                    if (isset($bmiDistribution[$assessment->nutritional_status])) {
                        $bmiDistribution[$assessment->nutritional_status]++;
                    } else {
                        $bmiDistribution[$assessment->nutritional_status] = 1;
                    }
                }
            }
        }

        // 2. BMI Trend per Term (Average BMI across terms)
        $termAverages = [
            'Term 1' => 0,
            'Term 2' => 0,
            'Term 3' => 0,
        ];
        $termCounts = [
            'Term 1' => 0,
            'Term 2' => 0,
            'Term 3' => 0,
        ];

        foreach ($sbfpStudents as $student) {
            foreach (['Term 1', 'Term 2', 'Term 3'] as $term) {
                if (!empty($student->termProgress[$term])) {
                    $sumTermBmi = collect($student->termProgress[$term])->sum('bmi');
                    $countTermBmi = count($student->termProgress[$term]);
                    $termAverages[$term] += $sumTermBmi;
                    $termCounts[$term] += $countTermBmi;
                }
            }
        }

        $termBmiChartLabels = ['Term 1', 'Term 2', 'Term 3'];
        $termBmiChartData = [];
        foreach (['Term 1', 'Term 2', 'Term 3'] as $term) {
            $termBmiChartData[] = $termCounts[$term] > 0 ? round($termAverages[$term] / $termCounts[$term], 2) : 0;
        }

        // 3. Recovery & Conversion Rate
        $malnourishedTerm1Count = 0;
        $recoveredCount = 0;

        foreach ($sbfpStudents as $student) {
            $t1Assessments = $student->termProgress['Term 1'] ?? [];
            if (!empty($t1Assessments)) {
                $t1Status = $t1Assessments[0]->nutritional_status;
                if (in_array($t1Status, ['Wasted', 'Severely Wasted'])) {
                    $malnourishedTerm1Count++;
                    $latestStatus = null;
                    foreach (['Term 3', 'Term 2', 'Term 1'] as $t) {
                        if (!empty($student->termProgress[$t])) {
                            $latestStatus = $student->termProgress[$t][0]->nutritional_status;
                            break;
                        }
                    }
                    if ($latestStatus === 'Normal') {
                        $recoveredCount++;
                    }
                }
            }
        }

        $recoveryRate = $malnourishedTerm1Count > 0 ? round(($recoveredCount / $malnourishedTerm1Count) * 100, 1) : 0;

        // 4. Section-wise Attendance Rate
        $sections = \App\Models\Section::with('students.attendanceLogs')->get();
        $sectionAttendanceLabels = [];
        $sectionAttendanceRates = [];

        foreach ($sections as $section) {
            $totalLogs = 0;
            $presentLogs = 0;
            foreach ($section->students as $student) {
                foreach ($student->attendanceLogs as $log) {
                    $totalLogs++;
                    if ($log->status === 'present') {
                        $presentLogs++;
                    }
                }
            }
            $rate = $totalLogs > 0 ? round(($presentLogs / $totalLogs) * 100, 1) : 0;
            $sectionAttendanceLabels[] = $section->name;
            $sectionAttendanceRates[] = $rate;
        }

        return view('dashboards.admin', compact('sbfpStudents', 'bmiDistribution', 'termBmiChartLabels', 'termBmiChartData', 'malnourishedTerm1Count', 'recoveredCount', 'recoveryRate', 'selectedTerm', 'sectionAttendanceLabels', 'sectionAttendanceRates'));
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