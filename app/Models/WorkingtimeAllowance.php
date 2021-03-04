<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingtimeAllowance extends Model
{
    protected $guarded = [];

    function allowance()
    {
        return $this->belongsTo('App\Models\Allowance', 'allowance_id', 'id');
    }
    function workingtime()
    {
        return $this->belongsTo('App\Models\Workingtime', 'workingtime_id', 'id');
    }
}