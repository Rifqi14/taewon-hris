<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllowance extends Model
{
    protected $guarded = [];
    public function allowance()
    {
        return $this->belongsTo('App\Models\Allowance', 'allowance_id');
    }
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }
}