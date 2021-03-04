<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveLog extends Model
{
    protected $guarded = [];
    public function leave()
    {
        return $this->belongsTo('App\Models\Leave', 'leave_id');
    }
    public function attendance()
    {
        return $this->belongsTo('App\Models\Attendance', 'reference_id');
    }
}