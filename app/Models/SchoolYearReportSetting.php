<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolYearReportSetting extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'school_year_id',
        'project_development_officer_name',
    ];

    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
