<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendanceRecord extends Model
{
    use HasFactory;

    public const PERIOD_MORNING = 'morning';
    public const PERIOD_AFTERNOON = 'afternoon';
    public const PERIODS = [self::PERIOD_MORNING, self::PERIOD_AFTERNOON];

    protected $fillable = [
        'sbfp_participant_id',
        'recorded_by_user_id',
        'attendance_date',
        'meal_period',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    public function sbfpParticipant()
    {
        return $this->belongsTo(SbfpParticipant::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}