<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportDriver extends Model
{
    protected $guarded = [];

    public function driver()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'driver_id');
    }
    public function dailyreportdriverdetail()
    {
        return $this->hasMany('App\Models\DailyReportDriverDetail', 'daily_report_driver_id', 'id');
    }
    public function dailyreportdriveradditional()
    {
        return $this->hasMany('App\Models\DailyReportDriverAdditional', 'daily_report_driver_id', 'id');
    }
}