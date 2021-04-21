<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeschemeDepartment extends Model
{
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function overtimescheme()
    {
        return $this->belongsTo(OvertimeScheme::class, 'overtime_scheme_id', 'id');
    }
    public function scopeByOvertimeScheme($query, $overtime_scheme_id)
    {
        return $query->where('overtime_scheme_id', $overtime_scheme_id);
    }
    public function scopeByDepartment($query, $department_id)
    {
        return $query->where('department_id', $department_id);
    }
}
