<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\NutritionMeasurement;
use App\Models\SbfpParticipant;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use RuntimeException;

class StudentSpreadsheetImportService
{
    public function preview(UploadedFile $file, User $actor): array
    {
        $rows = $this->readRows($file);
        $rows = $this->rowsForActor($rows, $actor);

        return [
            'rows' => array_map(function (array $row, int $index): array {
                $row['generated_lrn'] = $this->generatedLrn($row);
                $row['display_number'] = $index + 1;

                return $row;
            }, $rows, array_keys($rows)),
            'not_found' => $actor->isEncoder() && $rows === [],
        ];
    }

    public function import(UploadedFile $file, User $actor): array
    {
        $rows = $this->rowsForActor($this->readRows($file), $actor);
        $activeSchoolYearId = SchoolYearManager::activeSchoolYearId();

        if (! $activeSchoolYearId) {
            throw new RuntimeException('There is no active school year available for this import.');
        }

        if ($actor->isEncoder() && $rows === []) {
            return ['imported' => 0, 'skipped' => 0, 'accounts_created' => 0, 'errors' => [], 'not_found' => true];
        }

        $result = ['imported' => 0, 'skipped' => 0, 'accounts_created' => 0, 'errors' => [], 'not_found' => false];

        DB::transaction(function () use ($rows, $actor, $activeSchoolYearId, &$result): void {
            $encoderAssignments = [];
            foreach ($rows as $row) {
                $key = $row['grade'].'|'.$this->normalizeSection($row['section']);
                $encoderAssignments[$key] = $row;
            }

            if ($actor->isAdminOrAbove()) {
                foreach ($encoderAssignments as $row) {
                    try {
                        if ($this->ensureEncoder($row['grade'], $row['section'], $activeSchoolYearId)) {
                            $result['accounts_created']++;
                        }
                    } catch (RuntimeException $exception) {
                        if (count($result['errors']) < 20) {
                            $result['errors'][] = $this->gradeLabel($row['grade']).' '.$this->displaySection($row['section']).': '.$exception->getMessage();
                        }
                    }
                }
            }

            $this->importRows($rows, $activeSchoolYearId, $result);
        });

        AuditLogger::log(
            'Imported',
            'Students',
            'Imported '.$result['imported'].' learner(s) from Excel; '.$result['skipped']
                .' row(s) skipped and '.$result['accounts_created'].' encoder account(s) created.'
        );

        return $result;
    }

    public function importStoredFile(string $path, User $actor): array
    {
        $file = new UploadedFile($path, basename($path), 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        return $this->import($file, $actor);
    }

    private function rowsForActor(array $rows, User $actor): array
    {
        if (! $actor->isEncoder()) {
            return $rows;
        }

        $grade = $actor->advisory_grade_level;
        $section = $this->normalizeSection($actor->advisory_section);

        if ($grade === null || $section === '') {
            return [];
        }

        return array_values(array_filter($rows, fn (array $row) => $row['grade'] === (int) $grade
            && $this->normalizeSection($row['section']) === $section));
    }

    private function importRows(array $rows, int $schoolYearId, array &$result): void
    {
        $rowsByLrn = [];
        foreach ($rows as $row) {
            $rowsByLrn[$this->generatedLrn($row)] = $row;
        }

        $lrns = array_keys($rowsByLrn);
        $students = Student::whereIn('lrn', $lrns)->get()->keyBy('lrn');
        $newStudentRows = [];
        $now = now();
        foreach ($rowsByLrn as $lrn => $row) {
            if ($students->has($lrn)) {
                $student = $students->get($lrn);
                if (str_ends_with(trim((string) $student->last_name), ',')) {
                    $student->update(['last_name' => rtrim(trim((string) $student->last_name), ',')]);
                }

                continue;
            }

            $newStudentRows[] = [
                'lrn' => $lrn,
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'middle_name' => $row['middle_name'],
                'sex' => $row['sex'],
                'birth_date' => $row['birth_date'],
                'guardian_name' => null,
                'guardian_email' => '',
                'guardian_contact' => null,
                'address' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($newStudentRows !== []) {
            Student::insert($newStudentRows);
            $students = Student::whereIn('lrn', $lrns)->get()->keyBy('lrn');
        }

        $studentIds = $students->pluck('id')->all();
        $existingEnrollments = Enrollment::whereIn('student_id', $studentIds)
            ->where('school_year_id', $schoolYearId)
            ->active()
            ->get()
            ->keyBy('student_id');
        $newEnrollmentRows = [];
        foreach ($rowsByLrn as $lrn => $row) {
            $student = $students->get($lrn);
            $existingEnrollment = $existingEnrollments->get($student->id);
            if ($existingEnrollment) {
                if ((int) $existingEnrollment->grade_level !== $row['grade']
                    || $this->normalizeSection($existingEnrollment->section) !== $this->normalizeSection($row['section'])) {
                    $result['errors'][] = 'Row '.$row['row_number'].': The generated learner ID already belongs to another active section.';
                }
                $result['skipped']++;

                continue;
            }

            $newEnrollmentRows[] = [
                'student_id' => $student->id,
                'school_year_id' => $schoolYearId,
                'grade_level' => $row['grade'],
                'section' => $this->displaySection($row['section']),
                'status' => Enrollment::STATUS_ENROLLED,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($newEnrollmentRows === []) {
            return;
        }

        Enrollment::insert($newEnrollmentRows);
        $newStudentIds = array_column($newEnrollmentRows, 'student_id');
        $enrollments = Enrollment::whereIn('student_id', $newStudentIds)
            ->where('school_year_id', $schoolYearId)
            ->active()
            ->get()
            ->keyBy('student_id');
        $participantRows = [];
        foreach ($newEnrollmentRows as $enrollmentRow) {
            $row = $rowsByLrn[$students->firstWhere('id', $enrollmentRow['student_id'])->lrn];
            $participantRows[] = [
                'enrollment_id' => $enrollments->get($enrollmentRow['student_id'])->id,
                'parent_consent' => app(SbfpParentApprovalService::class)->isAutomaticallyApprovedGrade($row['grade'])
                    ? 'approved'
                    : 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        SbfpParticipant::insert($participantRows);
        $participants = SbfpParticipant::whereIn('enrollment_id', array_column($participantRows, 'enrollment_id'))
            ->get()
            ->keyBy('enrollment_id');
        $measurementRows = [];
        foreach ($newEnrollmentRows as $enrollmentRow) {
            $row = $rowsByLrn[$students->firstWhere('id', $enrollmentRow['student_id'])->lrn];
            if ($row['weight'] === null || $row['height'] === null) {
                continue;
            }
            $metrics = app(NutriCalculationService::class)->calculateBMI($row['weight'], $row['height']);
            $measurementRows[] = [
                'sbfp_participant_id' => $participants->get($enrollments->get($enrollmentRow['student_id'])->id)->id,
                'height' => $row['height'],
                'weight' => $row['weight'],
                'bmi' => $metrics['bmi'],
                'bmi_category' => $metrics['category'],
                'hfa' => trim((string) ($row['hfa'] ?? '')) ?: 'Normal',
                'measurement_period' => 'baseline',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if ($measurementRows !== []) {
            NutritionMeasurement::insert($measurementRows);
        }

        $result['imported'] += count($newEnrollmentRows);
    }

    private function ensureEncoder(int $grade, string $section, int $schoolYearId): bool
    {
        $section = $this->displaySection($section);
        $exists = User::where('role', User::ROLE_ENCODER)
            ->whereHas('schoolYearUserRecords', function ($query) use ($schoolYearId, $grade, $section) {
                $query->where('school_year_id', $schoolYearId)
                    ->where('advisory_grade_level', (string) $grade)
                    ->whereRaw('LOWER(TRIM(advisory_section)) = ?', [strtolower($section)]);
            })->exists();

        if ($exists) {
            return false;
        }

        $email = $this->accountEmail($grade, $section);
        if (User::where('email', $email)->exists()) {
            throw new RuntimeException("Cannot create the encoder account because {$email} is already in use.");
        }

        $depedId = $this->generatedDepedId($grade, $section);
        while (User::where('deped_id', $depedId)->exists()) {
            $depedId++;
        }

        $encoder = User::create([
            'name' => $this->gradeLabel($grade).' '.$section.' Encoder',
            'email' => $email,
            'password' => Hash::make('@Password1'),
            'sex' => 'Male',
            'role' => User::ROLE_ENCODER,
            'birthdate' => '1970-01-01',
            'position' => 'Teacher I',
            'advisory_grade_level' => $grade,
            'advisory_section' => $section,
            'deped_id' => $depedId,
            'is_active' => true,
        ]);
        $encoder->syncSchoolYearUserRecord($schoolYearId);

        return true;
    }

    private function readRows(UploadedFile $file): array
    {
        $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        $headerIndex = null;
        foreach ($rows as $index => $row) {
            $values = array_map(fn ($value) => strtolower(trim((string) $value)), $row);
            if (in_array('name', $values, true) && collect($values)->contains(fn ($value) => str_contains($value, 'grade'))) {
                $headerIndex = $index;
                break;
            }
        }

        if ($headerIndex === null) {
            throw new RuntimeException('The workbook must contain Name, Sex, Grade/Section, Date of Birth, Weight, and Height columns.');
        }

        $headers = array_map(fn ($value) => strtolower(trim((string) $value)), $rows[$headerIndex]);
        $column = fn (string $needle) => collect($headers)->search(fn ($header) => str_contains($header, $needle));
        $nameColumn = $column('name');
        $sexColumn = $column('sex');
        $gradeColumn = $column('grade');
        $birthColumn = $column('date of birth');
        $weightColumn = $column('weight');
        $heightColumn = $column('height');
        if (in_array(false, [$nameColumn, $sexColumn, $gradeColumn, $birthColumn, $weightColumn, $heightColumn], true)) {
            throw new RuntimeException('The workbook is missing one or more required learner columns.');
        }

        $parsed = [];
        foreach (array_slice($rows, $headerIndex + 1) as $offset => $row) {
            $name = trim((string) ($row[$nameColumn] ?? ''));
            if ($name === '') {
                continue;
            }

            [$grade, $section] = $this->parseGradeSection((string) ($row[$gradeColumn] ?? ''));
            if ($grade === null || $section === '') {
                continue;
            }
            [$lastName, $firstName, $middleName] = $this->parseName($name);
            $parsed[] = [
                'row_number' => $headerIndex + $offset + 2,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'middle_name' => $middleName,
                'sex' => strtoupper(trim((string) ($row[$sexColumn] ?? ''))) === 'F' ? 'Female' : 'Male',
                'birth_date' => $this->parseDate($row[$birthColumn] ?? null),
                'grade' => $grade,
                'section' => $section,
                'weight' => is_numeric($row[$weightColumn] ?? null) ? (float) $row[$weightColumn] : null,
                'height' => is_numeric($row[$heightColumn] ?? null)
                    ? (float) $row[$heightColumn] * (str_contains(strtolower($headers[$heightColumn]), 'meter') ? 100 : 1)
                    : null,
                'hfa' => ($hfaColumn = $column('hfa')) !== false ? ($row[$hfaColumn] ?? null) : null,
            ];
        }

        if ($parsed === []) {
            throw new RuntimeException('No valid learner rows were found in the workbook.');
        }

        return $parsed;
    }

    private function parseName(string $name): array
    {
        $name = trim(preg_replace('/\s+/', ' ', $name) ?? $name);
        $commaPosition = strpos($name, ',');

        if ($commaPosition !== false) {
            $surname = trim(substr($name, 0, $commaPosition));
            $givenNames = preg_split('/\s+/', trim(substr($name, $commaPosition + 1))) ?: [];
            $prefixes = ['de', 'dela', 'del', 'delos', 'de la', 'de los', 'san', 'santa'];

            if (in_array(strtolower($surname), $prefixes, true) && isset($givenNames[0])) {
                $surname .= ' '.$givenNames[0];
                array_shift($givenNames);
            }
        } else {
            $parts = preg_split('/\s+/', $name) ?: [];
            $surname = array_shift($parts) ?? '';
            $givenNames = $parts;
        }

        $firstName = array_shift($givenNames) ?? $surname;

        return [
            $surname,
            $firstName,
            $givenNames === [] ? null : implode(' ', $givenNames),
        ];
    }

    private function parseGradeSection(string $value): array
    {
        $parts = preg_split('/\s*-\s*/', trim($value), 2);
        if (count($parts) !== 2) {
            return [null, ''];
        }
        $gradeText = strtolower(trim($parts[0]));
        $grade = $gradeText === 'kinder' ? 0 : (preg_match('/\d+/', $gradeText, $match) ? (int) $match[0] : null);

        return [$grade, trim($parts[1])];
    }

    private function parseDate(mixed $value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }
        $value = trim((string) $value);
        if (preg_match('/(\d{4})年(\d{1,2})月(\d{1,2})日/u', $value, $match)) {
            return Carbon::createFromDate((int) $match[1], (int) $match[2], (int) $match[3])->format('Y-m-d');
        }
        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    private function generatedLrn(array $row): string
    {
        return (string) (100000000000 + (int) substr(sprintf('%u', crc32($row['grade'].'|'.$this->normalizeSection($row['section']).'|'.strtoupper($row['last_name'].'|'.$row['first_name'].'|'.($row['middle_name'] ?? '')))), 0, 11));
    }

    private function generatedDepedId(int $grade, string $section): int
    {
        return 900000000 + abs(crc32($grade.'|'.$this->normalizeSection($section))) % 99999999;
    }

    private function accountEmail(int $grade, string $section): string
    {
        return strtolower(($grade === 0 ? 'kinder' : 'grade'.$grade).preg_replace('/[^a-z0-9]+/i', '', $section).'@nutrisight.test');
    }

    private function gradeLabel(int $grade): string
    {
        return $grade === 0 ? 'Kinder' : 'Grade '.$grade;
    }

    private function normalizeSection(?string $section): string
    {
        return strtolower(trim((string) $section));
    }

    private function displaySection(string $section): string
    {
        return ucfirst(strtolower(trim($section)));
    }
}
