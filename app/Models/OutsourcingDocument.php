<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutsourcingDocument extends Model
{
    protected $guarded=[];

    public function outsourcing() {
        return $this->hasOne('App\Models\Outsourcing', 'id', 'outsourcing_id');
    }
}
