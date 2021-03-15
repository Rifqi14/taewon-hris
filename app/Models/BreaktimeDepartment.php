<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreaktimeDepartment extends Model
{
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function breaktime()
    {
        return $this->belongsTo(BreakTime::class, 'breaktime_id', 'id');
    }
    
    /**
     * Scope to get department in breaktime by breaktime_id
     *
     * @param $query
     * @param int $breaktime_id
     * @return \Illuminate\Http\Response
     */
    public function scopeByBreaktime($query, $breaktime_id)
    {
        return $query->where('breaktime_id', $breaktime_id);
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