<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $guarded = [];
    public function parents() {
        return $this->hasOne('App\Models\ProductCategory', 'id', 'parent');
    }

    public function subcategories(){

        return $this->hasMany('App\Models\ProductCategory', 'parent');

    }
}
