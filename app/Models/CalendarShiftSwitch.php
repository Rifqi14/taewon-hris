<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarShiftSwitch extends Model
{
    protected $guarded = [];
    public function exception() {
        return $this->belongsTo(CalendarException::class, 'id', 'calendar_exceptions_id');
    }
    public function workingtime()
    {
        return $this->belongsTo(Workingtime::class, 'id', 'workingtime_id');
    }
}