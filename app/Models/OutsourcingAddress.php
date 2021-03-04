<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsourcingAddress extends Model
{
    protected $guarded=[];

    public function outsourcing() {
        return $this->hasOne('App\Models\Outsourcing', 'id', 'outsourcing_id');
    }

    public function principle() {
        return $this->hasOne('App\Models\Principle', 'id', 'principle_id');
    }

    public function province() {
        return $this->hasOne('App\Models\Province', 'id', 'province_id');
    }
    
    public function district() {
        return $this->hasOne('App\Models\District', 'id', 'district_id');
    }

    public function region() {
        return $this->hasOne('App\Models\Region', 'id', 'region_id');
    }

    public function getFullAddressAttribute()
    {
        return $this->address . '</br> ' .$this->district->name. ','. ' ' .$this->region->name. ','. ' ' 
        . $this->province->name . ','. ' ' . $this->kode_pos;
    }
}
