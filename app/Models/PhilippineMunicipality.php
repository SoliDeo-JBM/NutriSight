<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhilippineMunicipality extends Model
{
    protected $table = 'ph_municipalities';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['code', 'name', 'province_code', 'region_code'];

    public function province()
    {
        return $this->belongsTo(PhilippineProvince::class, 'province_code', 'code');
    }

    public function barangays()
    {
        return $this->hasMany(PhilippineBarangay::class, 'municipality_code', 'code');
    }
}