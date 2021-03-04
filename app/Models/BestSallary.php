<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BestSallary extends Model
{
	protected $table = "bestsallarys";

	protected $guarded=[];
    public function region() {
        return $this->hasOne('App\Models\Region', 'id', 'region_id');
    }
}
