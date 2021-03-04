<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverList extends Model
{
    protected $guarded = [];
    public function driverallowance()
    {
        return $this->belongsTo('App\Models\DriverAllowance', 'driver_allowance_id');
    }
}