<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceRule extends Model
{
    protected $guarded = [];
    public function allowance()
    {
        return $this->belongsTo('App\Models\Allowance', 'allowance_id');
    }
}