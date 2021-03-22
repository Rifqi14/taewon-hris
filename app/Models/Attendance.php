<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
    public function workingtime()
    {
        return $this->belongsTo('App\Models\Workingtime', 'workingtime_id', 'id');
    }
    public function leave()
    {
        return $this->hasMany('App\Models\LeaveLog', 'reference_id');
    }
    // public function schemalist()
    // {
    //     return $this->belongsTo('App\Models\OvertimeScheme', 'overtime_scheme_id');
    // }

    /**
     * Method to get attendance data by employee id in attendances table
     *
     * @param $query
     * @param int $employee_id
     * @return \Illuminate\Http\Response
     */
    public function scopeEmployeeAttendance($query, $employee_id)
    {
        return $query->where('employee_id', $employee_id);
    }

    /**
     * Method to get attendance data by date in attendances table
     *
     * @param $query
     * @param int $date
     * @return \Illuminate\Http\Response
     */
    public function scopeAttendanceDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }
}