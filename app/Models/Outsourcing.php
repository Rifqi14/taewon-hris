<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outsourcing extends Model
{
    protected $guarded=[];

    public function addresses(){
        return $this->hasMany('App\Models\OutsourcingAddress');
    }

    public function workgroup(){
        return $this->hasOne('App\Models\WorkGroup', 'id','workgroup_id');
    }
}
