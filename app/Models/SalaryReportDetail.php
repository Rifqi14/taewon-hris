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
}