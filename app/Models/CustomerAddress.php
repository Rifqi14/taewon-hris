<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $guarded = [];
    public function customer() {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }
    public function province() {
        return $this->hasOne('App\Models\Province', 'id', 'province_id');
    }
    public function district() {
        return $this->hasOne('App\Models\District', 'id', 'district_id');
    }
}
