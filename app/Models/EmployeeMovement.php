<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeMovement extends Model
{
    protected $guarded = [];


    public function Title()
    {
        return $this->belongsTo('App\Models\Title');
    }

    public function Employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
