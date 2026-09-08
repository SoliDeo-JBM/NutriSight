<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceReportSection extends Model
{
  use HasFactory;

  protected $fillable = ['attendance_report_month_id', 'grade_level', 'section'];

  public function month()
  {
    return $this->belongsTo(AttendanceReportMonth::class, 'attendance_report_month_id');
  }
}