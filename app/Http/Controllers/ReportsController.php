<?php

namespace App\Http\Controllers;

use App\Exports\DepEdForm1Export;
use App\Models\ReportPeriod;
use App\Models\ReportPeriodRow;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Services\SchoolYearManager;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class ReportsController extends Controller
{
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
    public function sbfpAttendance()
    {
        return view('admin.reports.attendance.index');
    }

    // Annual Consolidated Report hierarchy
    public function sbfpYearly()
    {
        $schoolYears = SchoolYear::with(['reportPeriods' => fn($query) => $query->orderBy('month')->orderBy('name')])
            ->orderByDesc('start_date')->get();

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

    public function showAnnualSummary(SchoolYear $schoolYear)
    {
        $rows = $this->formRows($this->summaryRows($schoolYear));

        return view('admin.reports.consolidated.report', compact('schoolYear', 'rows') + ['period' => null, 'isSummary' => true]);
    }

    public function generateReportPeriod(ReportPeriod $period)
    {
        $period->load('schoolYear');
        // Replace the snapshot so a previous term can never remain in this period.
        $period->rows()->delete();
        $rows = collect($this->blankRows())->keyBy(fn($row) => $row['grade_level'] . '-' . $row['sex']);
        $students = Student::with([
            'enrollments' => function ($query) use ($period) {
                $query->where('school_year_id', $period->school_year_id)
                    ->with('sbfpParticipant.nutritionMeasurements');
            }
        ])->whereHas('enrollments', fn($query) => $query->where('school_year_id', $period->school_year_id))->get();

        foreach ($students as $student) {
            $enrollment = $student->enrollments->first();
            $key = $enrollment->grade_level . '-' . $this->sexKey($student->sex);
            $row = $rows->get($key);
            if (!$row)
                continue;
            $row['enrollment']++;
            $measurement = $enrollment->sbfpParticipant?->nutritionMeasurements
                ->where('measurement_period', $period->measurement_period)->sortByDesc('created_at')
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

        return redirect()->back()->with('success', 'Report data generated from the selected measurement period.');
    }

    public function exportAnnualConsolidatedExcel(ReportPeriod|SchoolYear|null $target = null)
    {
        $period = $target instanceof ReportPeriod ? $target : null;
        $schoolYear = $period?->schoolYear ?? ($target instanceof SchoolYear ? $target : SchoolYearManager::activeSchoolYear());
        $rows = $period ? $this->formRows($this->periodRows($period)) : $this->formRows($this->summaryRows($schoolYear));

        return Excel::download(new DepEdForm1Export($rows, $schoolYear, $period), 'sbfp-form-1-' . ($period?->name ?? 'summary') . '.xlsx');
    }

    public function exportAnnualSummaryExcel(SchoolYear $schoolYear)
    {
        return $this->exportAnnualConsolidatedExcel($schoolYear);
    }
    public function exportAnnualSummaryDocx(SchoolYear $schoolYear)
    {
        return $this->exportAnnualConsolidatedDocx($schoolYear);
    }
    public function exportAnnualSummaryPdf(SchoolYear $schoolYear)
    {
        return $this->exportAnnualConsolidatedPdf($schoolYear);
    }
    public function exportAnnualSummarySql(SchoolYear $schoolYear)
    {
        return $this->exportAnnualConsolidatedSql($schoolYear);
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

    public function exportAnnualConsolidatedDocx(ReportPeriod|SchoolYear|null $target = null)
    {
        $period = $target instanceof ReportPeriod ? $target : null;
        $schoolYear = $period?->schoolYear ?? ($target instanceof SchoolYear ? $target : SchoolYearManager::activeSchoolYear());
        $rows = $period ? $this->formRows($this->periodRows($period)) : $this->formRows($this->summaryRows($schoolYear));
        $word = new PhpWord();
        $section = $word->addSection(['orientation' => 'landscape', 'margin' => 400]);
        $section->addText("SCHOOL-BASED FEEDING PROGRAM - FORM 1", ['bold' => true, 'size' => 14]);
        $section->addText("Marisol Bliss Elementary School | SY {$schoolYear?->year} | " . ($period?->name ?? 'Summary'));
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

    public function exportAnnualConsolidatedPdf(ReportPeriod|SchoolYear|null $target = null)
    {
        $period = $target instanceof ReportPeriod ? $target : null;
        $schoolYear = $period?->schoolYear ?? ($target instanceof SchoolYear ? $target : SchoolYearManager::activeSchoolYear());
        $rows = $period ? $this->formRows($this->periodRows($period)) : $this->formRows($this->summaryRows($schoolYear));

        return Pdf::loadView('admin.reports.consolidated.print', compact('schoolYear', 'period', 'rows'))->setPaper('a4', 'landscape')->download('sbfp-form-1.pdf');
    }

    public function exportAnnualConsolidatedSql(ReportPeriod|SchoolYear|null $target = null): Response
    {
        $period = $target instanceof ReportPeriod ? $target : null;
        $query = DB::table('report_period_rows');
        if ($period)
            $query->where('report_period_id', $period->id);
        else
            $query->whereIn('report_period_id', ReportPeriod::where('school_year_id', $target instanceof SchoolYear ? $target->id : SchoolYearManager::activeSchoolYearId())->pluck('id'));
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

    private function summaryRows(SchoolYear $schoolYear): array
    {
        $rows = collect($this->blankRows())->keyBy(fn($row) => $row['grade_level'] . '-' . $row['sex']);
        $students = Student::with([
            'enrollments' => function ($query) use ($schoolYear) {
                $query->where('school_year_id', $schoolYear->id)
                    ->with('sbfpParticipant.nutritionMeasurements');
            },
        ])->whereHas('enrollments', fn($query) => $query->where('school_year_id', $schoolYear->id))->get();

        foreach ($students as $student) {
            $enrollment = $student->enrollments->first();
            if (!$enrollment) {
                continue;
            }
            $key = $enrollment->grade_level . '-' . $this->sexKey($student->sex);
            $row = $rows->get($key);
            if (!$row) {
                continue;
            }

            // One enrolment per student; use the best/latest available term once.
            $row['enrollment']++;
            $measurements = $enrollment->sbfpParticipant?->nutritionMeasurements ?? collect();
            $measurement = collect(['end', 'mid', 'baseline'])
                ->map(fn($term) => $measurements->where('measurement_period', $term)->sortByDesc('created_at')->first())
                ->filter()->first();

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

        return $rows->values()->all();
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
        return view('admin.reports.assessment.index');
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
