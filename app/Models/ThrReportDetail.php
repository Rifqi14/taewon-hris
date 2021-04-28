<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThrReportDetail extends Model
{
    protected $guarded = [];

    function thrreport()
    {
        return $this->belongsTo('App\Models\ThrReport', 'thr_report_id');
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
