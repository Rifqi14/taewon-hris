<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintananceItem extends Model
{
    protected $guarded = [];
    protected $table = 'maintanance_items';

    public function maintanance()
    {
        return $this->hasOne('App\Models\Maintanance', 'id', 'maintanance_id');
    }
}
