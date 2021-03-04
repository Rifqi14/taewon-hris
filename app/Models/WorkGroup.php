<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkGroup extends Model
{
    protected $guarded = [];
    public function workgroupmaster()
    {
        return $this->hasOne('App\Models\WorkgroupMaster', 'id', 'workgroupmaster_id');
    }

    public function breaktime(){
        return $this->belongsToMany('App\Models\BreakTime');
    }
}