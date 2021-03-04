<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function uom() {
        return $this->hasOne('App\Models\Uom', 'id', 'uom_id');
    }

    public function productcategory() {
        return $this->hasOne('App\Models\ProductCategory', 'id', 'productcategory_id');
    }
}
