<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeAllowance extends Model
{
    protected $guarded = [];

    public function allowance()
    {
        return $this->belongsTo(Allowance::class, 'allowance_id', 'id');
    }
    public function overtimescheme()
    {
        return $this->belongsTo(OvertimeScheme::class, 'overtime_scheme_id', 'id');
    }
    public function scopeByOvertimeScheme($query, $overtime_scheme_id)
    {
        return $query->where('overtime_scheme_id', $overtime_scheme_id);
    }
    public function scopeByAllowance($query, $allowance_id)
    {
        return $query->where('allowance_id', $allowance_id);
    }
}
