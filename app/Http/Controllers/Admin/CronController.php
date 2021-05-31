<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\WorkGroup;
use App\Models\WorkgroupAllowance;
use App\Models\EmployeeAllowance;
use App\Models\Allowance;
use App\Models\AlphaPenalty;
use App\Models\Config;
use App\Models\Attendance;
use App\Models\OvertimeScheme;
use App\Models\OvertimeSchemeList;
use App\Models\SalaryIncreases;
use App\Models\Overtime;
use App\Models\DocumentManagement;
use App\Models\EmployeeContract;
use App\Models\EmployeeSalary;
use App\Models\Leave;
use App\Models\LeaveDetail;
use App\Models\LeaveLog;
use App\Models\LeaveSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CronController extends Controller
{
    public function generateCreatedDateEmployeeSalary()
    {
        $employeeSalaries = EmployeeSalary::all();
        foreach ($employeeSalaries as $key => $value) {
            $update = EmployeeSalary::find($value->id);

            $update->created_date = Carbon::parse($update->created_at)->toDateString();
            $update->save();
        }
    }

    public function generateLeavePenalty()
    {
        $leaves = Leave::all();

        foreach ($leaves as $key => $leave) {
            $leaveSettings = LeaveSetting::find($leave->leave_setting_id);
            if ($leaveSettings && $leaveSettings->description == 0) {
                $leaveLogs = LeaveLog::where('leave_id', $leave->id)->get();
                foreach ($leaveLogs as $key => $log) {
                    $employeeBaseSalary = EmployeeSalary::where('employee_id', $leave->employee_id)->orderBy('updated_at', 'desc')->first();
                    $deletePenalty = AlphaPenalty::where('employee_id', $leave->employee_id)->where('date', $log->date)->first();
                    if ($deletePenalty) {
                        $deletePenalty->delete();
                    }
                    if ($employeeBaseSalary && $employeeBaseSalary->amount > 0) {
                        $alphaPenalty = AlphaPenalty::create([
                            'employee_id'       => $leave->employee_id,
                            'date'              => $log->date,
                            'salary'            => $employeeBaseSalary ? $employeeBaseSalary->amount : 0,
                            'penalty'           => $employeeBaseSalary ? $employeeBaseSalary->amount / 30 : 0,
                            'leave_id'          => $leave->id
                        ]);
                        if (!$alphaPenalty) {
                            return response()->json([
                                'status'    => false,
                                'message'   => $alphaPenalty
                            ], 400);
                        }
                    }
                }
            }
        }
    }

    public function generate_allowance()
    {
        $before_month = date('m', strtotime(date('Y-m-d') . "-1 month"));
        $before_year = date('Y', strtotime(date('Y-m-d') . "-1 month"));
        $after_month = date('m');
        $after_year = date('Y');
        $employeeallowances = EmployeeAllowance::where(['month' => $before_month, 'year' => $before_year])->get();
        foreach ($employeeallowances as $employeeallowance) {
            $cek = EmployeeAllowance::where(['employee_id' => $employeeallowance->employee_id, 'allowance_id' => $employeeallowance->allowance_id, 'month' => $after_month, 'year' => $after_year])->first();
            if (!$cek) {
                $allowance = Allowance::where('id', $employeeallowance->allowance_id)->first();
                if ($allowance->reccurance == 'monthly') {
                    EmployeeAllowance::create([
                        'employee_id' => $employeeallowance->employee_id,
                        'allowance_id' => $employeeallowance->allowance_id,
                        'value' => $employeeallowance->value,
                        'type' => $employeeallowance->type,
                        'month' => $after_month,
                        'year' => $after_year,
                        'status' => $employeeallowance->status,
                        'factor' => $employeeallowance->factor
                    ]);
                } else {
                    EmployeeAllowance::create([
                        'employee_id' => $employeeallowance->employee_id,
                        'allowance_id' => $employeeallowance->allowance_id,
                        'value' => $employeeallowance->value,
                        'type' => $employeeallowance->type,
                        'month' => $after_month,
                        'year' => $after_year,
                        'status' => $employeeallowance->status,
                        'factor' => 0
                    ]);
                }
            }
        }
        $employees = Employee::all();
        foreach ($employees as $employee) {
            $workgroup = WorkGroup::where('id', $employee->workgroup_id)->first();
            if ($workgroup) {
                $workgroupallowances = WorkgroupAllowance::where(['workgroup_id' => $employee->workgroup_id, 'is_default' => 1])->get();
                foreach ($workgroupallowances as $workgroupallowance) {
                    $cek = EmployeeAllowance::where(['employee_id' => $employee->id, 'allowance_id' => $workgroupallowance->allowance_id, 'month' => $after_month, 'year' => $after_year])->first();
                    if (!$cek) {
                        $allowance = Allowance::where('id', $workgroupallowance->allowance_id)->first();
                        if ($allowance->reccurance == 'monthly') {
                            EmployeeAllowance::create([
                                'employee_id' => $employee->id,
                                'allowance_id' => $workgroupallowance->allowance_id,
                                'value' => $workgroupallowance->value,
                                'type' => $workgroupallowance->type,
                                'month' => $after_month,
                                'year' => $after_year,
                                'status' => 1,
                                'factor' => 1
                            ]);
                        } else {
                            EmployeeAllowance::create([
                                'employee_id' => $employee->id,
                                'allowance_id' => $workgroupallowance->allowance_id,
                                'value' => $workgroupallowance->value,
                                'type' => $workgroupallowance->type,
                                'month' => $after_month,
                                'year' => $after_year,
                                'status' => 1,
                                'factor' => 0
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function copy_allowance(Request $request)
    {
        $before_month = $request->before_month;
        $before_year = $request->before_year;
        $after_month = $request->after_month;
        $after_year = $request->after_year;
        $employeeallowances = EmployeeAllowance::where(['month' => $after_month, 'year' => $after_year])->get();
        foreach ($employeeallowances as $employeeallowance) {
            $cek = EmployeeAllowance::where(['employee_id' => $employeeallowance->employee_id, 'allowance_id' => $employeeallowance->allowance_id, 'month' => $before_month, 'year' => $before_year])->first();
            if (!$cek) {
                $allowance = Allowance::where('id', $employeeallowance->allowance_id)->first();
                if ($allowance->reccurance == 'monthly') {
                    EmployeeAllowance::create([
                        'employee_id' => $employeeallowance->employee_id,
                        'allowance_id' => $employeeallowance->allowance_id,
                        'value' => $employeeallowance->value,
                        'type' => $employeeallowance->type,
                        'month' => $before_month,
                        'year' => $before_year,
                        'status' => 1,
                        'factor' => $employeeallowance->factor
                    ]);
                } else {
                    EmployeeAllowance::create([
                        'employee_id' => $employeeallowance->employee_id,
                        'allowance_id' => $employeeallowance->allowance_id,
                        'value' => $employeeallowance->value,
                        'type' => $employeeallowance->type,
                        'month' => $before_month,
                        'year' => $before_year,
                        'status' => 1,
                        'factor' => 0
                    ]);
                }
            }
        }
    }
    public function check_expired_document()
    {
        $now = Carbon::now()->format('Y-m-d');
        $documents   = DocumentManagement::whereRaw("DATE_PART('day',expired_date::timestamp - '$now'::timestamp) <= nilai")->where('expired_date', '>=', $now)->get();
        if ($documents) {
            dispatch(new \App\Jobs\SendEmail($documents));
        }
    }

    public function check_expired_contract()
    {
        $config = Config::where('option', 'expired_contract')->get()->first();
        $todata = date('Y-m-d', strtotime('+' . $config->value . " Days"));
        //Select Pagination
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.name as title_name',
            'work_groups.id as workgroup_id',
            'work_groups.name as workgroup_name',
            'employee_contracts.end_date'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->leftJoin('employee_contracts', 'employee_contracts.employee_id', '=', 'employees.id');
        $query->where('employee_contracts.end_date', '<=', $todata);
        $contracts = $query->get();
        if ($contracts) {
            dispatch(new \App\Jobs\ContractEmail($contracts));
        }
    }

    public function check_reset_leave()
    {
        $leavedetails = LeaveDetail::where('to_balance', '=', Carbon::now()->addMonth(2)->format('Y-m-d'))->get();
        // dd(Carbon::now()->addYear(1));
        // dd($leavedetails);
        foreach ($leavedetails as $key => $leavedetail) {
            $reset = LeaveDetail::create([
                'leavesetting_id'   => $leavedetail->leavesetting_id,
                'employee_id'       => $leavedetail->employee_id,
                'balance'           => $leavedetail->balance,
                'year_balance'      => Carbon::now()->addYear(1)->format('Y'),
                'from_balance'      => Carbon::parse($leavedetail->from_balance)->addYear(),
                'to_balance'        => Carbon::parse($leavedetail->to_balance)->addYear()
            ]);
            if ($reset->balance > 0) {
                $reset->remaining_balance = $leavedetail->over_balance > 0 && $leavedetail->over_balance < $leavedetail->balance ? $leavedetail->balance - $leavedetail->over_balance : $leavedetail->balance;
                $reset->used_balance = $leavedetail->over_balance > 0 && $leavedetail->over_balance < $leavedetail->balance ? $reset->balance - $reset->remaining_balance : 0;
                $reset->over_balance = $leavedetail->over_balance > $leavedetail->balance ? $leavedetail->over_balance - $leavedetail->balance : 0;
                $reset->save();
            } else {
                $reset->used_balance = 0;
                $reset->remaining_balance = -1;
                $reset->over_balance = 0;
                $reset->save();
            }
        }
    }

    public function generateOvertimeApprove($from, $to)
    {
        $attendances = Attendance::where('status', 1)->whereBetween('attendance_date', [$from, $to])->get();
        foreach ($attendances as $key => $attendance) {
            $employee = Employee::find($attendance->employee_id);
            $rules = OvertimeSchemeList::select('hour','amount')->where('overtime_scheme_id', $attendance->overtime_scheme_id)->groupBy('hour','amount')->orderBy('hour', 'asc')->get();
            $listdel = Overtime::where('employee_id','=', $attendance->employee_id)->where('date','=', $attendance->attendance_date);
            if ($listdel) {
                $listdel->delete();
            }
            if($employee->overtime == 'yes' && $rules){
                $i = 0;
                $overtimes = $attendance->adj_over_time;
                $length = count($rules);
                foreach ($rules as $key => $value) {
                    $date = Carbon::parse($attendance->attendance_date);
                    $employeeBaseSalary = EmployeeSalary::where('employee_id', $attendance->employee_id)->orderBy('updated_at', 'desc')->first();
                    if ($overtimes >= 0) {
                        $overtime = Overtime::create([
                            'employee_id'   => $attendance->employee_id,
                            'day'           => $attendance->day,
                            'scheme_rule'   => $value->hour,
                            'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                            'amount'        => $value->amount,
                            'basic_salary'  => $employeeBaseSalary ? $employeeBaseSalary->amount / 173 : 0,
                            'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date)
                        ]);
                    } else {
                        continue;
                    }
                    $overtime->final_salary = $overtime->hour * $overtime->amount * $overtime->basic_salary;
                    $overtime->save();
                    $i++;
                    $overtimes = $overtimes - 1;
                    if (!$overtime) {
                        DB::rollBack();
                        return response()->json([
                            'status'     => false,
                            'message'     => $overtime
                        ], 400);
                    }
                    // if ($attendance->attendance_date >= $sallary->max('date')) {
                    // }else{
                    //     $getSallary = EmployeeSalary::where('employee_id', '=', $attendance->employee_id)->orderBy('created_at', 'desc')->first();
                    //     if ($overtimes >= 0) {
                    //         $overtime = Overtime::create([
                    //             'employee_id'   => $attendance->employee_id,
                    //             'day'           => $attendance->day,
                    //             'scheme_rule'   => $value->hour,
                    //             'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                    //             'amount'        => $value->amount,
                    //             'basic_salary'  => $getSallary ? $getSallary->amount / 173 : 0,
                    //             'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date)
                    //         ]);
                    //     } else {
                    //         continue;
                    //     }
                    //     $overtime->final_salary = $overtime->hour * $overtime->amount * $overtime->basic_salary;
                    //     $overtime->save();
                    //     $i++;
                    //     $overtimes = $overtimes - 1;
                    //     if (!$overtime) {
                    //         DB::rollBack();
                    //         return response()->json([
                    //             'status'     => false,
                    //             'message'     => $overtime
                    //         ], 400);
                    //     }
                    // }
                }
            }
        }
    }

    public function generateOvertimeScheme()
    {
        $attendances = Attendance::all();
        foreach ($attendances as $key => $attendance) {
            $overtimeScheme = OvertimeSchemeList::where('recurrence_day', $attendance->day)->get();
            foreach ($overtimeScheme as $key => $oScheme) {
                $update = Attendance::find($attendance->id);
                $update->overtime_scheme_id = $oScheme->overtime_scheme_id;
                $update->save();
            }
        }
    }

    public function dailyAttendance(){
        DB::beginTransaction();
        $employees   = Employee::where('status', 1)->get();
        $readConfigs = Config::where('option', 'cut_off')->first();
        foreach ($employees as $employee) {
            $attendance_date = date('Y-m-d');
            $attendance = Attendance::where('employee_id', $employee->id)->where('attendance_date', '=', $attendance_date)->first();
            if($attendance){
                $exception_date = $this->employee_calendar($employee->id);
                $date = $new_date;   
            }
        }
    }
}