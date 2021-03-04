<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    public function place()
    {
        return $this->hasOne('App\Models\Region', 'id', 'place_of_birth');
    }

    public function region()
    {
        return $this->hasOne('App\Models\Region', 'id', 'region_id');
    }

    public function title()
    {
        return $this->belongsTo('App\Models\Title', 'title_id');
    }

    public function grade()
    {
        return $this->hasOne('App\Models\Grade', 'id', 'grade_id');
    }

    public function province()
    {
        return $this->hasOne('App\Models\Province', 'id', 'province_id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }

    public function workingtime()
    {
        return $this->hasOne('App\Models\Workingtime', 'id', 'working_time');
    }

    public function workgroup()
    {
        return $this->hasOne('App\Models\WorkGroup', 'id', 'workgroup_id');
    }

    public function calendar()
    {
        return $this->hasOne('App\Models\Calendar', 'id', 'calendar_id');
    }
    public function outsourcing()
    {
        return $this->hasOne('App\Models\Outsourcing', 'id', 'outsourcing_id');
    }
    public function salary()
    {
        return $this->hasMany('App\Models\SalaryReport', 'employee_id');
    }
    public function employee_contracts()
    {
        return $this->hasMany('App\Models\EmployeeContract', 'employee_id');
    }
    public function employee_salary()
    {
        return $this->hasMany('App\Models\EmployeeSalary', 'employee_id');
    }
    public function employee_allowance()
    {
        return $this->hasMany('App\Models\EmployeeAllowance', 'employee_id');
    }
    public function leave()
    {
        return $this->hasMany('App\Models\Leave', 'employee_id');
    }

    public function scopeGetActiveEmployees($query)
    {
        return $query->where('status', 1);
    }
}