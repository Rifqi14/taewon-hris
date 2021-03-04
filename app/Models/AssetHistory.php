<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetHistory extends Model
{
    protected $guarded = [];
    public function asset()
    {
        return $this->hasOne('App\Models\Asset', 'id', 'asset_id');
    }
}