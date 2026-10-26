<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePdfExport extends Model
{
    public const STATUS_QUEUED = 'queued';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'attendance_report_month_id',
        'grade_level',
        'section',
        'scope_is_encoder',
        'status',
        'file_path',
        'failure_message',
        'expires_at',
        'data_preparation_ms',
        'pdf_render_ms',
        'peak_memory_bytes',
    ];

    protected function casts(): array
    {
        return [
            'scope_is_encoder' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportMonth()
    {
        return $this->belongsTo(AttendanceReportMonth::class, 'attendance_report_month_id');
    }
}