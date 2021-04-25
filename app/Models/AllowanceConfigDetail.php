<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceConfigDetail extends Model
{
    protected $guarded = [];

    public function allowance()
    {
        return $this->belongsTo(Allowance::class, 'allowance_id', 'id');
    }
    public function allowance_config()
    {
        return $this->belongsTo(AllowanceConfig::class, 'allowance_config_id', 'id');
    }

    public function scopeByAllowanceConfig($query, $allowance_config_id)
    {
        return $query->where('allowance_config_id', $allowance_config_id);
    }
    public function scopeByAllowance($query, $allowance_id)
    {
        return $query->where('allowance_id', $allowance_id);
    }
}
