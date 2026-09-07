<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnnualConsolidatedReportExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
  public function __construct(private readonly Collection $rows)
  {
  }

  public static function columnHeadings(): array
  {
    return ['LRN', 'Name', 'Sex', 'Grade Level', 'Section', 'Baseline Height', 'Baseline Weight', 'Baseline BMI', 'Baseline BMI Status', 'Baseline HFA', 'Endline Height', 'Endline Weight', 'Endline BMI', 'Endline BMI Status', 'Endline HFA'];
  }

  public static function values(array $row): array
  {
    return [$row['lrn'], $row['name'], $row['sex'], $row['grade_level'], $row['section'], $row['baseline_height'], $row['baseline_weight'], $row['baseline_bmi'], $row['baseline_status'], $row['baseline_hfa'], $row['endline_height'], $row['endline_weight'], $row['endline_bmi'], $row['endline_status'], $row['endline_hfa']];
  }

  public function array(): array
  {
    return $this->rows->map(fn(array $row) => self::values($row))->all();
  }

  public function headings(): array
  {
    return self::columnHeadings();
  }

  public function styles(Worksheet $sheet): array
  {
    return [1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F4E78']]]];
  }
}