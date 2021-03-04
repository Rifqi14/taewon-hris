<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveDepartment extends Model
{
    protected $guarded = [];
    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }
    public function leavesetting()
    {
        return $this->belongsTo('App\Models\LeaveSetting', 'leavesetting_id');
    }
}