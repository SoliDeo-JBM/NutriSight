<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $year
 * @property bool $is_active
 * @property \Carbon\CarbonInterface $start_date
 * @property \Carbon\CarbonInterface $end_date
 */
class SchoolYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'is_active',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function reportPeriods()
    {
        return $this->hasMany(ReportPeriod::class);
    }
}