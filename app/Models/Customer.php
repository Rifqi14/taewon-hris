<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function customergroup() {
        return $this->hasOne('App\Models\CustomerGroup', 'id', 'customergroup_id');
    }

}
