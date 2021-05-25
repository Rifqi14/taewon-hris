<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    protected $guarded = [];

    // public function breaktimeline()
    // {
    //     return $this->hasMany('App\Models\BreakTimeLine', 'breaktime_id', 'id');
    // }
    public function breaktimeline()
    {
        return $this->hasMany('App\Models\BreakTimeLine', 'breaktime_id', 'id');
    }
    public function breaktimedepartment()
    {
        return $this->hasMany(BreaktimeDepartment::class, 'breaktime_id', 'id');
    }
}