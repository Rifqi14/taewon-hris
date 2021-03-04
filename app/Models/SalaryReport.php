<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryReport extends Model
{
    protected $guarded = [];
    public function salarydetail()
    {
        return $this->hasMany('App\Models\SalaryReportDetail', 'salary_report_id', 'id');
    }
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
}
