<?php

namespace App\Models;

use App\Services\SbfpParentApprovalService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    public const STATUS_ENROLLED = 'enrolled';

    public const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'student_id',
        'school_year_id',
        'grade_level',
        'section',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ENROLLED);
    }

    public function scopeEligibleForSbfp($query)
    {
        return $query->where(function ($eligibilityQuery) {
            $eligibilityQuery->whereIn('grade_level', SbfpParentApprovalService::AUTOMATIC_APPROVAL_GRADES)
                ->orWhereHas('sbfpParticipant.nutritionMeasurements', function ($measurementQuery) {
                    $measurementQuery->where('measurement_period', 'baseline')
                        ->whereIn('bmi_category', ['Wasted', 'Severely Wasted']);
                });
        });
    }

    public function scopeActiveApprovedSbfp($query)
    {
        return $query->active()
            ->eligibleForSbfp()
            ->whereHas('sbfpParticipant', fn($participantQuery) => $participantQuery->where('parent_consent', 'approved'));
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function sbfpParticipant()
    {
        return $this->hasOne(SbfpParticipant::class);
    }
}
