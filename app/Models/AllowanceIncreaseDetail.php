<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceIncreaseDetail extends Model
{
    protected $guarded = [];

    public function allowanceIncrease(){
        return $this->hasOne('App\Models\AllowanceIncrease', 'id', 'allowance_increase_id');
    }
    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }
}
