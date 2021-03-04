<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumeOil extends Model
{
    protected $guarded = [];
    protected $table = 'consume_oils';

    public function asset()
    {
        return $this->hasOne('App\Models\Asset', 'id', 'vehicle_id');
    }
    public function oil()
    {
        return $this->hasOne('App\Models\Asset', 'id', 'oil_id');
    }
}