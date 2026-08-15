<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'assessed_by_user_id',
        'assessment_date',
        'weight_kg',
        'height_m',
        'bmi',
        'nutritional_status',
        'remarks',
    ];

    protected $casts = [
        'assessment_date' => 'date',
        'weight_kg' => 'decimal:2',
        'height_m' => 'decimal:2',
        'bmi' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function assessor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by_user_id');
    }
}