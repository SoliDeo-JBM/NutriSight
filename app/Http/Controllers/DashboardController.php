<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAttendanceRecord;
use App\Models\User;
use App\Services\SchoolYearManager;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function landing()
    {
        return view('welcome', [
            'landingPreviewMetrics' => $this->landingPreviewMetrics(),
            'landingAssessmentMetrics' => $this->landingAssessmentMetrics(),
        ]);
    }

    public function superAdmin(Request $request)
    {
        return $this->admin($request);
    }

    public function admin(Request $request)
    {
        $selectedPeriod = $request->get('period', 'Baseline');
        if (!in_array($selectedPeriod, ['Baseline', 'Midline', 'Endline'], true)) {
            $selectedPeriod = 'Baseline';
        }
        $periods = ['Baseline', 'Midline', 'Endline'];
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $activeSchoolYear = SchoolYearManager::activeSchoolYear();

        // Get all SBFP participants for active school year
        $students = Student::with(['enrollments' => function ($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)->with(['sbfpParticipant.nutritionMeasurements' => function ($sub) {
                $sub->orderBy('created_at', 'desc');
            }]);
        }])->whereHas('enrollments', function ($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)->whereHas('sbfpParticipant', function ($sub) {
                $sub->where('parent_consent', 'approved')
                    ->whereHas('nutritionMeasurements', function ($measurementQuery) {
                        $measurementQuery->where('measurement_period', 'baseline')
                            ->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                    });
            });
        })->get();

        $sbfpStudents = $students->map(function ($student) use ($activeSyId) {
            $enrollment = $student->enrollments->where('school_year_id', $activeSyId)->first();
            $participant = $enrollment?->sbfpParticipant;
            $measurements = $participant ? $participant->nutritionMeasurements : collect();
            $student->grade_level = $enrollment?->grade_level;
            $student->section = $enrollment?->section;
            $student->dashboardPeriods = $this->groupMeasurementsByPeriod($measurements);
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
            $measurement = $this->measurementForPeriod($student->dashboardPeriods, $selectedPeriod);
            if ($measurement && isset($bmiDistribution[$measurement->bmi_category])) {
                $bmiDistribution[$measurement->bmi_category]++;
            }
        }

        $periodAverages = array_fill_keys($periods, 0);
        $periodCounts = array_fill_keys($periods, 0);

        foreach ($sbfpStudents as $student) {
            foreach ($periods as $period) {
                $measurement = $this->measurementForPeriod($student->dashboardPeriods, $period);
                if ($measurement) {
                    $periodAverages[$period] += $measurement->bmi;
                    $periodCounts[$period]++;
                }
            }
        }

        $periodBmiChartLabels = $periods;
        $periodBmiChartData = [];
        foreach ($periods as $period) {
            $periodBmiChartData[] = $periodCounts[$period] > 0
                ? round($periodAverages[$period] / $periodCounts[$period], 2)
                : 0;
        }

        $recoveredCount = 0;

        foreach ($sbfpStudents as $student) {
            $measurement = $this->measurementForPeriod($student->dashboardPeriods, $selectedPeriod);
            if ($measurement?->bmi_category === 'Normal') {
                $recoveredCount++;
            }
        }

        $totalSbfpStudents = $sbfpStudents->count();
        $recoveryRate = $totalSbfpStudents > 0 ? round(($recoveredCount / $totalSbfpStudents) * 100, 1) : 0;

        // Aggregate attendance in one query instead of loading every log per grade.
        $gradeLevels = [0, 1, 2, 3, 4, 5, 6, 7];
        $gradeLabels = ['Kinder', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'SPED'];
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

        $dashboardRoute = Auth::user()->role === 'super_admin' ? 'super-admin.dashboard' : 'admin.dashboard';
        $dashboardTitle = Auth::user()->role === 'super_admin'
            ? 'Super Admin Dashboard - System Nutrition Overview'
            : 'Admin Dashboard - Nutritional Analytics & Period Progress';

        return view('dashboards.admin', compact('sbfpStudents', 'totalSbfpStudents', 'bmiDistribution', 'periodBmiChartLabels', 'periodBmiChartData', 'recoveredCount', 'recoveryRate', 'selectedPeriod', 'activeSchoolYear', 'sectionAttendanceLabels', 'sectionAttendanceRates', 'dashboardRoute', 'dashboardTitle'));
    }

    public function encoder()
    {
        /** @var User $user */
        $user = Auth::user();
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $studentQuery = Student::whereHas('enrollments', function ($q) use ($activeSyId, $user) {
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
        $totalSbfp = (clone $studentQuery)->whereHas('enrollments', function ($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)
                ->whereHas('sbfpParticipant', function ($participantQuery) {
                    $participantQuery->where('parent_consent', 'approved')
                        ->whereHas('nutritionMeasurements', function ($measurementQuery) {
                            $measurementQuery->where('measurement_period', 'baseline')
                                ->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                        });
                });
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
            ->map(fn($date) => (int) ($attendanceCountsByDate[$date] ?? 0))
            ->all();

        return view('dashboards.encoder', compact('totalStudents', 'totalSbfp', 'attendanceDates', 'attendanceCounts'));
    }

    private function groupMeasurementsByPeriod($measurements)
    {
        $periods = ['Baseline' => [], 'Midline' => [], 'Endline' => []];
        foreach ($measurements as $m) {
            $period = match (strtolower($m->measurement_period ?? '')) {
                'baseline' => 'Baseline',
                'midline', 'mid' => 'Midline',
                'endline', 'end' => 'Endline',
                default => null,
            };
            if ($period) {
                $periods[$period][] = $m;
            }
        }
        return $periods;
    }

    private function measurementForPeriod(array $periodProgress, string $selectedPeriod)
    {
        return !empty($periodProgress[$selectedPeriod])
            ? $periodProgress[$selectedPeriod][0]
            : null;
    }

    private function landingPreviewMetrics(): array
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $periods = ['Baseline', 'Midline', 'Endline'];
        $students = Student::with(['enrollments' => function ($query) use ($activeSyId) {
            $query->where('school_year_id', $activeSyId)->with(['sbfpParticipant.nutritionMeasurements' => function ($measurementQuery) {
                $measurementQuery->orderBy('created_at', 'desc');
            }]);
        }])->whereHas('enrollments', function ($query) use ($activeSyId) {
            $query->where('school_year_id', $activeSyId)->whereHas('sbfpParticipant', function ($participantQuery) {
                $participantQuery->where('parent_consent', 'approved')
                    ->whereHas('nutritionMeasurements', function ($measurementQuery) {
                        $measurementQuery->where('measurement_period', 'baseline')
                            ->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                    });
            });
        })->get()->map(function ($student) use ($activeSyId) {
            $enrollment = $student->enrollments->where('school_year_id', $activeSyId)->first();
            $student->dashboardPeriods = $this->groupMeasurementsByPeriod($enrollment?->sbfpParticipant?->nutritionMeasurements ?? collect());
            return $student;
        });

        $total = $students->count();
        $metrics = [];

        foreach ($periods as $period) {
            $distribution = array_fill_keys(['Normal', 'Wasted', 'Severely Wasted', 'Overweight', 'Obese'], 0);
            $periodBmiTotal = 0;
            $periodBmiCount = 0;

            foreach ($students as $student) {
                $measurement = $this->measurementForPeriod($student->dashboardPeriods, $period);
                if (!$measurement) {
                    continue;
                }

                if (array_key_exists($measurement->bmi_category, $distribution)) {
                    $distribution[$measurement->bmi_category]++;
                }
                $periodBmiTotal += $measurement->bmi;
                $periodBmiCount++;
            }

            $normal = $distribution['Normal'];
            $metrics[$period] = [
                'total' => $total,
                'recovery' => $total > 0 ? round(($normal / $total) * 100, 1) : 0,
                'recovered' => $normal,
                'normal' => $normal,
                'atRisk' => $distribution['Wasted'] + $distribution['Severely Wasted'],
                'averages' => $periodBmiCount > 0 ? round($periodBmiTotal / $periodBmiCount, 2) : 0,
                'trend' => $this->landingBmiTrend($students, $periods),
                'distribution' => array_values($distribution),
            ];
        }

        return $metrics;
    }

    private function landingBmiTrend($students, array $periods): array
    {
        return collect($periods)->map(function ($period) use ($students) {
            $measurements = $students->map(fn ($student) => $this->measurementForPeriod($student->dashboardPeriods, $period))->filter();
            return $measurements->isNotEmpty() ? round($measurements->avg('bmi'), 2) : 0;
        })->values()->all();
    }

    private function landingAssessmentMetrics(): array
    {
        $schoolYear = SchoolYearManager::activeSchoolYear();
        $students = Student::with(['enrollments' => function ($query) use ($schoolYear) {
            $query->where('school_year_id', $schoolYear?->id)
                ->with(['sbfpParticipant.nutritionMeasurements', 'sbfpParticipant.attendanceRecords']);
        }])->whereHas('enrollments', function ($query) use ($schoolYear) {
            $query->where('school_year_id', $schoolYear?->id)
                ->whereHas('sbfpParticipant', fn ($participant) => $participant->where('parent_consent', 'approved'));
        })->get();

        $attendanceStudents = 0;
        $completeAttendance = 0;
        $malnourished = 0;
        $recovered = 0;

        foreach ($students as $student) {
            $participant = $student->enrollments->first()?->sbfpParticipant;
            $records = $participant?->attendanceRecords ?? collect();

            if ($records->isNotEmpty()) {
                $attendanceStudents++;
                if ($records->every(fn ($record) => strtolower((string) $record->status) !== 'absent')) {
                    $completeAttendance++;
                }
            }

            $measurements = $participant?->nutritionMeasurements?->sortByDesc('created_at') ?? collect();
            $baseline = $measurements->firstWhere('measurement_period', 'baseline');
            if ($baseline && in_array($baseline->bmi_category, ['Wasted', 'Severely Wasted'], true)) {
                $malnourished++;
                $endline = $measurements->firstWhere('measurement_period', 'endline');
                if ($endline?->bmi_category === 'Normal') {
                    $recovered++;
                }
            }
        }

        $withAbsences = $attendanceStudents - $completeAttendance;
        $stillNeedingSupport = $malnourished - $recovered;

        return [
            'schoolYear' => $schoolYear?->year ?? 'No active school year',
            'attendanceStudents' => $attendanceStudents,
            'completeAttendance' => $completeAttendance,
            'completeAttendanceRate' => $attendanceStudents ? round($completeAttendance / $attendanceStudents * 100, 1) : 0,
            'withAbsences' => $withAbsences,
            'withAbsencesRate' => $attendanceStudents ? round($withAbsences / $attendanceStudents * 100, 1) : 0,
            'malnourished' => $malnourished,
            'recovered' => $recovered,
            'recoveredRate' => $malnourished ? round($recovered / $malnourished * 100, 1) : 0,
            'stillNeedingSupport' => $stillNeedingSupport,
            'stillNeedingSupportRate' => $malnourished ? round($stillNeedingSupport / $malnourished * 100, 1) : 0,
        ];
    }
}
