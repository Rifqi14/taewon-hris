<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveSetting extends Model
{
    protected $guarded = [];
    public function parent()
    {
        return $this->hasOne('App\Models\LeaveSetting', 'id', 'parent_id');
    }
    public function leavedetail()
    {
        return $this->hasMany('App\Models\LeaveDetail', 'leavesetting_id');
    }
    public function leavedepartment()
    {
        return $this->hasMany('App\Models\LeaveDepartment', 'leave_setting_id');
    }
    public function leave()
    {
        return $this->hasMany('App\Models\Leave', 'leave_setting_id');
    }

    /**
     * Described relation many-to-many with penalty_configs table
     *
     * @return \Illuminate\Http\Response
     */
    public function penaltyconfig()
    {
        return $this->belongsToMany(PenaltyConfig::class, 'penalty_config_leave_settings', 'penalty_config_id', 'leave_setting_id');
    }
}