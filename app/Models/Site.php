<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $guarded = [];
    public function province() {
        return $this->hasOne('App\Models\Province', 'id', 'province_id');
    }
    public function region() {
        return $this->hasOne('App\Models\Region', 'id', 'region_id');
    }
    public function district() {
        return $this->hasOne('App\Models\District', 'id', 'district_id');
    }
}
