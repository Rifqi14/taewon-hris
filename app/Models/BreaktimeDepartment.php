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
}