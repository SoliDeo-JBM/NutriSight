<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_date',
        'meal_name',
        'recorded_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'meal_date' => 'date',
        ];
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }
}
