<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PphReport extends Model
{
    protected $guarded = [];
    public function pphdetail()
    {
        return $this->hasMany('App\Models\PphReportDetail', 'pph_report_id', 'id');
    }
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
}
