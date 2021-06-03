<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AlphaPenalty;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\Config;
use App\Models\EmployeeSalary;
use App\Models\EmployeeAllowance;
use App\Models\PenaltyConfigDetail;
use App\Models\LeaveDetail;
use App\Models\LeaveLog;
use App\Models\LeaveSetting;
use App\Models\PenaltyConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class LeaveApprovalController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'leaveapproval'));
    }
    public function readapproval(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;
        $nik = $request->nik;
        $name = strtoupper(str_replace("'","''",$request->name));

        // Count Data
        $query = DB::table('leaves');
        $query->select(
            'leaves.*',
            'employees.name as employee_name',
            'employees.nid as employee_id',
            'titles.name as title_name',
            'departments.name as department_name',
            'leave_settings.leave_name as leave_type',
            DB::raw("(SELECT MIN(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as start_date"),
            DB::raw("(SELECT MAX(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as finish_date")
        );
        $query->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id');
        $query->leftJoin('employees', 'employees.id', '=', 'leaves.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id');
        $query->where('leaves.status', '=', 0);
        if ($from && $to) {
            $query->whereBetween('leave_logs.date', [$from, $to]);
        }
        if ($name) {
            $query->whereRaw("upper(employees.name) like '%$name%'");
        }
        if ($nik) {
            $query->whereRaw("employees.nid like '%$nik%'");
        }
        $query->groupBy('leaves.id', 'employees.name', 'employees.nid', 'titles.name', 'departments.name', 'leave_settings.leave_name');
        $recordsTotal = $query->get()->count();

        // Select Pagination
        $query = DB::table('leaves');
        $query->select(
            'leaves.*',
            'employees.name as employee_name',
            'employees.nid as employee_id',
            'titles.name as title_name',
            'departments.name as department_name',
            'leave_settings.leave_name as leave_type',
            'leaves.id as leave_id',
            DB::raw("(SELECT MIN(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as start_date"),
            DB::raw("(SELECT MAX(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as finish_date")
        );
        $query->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id');
        $query->leftJoin('employees', 'employees.id', '=', 'leaves.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id');
        $query->where('leaves.status', '=', 0);
        if ($from && $to) {
            $query->whereBetween('leave_logs.date', [$from, $to]);
        }
        if ($name) {
            $query->whereRaw("upper(employees.name) like '%$name%'");
        }
        if ($nik) {
            $query->whereRaw("employees.nid like '%$nik%'");
        }
        $query->offset($start);
        $query->limit($length);
        if ($sort != 'no') {
            $query->orderBy($sort, $dir);
        }
        $query->groupBy('leaves.id', 'employees.name', 'employees.nid', 'titles.name', 'departments.name', 'leave_settings.leave_name');
        $leaves = $query->get();

        $data = [];
        foreach ($leaves as $leave) {
            $leave->no      = ++$start;
            $leave->min     = LeaveLog::where('leave_id', $leave->leave_id)->min('date');
            $leave->max     = LeaveLog::where('leave_id', $leave->leave_id)->max('date');
            $leave->date    = changeDateFormat('d-m-Y', $leave->created_at);
            $data[]         = $leave;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }
    public function indexapproval()
    {
        // $employees = Employee::all();
        $emp = DB::table('employees');
        $emp->select('employees.*');
        $emp->where('status', 1);
        $employees = $emp->get();
        return view('admin.leaveapproval.indexapproval', compact('employees'));
    }
    public function editapproval($id)
    {
        $leave = Leave::find($id);
        if ($leave) {
            return view('admin.leaveapproval.editapproval', compact('leave'));
        } else {
            abort(404);
        }
    }
    public function updateapprove(Request $request, $id)
    {
        $leave = Leave::find($id);
        $leave->status = $request->status;
        $leave->save();
        if ($request->status == "1") {
            // dd($request->status == "1");
            $leaveSettingType = LeaveSetting::find($leave->leave_setting_id);
            $employee = Employee::find($leave->employee_id);
            $penalty_config = PenaltyConfig::leftJoin('penalty_config_leave_settings', 'penalty_config_leave_settings.penalty_config_id', '=','penalty_configs.id')
            ->where('penalty_config_leave_settings.leave_setting_id', $leaveSettingType->id)->where('workgroup_id', $employee->workgroup_id)->first();
            if (!$penalty_config) {
                return response()->json([
                    'status'     => false,
                    'message'   => "This employee don't have penalty config"
                ], 400);
            }
            // dd($penalty_config, $employee->workgroup_id);
            $leaveLogs = LeaveLog::where('leave_id', $leave->id)->get();
            if($leaveSettingType->description == 0){
                if($penalty_config){
                    // dd($penalty_config);
                    $allowance_id = [];
                    $penaltyconfigdetails = PenaltyConfigDetail::where('penalty_config_id', $penalty_config->id)->get();
                    foreach($penaltyconfigdetails as $penaltyconfigdetail){
                        $allowance_id[] = $penaltyconfigdetail->allowance_id;
                    }
                    if($penalty_config->type == 'BASIC')
                    {
                        foreach ($leaveLogs as $key => $log) {
                            $employeeBaseSalary = EmployeeSalary::where('employee_id', $employee->id)->where('created_date', '<', $log->date)->orderBy('created_date', 'desc')->first();
                            if (!$employeeBaseSalary) {
                                return response()->json([
                                    'status'     => false,
                                    'message'   => "Could not find basic salary for this employee"
                                ], 400);
                            }
                            $deletePenalty = AlphaPenalty::where('employee_id', $leave->employee_id)->where('date', $log->date)->first();
                            if ($deletePenalty) {
                                $deletePenalty->delete();
                            }
    
                            $readConfigs = Config::where('option', 'cut_off')->first();
                            $cut_off = $readConfigs->value;
                            if (date('d', strtotime($log->date)) > $cut_off) {
                                $month = date('m', strtotime($log->date));
                                $year = date('Y', strtotime($log->date));
                                $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                                $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                            } else {
                                $month =  date('m', strtotime($log->date));
                                $year =  date('Y', strtotime($log->date));
                            }
    
                            if ($employeeBaseSalary && $employeeBaseSalary->amount > 0) {
                                $alphaPenalty = AlphaPenalty::create([
                                    'employee_id'       => $leave->employee_id,
                                    'date'              => $log->date,
                                    'salary'            => $employeeBaseSalary ? $employeeBaseSalary->amount : 0,
                                    'penalty'           => $employeeBaseSalary ? $employeeBaseSalary->amount / 30 : 0,
                                    'leave_id'          => $id,
                                    'year'              => $year,
                                    'month'             => $month
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
                    if($penalty_config->type == 'ALLOWANCE')
                    {
                        foreach ($leaveLogs as $key => $log) {
                            $employeeBaseSalary = EmployeeSalary::where('employee_id', $employee->id)->where('created_date', '<', $log->date)->orderBy('created_date', 'desc')->first();
                            if (!$employeeBaseSalary) {
                                return response()->json([
                                    'status'     => false,
                                    'message'   => "Could not find basic salary for this employee"
                                ], 400);
                            }
                            
                            $readConfigs = Config::where('option', 'cut_off')->first();
                            $cut_off = $readConfigs->value;
                            if (date('d', strtotime($log->date)) > $cut_off) {
                                $month = date('m', strtotime($log->date));
                                $year = date('Y', strtotime($log->date));
                                $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                                $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                            } else {
                                $month =  date('m', strtotime($log->date));
                                $year =  date('Y', strtotime($log->date));
                            }
                            $employeeAllowance = EmployeeAllowance::select(DB::raw('coalesce(sum(value::integer),0) as total'))
                            ->where('employee_id', $employee->id)->where('month', $month)->where('year', $year)->whereIn('allowance_id',$allowance_id)->first();
                            // dd($employeeAllowance);
                            $deletePenalty = AlphaPenalty::where('employee_id', $leave->employee_id)->where('date', $log->date)->first();
                            if ($deletePenalty) {
                                $deletePenalty->delete();
                            }
    
                            if ($employeeBaseSalary && $employeeBaseSalary->amount > 0) {
                                $alphaPenalty = AlphaPenalty::create([
                                    'employee_id'       => $leave->employee_id,
                                    'date'              => $log->date,
                                    'salary'            => $employeeAllowance ? $employeeAllowance->total : 0,
                                    'penalty'           => $employeeAllowance ? $employeeAllowance->total / 30 : 0,
                                    'leave_id'          => $id,
                                    'year'              => $year,
                                    'month'             => $month
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
                    if($penalty_config->type == 'BASIC & ALLOWANCE') {
                        foreach ($leaveLogs as $key => $log) {
                            $employeeBaseSalary = EmployeeSalary::where('employee_id', $employee->id)->where('created_date', '<', $log->date)->orderBy('created_date', 'desc')->first();
                            if (!$employeeBaseSalary) {
                                return response()->json([
                                    'status'     => false,
                                    'message'   => "Could not find basic salary for this employee"
                                ], 400);
                            }
    
                            $readConfigs = Config::where('option', 'cut_off')->first();
                            $cut_off = $readConfigs->value;
                            if (date('d', strtotime($log->date)) > $cut_off) {
                                $month = date('m', strtotime($log->date));
                                $year = date('Y', strtotime($log->date));
                                $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                                $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                            } else {
                                $month =  date('m', strtotime($log->date));
                                $year =  date('Y', strtotime($log->date));
                            }
                            $employeeAllowance = EmployeeAllowance::select(DB::raw('coalesce(sum(value::integer),0) as total'))->where('employee_id', $employee->id)
                            ->where('month', $month)->where('year', $year)->whereIn('allowance_id', $allowance_id)->first();
                            dd($allowance_id);
                            $deletePenalty = AlphaPenalty::where('employee_id', $leave->employee_id)->where('date', $log->date)->first();
                            if ($deletePenalty) {
                                $deletePenalty->delete();
                            }
    
    
                            if ($employeeBaseSalary && $employeeBaseSalary->amount > 0) {
                                $alphaPenalty = AlphaPenalty::create([
                                    'employee_id'       => $leave->employee_id,
                                    'date'              => $log->date,
                                    'salary'            => $employeeAllowance ? $employeeAllowance->total + $employeeBaseSalary->amount : 0,
                                    'penalty'           => $employeeAllowance ? ($employeeAllowance->total + $employeeBaseSalary->amount) / 30 : 0,
                                    'leave_id'          => $id,
                                    'year'              => $year,
                                    'month'             => $month
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
        }
        if ($leave->status == 2) {
            $detail = LeaveDetail::where('employee_id', $leave->employee_id)->where('leavesetting_id', $leave->leave_setting_id)->first();
            $detail->used_balance = $detail->used_balance - $leave->duration;
            if ($detail->remaining_balance != -1) {
                $detail->remaining_balance = $detail->remaining_balance > 0 && $detail->over_balance == 0 ? $detail->remaining_balance + $leave->duration : 0;
                $detail->over_balance = $detail->remaining_balance == 0 && $detail->over_balance > 0 ? $detail->over_balance - $leave->duration : 0;
            }
            $detail->save();
        }
        if (!$leave) {
            return response()->json([
                'status'    => false,
                'message'   => $leave
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('leaveapproval.indexapproval')
        ], 200);
    }
}