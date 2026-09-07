<?php

namespace App\Exports;

use App\Models\AttendanceReportMonth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceReportExport implements FromArray, ShouldAutoSize, WithHeadings
{
  public function __construct(private readonly Collection $students, private readonly AttendanceReportMonth $month)
  {
  }

  public static function columnHeadings(): array
  {
    return array_merge(['No.', 'Name of Pupil', 'Grade Level', 'Section'], range(1, 20));
  }

  public static function values(array $student): array
  {
    return array_merge([$student['number'], $student['name'], $student['grade_level'] === 0 ? 'Kinder' : ($student['grade_level'] === 7 ? 'SPED' : 'Grade ' . $student['grade_level']), $student['section']], array_map(fn($status) => in_array(strtolower((string) $status), ['present', 'p', 'served']) ? 'P' : (in_array(strtolower((string) $status), ['absent', 'a']) ? 'A' : ''), $student['days']));
  }

  public function array(): array
  {
    return collect($this->students)->map(fn(array $student) => self::values($student))->all();
  }

  public function headings(): array
  {
    return self::columnHeadings();
  }
}
