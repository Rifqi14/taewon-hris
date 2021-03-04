<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CalendarException extends Model
{
    protected $guarded = [];
    public function calendar() {
        return $this->hasOne('App\Models\Calendar', 'id', 'calendar_id');
    }
}
