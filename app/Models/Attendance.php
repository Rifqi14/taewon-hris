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
    public function scopeEmployeeAttendance($query, $employee_id)
    {
        return $query->where('employee_id', $employee_id);
    }
    public function scopeAttendanceDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }
}