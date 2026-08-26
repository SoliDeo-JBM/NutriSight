<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'grade_level', 'adviser_id', 'school_year_id'];

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(Program::class, 'school_year_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
