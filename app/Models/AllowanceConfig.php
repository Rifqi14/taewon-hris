<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceConfig extends Model
{
    protected $guarded = [];

    public function allowance()
    {
        return $this->belongsTo(WorkgroupAllowance::class, 'allowance_id', 'id');
    }
    public function workgroup()
    {
        return $this->belongsTo(WorkgroupAllowance::class, 'workgroup_id', 'id');
    }
    public function allowanceconfigdetail()
    {
        return $this->hasMany(AllowanceConfigDetail::class, 'allowance_config_id', 'id');
    }
}
