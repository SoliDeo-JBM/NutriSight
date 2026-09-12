<?php

namespace App\Http\Controllers;

use App\Exports\DepEdForm1Export;
use App\Models\ReportPeriod;
use App\Models\ReportPeriodRow;
use App\Models\AttendanceReportMonth;
use App\Models\AttendanceReportSection;
use App\Exports\AttendanceReportExport;
use App\Exports\AssessmentReportExport;
use App\Models\SchoolYear;
use App\Models\StudentAttendanceRecord;
use App\Models\Student;
use App\Services\SchoolYearManager;
use App\Services\ReportPeriodManager;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ReportsController extends Controller
{
    public function __construct(private readonly ReportPeriodManager $reportPeriodManager)
    {
    }

    public function admin()
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();

        $students = Student::with([
            'enrollments' => function ($q) use ($activeSyId) {
                $q->where('school_year_id', $activeSyId)->with([
                    'sbfpParticipant.nutritionMeasurements' => function ($sub) {
                        $sub->orderBy('created_at', 'desc');
                    }
                ]);
            }
        ])->whereHas('enrollments', function ($q) use ($activeSyId) {
            $q->where('school_year_id', $activeSyId)->whereHas('sbfpParticipant', function ($sub) {
                $sub->where('parent_consent', 'approved');
            });
        })->get();

        $sbfpStudents = $students->map(function ($student) use ($activeSyId) {
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


    // SBFP Reports Main Menu
    public function sbfpIndex()
    {
        return view('admin.reports.index');
    }

    // Attendance Summary Report
    public function sbfpAttendance(Request $request)
    {
        $this->reportPeriodManager->syncExistingRecords();
        $schoolYears = SchoolYear::orderByDesc('start_date')->get();
        $schoolYears->load(['attendanceReportMonths' => fn($query) => $query->orderBy('month')]);

        return view('admin.reports.attendance.index', compact('schoolYears'));
    }

    public function storeAttendanceMonth(Request $request)
    {
        $validated = $request->validate(['school_year_id' => 'required|exists:school_years,id', 'month' => 'required|integer|between:1,12']);
        if (AttendanceReportMonth::where($validated)->exists()) {
            return redirect()->back()->withErrors(['month' => 'This month already exists for the selected school year. Choose another month.'])->withInput();
        }
        $month = AttendanceReportMonth::create($validated);

        return redirect()->route('admin.reports.sbfp.attendance.month', $month)->with('success', 'Attendance month created.');
    }

    public function updateAttendanceMonth(Request $request, AttendanceReportMonth $month)
    {
        $validated = $request->validate(['month' => 'required|integer|between:1,12']);
        $duplicate = AttendanceReportMonth::where('school_year_id', $month->school_year_id)
            ->where('month', $validated['month'])->where('id', '!=', $month->id)->exists();
        if ($duplicate) {
            return redirect()->back()->withErrors(['month' => 'This month already exists for the selected school year. Choose another month.'])->withInput();
        }
        $month->update(['month' => $validated['month']]);

        return redirect()->back()->with('success', 'Attendance month updated.');
    }

    public function destroyAttendanceMonth(AttendanceReportMonth $month)
    {
        $month->delete();

        return redirect()->route('admin.reports.sbfp.attendance')->with('success', 'Attendance month deleted.');
    }

    public function showAttendanceMonth(Request $request, AttendanceReportMonth $month)
    {
        $month->load('schoolYear');
        $selectedGrade = $request->filled('grade') ? (int) $request->input('grade') : null;
        $selectedSection = $request->filled('section') ? $request->input('section') : null;
        $students = $this->attendanceStudents($month->schoolYear, $month->month, $selectedGrade, $selectedSection);
        $grades = DB::table('enrollments')->where('school_year_id', $month->school_year_id)->whereNotNull('grade_level')->distinct()->orderBy('grade_level')->pluck('grade_level');
        $sections = DB::table('enrollments')->where('school_year_id', $month->school_year_id)->whereNotNull('section')->distinct()->orderBy('section')->pluck('section');
        return view('admin.reports.attendance.month', ['schoolYear' => $month->schoolYear, 'reportMonth' => $month, 'month' => $month->month, 'calendarYear' => $this->attendanceCalendarYear($month->schoolYear, $month->month), 'students' => $students, 'grades' => $grades, 'sections' => $sections, 'selectedGrade' => $selectedGrade, 'selectedSection' => $selectedSection]);
    }

    public function storeAttendanceSection(Request $request, AttendanceReportMonth $month)
    {
        $validated = $request->validate(['grade_level' => 'required|integer|between:0,7', 'section' => 'required|string|max:100']);
        $validated['section'] = trim($validated['section']);
        if (AttendanceReportSection::where('attendance_report_month_id', $month->id)->where($validated)->exists()) {
            return redirect()->back()->withErrors(['section' => 'This section already exists for the selected grade and month.'])->withInput();
        }
        $section = AttendanceReportSection::create(['attendance_report_month_id' => $month->id] + $validated);
        return redirect()->route('admin.reports.sbfp.attendance.grade', [$month, $section->grade_level])->with('success', 'Attendance section created.');
    }

    public function showAttendanceGrade(AttendanceReportMonth $month, int $grade)
    {
        abort_unless($grade >= 0 && $grade <= 7, 404);
        $month->load(['schoolYear', 'sections' => fn($query) => $query->where('grade_level', $grade)->orderBy('section')]);
        return view('admin.reports.attendance.sections', compact('month', 'grade'));
    }

    public function showAttendanceSection(AttendanceReportSection $section)
    {
        $section->load('month.schoolYear');
        $students = $this->attendanceStudents($section->month->schoolYear, $section->month->month, $section->grade_level, $section->section);
        return view('admin.reports.attendance.month', ['schoolYear' => $section->month->schoolYear, 'reportMonth' => $section->month, 'section' => $section, 'month' => $section->month->month, 'calendarYear' => $this->attendanceCalendarYear($section->month->schoolYear, $section->month->month), 'students' => $students]);
    }

    public function showAttendanceGradeSummary(AttendanceReportMonth $month, int $grade)
    {
        abort_unless($grade >= 0 && $grade <= 7, 404);
        $students = $this->attendanceStudents($month->schoolYear, $month->month, $grade);
        return view('admin.reports.attendance.summary', ['schoolYear' => $month->schoolYear, 'students' => $students, 'month' => $month, 'grade' => $grade]);
    }

    public function showAttendanceSummary(SchoolYear $schoolYear)
    {
        $students = Student::with(['enrollments' => fn($query) => $query->where('school_year_id', $schoolYear->id)->with('sbfpParticipant.attendanceRecords')])
            ->whereHas('enrollments', fn($query) => $query->where('school_year_id', $schoolYear->id))->orderBy('last_name')->orderBy('first_name')->get()
            ->map(function (Student $student) use ($schoolYear) {
                $records = $student->enrollments->first()?->sbfpParticipant?->attendanceRecords ?? collect();
                $present = $records->filter(fn($record) => in_array(strtolower((string) $record->status), ['present', 'p', 'served']))->count();
                return ['name' => trim($student->last_name . ', ' . $student->first_name . ' ' . ($student->middle_name ?? '')), 'present' => $present, 'recorded' => $records->count()];
            });
        return view('admin.reports.attendance.summary', compact('schoolYear', 'students'));
    }

    private function attendanceStudents(SchoolYear $schoolYear, int $month, ?int $grade = null, ?string $section = null)
    {
        $calendarYear = $this->attendanceCalendarYear($schoolYear, $month);
        return Student::with([
            'enrollments' => function ($query) use ($schoolYear, $grade, $section) {
                $query->where('school_year_id', $schoolYear->id)->when($grade !== null, fn($q) => $q->where('grade_level', $grade))->when($section !== null, fn($q) => $q->where('section', $section))->with('sbfpParticipant.attendanceRecords');
            }
        ])
            ->whereHas('enrollments', function ($query) use ($schoolYear, $grade, $section) {
                $query->where('school_year_id', $schoolYear->id)->when($grade !== null, fn($q) => $q->where('grade_level', $grade))->when($section !== null, fn($q) => $q->where('section', $section));
            })->orderBy('last_name')->orderBy('first_name')->get()
            ->map(function (Student $student, int $index) use ($month, $calendarYear) {
                $records = $student->enrollments->first()?->sbfpParticipant?->attendanceRecords ?? collect();
                $days = [];
                for ($day = 1; $day <= 20; $day++) {
                    $record = $records->first(fn($item) => $item->attendance_date?->year === $calendarYear && $item->attendance_date?->month === $month && $item->attendance_date?->day === $day);
                    $days[$day] = $record?->status;
                }
                return ['number' => $index + 1, 'name' => trim($student->last_name . ', ' . $student->first_name . ' ' . ($student->middle_name ?? '')), 'grade_level' => $student->enrollments->first()?->grade_level, 'section' => $student->enrollments->first()?->section, 'days' => $days];
            });
    }

    public function exportAttendanceExcel(AttendanceReportMonth $month)
    {
        $month->load('schoolYear');
        return Excel::download(new AttendanceReportExport($this->attendanceStudents($month->schoolYear, $month->month), $month), 'attendance-' . $month->month . '.xlsx');
    }

    public function exportAttendanceDocx(AttendanceReportMonth $month)
    {
        $month->load('schoolYear');
        $students = $this->attendanceStudents($month->schoolYear, $month->month);
        $word = new PhpWord();
        $section = $word->addSection(['orientation' => 'landscape', 'margin' => 400]);
        $section->addText('SCHOOL-BASED FEEDING PROGRAM - RECORD OF DAILY FEEDING', ['bold' => true, 'size' => 13]);
        $section->addText('For the month of ' . date('F', mktime(0, 0, 0, $month->month, 1)) . ', SY ' . $month->schoolYear->year);
        $table = $section->addTable(['borderSize' => 6]);
        $table->addRow();
        foreach (AttendanceReportExport::columnHeadings() as $heading)
            $table->addCell(700)->addText($heading, ['bold' => true, 'size' => 7]);
        foreach ($students as $student) {
            $table->addRow();
            foreach (AttendanceReportExport::values($student) as $value)
                $table->addCell(700)->addText((string) $value, ['size' => 7]);
        }
        $path = tempnam(sys_get_temp_dir(), 'nutrisight-attendance-') . '.docx';
        IOFactory::createWriter($word, 'Word2007')->save($path);
        return response()->download($path, 'attendance-report.docx')->deleteFileAfterSend(true);
    }

    public function exportAttendancePdf(AttendanceReportMonth $month)
    {
        $month->load('schoolYear');
        $students = $this->attendanceStudents($month->schoolYear, $month->month);
        return Pdf::loadView('admin.reports.attendance.print', compact('month', 'students'))->setPaper('a4', 'landscape')->download('attendance-report.pdf');
    }

    public function exportAttendanceSql(AttendanceReportMonth $month): Response
    {
        $month->load('schoolYear');
        $participantIds = Student::whereHas('enrollments', fn($q) => $q->where('school_year_id', $month->school_year_id))->with('enrollments.sbfpParticipant')->get()->flatMap(fn($s) => $s->enrollments->flatMap(fn($e) => $e->sbfpParticipant?->id))->filter()->unique();
        $sql = "-- NutriSight attendance records\n";
        foreach (DB::table('student_attendance_records')->whereIn('sbfp_participant_id', $participantIds)->whereYear('attendance_date', $this->attendanceCalendarYear($month->schoolYear, $month->month))->whereMonth('attendance_date', $month->month)->get() as $record) {
            $attributes = (array) $record;
            $columns = implode(', ', array_map(fn($column) => '`' . $column . '`', array_keys($attributes)));
            $values = implode(', ', array_map(fn($value) => $value === null ? 'NULL' : DB::getPdo()->quote((string) $value), $attributes));
            $sql .= "INSERT INTO `student_attendance_records` ({$columns}) VALUES ({$values});\n";
        }
        return response($sql, 200, ['Content-Type' => 'application/sql', 'Content-Disposition' => 'attachment; filename="attendance-report.sql"']);
    }

    private function attendanceCalendarYear(?SchoolYear $schoolYear, int $month): int
    {
        $startYear = (int) substr((string) ($schoolYear?->year ?? now()->year), 0, 4);
        return $month >= 6 ? $startYear : $startYear + 1;
    }

    // Annual Consolidated Report hierarchy
    public function sbfpYearly()
    {
        $this->reportPeriodManager->syncExistingRecords();
        $schoolYears = SchoolYear::with(['reportPeriods' => fn($query) => $query->orderByRaw("CASE measurement_period WHEN 'baseline' THEN 1 WHEN 'mid' THEN 2 WHEN 'end' THEN 3 ELSE 4 END")->orderBy('month')->orderBy('name')])
            ->orderByDesc('start_date')->get();
        $schoolYears->each(function (SchoolYear $schoolYear): void {
            $schoolYear->reportPeriods->each(function (ReportPeriod $period): void {
                if ($period->month) {
                    $period->name = date('F', mktime(0, 0, 0, (int) $period->month, 1));
                }
            });
        });

        return view('admin.reports.consolidated.index', compact('schoolYears'));
    }

    public function sbfpConsolidated()
    {
        return $this->sbfpYearly();
    }

    public function storeReportPeriod(Request $request)
    {
        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'measurement_period' => 'required|in:baseline,mid,end',
            'month' => 'required|integer|between:1,12',
        ]);
        $validated['name'] = date('F', mktime(0, 0, 0, (int) $validated['month'], 1));
        $duplicate = ReportPeriod::where('school_year_id', $validated['school_year_id'])
            ->where(function ($query) use ($validated) {
                $query->where('measurement_period', $validated['measurement_period'])
                    ->orWhere('month', $validated['month']);
            })->first();
        if ($duplicate) {
            $reason = $duplicate->measurement_period === $validated['measurement_period']
                ? ucfirst($validated['measurement_period']) . ' already exists for this school year.'
                : date('F', mktime(0, 0, 0, (int) $validated['month'], 1)) . ' is already assigned to another term.';
            return redirect()->back()->withErrors(['month' => $reason . ' Choose a different term and month.'])->withInput();
        }
        $period = ReportPeriod::create($validated);

        return redirect()->route('admin.reports.sbfp.annual.period', $period)->with('success', 'Report period created empty. Use Auto-Generate or enter values and Save.');
    }

    public function showReportPeriod(ReportPeriod $period)
    {
        $this->refreshReportPeriod($period);
        if ($period->month) {
            $period->name = date('F', mktime(0, 0, 0, (int) $period->month, 1));
        }
        $period->load(['schoolYear', 'rows' => fn($query) => $query->orderBy('grade_level')->orderBy('sex')]);
        $rows = $this->formRows($period->rows->isEmpty() ? $this->blankRows() : $period->rows->toArray());

        return view('admin.reports.consolidated.report', [
            'schoolYear' => $period->schoolYear,
            'period' => $period,
            'rows' => $rows,
            'isSummary' => false,
        ]);
    }

    public function updateReportPeriod(Request $request, ReportPeriod $period)
    {
        $validated = $request->validate([
            'measurement_period' => 'required|in:baseline,mid,end',
            'month' => 'required|integer|between:1,12',
        ]);
        $name = date('F', mktime(0, 0, 0, (int) $validated['month'], 1));
        $duplicate = ReportPeriod::where('school_year_id', $period->school_year_id)
            ->where('id', '!=', $period->id)
            ->where(function ($query) use ($validated) {
                $query->where('measurement_period', $validated['measurement_period'])
                    ->orWhere('month', $validated['month']);
            })->first();

        if ($duplicate) {
            $reason = $duplicate->measurement_period === $validated['measurement_period']
                ? ucfirst($validated['measurement_period']) . ' already exists for this school year.'
                : date('F', mktime(0, 0, 0, (int) $validated['month'], 1)) . ' is already assigned to another term.';
            return redirect()->back()->withErrors(['month' => $reason . ' Choose a different term and month.'])->withInput();
        }

        $termChanged = $period->month !== (int) $validated['month'] || $period->measurement_period !== $validated['measurement_period'];
        $period->update(['month' => $validated['month'], 'name' => $name, 'measurement_period' => $validated['measurement_period']]);
        if ($termChanged) {
            $period->rows()->delete();
        }

        return redirect()->back()->with('success', 'Report period updated.');
    }

    public function destroyReportPeriod(ReportPeriod $period)
    {
        $period->delete();

        return redirect()->route('admin.reports.sbfp.annual')->with('success', 'Baseline period deleted.');
    }

    public function generateReportPeriod(ReportPeriod $period)
    {
        $this->refreshReportPeriod($period);

        return redirect()->back()->with('success', 'Report data refreshed from the selected measurement period.');
    }

    private function refreshReportPeriod(ReportPeriod $period): void
    {
        $period->load('schoolYear');
        $measurementPeriod = match ($period->measurement_period) {
            'mid' => 'midline',
            'end' => 'endline',
            default => 'baseline',
        };
        // Replace the snapshot so a previous term can never remain in this period.
        $period->rows()->delete();
        $rows = collect($this->blankRows())->keyBy(fn($row) => $row['grade_level'] . '-' . $row['sex']);
        $students = Student::with([
            'enrollments' => function ($query) use ($period) {
                $query->where('school_year_id', $period->school_year_id)
                    ->with('sbfpParticipant.nutritionMeasurements');
            }
        ])->whereHas('enrollments', function ($query) use ($period) {
            $query->where('school_year_id', $period->school_year_id)
                ->whereHas('sbfpParticipant', function ($participantQuery) {
                    $participantQuery->where('parent_consent', 'approved')
                        ->whereHas('nutritionMeasurements', function ($measurementQuery) {
                            $measurementQuery->where('measurement_period', 'baseline')
                                ->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                        });
                });
        })->get();

        foreach ($students as $student) {
            $enrollment = $student->enrollments->first();
            $key = $enrollment->grade_level . '-' . $this->sexKey($student->sex);
            $row = $rows->get($key);
            if (!$row)
                continue;
            $row['enrollment']++;
            $measurement = $enrollment->sbfpParticipant?->nutritionMeasurements
                ->where('measurement_period', $measurementPeriod)->sortByDesc('created_at')
                ->first();
            if ($measurement) {
                $row['pupils_weighed']++;
                if (filled($measurement->height)) {
                    $row['pupils_height_taken']++;
                }
                $this->incrementStatus($row, 'bmi', $measurement->bmi_category);
                $this->incrementStatus($row, 'hfa', $measurement->hfa);
            }
            $rows->put($key, $row);
        }
        foreach ($rows as $row) {
            ReportPeriodRow::updateOrCreate(['report_period_id' => $period->id, 'grade_level' => $row['grade_level'], 'sex' => $row['sex']], $row);
        }

    }

    public function exportAnnualConsolidatedExcel(ReportPeriod $period)
    {
        $schoolYear = $period->schoolYear;
        $rows = $this->formRows($this->periodRows($period));

        return Excel::download(new DepEdForm1Export($rows, $schoolYear, $period), 'sbfp-form-1-' . ($period?->name ?? 'summary') . '.xlsx');
    }
    public function exportAnnualPeriodExcel(ReportPeriod $period)
    {
        return $this->exportAnnualConsolidatedExcel($period);
    }
    public function exportAnnualPeriodDocx(ReportPeriod $period)
    {
        return $this->exportAnnualConsolidatedDocx($period);
    }
    public function exportAnnualPeriodPdf(ReportPeriod $period)
    {
        return $this->exportAnnualConsolidatedPdf($period);
    }
    public function exportAnnualPeriodSql(ReportPeriod $period)
    {
        return $this->exportAnnualConsolidatedSql($period);
    }

    public function exportAnnualConsolidatedDocx(ReportPeriod $period)
    {
        $schoolYear = $period->schoolYear;
        $rows = $this->formRows($this->periodRows($period));
        $word = new PhpWord();
        $section = $word->addSection(['orientation' => 'landscape', 'margin' => 400]);
        $section->addText("SCHOOL-BASED FEEDING PROGRAM - FORM 1", ['bold' => true, 'size' => 14]);
        $section->addText("Marisol Bliss Elementary School | SY {$schoolYear->year} | {$period->name}");
        $table = $section->addTable(['borderSize' => 6, 'cellMargin' => 40]);
        $table->addRow();
        foreach (DepEdForm1Export::columnHeadings() as $heading)
            $table->addCell(850)->addText($heading, ['bold' => true, 'size' => 7]);
        foreach ($rows as $row) {
            $table->addRow();
            foreach (DepEdForm1Export::values($row) as $value)
                $table->addCell(850)->addText((string) $value, ['size' => 7]);
        }
        $path = tempnam(sys_get_temp_dir(), 'nutrisight-form1-') . '.docx';
        IOFactory::createWriter($word, 'Word2007')->save($path);

        return response()->download($path, 'sbfp-form-1.docx')->deleteFileAfterSend(true);
    }

    public function exportAnnualConsolidatedPdf(ReportPeriod $period)
    {
        $schoolYear = $period->schoolYear;
        $rows = $this->formRows($this->periodRows($period));

        return Pdf::loadView('admin.reports.consolidated.print', compact('schoolYear', 'period', 'rows'))->setPaper('a4', 'landscape')->download('sbfp-form-1.pdf');
    }

    public function exportAnnualConsolidatedSql(ReportPeriod $period): Response
    {
        $query = DB::table('report_period_rows');
        $query->where('report_period_id', $period->id);
        $sql = "-- NutriSight SBFP Form 1 aggregate rows\n";
        foreach ($query->get() as $record) {
            $attributes = (array) $record;
            $columns = implode(', ', array_map(fn($column) => '`' . $column . '`', array_keys($attributes)));
            $values = implode(', ', array_map(fn($value) => $value === null ? 'NULL' : DB::getPdo()->quote((string) $value), $attributes));
            $sql .= "INSERT INTO `report_period_rows` ({$columns}) VALUES ({$values});\n";
        }

        return response($sql, 200, ['Content-Type' => 'application/sql', 'Content-Disposition' => 'attachment; filename="sbfp-form-1.sql"']);
    }

    private function periodRows(ReportPeriod $period): array
    {
        $period->loadMissing('rows');
        return $period->rows->isEmpty() ? $this->blankRows() : $period->rows->sortBy(['grade_level', 'sex'])->values()->toArray();
    }

    private function formRows(array $rows): array
    {
        $result = [];
        $groupedRows = collect($rows)->groupBy('grade_level');
        foreach (range(0, 7) as $grade) {
            $gradeRows = $groupedRows->get($grade, collect());
            foreach (['M', 'F'] as $sex) {
                $result[] = $gradeRows->firstWhere('sex', $sex) ?? $this->emptyAggregateRow($grade, $sex);
            }
            $total = $result[count($result) - 2];
            $female = $result[count($result) - 1];
            foreach (array_keys($total) as $column) {
                if (is_int($total[$column]) && $column !== 'grade_level')
                    $total[$column] += (int) $female[$column];
            }
            $total['sex'] = 'Total';
            $result[] = $total;
        }

        $grandTotals = collect($result)->whereIn('sex', ['M', 'F'])->values();
        foreach (['M', 'F'] as $sex) {
            $grandRow = $this->emptyAggregateRow(-1, $sex);
            foreach ($grandTotals->where('sex', $sex) as $source) {
                foreach (array_keys($grandRow) as $column) {
                    if (is_int($grandRow[$column]) && $column !== 'grade_level') {
                        $grandRow[$column] += (int) $source[$column];
                    }
                }
            }
            $result[] = $grandRow;
        }
        $grandTotal = $this->emptyAggregateRow(-1, 'Total');
        $grandMale = $result[count($result) - 2];
        $grandFemale = $result[count($result) - 1];
        foreach (array_keys($grandTotal) as $column) {
            if (is_int($grandTotal[$column]) && $column !== 'grade_level') {
                $grandTotal[$column] = (int) $grandMale[$column] + (int) $grandFemale[$column];
            }
        }
        $result[] = $grandTotal;

        return $result;
    }

    private function blankRows(): array
    {
        $rows = [];
        for ($grade = 0; $grade <= 7; $grade++)
            foreach (['M', 'F'] as $sex)
                $rows[] = $this->emptyAggregateRow($grade, $sex);
        return $rows;
    }

    private function emptyAggregateRow(int $gradeLevel, string $sex): array
    {
        return ['grade_level' => $gradeLevel, 'sex' => $sex, 'enrollment' => 0, 'pupils_weighed' => 0, 'pupils_height_taken' => 0, 'bmi_severely_wasted' => 0, 'bmi_wasted' => 0, 'bmi_normal' => 0, 'bmi_overweight' => 0, 'bmi_obese' => 0, 'hfa_severely_stunted' => 0, 'hfa_stunted' => 0, 'hfa_normal' => 0, 'hfa_tall' => 0];
    }

    private function sexKey(?string $sex): string
    {
        return strtoupper(substr($sex ?? '', 0, 1)) === 'F' ? 'F' : 'M';
    }

    private function incrementStatus(array &$row, string $type, ?string $status): void
    {
        $value = strtolower(trim((string) $status));
        $map = $type === 'bmi' ? ['severely wasted' => 'bmi_severely_wasted', 'severely underweight' => 'bmi_severely_wasted', 'wasted' => 'bmi_wasted', 'underweight' => 'bmi_wasted', 'normal' => 'bmi_normal', 'overweight' => 'bmi_overweight', 'obese' => 'bmi_obese'] : ['severely stunted' => 'hfa_severely_stunted', 'stunted' => 'hfa_stunted', 'normal' => 'hfa_normal', 'tall' => 'hfa_tall'];
        foreach ($map as $needle => $column)
            if ($value === $needle || str_contains($value, $needle)) {
                $row[$column]++;
                return;
            }
    }

    // SBFP Assessment Report
    public function sbfpAssessment()
    {
        $schoolYear = SchoolYearManager::activeSchoolYear();
        $assessment = $this->assessmentData($schoolYear);

        return view('admin.reports.assessment.index', compact('schoolYear', 'assessment'));
    }

    public function exportAssessmentExcel()
    {
        return Excel::download(new AssessmentReportExport($this->assessmentData(SchoolYearManager::activeSchoolYear())), 'sbfp-assessment.xlsx');
    }

    public function exportAssessmentDocx()
    {
        $assessment = $this->assessmentData(SchoolYearManager::activeSchoolYear());
        $word = new PhpWord();
        $section = $word->addSection(['orientation' => 'landscape', 'margin' => 600]);
        $section->addText('SCHOOL-BASED FEEDING PROGRAM - ASSESSMENT REPORT', ['bold' => true, 'size' => 14]);
        $section->addText("Marisol Bliss Elementary School | SY {$assessment['school_year']}");
        $table = $section->addTable(['borderSize' => 6, 'cellMargin' => 80]);
        $table->addRow();
        foreach (AssessmentReportExport::columnHeadings() as $heading) {
            $table->addCell(2600)->addText($heading, ['bold' => true]);
        }
        foreach (AssessmentReportExport::values($assessment) as $row) {
            $table->addRow();
            foreach ($row as $value) {
                $table->addCell(2600)->addText((string) $value);
            }
        }
        $path = tempnam(sys_get_temp_dir(), 'nutrisight-assessment-') . '.docx';
        IOFactory::createWriter($word, 'Word2007')->save($path);

        return response()->download($path, 'sbfp-assessment.docx')->deleteFileAfterSend(true);
    }

    public function exportAssessmentPdf()
    {
        $schoolYear = SchoolYearManager::activeSchoolYear();
        $assessment = $this->assessmentData($schoolYear);

        return Pdf::loadView('admin.reports.assessment.print', compact('schoolYear', 'assessment'))->setPaper('a4', 'landscape')->download('sbfp-assessment.pdf');
    }

    public function exportAssessmentSql(): Response
    {
        $assessment = $this->assessmentData(SchoolYearManager::activeSchoolYear());
        $columns = implode(', ', array_map(fn ($column) => '`' . $column . '`', AssessmentReportExport::columnKeys()));
        $values = implode(', ', array_map(fn ($value) => DB::getPdo()->quote((string) $value), AssessmentReportExport::values($assessment)));
        $sql = "-- NutriSight SBFP Assessment report export\nINSERT INTO `sbfp_assessment_report_exports` ({$columns}) VALUES ({$values});\n";

        return response($sql, 200, ['Content-Type' => 'application/sql', 'Content-Disposition' => 'attachment; filename="sbfp-assessment.sql"']);
    }

    private function assessmentData(?SchoolYear $schoolYear): array
    {
        $students = Student::with([
            'enrollments' => function ($query) use ($schoolYear) {
                $query->where('school_year_id', $schoolYear?->id)
                    ->with(['sbfpParticipant.nutritionMeasurements', 'sbfpParticipant.attendanceRecords']);
            },
        ])->whereHas('enrollments', function ($query) use ($schoolYear) {
            $query->where('school_year_id', $schoolYear?->id)
                ->whereHas('sbfpParticipant', fn ($participant) => $participant->where('parent_consent', 'approved'));
        })->get();

        $attendanceStudents = 0;
        $completeAttendance = 0;
        $malnourished = 0;
        $recovered = 0;

        foreach ($students as $student) {
            $enrollment = $student->enrollments->first();
            $participant = $enrollment?->sbfpParticipant;
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
                $latestPeriodMeasurement = collect(['endline', 'midline', 'baseline'])
                    ->map(fn ($period) => $measurements->firstWhere('measurement_period', $period))
                    ->filter()
                    ->first();
                if ($latestPeriodMeasurement?->bmi_category === 'Normal') {
                    $recovered++;
                }
            }
        }

        $withAbsences = $attendanceStudents - $completeAttendance;
        $stillNeedingSupport = $malnourished - $recovered;

        return [
            'school_year' => $schoolYear?->year ?? 'No active school year',
            'attendance_students' => $attendanceStudents,
            'complete_attendance' => $completeAttendance,
            'complete_attendance_rate' => $attendanceStudents ? round($completeAttendance / $attendanceStudents * 100, 1) : 0,
            'with_absences' => $withAbsences,
            'with_absences_rate' => $attendanceStudents ? round($withAbsences / $attendanceStudents * 100, 1) : 0,
            'malnourished' => $malnourished,
            'recovered' => $recovered,
            'recovered_rate' => $malnourished ? round($recovered / $malnourished * 100, 1) : 0,
            'still_needing_support' => $stillNeedingSupport,
            'still_needing_support_rate' => $malnourished ? round($stillNeedingSupport / $malnourished * 100, 1) : 0,
        ];
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
