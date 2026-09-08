<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepEdForm1Export implements FromArray, ShouldAutoSize, WithEvents, WithHeadings, WithStyles
{
  public function __construct(private readonly array $rows, private readonly mixed $schoolYear = null, private readonly mixed $period = null)
  {
  }

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
    return [
      ['Grade Levels', 'Sex', 'Enrolment', '', 'Pupils Weighed', '', 'BODY MASS INDEX (BMI)', '', '', '', '', '', '', '', '', 'HEIGHT-FOR-AGE (HFA)', '', '', '', '', '', '', '', 'Pupils Taken Height', ''],
      ['', '', 'Enrolment', 'No.', '%', 'Severely Wasted No.', '%', 'Wasted No.', '%', 'Normal No.', '%', 'Overweight No.', '%', 'Obese No.', '%', 'Severely Stunted No.', '%', 'Stunted No.', '%', 'Normal No.', '%', 'Tall No.', '%', 'No.', '%'],
    ];
  }

  public function styles(Worksheet $sheet): array
  {
    return [
      1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F4E78']], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
      2 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E2F3']], 'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true]],
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $sheet = $event->sheet->getDelegate();
        foreach (['A1:A2', 'B1:B2', 'C1:C2', 'D1:E1', 'F1:O1', 'P1:W1', 'X1:Y1'] as $range)
          $sheet->mergeCells($range);
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(2)->setRowHeight(34);
        $sheet->freezePane('C3');
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