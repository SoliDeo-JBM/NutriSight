<?php

namespace App\Exports;

use App\Models\AttendanceReportMonth;
use App\Services\SchoolLogoService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class AttendanceReportExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings
{
  public function __construct(private readonly Collection $students, private readonly AttendanceReportMonth $month, private readonly string $adminName = 'Full Name of the Admin', private readonly string $superAdminName = 'Full Name of the Super Admin')
  {
  }

  public static function columnHeadings(?int $daysInMonth = null): array
  {
    return array_merge(['No.', 'Name of Pupil', 'Grade Level', 'Section'], range(1, $daysInMonth ?? 31));
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
    $calendarYear = (int) substr((string) $this->month->schoolYear->year, 0, 4) + ($this->month->month < 6 ? 1 : 0);
    $daysInMonth = (int) date('t', mktime(0, 0, 0, $this->month->month, 1, $calendarYear));
    return self::columnHeadings($daysInMonth);
  }

  public function registerEvents(): array
  {
    return [AfterSheet::class => function (AfterSheet $event): void {
      $sheet = $event->sheet->getDelegate();
      $sheet->insertNewRowBefore(1, 5);
      $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headings()));
      foreach (['A1:' . $lastColumn . '1', 'A2:' . $lastColumn . '2', 'A3:' . $lastColumn . '3', 'A4:' . $lastColumn . '4'] as $range) {
        $sheet->mergeCells($range);
      }
      $sheet->setCellValue('A1', 'Department of Education');
      $sheet->setCellValue('A2', 'Bureau of Learner Support Services');
      $sheet->setCellValue('A3', 'SCHOOL-BASED FEEDING PROGRAM - RECORD OF DAILY FEEDING');
      $sheet->setCellValue('A4', 'Attendance for ' . date('F Y', mktime(0, 0, 0, $this->month->month, 1)) . ' | SY ' . $this->month->schoolYear->year);
      $lastRow = count($this->students) + 6;
      $signatureRow = $lastRow + 2;
      $lastColumnIndex = count($this->headings());
      $preparedEnd = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex((int) ceil($lastColumnIndex / 2));
      $notedStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex((int) ceil($lastColumnIndex / 2) + 1);
      foreach ([
        'A' . $signatureRow . ':' . $preparedEnd . $signatureRow,
        $notedStart . $signatureRow . ':' . $lastColumn . $signatureRow,
        'A' . ($signatureRow + 1) . ':' . $preparedEnd . ($signatureRow + 1),
        $notedStart . ($signatureRow + 1) . ':' . $lastColumn . ($signatureRow + 1),
        'A' . ($signatureRow + 2) . ':' . $preparedEnd . ($signatureRow + 2),
        $notedStart . ($signatureRow + 2) . ':' . $lastColumn . ($signatureRow + 2),
        'A' . ($signatureRow + 3) . ':' . $preparedEnd . ($signatureRow + 3),
        $notedStart . ($signatureRow + 3) . ':' . $lastColumn . ($signatureRow + 3),
      ] as $range) {
        $sheet->mergeCells($range);
      }
      $sheet->setCellValue('A' . $signatureRow, 'Prepared by:');
      $sheet->setCellValue($notedStart . $signatureRow, 'Noted by:');
      $sheet->setCellValue('A' . ($signatureRow + 1), '____________________________');
      $sheet->setCellValue($notedStart . ($signatureRow + 1), '________________________________');
      $sheet->setCellValue('A' . ($signatureRow + 2), $this->adminName);
      $sheet->setCellValue($notedStart . ($signatureRow + 2), $this->superAdminName);
      $sheet->setCellValue('A' . ($signatureRow + 3), 'Project Development Officer');
      $sheet->setCellValue($notedStart . ($signatureRow + 3), 'School Head');
      foreach ([[SchoolLogoService::path(), 'A1'], [public_path('images/id/kagawaran_ng_edukasyo.jpeg'), $lastColumn . '1']] as [$path, $coordinate]) {
        $drawing = new Drawing();
        $drawing->setPath($path);
        $drawing->setHeight(42);
        $drawing->setCoordinates($coordinate);
        $drawing->setWorksheet($sheet);
      }
      $sheet->getStyle('A1:' . $lastColumn . ($signatureRow + 3))->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
      $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(14);
      $sheet->getStyle('A1:A4')->getFont()->setSize(11);
      $sheet->getStyle('A' . $signatureRow . ':' . $lastColumn . $signatureRow)->getFont()->setBold(true);
      $sheet->getStyle('A' . ($signatureRow + 2) . ':' . $lastColumn . ($signatureRow + 2))->getFont()->setBold(true);
    }];
  }
}
