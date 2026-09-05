<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'date', 'status', 'school_year_id'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
