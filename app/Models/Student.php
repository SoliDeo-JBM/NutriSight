<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\SchoolYearManager;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'lrn',
        'first_name',
        'last_name',
        'name_extension',
        'middle_name',
        'sex',
        'birth_date',
        'guardian_name',
        'guardian_email',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function getStudentNumberAttribute()
    {
        return $this->lrn;
    }

    public function getParentApprovalStatusAttribute()
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $this->enrollments()->where('school_year_id', $activeSyId)->first();
        return $enrollment?->sbfpParticipant?->parent_consent;
    }

    public function getDisapprovalReasonAttribute()
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $this->enrollments()->where('school_year_id', $activeSyId)->first();
        return $enrollment?->sbfpParticipant?->disapproval_reason;
    }

    public function getIsPermittedAttribute()
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $this->enrollments()->where('school_year_id', $activeSyId)->first();
        return $enrollment?->sbfpParticipant?->parent_consent === 'approved';
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function nutritionalRecords()
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $this->enrollments()->where('school_year_id', $activeSyId)->first();
        if ($enrollment && $enrollment->sbfpParticipant) {
            return $enrollment->sbfpParticipant->nutritionMeasurements();
        }
        return NutritionMeasurement::whereRaw('1 = 0');
    }

    public function assessments()
    {
        return $this->nutritionalRecords();
    }
}
