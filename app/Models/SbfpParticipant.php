<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SbfpParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'parent_consent',
        'disapproval_reason',
    ];

    // Belongs to a specific Enrollment
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    // Has many BMI/Height measurements over the year (Baseline, Mid, End)
    public function nutritionMeasurements()
    {
        return $this->hasMany(NutritionMeasurement::class);
    }

    // Has many daily attendance logs
    public function attendanceRecords()
    {
        return $this->hasMany(StudentAttendanceRecord::class);
    }

    // Has many daily feeding/meal logs
    public function feedingRecords()
    {
        return $this->hasMany(StudentFeedingRecord::class);
    }
}