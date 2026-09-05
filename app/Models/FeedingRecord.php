<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'recorded_by_user_id',
        'feeding_date',
        'meal_type',
        'meal_served',
        'photo_path',
        'remarks',
    ];

    protected $casts = [
        'feeding_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}