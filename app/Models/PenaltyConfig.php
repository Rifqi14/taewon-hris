<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltyConfig extends Model
{
    protected $guarded = [];

    /**
     * Described has many (one-to-many) relationship with penalty_config_details table
     *
     * @return \Illuminate\Http\Response
     */
    public function allowance()
    {
        return $this->belongsToMany(Allowance::class, 'penalty_config_details', 'penalty_config_id', 'allowance_id')->withTimestamps();
    }

    /**
     * Described belongs to (many-to-one) relationship with leave_settings table
     *
     * @return \Illuminate\Http\Response
     */
    public function leave()
    {
        return $this->belongsToMany(LeaveSetting::class, 'penalty_config_leave_settings', 'penalty_config_id', 'leave_setting_id')->withTimestamps();
    }

    /**
     * Described belongs to (many-to-one) relationship with work_groups table
     *
     * @return \Illuminate\Http\Response
     */
    public function workgroup()
    {
        return $this->belongsTo(WorkGroup::class, 'workgroup_id', 'id');
    }

    /**
     * Described has many (one-to-many) relationship with penalty_config_details table
     *
     * @return \Illuminate\Http\Response
     */
    public function detail()
    {
        return $this->hasMany(PenaltyConfigDetail::class, 'penalty_config_id', 'id');
    }

    /**
     * Method to give conditional where to query get by workgroup id
     *
     * @param $query
     * @param int $workgroupID
     * @return \Illuminate\Http\Response
     */
    public function scopeByWorkgroup($query, $workgroupID)
    {
        return $query->where('workgroup_id', $workgroupID);
    }
}