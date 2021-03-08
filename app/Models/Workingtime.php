<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workingtime extends Model
{
    protected $guarded = [];

    public function detail()
    {
        return $this->hasMany('App\Models\WorkingtimeDetail', 'workingtime_id', 'id');
    }
    public function workingtimeallowance()
    {
        return $this->hasMany('App\Models\WorkingtimeAllowance', 'workingtime_id', 'id');
    }
    public function workingtimedepartment()
    {
        return $this->hasMany(DepartmentShift::class, '');
    }
}