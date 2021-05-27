<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryReportDetail extends Model
{
    protected $guarded = [];

    function salaryreport()
    {
        return $this->belongsTo('App\Models\SalaryReport', 'salary_report_id');
    }
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
    public function groupAllowance()
    {
        return $this->belongsTo('App\Models\GroupAllowance', 'group_allowance_id', 'id');
    }
}