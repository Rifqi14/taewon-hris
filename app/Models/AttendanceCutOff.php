<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCutOff extends Model
{
    protected $guarded = [];

    public function attendancecutoffdepartment()
    {
        return $this->hasMany(AttendanceCutOffDepartment::class, 'attendance_cut_off_id', 'id');
    }
}
