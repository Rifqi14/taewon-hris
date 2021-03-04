<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryIncreaseDetail extends Model
{
    protected $guarded = [];

    public function salaryincreases(){
        return $this->hasOne('App\Models\SalaryIncreases', 'id', 'salaryincreases_id');
    }
}
