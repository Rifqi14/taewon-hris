<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class GroupAllowance extends Model
{
    use AutoNumberTrait;
    protected $guarded = [];
    public function site()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_id');
    }
    public function getAutoNumberOptions()
    {
        return [
            'code_system' => [
                'format' => $this->site->code . '-GRPAL-?', // autonumber format. '?' will be replaced with the generated number.
                'length' => 7 // The number of digits in an autonumber
            ],
        ];
    }
}
