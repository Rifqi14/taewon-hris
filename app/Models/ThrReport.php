<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThrReport extends Model
{
    protected $guarded = [];
    public function thrdetail()
    {
        return $this->hasMany('App\Models\ThrReportDetail', 'thr_report_id', 'id');
    }
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
}
