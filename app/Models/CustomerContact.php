<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
    protected $guarded = [];
    public function customer() {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }
}
