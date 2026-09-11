<?php

namespace App\Services;

use App\Models\ReportPeriod;
use App\Models\AttendanceReportMonth;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportPeriodManager
{
    public function syncExistingRecords(): void
    {
        DB::table('student_attendance_records as records')
            ->join('sbfp_participants as participants', 'participants.id', '=', 'records.sbfp_participant_id')
            ->join('enrollments', 'enrollments.id', '=', 'participants.enrollment_id')
            ->select('enrollments.school_year_id', 'records.attendance_date')
            ->whereNotNull('records.attendance_date')
            ->distinct()
            ->orderBy('enrollments.school_year_id')
            ->orderBy('records.attendance_date')
            ->get()
            ->each(function ($record): void {
                AttendanceReportMonth::firstOrCreate([
                    'school_year_id' => (int) $record->school_year_id,
                    'month' => Carbon::parse($record->attendance_date)->month,
                ]);
            });

        DB::table('nutrition_measurements as measurements')
            ->join('sbfp_participants as participants', 'participants.id', '=', 'measurements.sbfp_participant_id')
            ->join('enrollments', 'enrollments.id', '=', 'participants.enrollment_id')
            ->select('enrollments.school_year_id', 'measurements.measurement_period', 'measurements.created_at')
            ->whereIn('measurements.measurement_period', ['baseline', 'mid', 'midline', 'end', 'endline'])
            ->distinct()
            ->orderBy('enrollments.school_year_id')
            ->orderBy('measurements.created_at')
            ->get()
            ->each(function ($record): void {
                $this->ensure(
                    (int) $record->school_year_id,
                    (string) $record->measurement_period,
                    $record->created_at ? Carbon::parse($record->created_at) : null
                );
            });
    }

    public function ensure(int $schoolYearId, string $measurementPeriod, ?CarbonInterface $recordedAt = null): ?ReportPeriod
    {
        $canonicalPeriod = match (strtolower($measurementPeriod)) {
            'baseline' => 'baseline',
            'mid', 'midline' => 'mid',
            'end', 'endline' => 'end',
            default => null,
        };

        if ($canonicalPeriod === null) {
            return null;
        }

        $existing = ReportPeriod::where('school_year_id', $schoolYearId)
            ->where('measurement_period', $canonicalPeriod)
            ->first();

        if ($existing) {
            return $existing;
        }

        $date = $recordedAt ?? now();
        $month = $date->month;

        try {
            $displayName = [
                'baseline' => 'Baseline',
                'mid' => 'Midline',
                'end' => 'Endline',
            ][$canonicalPeriod];

            return ReportPeriod::create([
                'school_year_id' => $schoolYearId,
                'measurement_period' => $canonicalPeriod,
                'month' => $month,
                'name' => $displayName,
            ]);
        } catch (QueryException $exception) {
            Log::warning('Automatic report period creation was skipped after a concurrent or duplicate insert.', [
                'school_year_id' => $schoolYearId,
                'measurement_period' => $canonicalPeriod,
                'month' => $month,
                'error' => $exception->getMessage(),
            ]);

            return ReportPeriod::where('school_year_id', $schoolYearId)
                ->where('measurement_period', $canonicalPeriod)
                ->first();
        }
    }
}
