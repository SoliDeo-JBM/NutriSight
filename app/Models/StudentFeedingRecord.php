<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFeedingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'sbfp_participant_id',
        'recorded_by_user_id',
        'feeding_date',
        'meal_type',
        'meal_served',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'feeding_date' => 'date',
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