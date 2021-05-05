<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryDeduction extends Model
{
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'id');
    }
    public function title()
    {
        return $this->belongsTo('App\Models\Title', 'title_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }
    public function workgroup()
    {
        return $this->belongsTo('App\Models\WorkGroup', 'workgroup_id', 'id');
    }
}
