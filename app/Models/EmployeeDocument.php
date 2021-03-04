<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $guarded = [];
    
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}
