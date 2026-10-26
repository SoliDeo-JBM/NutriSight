<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Console\Scheduling\Schedule;
use App\Models\AttendancePdfExport;
use Illuminate\Support\Facades\Storage;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('attendance:mark-absent')->dailyAt('23:59');
        $schedule->call(function (): void {
            $disk = Storage::disk(config('filesystems.report_exports_disk', 'local'));
            AttendancePdfExport::where('expires_at', '<=', now())->chunkById(100, function ($exports) use ($disk): void {
                foreach ($exports as $export) {
                    if ($export->file_path) {
                        $disk->delete($export->file_path);
                    }
                    $export->delete();
                }
            });
        })->hourly()->name('prune-expired-attendance-pdf-exports')->withoutOverlapping();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active' => \App\Http\Middleware\EnsureUserIsActive::class,
            'active-school-year' => \App\Http\Middleware\EnsureSelectedSchoolYearIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
