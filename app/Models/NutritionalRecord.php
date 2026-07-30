<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutritionalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'type', 'weight', 'height', 'bmi', 'bmi_category', 'height_for_age', 'remarks', 'recorded_at'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
