<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\AttendanceService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('attendance:mark-absent', function () {
    $marked = app(AttendanceService::class)->markPastMealDaysAbsent();
    $this->info("Marked {$marked} learner attendance record(s) absent.");
})->purpose('Mark approved SBFP learners absent for past meal days without attendance');
