<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceReport extends Model
{
    protected $guarded = [];
    public function allowancedetail()
    {
        return $this->hasMany('App\Models\AllowanceReportDetail', 'allowance_report_id', 'id');
    }
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
}
