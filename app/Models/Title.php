<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class Title extends Model
{
    use AutoNumberTrait;
    protected $guarded = [];

    public function parent() {
        return $this->hasOne('App\Models\Title', 'id', 'parent_id');
    }

    public function department(){
    	return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function site() {
        return $this->hasOne('App\Models\Site', 'id', 'site_id');
    }

    public function getAutoNumberOptions()
    {
        return [
            'code_system' => [
                'format' => $this->site->code. '-POST-?', // autonumber format. '?' will be replaced with the generated number.
                'length' => 7 // The number of digits in an autonumber
            ],
        ];
    }
}
