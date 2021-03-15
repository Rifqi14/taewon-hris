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

    /**
     * Scope to get department in shift by workingtime_id
     *
     * @param $query
     * @param int $workingtime_id
     * @return \Illuminate\Http\Response
     */
    public function scopeByShift($query, $workingtime_id)
    {
        return $query->where('workingtime_id', $workingtime_id);
    }

    /**
     * Scope to get all shift by department_id
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