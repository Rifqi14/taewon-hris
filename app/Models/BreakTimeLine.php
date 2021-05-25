<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakTimeLine extends Model
{
    protected $guarded = [];

    public function breaktime()
    {
        return $this->belongsTo('App\Models\BreakTime', 'breaktime_id', 'id');
    }
    
    public function workgroup(){
        return $this->belongsTo('App\Models\WorkGroup');
    }
}