<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $guarded = [];
    public function region() {
        return $this->hasOne('App\Models\Region', 'id', 'region_id');
    }
}
