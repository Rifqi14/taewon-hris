<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDetailAllowance extends Model
{
    protected $table = 'employee_detailallowances';
    protected $guarded = [];

    public function allowance() {
        return $this->hasOne('App\Models\Allowance', 'id', 'allowance_id');
    }

    public function workingtime() {
        return $this->hasOne('App\Models\Workingtime', 'id', 'workingtime_id');
    }

    public function employee() {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }
}
