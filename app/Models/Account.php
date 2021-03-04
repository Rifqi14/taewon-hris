<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];
    public function parent()
    {
        return $this->hasOne('App\Models\Account', 'id', 'parent_id');
    }
}