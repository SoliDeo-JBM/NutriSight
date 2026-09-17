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
use App\Services\SchoolLogoService;

class AssessmentReportWorkbookExport implements Export, WithMultipleSheets
{
    public function __construct(private readonly array $assessment, private readonly string $adminName = 'Full Name of the Admin', private readonly string $superAdminName = 'Full Name of the Super Admin') {}

    public function sheets(): array
    {
        $assessment = $this->assessment;

        return [
            new AssessmentReportSheet('Summary', AssessmentReportExport::columnHeadings(), [AssessmentReportExport::values($assessment)], $this->assessment, $this->adminName, $this->superAdminName),
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
        private readonly array $assessment = [],
        private readonly string $adminName = 'Full Name of the Admin',
        private readonly string $superAdminName = 'Full Name of the Super Admin',
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

        return [AfterSheet::class => function (AfterSheet $event): void {
            $sheet = $event->sheet->getDelegate();
            $sheet->insertNewRowBefore(1, 5);
            $sheet->mergeCells('A1:K1');
            $sheet->mergeCells('A2:K2');
            $sheet->mergeCells('A3:K3');
            $sheet->mergeCells('A4:K4');
            $lastRow = count($this->rows) + 6;
            $signatureRow = $lastRow + 2;
            foreach ([
                'A' . $signatureRow . ':F' . $signatureRow,
                'G' . $signatureRow . ':K' . $signatureRow,
                'A' . ($signatureRow + 1) . ':F' . ($signatureRow + 1),
                'G' . ($signatureRow + 1) . ':K' . ($signatureRow + 1),
                'A' . ($signatureRow + 2) . ':F' . ($signatureRow + 2),
                'G' . ($signatureRow + 2) . ':K' . ($signatureRow + 2),
                'A' . ($signatureRow + 3) . ':F' . ($signatureRow + 3),
                'G' . ($signatureRow + 3) . ':K' . ($signatureRow + 3),
            ] as $range) {
                $sheet->mergeCells($range);
            }
            $sheet->setCellValue('A1', 'Department of Education');
            $sheet->setCellValue('A2', 'Bureau of Learner Support Services');
            $sheet->setCellValue('A3', 'SCHOOL-BASED FEEDING PROGRAM - ASSESSMENT REPORT');
            $sheet->setCellValue('A4', 'Marisol Bliss Elementary School | SY ' . ($this->assessment['school_year'] ?? ''));
            $sheet->setCellValue('A' . $signatureRow, 'Prepared by:');
            $sheet->setCellValue('G' . $signatureRow, 'Noted by:');
            $sheet->setCellValue('A' . ($signatureRow + 1), '____________________________');
            $sheet->setCellValue('G' . ($signatureRow + 1), '________________________________');
            $sheet->setCellValue('A' . ($signatureRow + 2), $this->adminName);
            $sheet->setCellValue('G' . ($signatureRow + 2), $this->superAdminName);
            $sheet->setCellValue('A' . ($signatureRow + 3), 'Project Development Officer');
            $sheet->setCellValue('G' . ($signatureRow + 3), 'School Head');
            foreach ([[SchoolLogoService::path(), 'A1'], [public_path('images/id/kagawaran_ng_edukasyo.jpeg'), 'K1']] as [$path, $coordinate]) {
                $drawing = new Drawing();
                $drawing->setPath(public_path($path));
                $drawing->setHeight(42);
                $drawing->setCoordinates($coordinate);
                $drawing->setWorksheet($sheet);
            }
            $sheet->getStyle('A1:K' . ($signatureRow + 3))->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
            $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A' . $signatureRow . ':K' . $signatureRow)->getFont()->setBold(true);
            $sheet->getStyle('A' . ($signatureRow + 2) . ':K' . ($signatureRow + 2))->getFont()->setBold(true);
        }];
    }
}
