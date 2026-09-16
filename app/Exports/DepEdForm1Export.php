<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepEdForm1Export implements FromArray, ShouldAutoSize, WithEvents, WithHeadings, WithStyles
{
  public function __construct(private readonly array $rows, private readonly mixed $schoolYear = null, private readonly mixed $period = null, private readonly string $adminName = 'Full Name of the Admin', private readonly string $superAdminName = 'Full Name of the Super Admin') {}

  public static function columnHeadings(): array
  {
    return ['Grade Level', 'Sex', 'Enrolment', 'Pupils Weighed No.', 'Pupils Weighed %', 'BMI Severely Wasted No.', 'BMI Severely Wasted %', 'BMI Wasted No.', 'BMI Wasted %', 'BMI Normal No.', 'BMI Normal %', 'BMI Overweight No.', 'BMI Overweight %', 'BMI Obese No.', 'BMI Obese %', 'HFA Severely Stunted No.', 'HFA Severely Stunted %', 'HFA Stunted No.', 'HFA Stunted %', 'HFA Normal No.', 'HFA Normal %', 'HFA Tall No.', 'HFA Tall %', 'Pupils Taken Height No.', 'Pupils Taken Height %'];
  }

  public static function values(array $row): array
  {
    $enrollment = (int) ($row['enrollment'] ?? 0);
    $weighed = (int) ($row['pupils_weighed'] ?? 0);
    $percentage = static fn(int $value, int $denominator): string => $denominator ? number_format($value / $denominator * 100, 2) . '%' : '0.00%';
    return [
      static::gradeLabel((int) $row['grade_level']),
      $row['sex'],
      $enrollment,
      $weighed,
      $percentage($weighed, $enrollment),
      $row['bmi_severely_wasted'],
      $percentage((int) $row['bmi_severely_wasted'], $weighed),
      $row['bmi_wasted'],
      $percentage((int) $row['bmi_wasted'], $weighed),
      $row['bmi_normal'],
      $percentage((int) $row['bmi_normal'], $weighed),
      $row['bmi_overweight'],
      $percentage((int) $row['bmi_overweight'], $weighed),
      $row['bmi_obese'],
      $percentage((int) $row['bmi_obese'], $weighed),
      $row['hfa_severely_stunted'],
      $percentage((int) $row['hfa_severely_stunted'], $weighed),
      $row['hfa_stunted'],
      $percentage((int) $row['hfa_stunted'], $weighed),
      $row['hfa_normal'],
      $percentage((int) $row['hfa_normal'], $weighed),
      $row['hfa_tall'],
      $percentage((int) $row['hfa_tall'], $weighed),
      $row['pupils_height_taken'],
      $percentage((int) $row['pupils_height_taken'], $enrollment),
    ];
  }

  public function array(): array
  {
    return collect($this->rows)->map(fn(array $row) => static::values($row))->all();
  }

  public function headings(): array
  {
    $periodLabel = ucfirst($this->period?->measurement_period ?? 'Summary');
    $month = $this->period?->month ? date('F', mktime(0, 0, 0, $this->period->month, 1)) : 'Month';
    return [
      ['Department of Education'],
      ['Bureau of Learner Support Services'],
      ['NUTRITIONAL STATUS REPORT OF MARISOL BLISS ELEMENTARY SCHOOL'],
      ["{$periodLabel} ({$month}) SY {$this->schoolYear?->year}"],
      ['Grade Levels', 'Enrollment', '', '', 'Pupils Weighed', '', 'BODY MASS INDEX (BMI)', '', '', '', '', '', '', '', '', 'HEIGHT-FOR-AGE (HFA)', '', '', '', '', '', '', '', 'Pupils Taken Height', ''],
      ['', '', '', '', '', 'Severely Wasted', '', 'Wasted', '', 'Normal', '', 'Overweight', '', 'Obese', '', 'Severely Stunted', '', 'Stunted', '', 'Normal', '', 'Tall', '', '', ''],
      ['', '', '', 'No.', '%', 'No.', '%', 'No.', '%', 'No.', '%', 'No.', '%', 'No.', '%', 'No.', '%', 'No.', '%', 'No.', '%', 'No.', '%', 'No.', '%'],
    ];
  }

  public function styles(Worksheet $sheet): array
  {
    return [
      1 => ['font' => ['size' => 11], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center']],
      2 => ['font' => ['size' => 11], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center']],
      3 => ['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center']],
      4 => ['font' => ['italic' => true, 'size' => 11], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center']],
      5 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F4E78']], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
      6 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E2F3']], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
      7 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EAF1F8']], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $sheet = $event->sheet->getDelegate();
        foreach (['A1:Y1', 'A2:Y2', 'A3:Y3', 'A4:Y4', 'A5:A7', 'B5:C7', 'D5:E6', 'F5:O5', 'P5:W5', 'X5:Y6', 'F6:G6', 'H6:I6', 'J6:K6', 'L6:M6', 'N6:O6', 'P6:Q6', 'R6:S6', 'T6:U6', 'V6:W6'] as $range)
          $sheet->mergeCells($range);
        $periodText = new RichText();
        $periodRun = $periodText->createTextRun(ucfirst($this->period?->measurement_period ?? 'Summary'));
        $periodRun->getFont()->setItalic(true)->getColor()->setRGB('2563EB');
        $monthRun = $periodText->createTextRun(' (' . ($this->period?->month ? date('F', mktime(0, 0, 0, $this->period->month, 1)) : 'Month') . ')');
        $monthRun->getFont()->setItalic(true)->getColor()->setRGB('2563EB');
        $detailsRun = $periodText->createTextRun(' SY ');
        $detailsRun->getFont()->setItalic(true);
        $yearRun = $periodText->createTextRun((string) $this->schoolYear?->year);
        $yearRun->getFont()->setBold(true)->setItalic(true);
        $sheet->getCell('A4')->setValue($periodText);
        // MBES school logo on the left, DepEd seal on the right.
        foreach ([['images/id/mbes-logo-1.png', 'A1'], ['images/id/kagawaran_ng_edukasyo.jpeg', 'Y1']] as [$path, $coordinate]) {
          $drawing = new Drawing();
          $drawing->setPath(public_path($path));
          $drawing->setHeight(42);
          $drawing->setCoordinates($coordinate);
          $drawing->setOffsetX($coordinate === 'A1' ? 8 : 0);
          $drawing->setWorksheet($sheet);
        }
        $lastRow = count($this->rows) + 7;
        $sheet->getStyle("A1:Y{$lastRow}")->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
        $rowNumber = 8;
        foreach (collect($this->rows)->groupBy('grade_level') as $gradeRows) {
          $lastRowNumber = $rowNumber + $gradeRows->count() - 1;
          if ($lastRowNumber > $rowNumber)
            $sheet->mergeCells("A{$rowNumber}:A{$lastRowNumber}");
          $rowNumber = $lastRowNumber + 1;
        }
        $signatureRow = $lastRow + 2;
        $sheet->mergeCells("A{$signatureRow}:L{$signatureRow}");
        $sheet->mergeCells("M{$signatureRow}:Y{$signatureRow}");
        $sheet->mergeCells("A" . ($signatureRow + 1) . ":L" . ($signatureRow + 1));
        $sheet->mergeCells("M" . ($signatureRow + 1) . ":Y" . ($signatureRow + 1));
        $sheet->mergeCells("A" . ($signatureRow + 2) . ":L" . ($signatureRow + 2));
        $sheet->mergeCells("M" . ($signatureRow + 2) . ":Y" . ($signatureRow + 2));
        $sheet->mergeCells("A" . ($signatureRow + 3) . ":L" . ($signatureRow + 3));
        $sheet->mergeCells("M" . ($signatureRow + 3) . ":Y" . ($signatureRow + 3));
        $sheet->setCellValue("A{$signatureRow}", 'Prepared by:');
        $sheet->setCellValue("M{$signatureRow}", 'Noted by:');
        $sheet->setCellValue("A" . ($signatureRow + 1), '____________________________');
        $sheet->setCellValue("M" . ($signatureRow + 1), '________________________________');
        $sheet->setCellValue("A" . ($signatureRow + 2), $this->adminName);
        $sheet->setCellValue("M" . ($signatureRow + 2), $this->superAdminName);
        $sheet->setCellValue("A" . ($signatureRow + 3), 'Project Development Officer');
        $sheet->setCellValue("M" . ($signatureRow + 3), 'School Head');
        $sheet->getStyle("A{$signatureRow}:Y" . ($signatureRow + 3))->getAlignment()->setHorizontal('center')->setVertical('center');
        $sheet->getStyle("A{$signatureRow}:Y{$signatureRow}")->getFont()->setBold(true);
        $sheet->getStyle("A" . ($signatureRow + 2) . ":Y" . ($signatureRow + 2))->getFont()->setBold(true);
        $sheet->getRowDimension(1)->setRowHeight(18);
        $sheet->getRowDimension(2)->setRowHeight(24);
        $sheet->getRowDimension(3)->setRowHeight(24);
        $sheet->getRowDimension(4)->setRowHeight(24);
        $sheet->getRowDimension(5)->setRowHeight(28);
        $sheet->getRowDimension(6)->setRowHeight(24);
        $sheet->getRowDimension(7)->setRowHeight(22);
        $sheet->freezePane('C8');
      }
    ];
  }

  private static function gradeLabel(int $grade): string
  {
    if ($grade === -1) {
      return 'GRAND TOTAL';
    }
    if ($grade === 7) {
      return 'SPED';
    }
    return $grade === 0 ? 'Kinder' : 'Grade ' . $grade;
  }
}
