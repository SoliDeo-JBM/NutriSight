<?php

namespace App\Services;

use App\Models\MealPlan;
use App\Models\SchoolYear;
use App\Models\SbfpParticipant;
use App\Models\StudentAttendanceRecord;
use App\Models\User;
use Carbon\Carbon;

class AttendanceService
{
    public function markPastMealDaysAbsent(?int $recordedByUserId = null, ?int $schoolYearId = null): int
    {
        $recordedByUserId ??= User::query()->orderBy('id')->value('id');
        $schoolYear = $schoolYearId ? SchoolYear::find($schoolYearId) : SchoolYear::where('is_active', true)->first();

        if (!$recordedByUserId || !$schoolYear) {
            return 0;
        }

        $mealSessions = MealPlan::query()
            ->whereDate('meal_date', '<', Carbon::today())
            ->whereDate('meal_date', '>=', $schoolYear->start_date)
            ->when($schoolYear->end_date, fn ($query) => $query->whereDate('meal_date', '<=', $schoolYear->end_date))
            ->selectRaw('DATE(meal_date) as attendance_date, meal_period')
            ->distinct()
            ->orderBy('attendance_date')
            ->orderBy('meal_period')
            ->get();

        if ($mealSessions->isEmpty()) {
            return 0;
        }

        $participantIds = SbfpParticipant::query()
            ->where('parent_consent', 'approved')
            ->whereHas('enrollment', fn ($query) => $query->where('school_year_id', $schoolYear->id))
            ->pluck('id');

        $markedCount = 0;

        foreach ($mealSessions as $mealSession) {
            $existingIds = StudentAttendanceRecord::query()
            ->whereDate('attendance_date', $mealSession->attendance_date)
            ->where('meal_period', $mealSession->meal_period)
                ->whereIn('sbfp_participant_id', $participantIds)
                ->pluck('sbfp_participant_id');

            $missingIds = $participantIds->diff($existingIds);
            if ($missingIds->isEmpty()) {
                continue;
            }

            $now = now();
            $rows = $missingIds->map(fn ($participantId) => [
                'sbfp_participant_id' => $participantId,
                'recorded_by_user_id' => $recordedByUserId,
                'attendance_date' => $mealSession->attendance_date,
                'meal_period' => $mealSession->meal_period,
                'status' => 'absent',
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            StudentAttendanceRecord::insert($rows);
            $markedCount += count($rows);
        }

        return $markedCount;
    }
}
