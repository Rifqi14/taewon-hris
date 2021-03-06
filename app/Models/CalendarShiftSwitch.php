<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarShiftSwitch extends Model
{
    protected $guarded = [];
    public function exception() {
        return $this->belongsTo(CalendarException::class, 'calendar_exceptions_id', 'id');
    }
    public function workingtime()
    {
        return $this->belongsTo(Workingtime::class, 'workingtime_id', 'id');
    }
}