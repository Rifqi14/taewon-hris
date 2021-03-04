<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkgroupAllowance extends Model
{
    protected $guarded = [];
    public function allowance() {
        return $this->hasOne('App\Models\Allowance', 'id', 'allowance_id');
    }
}
