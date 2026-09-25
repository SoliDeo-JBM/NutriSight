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
}