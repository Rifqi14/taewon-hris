<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = [];
    public function parent(){
        return $this->hasOne('App\Models\Menu', 'id','parent_id');
    }
}
