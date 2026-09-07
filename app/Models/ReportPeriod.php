<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $school_year_id
 * @property string $name
 * @property int|null $month
 * @property string $measurement_period
 */
class ReportPeriod extends Model
{
  use HasFactory;

  protected $fillable = ['school_year_id', 'name', 'month', 'measurement_period'];

  public function schoolYear()
  {
    return $this->belongsTo(SchoolYear::class);
  }

  public function rows()
  {
    return $this->hasMany(ReportPeriodRow::class);
  }
}