<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentShift extends Model
{
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function workingtime()
    {
        return $this->belongsTo(Workingtime::class, 'workingtime_id', 'id');
    }
}