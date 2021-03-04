<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAllowanceList extends Model
{
    protected $guarded = [];
    public function delivery_orders()
    {
        return $this->belongsTo('App\Models\DeliveryOrder', 'delivery_order_id');
    }
}