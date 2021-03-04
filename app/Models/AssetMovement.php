<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMovement extends Model
{
    protected $guarded = [];

    public function asset()
    {
        return $this->hasOne(Asset::class, 'id', 'asset_id');
    }
}
