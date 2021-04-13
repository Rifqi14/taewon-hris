<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Alfa6661\AutoNumber\AutoNumberTrait;

class Department extends Model
{
    use AutoNumberTrait;
    protected $guarded = [];

    public function parent()
    {
        return $this->hasOne('App\Models\Department', 'id', 'parent_id');
    }

    public function site()
    {
        return $this->hasOne('App\Models\Site', 'id', 'site_id');
    }

    public function employee()
    {
        return $this->hasMany('App\Models\Employee', 'department_id');
    }

    public function leavedepartment()
    {
        return $this->hasMany('App\Models\LeaveDepartment', 'department_id');
    }

    public function departmentshift()
    {
        return $this->hasMany(DepartmentShift::class, 'department_id', 'id');
    }

    public function breaktimedepartment()
    {
        return $this->hasMany(BreaktimeDepartment::class, 'department_id', 'id');
    }
    public function attendancecutoffdepartment()
    {
        return $this->hasMany(AttendanceCutOffDepartment::class, 'department_id', 'id');
    }

    public function getAutoNumberOptions()
    {
        return [
            'code_system' => [
                'format' => $this->site->code . '-DEPT-?', // autonumber format. '?' will be replaced with the generated number.
                'length' => 7 // The number of digits in an autonumber
            ],
        ];
    }

    /**
     * Define scope method to get active department
     *
     * @param $query
     * @return \Illuminate\Http\Response
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Get active department, call this method instead Department::where('status', 1)->get();
     *
     * @return \Illuminate\Http\Response
     */
    static function getActive()
    {
        return self::active()->get();
    }
}