<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\SchoolYearManager;

/**
 * @property int $id
 * @property string $guardian_email
 */
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

    public function getTermProgressAttribute()
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $this->enrollments()->where('school_year_id', $activeSyId)->first();
        $measurements = $enrollment?->sbfpParticipant?->nutritionMeasurements ?? collect();

        $terms = ['Term 1' => [], 'Term 2' => [], 'Term 3' => []];
        foreach ($measurements as $m) {
            $period = strtolower($m->measurement_period ?? '');
            if ($period === 'term 1' || $period === 'baseline' || $m->created_at?->month == 1) {
                $terms['Term 1'][] = $m;
            } elseif ($period === 'term 2' || $period === 'mid' || $m->created_at?->month == 2) {
                $terms['Term 2'][] = $m;
            } else {
                $terms['Term 3'][] = $m;
            }
        }
        return $terms;
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
