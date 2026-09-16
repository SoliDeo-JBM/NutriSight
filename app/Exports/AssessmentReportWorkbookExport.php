<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class AssessmentReportWorkbookExport implements Export, WithMultipleSheets
{
    public function __construct(private readonly array $assessment) {}

    public function sheets(): array
    {
        $assessment = $this->assessment;

        return [
            new AssessmentReportSheet('Summary', AssessmentReportExport::columnHeadings(), [AssessmentReportExport::values($assessment)]),
            new AssessmentReportSheet('Attendance Demographics', ['Sex', 'Age', 'Complete Attendance', 'Complete %', 'With Absences', 'Absence %'], $this->attendanceRows($assessment['attendance_demographics'])),
            new AssessmentReportSheet('Participants', ['Sex', 'Age', 'Participants', 'Percentage'], $this->participantRows($assessment['participant_demographics'])),
            new AssessmentReportSheet('Endline Recovery', ['Sex', 'Age', 'Recovered to Normal', 'Recovered %', 'Still Needing Support', 'Support %'], $this->recoveryRows($assessment['recovery_demographics'])),
            new AssessmentReportSheet('Period Summary', ['Period', 'Nutrition Status', 'Count'], $this->periodSummaryRows($assessment['period_summary'])),
            new AssessmentReportSheet('Period Demographics', ['Sex', 'Age', 'Baseline', 'Midline', 'Endline', 'Percentage'], $this->periodRows($assessment['period_demographics'])),
        ];
    }

    private function attendanceRows(array $rows): array
    {
        $total = array_sum(array_column($rows, 'count'));
        $complete = array_sum(array_column($rows, 'complete'));
        $absences = array_sum(array_column($rows, 'with_absences'));
        $result = array_map(fn($row) => [$row['sex'], $row['age'], $row['complete'], $total ? round($row['complete'] / $total * 100, 1) . '%' : '0%', $row['with_absences'], $total ? round($row['with_absences'] / $total * 100, 1) . '%' : '0%'], $rows);
        $result[] = ['Total', '', $complete, $total ? round($complete / $total * 100, 1) . '%' : '0%', $absences, $total ? round($absences / $total * 100, 1) . '%' : '0%'];
        return $result;
    }

    private function participantRows(array $rows): array
    {
        $total = array_sum(array_column($rows, 'count'));
        $result = array_map(fn($row) => [$row['sex'], $row['age'], $row['count'], $total ? round($row['count'] / $total * 100, 1) . '%' : '0%'], $rows);
        $result[] = ['Total', '', $total, '100%'];
        return $result;
    }

    private function recoveryRows(array $rows): array
    {
        $recovered = array_sum(array_column($rows, 'recovered'));
        $support = array_sum(array_column($rows, 'still_needing_support'));
        $total = $recovered + $support;
        $result = array_map(fn($row) => [$row['sex'], $row['age'], $row['recovered'], $total ? round($row['recovered'] / $total * 100, 1) . '%' : '0%', $row['still_needing_support'], $total ? round($row['still_needing_support'] / $total * 100, 1) . '%' : '0%'], $rows);
        $result[] = ['Total', '', $recovered, $total ? round($recovered / $total * 100, 1) . '%' : '0%', $support, $total ? round($support / $total * 100, 1) . '%' : '0%'];
        return $result;
    }

    private function periodRows(array $rows): array
    {
        $total = count($rows);
        $result = array_map(fn($row) => [$row['sex'], $row['age'], $row['baseline'], $row['midline'], $row['endline'], $total ? round(100 / $total, 1) . '%' : '0%'], $rows);
        $result[] = ['Total participants', '', '', '', '', $total ? $total . ' (100%)' : '0 (0%)'];
        return $result;
    }

    private function periodSummaryRows(array $periodSummary): array
    {
        $rows = [];
        foreach ($periodSummary as $period => $statuses) {
            foreach ($statuses as $status => $count) {
                $rows[] = [ucfirst($period), $status, $count];
            }
        }

        return $rows;
    }
}

class AssessmentReportSheet implements FromArray, WithEvents, WithHeadings, WithTitle
{
    public function __construct(
        private readonly string $title,
        private readonly array $headings,
        private readonly array $rows,
    ) {}

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array
    {
        if ($this->title !== 'Summary') {
            return [];
        }

        return [
            // Same school logo used on the SBFP student ID cards.
            // AfterSheet::class => function (AfterSheet $event) {
            //     $drawing = new Drawing();
            //     $drawing->setName('School logo');
            //     $drawing->setPath(public_path('images/id/mbes-logo-1.png'));
            //     $drawing->setHeight(42);
            //     $drawing->setCoordinates('A1');
            //     $drawing->setOffsetX(4);
            //     $drawing->setOffsetY(4);
            //     $drawing->setWorksheet($event->sheet->getDelegate());
            // },
        ];
    }
}
