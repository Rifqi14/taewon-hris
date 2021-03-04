<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class Position extends Model
{
    use AutoNumberTrait;
    protected $guarded=[];

    public function department(){
    	return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function parent(){
    	return $this->hasOne('App\Models\Position', 'id', 'paret_id');
    }

    public function getAutoNumberOptions()
    {
        return [
            'code_system' => [
                'format' => '000-POST-?', // autonumber format. '?' will be replaced with the generated number.
                'length' => 7 // The number of digits in an autonumber
            ],
        ];
    }
}
