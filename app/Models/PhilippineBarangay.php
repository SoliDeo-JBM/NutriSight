<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhilippineBarangay extends Model
{
    protected $table = 'ph_barangays';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['code', 'name', 'municipality_code', 'province_code'];

    public function municipality()
    {
        return $this->belongsTo(PhilippineMunicipality::class, 'municipality_code', 'code');
    }

    public function province()
    {
        return $this->belongsTo(PhilippineProvince::class, 'province_code', 'code');
    }
}