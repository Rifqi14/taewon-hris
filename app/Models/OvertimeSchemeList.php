<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeSchemeList extends Model
{
    protected $guarded = [];
    public function overtimescheme()
    {
        return $this->belongsTo('App\Models\OvertimeScheme', 'overtime_scheme_id');
    }
}