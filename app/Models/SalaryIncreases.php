<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class SalaryIncreases extends Model
{
    use AutoNumberTrait;
    protected $guarded = [];

    public function site()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_id');
    }
    
    public function getAutoNumberOptions()
    {
        return [
            'code_system' => [
                'format' => $this->site->code . '/INV/?', // autonumber format. '?' will be replaced with the generated number.
                'length' => 7 // The number of digits in an autonumber
            ],
        ];
    }

    public function salaryIncreaseDetail()
    {
        return $this->hasMany(SalaryIncreaseDetail::class, 'salaryincrease_id');
    }

    public function scopeGetSalaryIncreaseDetail($query, $employee_id, $month, $year)
    {
        $query = $this->whereHas('salaryIncreaseDetail', function ($q) use ($employee_id)
        {
            $q->where('employee_id', $employee_id);
        })->whereMonth('date', $month)->whereYear('date', $year);
        return $query;
    }
}