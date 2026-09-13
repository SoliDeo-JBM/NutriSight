<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYearUserRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'user_id',
        'role',
        'deped_id',
        'position',
        'advisory_grade_level',
        'advisory_section',
    ];

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}