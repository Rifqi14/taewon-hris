<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    protected $guarded = [];

    public function driver()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'driver_id');
    }
    public function reimbursementcalculation()
    {
        return $this->hasMany('App\Models\ReimbursementCalculation', 'reimbursement_id', 'id');
    }
    public function reimbursementallowance()
    {
        return $this->hasMany('App\Models\ReimbursementAllowance', 'reimbursement_id', 'id');
    }
    public function dailyreportdriver()
    {
        return $this->hasOne('App\Models\DailyReportDriver', 'id', 'daily_report_driver_id');
    }
}