<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    protected $guarded = [];
    public function account()
    {
        return $this->hasOne('App\Models\Account', 'id', 'account_id');
    }
    public function groupallowance()
    {
        return $this->hasOne('App\Models\GroupAllowance', 'id', 'group_allowance_id');
    }
    public function allowanceworkingtime()
    {
        return $this->hasMany('App\Models\WorkingtimeAllowance', 'allowance_id', 'id');
    }
}