<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $report_period_id
 * @property int $grade_level
 * @property string $sex
 * @property int $enrollment
 * @property int $pupils_weighed
 * @property int $pupils_height_taken
 * @property int $bmi_severely_wasted
 * @property int $bmi_wasted
 * @property int $bmi_normal
 * @property int $bmi_overweight
 * @property int $bmi_obese
 * @property int $hfa_severely_stunted
 * @property int $hfa_stunted
 * @property int $hfa_normal
 * @property int $hfa_tall
 */
class ReportPeriodRow extends Model
{
  use HasFactory;

  protected $fillable = [
    'report_period_id',
    'grade_level',
    'sex',
    'enrollment',
    'pupils_weighed',
    'pupils_height_taken',
    'bmi_severely_wasted',
    'bmi_wasted',
    'bmi_normal',
    'bmi_overweight',
    'bmi_obese',
    'hfa_severely_stunted',
    'hfa_stunted',
    'hfa_normal',
    'hfa_tall',
  ];

  public function period()
  {
    return $this->belongsTo(ReportPeriod::class, 'report_period_id');
  }
}