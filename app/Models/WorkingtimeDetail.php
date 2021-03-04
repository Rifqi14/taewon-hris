<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingtimeDetail extends Model
{
    protected $guarded = [];

    public function workingtime()
    {
        return $this->belongsTo('App\Models\Workingtime', 'workingtime_id', 'id');
    }
}