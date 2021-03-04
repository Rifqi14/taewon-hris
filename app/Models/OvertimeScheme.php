<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeScheme extends Model
{
    protected $guarded = [];
    public function overtimelist()
    {
        return $this->hasMany('App\Models\OvertimeSchemeList', 'overtime_scheme_id', 'id');
    }
}