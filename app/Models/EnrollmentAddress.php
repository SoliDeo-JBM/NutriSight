<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrollmentAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'house_number',
        'street',
        'purok',
        'province_code',
        'municipality_code',
        'barangay_code',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function province()
    {
        return $this->belongsTo(PhilippineProvince::class, 'province_code', 'code');
    }

    public function municipality()
    {
        return $this->belongsTo(PhilippineMunicipality::class, 'municipality_code', 'code');
    }

    public function barangay()
    {
        return $this->belongsTo(PhilippineBarangay::class, 'barangay_code', 'code');
    }
}