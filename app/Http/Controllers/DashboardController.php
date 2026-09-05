<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAttendanceRecord;
use App\Services\SchoolYearManager;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function superAdmin(Request $request)
    {
        return $this->admin($request);
    }

    public function admin(Request $request)
    {
        $selectedTerm = $request->get('term', 'all');
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        // Get all SBFP participants for active school year
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
            $student->termProgress = $this->groupMeasurementsByTerm($measurements);
            $student->assessments = $measurements;
            return $student;
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
                if ($latest && isset($bmiDistribution[$latest->bmi_category])) {
                    $bmiDistribution[$latest->bmi_category]++;
                } elseif ($latest) {
                    $bmiDistribution[$latest->bmi_category] = 1;
                }
            } else {
                $termMeasurements = $student->termProgress[$selectedTerm] ?? [];
                if (!empty($termMeasurements)) {
                    $measurement = $termMeasurements[0];
                    if (isset($bmiDistribution[$measurement->bmi_category])) {
                        $bmiDistribution[$measurement->bmi_category]++;
                    } else {
                        $bmiDistribution[$measurement->bmi_category] = 1;
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
            $t1Measurements = $student->termProgress['Term 1'] ?? [];
            if (!empty($t1Measurements)) {
                $t1Status = $t1Measurements[0]->bmi_category;
                if (in_array($t1Status, ['Wasted', 'Severely Wasted'])) {
                    $malnourishedTerm1Count++;
                    $latestStatus = null;
                    foreach (['Term 3', 'Term 2', 'Term 1'] as $t) {
                        if (!empty($student->termProgress[$t])) {
                            $latestStatus = $student->termProgress[$t][0]->bmi_category;
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

        // Grade Level Attendance Rate
        $gradeLevels = [0, 1, 2, 3, 4, 5, 6]; // or kindergarten/grades
        $gradeLabels = ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $sectionAttendanceLabels = $gradeLabels;
        $sectionAttendanceRates = [];

        foreach ($gradeLevels as $index => $gradeVal) {
            $totalLogs = 0;
            $presentLogs = 0;
            $gradeStudents = Student::with(['enrollments.sbfpParticipant.attendanceRecords'])
                ->whereHas('enrollments', function($q) use ($activeSyId, $gradeVal) {
                    $q->where('school_year_id', $activeSyId)->where('grade_level', $gradeVal);
                })->get();

            foreach ($gradeStudents as $student) {
                $enrollment = $student->enrollments->where('school_year_id', $activeSyId)->first();
                if ($enrollment && $enrollment->sbfpParticipant) {
                    foreach ($enrollment->sbfpParticipant->attendanceRecords as $log) {
                        $totalLogs++;
                        if ($log->status === 'present') {
                            $presentLogs++;
                        }
                    }
                }
            }
            $sectionAttendanceRates[] = $totalLogs > 0 ? round(($presentLogs / $totalLogs) * 100, 1) : 0;
        }

        return view('dashboards.admin', compact('sbfpStudents', 'bmiDistribution', 'termBmiChartLabels', 'termBmiChartData', 'malnourishedTerm1Count', 'recoveredCount', 'recoveryRate', 'selectedTerm', 'sectionAttendanceLabels', 'sectionAttendanceRates'));
    }

    public function encoder()
    {
        $user = auth()->user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        
        $studentQuery = Student::whereHas('enrollments', function($q) use ($activeSyId, $user) {
            $q->where('school_year_id', $activeSyId);
            if ($user && $user->isEncoder()) {
                $q->where('grade_level', $user->advisory_grade_level)
                  ->where('section', $user->advisory_section);
            }
        });

        $totalStudents = (clone $studentQuery)->count();
        $totalSbfp = (clone $studentQuery)->whereHas('enrollments.sbfpParticipant', function($q) {
            $q->where('parent_consent', 'approved');
        })->count();
        
        $attendanceDates = [];
        $attendanceCounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $attendanceDates[] = Carbon::parse($date)->format('M d');
            
            $attendanceCounts[] = StudentAttendanceRecord::where('attendance_date', $date)
                ->where('status', 'present')
                ->whereHas('sbfpParticipant.enrollment', function($q) use ($activeSyId, $user) {
                    $q->where('school_year_id', $activeSyId);
                    if ($user && $user->isEncoder()) {
                        $q->where('grade_level', $user->advisory_grade_level)
                          ->where('section', $user->advisory_section);
                    }
                })
                ->count();
        }

        return view('dashboards.encoder', compact('totalStudents', 'totalSbfp', 'attendanceDates', 'attendanceCounts'));
    }

    private function groupMeasurementsByTerm($measurements)
    {
        $terms = ['Term 1' => [], 'Term 2' => [], 'Term 3' => []];
        foreach ($measurements as $m) {
            $month = $m->created_at->month;
            if ($month == 1) {
                $terms['Term 1'][] = $m;
            } elseif ($month == 2) {
                $terms['Term 2'][] = $m;
            } else {
                $terms['Term 3'][] = $m;
            }
        }
        return $terms;
    }
}
