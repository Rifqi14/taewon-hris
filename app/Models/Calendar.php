<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $guarded = [];
    public function exception()
    {
        return $this->hasMany('App\Models\CalendarException', 'calendar_id', 'id');
    }
}