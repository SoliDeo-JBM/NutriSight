<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionMeasurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'sbfp_participant_id',
        'height',
        'weight',
        'bmi',
        'bmi_category',
        'hfa',
        'measurement_period',
        'remarks',
    ];

    public function sbfpParticipant()
    {
        return $this->belongsTo(SbfpParticipant::class);
    }

    public function getWeightKgAttribute()
    {
        return $this->weight;
    }

    public function getHeightMAttribute()
    {
        return is_numeric($this->height) ? ($this->height > 3 ? $this->height / 100 : $this->height) : 0;
    }

    public function getNutritionalStatusAttribute()
    {
        return $this->bmi_category;
    }

    public function getAssessmentDateAttribute()
    {
        return $this->created_at;
    }
}
