<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakTimeLine extends Model
{
    protected $guarded = [];

    public function workgroup(){
        return $this->hasOne('App\Models\WorkGroup','id','workgroup_id');
    }
}