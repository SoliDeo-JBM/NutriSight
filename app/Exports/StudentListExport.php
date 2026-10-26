<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentListExport extends DefaultValueBinder implements FromArray, ShouldAutoSize, WithCustomValueBinder, WithHeadings
{
    public function __construct(private readonly Collection $students)
    {
    }

    public function headings(): array
    {
        return [
            'No.',
            'LRN / ID',
            'Learner Name',
            'Grade',
            'Section',
            'Birthdate',
            'Age',
            'Sex',
            'Weight (kg)',
            'Height (cm)',
            'BMI',
            'BMI Category',
            'Height for Age',
            'Parent/Guardian Email',
            'Parent/Guardian Contact Number',
        ];
    }

    public function bindValue(Cell $cell, $value): bool
    {
        if ($cell->getColumn() === 'B' && $cell->getRow() > 1) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function array(): array
    {
        return $this->students->values()->map(function ($student, int $index): array {
            $enrollment = $student->enrollments->first();
            $baseline = $enrollment?->sbfpParticipant?->nutritionMeasurements
                ?->where('measurement_period', 'baseline')
                ->sortByDesc('created_at')
                ->first();

            return [
                $index + 1,
                (string) $student->student_number,
                trim($student->last_name.', '.$student->first_name.' '.($student->name_extension ?? '').' '.($student->middle_name ?? '')),
                $enrollment?->grade_level === 0 ? 'Kinder' : ($enrollment?->grade_level === 7 ? 'SPED' : 'Grade '.($enrollment?->grade_level ?? '-')),
                $enrollment?->section ?? '-',
                $student->birth_date?->format('Y-m-d') ?? '-',
                $student->birth_date?->age ?? '-',
                $student->sex ?? '-',
                $baseline?->weight ?? '-',
                $baseline?->height ?? '-',
                $baseline?->bmi ?? '-',
                $baseline?->bmi_category ?? 'N/A',
                $baseline?->hfa ?? 'Normal',
                $student->guardian_email ?? '-',
                $student->guardian_contact ?? '-',
            ];
        })->all();
    }
}
