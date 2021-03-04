<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustmentMass extends Model
{
    protected $guarded = [];
    public function adjustmentmasslines()
    {
        return $this->hasMany('App\Models\AdjustmentMassLines', 'adjustmentmass_id', 'id');
    }
}
