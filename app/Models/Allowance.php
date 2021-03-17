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

    /**
     * Method to define many-to-many relationship with penalty_configs table through pivot table penalty_config_details
     *
     * @return \Illuminate\Http\Response
     */
    public function penaltyconfig()
    {
        return $this->belongsToMany(PenaltyConfig::class, 'penalty_config_details', 'penalty_config_id', 'allowance_id');
    }

    /**
     * Described has many (one-to-many) relationship with penalty_config_details table
     *
     * @return \Illuminate\Http\Response
     */
    public function detail()
    {
        return $this->hasMany(PenaltyConfigDetail::class, 'allowance_id', 'id');
    }
    public function parentdetail()
    {
        return $this->hasMany(AllowanceDetail::class, 'allowance_id', 'id');
    }
    public function allowance()
    {
        return $this->belongsToMany(Allowance::class, 'allowance_details', 'allowance_id', 'allowancedetail_id')->withTimestamps();
    }


}