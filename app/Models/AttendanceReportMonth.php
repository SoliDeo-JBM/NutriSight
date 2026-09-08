<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** @property int $id @property int $school_year_id @property int $month */
class AttendanceReportMonth extends Model
{
  use HasFactory;

  protected $fillable = ['school_year_id', 'month'];

  public function schoolYear()
  {
    return $this->belongsTo(SchoolYear::class);
  }

  public function sections()
  {
    return $this->hasMany(AttendanceReportSection::class);
  }
}