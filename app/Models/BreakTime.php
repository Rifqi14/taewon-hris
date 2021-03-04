<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    protected $guarded = [];

    // public function workgroup()
    // {
    //     return $this->hasMany('App\Models\WorkGroup', 'id' ,'workgroup_id');
    // }
    public function breaktimeline()
    {
        return $this->hasMany('App\Models\BreakTimeLine', 'breaktime_id', 'id');
    }
}