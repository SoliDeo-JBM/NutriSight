<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAttendanceRecord;
use App\Services\SchoolYearManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Aggregate attendance in one query instead of loading every log per grade.
        $gradeLevels = [0, 1, 2, 3, 4, 5, 6];
        $gradeLabels = ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
        $sectionAttendanceLabels = $gradeLabels;
        $attendanceByGrade = DB::table('student_attendance_records as records')
            ->join('sbfp_participants as participants', 'participants.id', '=', 'records.sbfp_participant_id')
            ->join('enrollments', 'enrollments.id', '=', 'participants.enrollment_id')
            ->where('enrollments.school_year_id', $activeSyId)
            ->whereIn('enrollments.grade_level', $gradeLevels)
            ->groupBy('enrollments.grade_level')
            ->select([
                'enrollments.grade_level',
                DB::raw('COUNT(*) as total_logs'),
                DB::raw("SUM(CASE WHEN records.status = 'present' THEN 1 ELSE 0 END) as present_logs"),
            ])
            ->get()
            ->keyBy('grade_level');

        $sectionAttendanceRates = collect($gradeLevels)->map(function ($gradeLevel) use ($attendanceByGrade) {
            $attendance = $attendanceByGrade->get($gradeLevel);

            return $attendance && $attendance->total_logs > 0
                ? round(($attendance->present_logs / $attendance->total_logs) * 100, 1)
                : 0;
        })->all();

        return view('dashboards.admin', compact('sbfpStudents', 'bmiDistribution', 'termBmiChartLabels', 'termBmiChartData', 'malnourishedTerm1Count', 'recoveredCount', 'recoveryRate', 'selectedTerm', 'sectionAttendanceLabels', 'sectionAttendanceRates'));
    }

    public function encoder()
    {
        $user = auth()->user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        
        $studentQuery = Student::whereHas('enrollments', function($q) use ($activeSyId, $user) {
            $q->where('school_year_id', $activeSyId);
            if ($user && $user->isEncoder()) {
                if ($user->advisory_grade_level === null || $user->advisory_section === null) {
                    $q->whereRaw('1 = 0');
                } else {
                    $q->where('grade_level', $user->advisory_grade_level)
                      ->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($user->advisory_section))]);
                }
            }
        });

        $totalStudents = (clone $studentQuery)->count();
        $totalSbfp = (clone $studentQuery)->whereHas('enrollments.sbfpParticipant', function($q) {
            $q->where('parent_consent', 'approved');
        })->count();
        
        $attendanceDates = [];
        for ($i = 6; $i >= 0; $i--) {
            $attendanceDates[] = Carbon::today()->subDays($i)->toDateString();
        }

        $attendanceCountsByDate = StudentAttendanceRecord::query()
            ->join('sbfp_participants', 'sbfp_participants.id', '=', 'student_attendance_records.sbfp_participant_id')
            ->join('enrollments', 'enrollments.id', '=', 'sbfp_participants.enrollment_id')
            ->where('enrollments.school_year_id', $activeSyId)
            ->whereIn('student_attendance_records.attendance_date', $attendanceDates)
            ->where('student_attendance_records.status', 'present')
            ->when($user && $user->isEncoder(), function ($query) use ($user) {
                if ($user->advisory_grade_level === null || $user->advisory_section === null) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->where('enrollments.grade_level', $user->advisory_grade_level)
                        ->whereRaw('LOWER(TRIM(enrollments.section)) = ?', [strtolower(trim($user->advisory_section))]);
                }
            })
            ->groupBy('student_attendance_records.attendance_date')
            ->selectRaw('student_attendance_records.attendance_date, COUNT(*) as total_count')
            ->pluck('total_count', 'student_attendance_records.attendance_date');

        $attendanceCounts = collect($attendanceDates)
            ->map(fn ($date) => (int) ($attendanceCountsByDate[$date] ?? 0))
            ->all();

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
