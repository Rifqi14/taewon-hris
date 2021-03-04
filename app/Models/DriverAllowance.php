<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAllowance extends Model
{
    protected $guarded = [];
    public function driverlist()
    {
        return $this->hasMany('App\Models\DriverList', 'driver_allowance_id', 'id');
    }
}