<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_number', 'last_name', 'first_name', 'name_extension', 'middle_name',
        'birth_date', 'gender', 'grade_level', 'section', 'guardian_name',
        'guardian_contact', 'guardian_email', 'address', 'is_active', 'is_permitted',
        'parent_approval_status', 'disapproval_reason', 'medical_condition_notes', 'section_id'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function nutritionalRecords()
    {
        return $this->hasMany(NutritionalRecord::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function assessments()
    {
        return $this->hasMany(StudentAssessment::class);
    }
}
