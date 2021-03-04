<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    protected $guarded = [];

    public function uomcategory() {
        return $this->hasOne('App\Models\UomCategory', 'id', 'uomcategory_id');
    }
}
