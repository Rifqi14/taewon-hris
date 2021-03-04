<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetSerial extends Model
{
    protected $guarded = [];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function asset()
    {
        return $this->hasOne(Asset::class, 'id', 'asset_id');
    }
}
