<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $guarded = [];
    public function province() {
        return $this->hasOne('App\Models\Province', 'id', 'province_id');
    }
}
