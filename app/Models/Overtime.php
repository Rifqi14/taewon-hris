<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $guarded = [];
    public function employee_overtime()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }
}