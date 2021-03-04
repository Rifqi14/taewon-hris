<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkgroupMaster extends Model
{
    protected $guarded = [];
    public function workgroup()
    {
        return $this->hasOne('App\Models\Workgroup', 'id', 'workgroup_id');
    }
}