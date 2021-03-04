<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintanance extends Model
{
    protected $guarded = [];
    protected $table = 'maintanances';

    public function asset()
    {
        return $this->hasOne('App\Models\Asset', 'id', 'vehicle_id');
    }
    
}
