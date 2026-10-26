<?php

namespace App\Jobs;

use App\Models\AttendancePdfExport;
use App\Services\AttendanceReportData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class GenerateAttendancePdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;
    public int $tries = 1;

    public function __construct(public readonly int $exportId)
    {
    }

    public function handle(AttendanceReportData $reportData): void
    {
        $export = AttendancePdfExport::with(['user', 'reportMonth.schoolYear'])->find($this->exportId);
        if (!$export || $export->expires_at->isPast()) {
            return;
        }

        $export->update(['status' => AttendancePdfExport::STATUS_PROCESSING, 'failure_message' => null]);
        $startedAt = hrtime(true);

        try {
            $month = $export->reportMonth;
            $students = $reportData->students(
                $month->schoolYear,
                $month->month,
                $export->grade_level,
                $export->section,
                null,
                $export->scope_is_encoder,
            );
            [$adminName, $superAdminName] = $reportData->signatories($month->school_year_id);
            $calendarYear = $reportData->calendarYear($month->schoolYear, $month->month);
            $daysInMonth = (int) date('t', mktime(0, 0, 0, $month->month, 1, $calendarYear));
            $dailyTotals = array_fill(1, $daysInMonth, 0);

            foreach ($students as $student) {
                foreach ($student['days'] as $day => $status) {
                    if ($status === 'present') {
                        $dailyTotals[$day]++;
                    }
                }
            }

            $dataPreparedAt = hrtime(true);
            $pdfContents = Pdf::loadView('admin.reports.attendance.print', compact('month', 'students', 'calendarYear', 'dailyTotals', 'adminName', 'superAdminName'))
                ->setOption(['isHtml5ParserEnabled' => false, 'dpi' => 72])
                ->setPaper('a4', 'landscape')
                ->output();
            $renderedAt = hrtime(true);

            $path = 'attendance-pdf-exports/' . $export->id . '/' . Str::uuid() . '.pdf';
            $disk = Storage::disk(config('filesystems.report_exports_disk', 'local'));
            if (!$disk->put($path, $pdfContents)) {
                throw new \RuntimeException('Unable to store the generated attendance PDF.');
            }

            $export->update([
                'status' => AttendancePdfExport::STATUS_COMPLETED,
                'file_path' => $path,
                'data_preparation_ms' => (int) (($dataPreparedAt - $startedAt) / 1_000_000),
                'pdf_render_ms' => (int) (($renderedAt - $dataPreparedAt) / 1_000_000),
                'peak_memory_bytes' => memory_get_peak_usage(true),
            ]);

            Log::info('Attendance PDF export completed.', [
                'export_id' => $export->id,
                'student_count' => $students->count(),
                'data_preparation_ms' => $export->data_preparation_ms,
                'pdf_render_ms' => $export->pdf_render_ms,
                'total_ms' => (int) ((hrtime(true) - $startedAt) / 1_000_000),
                'peak_memory_bytes' => $export->peak_memory_bytes,
            ]);
        } catch (Throwable $exception) {
            $export->update([
                'status' => AttendancePdfExport::STATUS_FAILED,
                'failure_message' => 'The PDF could not be generated. Please try again or contact an administrator.',
            ]);

            Log::error('Attendance PDF export failed.', [
                'export_id' => $export->id,
                'exception' => $exception::class,
                'elapsed_ms' => (int) ((hrtime(true) - $startedAt) / 1_000_000),
                'peak_memory_bytes' => memory_get_peak_usage(true),
            ]);

            throw $exception;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $export = AttendancePdfExport::find($this->exportId);
        if (!$export || $export->status === AttendancePdfExport::STATUS_COMPLETED) {
            return;
        }

        if ($export->file_path) {
            Storage::disk(config('filesystems.report_exports_disk', 'local'))->delete($export->file_path);
        }

        $export->update([
            'status' => AttendancePdfExport::STATUS_FAILED,
            'failure_message' => 'The PDF could not be generated. Please try again or contact an administrator.',
        ]);

        Log::error('Attendance PDF export job exhausted its attempts.', [
            'export_id' => $export->id,
            'exception' => $exception ? $exception::class : null,
            'peak_memory_bytes' => memory_get_peak_usage(true),
        ]);
    }
}