<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $guarded = [];
    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }
    public function attendance()
    {
        return $this->hasOne('App\Models\Attendance', 'id', 'attendance_id');
    }

    public function scopeAttendanceID($query, $attendance_id)
    {
        return $query->where("attendance_id", $attendance_id);
    }
    public function scopeEmployeeID($query, $employee_id)
    {
        return $query->where("employee_id", $employee_id);
    }
    public function scopeType($query, $type)
    {
        return $query->where("type", $type);
    }
}