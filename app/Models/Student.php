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
        'guardian_contact',
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
        $consent = $enrollment?->sbfpParticipant?->parent_consent;
        return $consent === 'pending' ? null : $consent;
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
        $periods = $this->periodProgress;

        return [
            'Term 1' => $periods['Baseline'],
            'Term 2' => $periods['Midline'],
            'Term 3' => $periods['Endline'],
        ];
    }

    public function getPeriodProgressAttribute()
    {
        $activeSyId = SchoolYearManager::activeSchoolYearId();
        $enrollment = $this->enrollments()->where('school_year_id', $activeSyId)->first();
        $measurements = $enrollment?->sbfpParticipant?->nutritionMeasurements ?? collect();

        $periods = ['Baseline' => [], 'Midline' => [], 'Endline' => []];
        foreach ($measurements as $m) {
            $period = strtolower($m->measurement_period ?? '');
            if ($period === 'baseline') {
                $periods['Baseline'][] = $m;
            } elseif ($period === 'midline') {
                $periods['Midline'][] = $m;
            } elseif ($period === 'endline') {
                $periods['Endline'][] = $m;
            } else {
                continue;
            }
        }
        return $periods;
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
