<?php

namespace App\Services;

use App\Models\SchoolYear;
use App\Models\SchoolYearReportSetting;
use App\Models\Student;
use App\Models\User;

class AttendanceReportData
{
    public function students(SchoolYear $schoolYear, int $month, ?int $grade = null, ?string $section = null, ?User $scopeUser = null, bool $enforceAdvisoryScope = false)
    {
        $calendarYear = $this->calendarYear($schoolYear, $month);
        $monthStart = sprintf('%04d-%02d-01', $calendarYear, $month);
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        $grade ??= $scopeUser?->advisory_grade_level;
        $section ??= $scopeUser?->advisory_section;
        $enforceAdvisoryScope = $enforceAdvisoryScope || ($scopeUser?->isEncoder() ?? false);
        $approvedBeneficiary = function ($query) use ($schoolYear, $grade, $section, $enforceAdvisoryScope) {
            $query->where('school_year_id', $schoolYear->id)
                ->activeApprovedSbfp()
                ->when($grade !== null, fn($q) => $q->where('grade_level', $grade))
                ->when($section !== null, fn($q) => $q->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($section))]))
                ->when($enforceAdvisoryScope && ($grade === null || $section === null), fn($q) => $q->whereRaw('1 = 0'));
        };

        return Student::with([
            'enrollments' => function ($query) use ($schoolYear, $grade, $section, $enforceAdvisoryScope, $monthStart, $monthEnd) {
                $query->where('school_year_id', $schoolYear->id)
                    ->activeApprovedSbfp()
                    ->when($grade !== null, fn($q) => $q->where('grade_level', $grade))
                    ->when($section !== null, fn($q) => $q->whereRaw('LOWER(TRIM(section)) = ?', [strtolower(trim($section))]))
                    ->when($enforceAdvisoryScope && ($grade === null || $section === null), fn($q) => $q->whereRaw('1 = 0'))
                    ->with(['sbfpParticipant.attendanceRecords' => fn($attendanceQuery) => $attendanceQuery->whereBetween('attendance_date', [$monthStart, $monthEnd])]);
            }
        ])
            ->whereHas('enrollments', $approvedBeneficiary)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function (Student $student, int $index) use ($month, $calendarYear) {
                $records = $student->enrollments->first()?->sbfpParticipant?->attendanceRecords ?? collect();
                $recordsByDay = $records->groupBy(fn($record) => $record->attendance_date?->day);
                $days = [];
                $daysInMonth = (int) date('t', mktime(0, 0, 0, $month, 1, $calendarYear));

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $dayRecords = $recordsByDay->get($day, collect());
                    $days[$day] = $dayRecords->contains(fn($item) => in_array(strtolower((string) $item->status), ['present', 'p', 'served']))
                        ? 'present'
                        : ($dayRecords->contains(fn($item) => in_array(strtolower((string) $item->status), ['absent', 'a'])) ? 'absent' : null);
                }

                return [
                    'number' => $index + 1,
                    'name' => trim($student->last_name . ', ' . $student->first_name . ' ' . ($student->middle_name ?? '')),
                    'grade_level' => $student->enrollments->first()?->grade_level,
                    'section' => $student->enrollments->first()?->section,
                    'days' => $days,
                ];
            });
    }

    public function calendarYear(?SchoolYear $schoolYear, int $month): int
    {
        $startYear = (int) substr((string) ($schoolYear?->year ?? now()->year), 0, 4);

        return $month >= 6 ? $startYear : $startYear + 1;
    }

    public function signatories(?int $schoolYearId = null): array
    {
        $pdoName = $schoolYearId
            ? SchoolYearReportSetting::where('school_year_id', $schoolYearId)->value('project_development_officer_name')
            : null;
        $superAdmin = User::where('role', User::ROLE_SUPER_ADMIN)->orderBy('id')->first();

        return [
            $pdoName ? strtoupper(trim($pdoName)) : 'FULL NAME OF THE PROJECT DEVELOPMENT OFFICER',
            $superAdmin?->name ? strtoupper(trim($superAdmin->name)) : 'Full Name of the Super Admin',
        ];
    }
}