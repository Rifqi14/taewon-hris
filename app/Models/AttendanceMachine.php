<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceMachine extends Model
{
    protected $guarded = [];

    /**
     * Define scope method to get data by device serial number from attendance_machines table
     *
     * @param $query
     * @param string $deviceSN
     * @return \Illuminate\Http\Response
     */
    public function scopeByDeviceSN($query, $deviceSN)
    {
        return $query->where('device_sn', 'like', "%$deviceSN%");
    }

    /**
     * Define scope method to get data by point name from attendance_machines table
     *
     * @param $query
     * @param string $pointName
     * @return \Illuminate\Http\Response
     */
    public function scopeByPointName($query, $pointName)
    {
        return $query->where('point_name', $pointName);
    }
}