<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssessmentReportExport implements FromArray, ShouldAutoSize, WithHeadings
{
    public function __construct(private readonly array $assessment)
    {
    }

    public static function columnKeys(): array
    {
        return ['school_year', 'attendance_students', 'complete_attendance', 'complete_attendance_rate', 'with_absences', 'with_absences_rate', 'malnourished', 'recovered', 'recovered_rate', 'still_needing_support', 'still_needing_support_rate'];
    }

    public static function columnHeadings(): array
    {
        return ['School Year', 'Students With Attendance', 'Complete Attendance', 'Complete %', 'With Absences', 'Absences %', 'At-Risk Nutrition Cohort', 'Recovered to Normal', 'Recovered %', 'Still Needing Support', 'Support %'];
    }

    public static function values(array $assessment): array
    {
        return array_map(fn ($key) => $assessment[$key], self::columnKeys());
    }

    public function array(): array
    {
        return [self::values($this->assessment)];
    }

    public function headings(): array
    {
        return self::columnHeadings();
    }
}
