<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceDetail extends Model
{
    protected $guarded = [];

    public function header()
    {
        return $this->belongsTo(Allowance::class, 'allowance_id', 'id');
    }

    public function allowancedetail()
    {
        return $this->belongsTo(Allowance::class, 'allowancedetail_id', 'id');
    }
}
