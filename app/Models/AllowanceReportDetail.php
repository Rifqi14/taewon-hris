<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceReportDetail extends Model
{
    protected $guarded = [];

    function allowancereport()
    {
        return $this->belongsTo('App\Models\AllowanceReport', 'allowance_report_id');
    }
}
