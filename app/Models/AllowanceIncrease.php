<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceIncrease extends Model
{
    protected $guarded = [];

    public function allowance()
    {
        return $this->belongsTo(Allowance::class, 'allowance_id', 'id');
    }
}
