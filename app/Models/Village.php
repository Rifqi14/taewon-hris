<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $guarded = [];

    public function district() {
        return $this->belongsTo('App\Models\District');
    }
}
