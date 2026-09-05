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
}