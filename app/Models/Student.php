<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'lrn',
        'first_name',
        'last_name',
        'name_extension',
        'middle_name',
        'sex',
        'birth_date',
        'guardian_name',
        'guardian_email',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}