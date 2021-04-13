<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCutOffDepartment extends Model
{
    protected $guarded = [];
    
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function attendanceCutOff()
    {
        return $this->belongsTo(AttendanceCutOff::class, 'attendance_cut_off_id', 'id');
    }

    public function scopeByAttendanceCutOff($query, $attendance_cut_off_id)
    {
        return $query->where('attendance_cut_off_id', $attendance_cut_off_id);
    }

    /**
     * Scope to get all breaktime by department_id
     *
     * @param $query
     * @param int $department_id
     * @return \Illuminate\Http\Response
     */
    public function scopeByDepartment($query, $department_id)
    {
        return $query->where('department_id', $department_id);
    }
}
