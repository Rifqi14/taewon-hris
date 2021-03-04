<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $guarded = [];

    public function parent()
    {
        return $this->hasOne(AssetCategory::class, 'id', 'parent_id');
    }

    public function subcategories()
    {
        return $this->hasMany(AssetCategory::class, 'parent_id');
    }

}
