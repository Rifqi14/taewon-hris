<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarningLetter extends Model
{
    protected $guarded = [];
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id');
    }
}
