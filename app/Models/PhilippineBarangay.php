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
}