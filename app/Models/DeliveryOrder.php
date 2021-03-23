<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $guarded = [];

    public function driver()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'driver_id');
    }
    public function deliveryorderdetail()
    {
        return $this->hasMany('App\Models\DeliveryOrderDetail', 'delivery_order_id', 'id');
    }
    public function partner()
    {
        return $this->hasOne('App\Models\Partner', 'id', 'partner_id');
    }
}