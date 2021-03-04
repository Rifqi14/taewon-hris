<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = [];

    public function assetcategory()
    {
        return $this->hasOne(AssetCategory::class, 'id', 'assetcategory_id');
    }
    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
    public function drivers()
    {
        return $this->hasOne(Employee::class, 'id', 'driver_id');
    }

    public function assetserials()
    {
        return $this->hasMany(AssetSerial::class, 'asset_id', 'id');
    }
    public function assethistories()
    {
        return $this->hasMany(AssetHistory::class, 'asset_id', 'id');
    }
}
