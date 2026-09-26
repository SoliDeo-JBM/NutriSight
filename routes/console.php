<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\AttendanceService;
use App\Services\SchoolYearManager;
use App\Services\SbfpParentApprovalService;
use App\Models\SbfpParticipant;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:mark-absent', function () {
    $marked = app(AttendanceService::class)->markPastMealDaysAbsent();
    $this->info("Marked {$marked} learner attendance record(s) absent.");
})->purpose('Mark approved SBFP learners absent for past meal days without attendance');

Artisan::command('sbfp:approve-automatic-grades {--apply : Apply the scoped updates}', function () {
    $activeSyId = SchoolYearManager::activeSchoolYearId();
    $participants = SbfpParticipant::query()
        ->whereHas('enrollment', function ($query) use ($activeSyId) {
            $query->where('school_year_id', $activeSyId)
                ->active()
                ->whereIn('grade_level', SbfpParentApprovalService::AUTOMATIC_APPROVAL_GRADES);
        });

    $count = (clone $participants)->where(function ($query) {
        $query->where('parent_consent', '!=', 'approved')
            ->orWhereNull('parent_consent')
            ->orWhereNotNull('disapproval_reason');
    })->count();

    if (!$this->option('apply')) {
        $this->info("Dry run: {$count} active Kinder/Grade 1 participant(s) would be approved.");
        $this->info('Re-run with --apply to update records and close pending approval requests.');
        return 0;
    }

    $updated = 0;
    $participants->with('approvalRequests')->chunkById(100, function ($records) use (&$updated) {
        foreach ($records as $participant) {
            $participant->update([
                'parent_consent' => 'approved',
                'disapproval_reason' => null,
            ]);
            $participant->approvalRequests()
                ->where('status', 'pending')
                ->update([
                    'status' => 'superseded',
                    'closed_reason' => 'automatic_grade_eligibility',
                ]);
            $updated++;
        }
    });

    $this->info("Updated {$updated} active Kinder/Grade 1 participant(s).");
})->purpose('Approve active Kinder and Grade 1 SBFP participants safely');
