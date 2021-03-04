<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $table = 'employee_salarys';
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }
}