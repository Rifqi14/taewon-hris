<?php

namespace App\Models\AttendanceMachine;

use Illuminate\Database\Eloquent\Model;

class AttTransaction extends Model
{
    /**
     * Define the table associated with model from attendance_machine connection
     *
     * @var string
     */
    protected $connection = 'attendance_machine';

    /**
     * Defince the table associated with model
     *
     * @var string
     */
    protected $table = 'att_transaction';
}