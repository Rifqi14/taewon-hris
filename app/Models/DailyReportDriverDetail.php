<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class DailyReportDriverDetail extends Model
{
    use AutoNumberTrait;
    protected $guarded = [];

    public function getAutoNumberOptions()
    {
        return [
            'reff_detail' => [
                'format' => 'DE-?', // autonumber format. '?' will be replaced with the generated number.
                'length' => 7 // The number of digits in an autonumber
            ],
        ];
    }
}
