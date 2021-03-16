<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenaltyConfigDetail extends Model
{
    protected $guarded = [];

    /**
     * Described belongs to (many-to-one) relationship with penalty_configs table
     *
     * @return \Illuminate\Http\Response
     */
    public function header()
    {
        return $this->belongsTo(PenaltyConfig::class, 'penalty_config_id', 'id');
    }

    /**
     * Described belongs to (many-to-one) relationship with allowances table
     *
     * @return \Illuminate\Http\Response
     */
    public function allowance()
    {
        return $this->belongsTo(Allowance::class, 'allowance_id', 'id');
    }
}