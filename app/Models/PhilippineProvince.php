<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhilippineProvince extends Model
{
    protected $table = 'ph_provinces';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['code', 'name', 'region_code'];

    public function municipalities()
    {
        return $this->hasMany(PhilippineMunicipality::class, 'province_code', 'code');
    }

    public function barangays()
    {
        return $this->hasMany(PhilippineBarangay::class, 'province_code', 'code');
    }
}