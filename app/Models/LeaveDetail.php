<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveDetail extends Model
{
    protected $guarded = [];
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }
    public function leavesetting()
    {
        return $this->belongsTo('App\Models\LeaveSetting', 'leavesetting_id');
    }
}