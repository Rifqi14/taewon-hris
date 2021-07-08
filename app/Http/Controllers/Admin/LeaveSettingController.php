<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeaveSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveDepartment;
use App\Models\LeaveDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class LeaveSettingController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'leavesetting'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $employee = $request->employee_id;

        //Count Data
        $query = DB::table('leave_settings');
        $query->select('leave_settings.*');
        $query->where('status', '=', 1);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('leave_settings');
        $query->select('leave_settings.*');
        $query->where('status', '=', 1);
        $query->offset($start);
        $query->limit($length);
        $leaves = $query->get();

        $data = [];
        foreach ($leaves as $leave) {
            $leave->no = ++$start;
            $data[] = $leave;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $type = strtoupper($request->leave_type);
        $balance = $request->balance;
        $description = $request->description;

        // Count Data
        $query = LeaveSetting::with('parent');
        if ($balance) {
            $query->where('balance', $balance);
        }
        if ($description) {
            $query->where('description', $description);
        }
        $query->whereRaw("upper(leave_name) like '%$type%'");
        $recordsTotal = $query->count();

        // Select Pagination
        $query = LeaveSetting::with('parent');
        if ($balance) {
            $query->where('balance', $balance);
        }
        if ($description) {
            $query->where('description', $description);
        }
        $query->whereRaw("upper(leave_name) like '%$type%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('path', $dir);
        $balances = $query->get();

        $data = [];
        foreach ($balances as $balance) {
            $balance->no    = ++$start;
            $data[]         = $balance;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.leavesetting.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.leavesetting.create');
    }

    public function getLatestId()
    {
        $read = LeaveSetting::max('id');
        return $read + 1;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leave_name'    => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $id = $this->getLatestId();

        DB::beginTransaction();
        $switchDayOffCheck = LeaveSetting::whereRaw("upper(leave_name) like '%SWITCH DAY OFF%'")->first();
        if ($switchDayOffCheck && strtoupper($request->leave_name) == strtoupper(@$switchDayOffCheck->leave_name)) {
            DB::rollBack();
            return response()->json([
                'status'        => false,
                'message'       => 'Leave switch day off already exist. You can\' create leave switch day off more then one.'
            ], 400);
        }
        // dd($request->coordinate);
        $leavesetting = LeaveSetting::create([
            'id'            => $id,
            'leave_name'    => $request->leave_name,
            'parent_id'     => $request->parent_id ? $request->parent_id : 0,
            'balance'       => $request->unlimited ? -1 : $request->balance,
            'reset_time'    => $request->reset_time,
            'use_time'      => $request->use_time,
            'label_color'   => $request->label_color,
            'note'          => $request->note,
            'status'        => $request->status,
            'description'   => $request->description,
            'coordinate' 	=> implode(',', $request->coordinate)
        ]);
        if ($request->reset_time == 'specificdate') {
            $leavesetting->specific_date = $request->date;
        }
        $leavesetting->path = implode(' -> ', $this->createPath($leavesetting->id, []));
        $leavesetting->level = count($this->createLevel($leavesetting->id, []));
        $leavesetting->save();
        if ($leavesetting) {
            if (isset($request->all)) {
                $departments = Department::all();
                $department_ins = [];
                foreach ($departments as $key => $department) {
                    $department_ins[] = array(
                        'leave_setting_id'  => $leavesetting->id,
                        'department_id'     => $department->id,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString()
                    );
                }
                $leavedepartment = LeaveDepartment::insert($department_ins);
                if ($leavedepartment) {
                    foreach ($departments as $key => $dept_emp) {
                        $employees = Employee::where('department_id', $dept_emp->id)->get();
                        foreach ($employees as $key => $employee) {
                            $leavedetail = LeaveDetail::create([
                                'leavesetting_id'   => $leavesetting->id,
                                'employee_id'       => $employee->id,
                                'balance'           => $request->unlimited ? -1 : $request->balance,
                                'used_balance'      => 0,
                                'remaining_balance' => $request->unlimited ? -1 : $request->balance,
                                'over_balance'      => 0,
                                'year_balance'      => date('Y')
                            ]);
                            if ($leavesetting->reset_time == 'beginningyear') {
                                $leavedetail->from_balance = Carbon::now()->startOfYear();
                                $leavedetail->to_balance = Carbon::now()->endOfYear();
                                $leavedetail->save();
                            } elseif ($leavesetting->reset_time == 'specificdate') {
                                $dateMonthArray = explode('-', $leavesetting->specific_date);
                                $date = $dateMonthArray[2];
                                $month = $dateMonthArray[1];
                                $leavedetail->from_balance = Carbon::createFromDate(date('Y'), $month, $date);
                                $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                $leavedetail->to_balance = Carbon::parse($next_year)->subDay();
                                $leavedetail->save();
                            } else {
                                $dateMonthArray = explode('-', $employee->join_date);
                                $date = $dateMonthArray[2];
                                $month = $dateMonthArray[1];
                                $leavedetail->from_balance = Carbon::createFromDate(date('Y'), $month, $date);
                                $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                $leavedetail->to_balance = Carbon::parse($next_year)->subDay();
                                $leavedetail->save();
                            }
                            if (!$leavedetail) {
                                DB::rollBack();
                                return response()->json([
                                    'status'        => false,
                                    'message'       => $leavedetail
                                ], 400);
                            }
                        }
                    }
                } else {
                    DB::rollBack();
                    return response()->json([
                        'status'        => false,
                        'message'       => $leavedepartment
                    ], 400);
                }
            } else {
                $dept_choose = $request->department ?  explode(',', $request->department) : null;
                foreach ($dept_choose as $key => $department) {
                    $leavedepartment = LeaveDepartment::create([
                        'leave_setting_id'  => $leavesetting->id,
                        'department_id'     => $department
                    ]);
                    if ($leavedepartment) {
                        $employees = Employee::where('department_id', $department)->get();
                        foreach ($employees as $key => $employee) {
                            $leavedetail = LeaveDetail::create([
                                'leavesetting_id'   => $leavesetting->id,
                                'employee_id'       => $employee->id,
                                'balance'           => $request->unlimited ? -1 : $request->balance,
                                'used_balance'      => 0,
                                'remaining_balance' => $request->unlimited ? -1 : $request->balance,
                                'over_balance'      => 0,
                                'year_balance'      => date('Y')
                            ]);
                            if ($leavesetting->reset_time == 'beginningyear') {
                                $leavedetail->from_balance = Carbon::now()->startOfYear();
                                $leavedetail->to_balance = Carbon::now()->endOfYear();
                                $leavedetail->save();
                            } elseif ($leavesetting->reset_time == 'specificdate') {
                                $dateMonthArray = explode('-', $leavesetting->specific_date);
                                $date = $dateMonthArray[2];
                                $month = $dateMonthArray[1];
                                $leavedetail->from_balance = Carbon::createFromDate(date('Y'), $month, $date);
                                $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                $leavedetail->to_balance = Carbon::parse($next_year)->subDay();
                                $leavedetail->save();
                            } else {
                                $dateMonthArray = explode('-', $employee->join_date);
                                $date = $dateMonthArray[2];
                                $month = $dateMonthArray[1];
                                $leavedetail->from_balance = Carbon::createFromDate(date('Y'), $month, $date);
                                $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                $leavedetail->to_balance = Carbon::parse($next_year)->subDay();
                                $leavedetail->save();
                            }
                            if (!$leavedetail) {
                                DB::rollBack();
                                return response()->json([
                                    'status'        => false,
                                    'message'       => $leavedetail
                                ], 400);
                            }
                        }
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'status'        => false,
                            'message'       => $leavedepartment
                        ], 400);
                    }
                }
            }
        } elseif (!$leavesetting) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $leavesetting
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'        => true,
            'results'       => route('leavesetting.index')
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LeaveSetting  $leaveSetting
     * @return \Illuminate\Http\Response
     */
    public function show(LeaveSetting $leaveSetting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LeaveSetting  $leaveSetting
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $leavesetting = LeaveSetting::with('parent')->with('leavedetail')->with('leavedepartment')->find($id);
        $department = Department::all()->count();

        // return response()->json($coordinate);
        if ($leavesetting) {
            $leavesetting->coordinate = explode(',', $leavesetting->coordinate);
            return view('admin.leavesetting.edit', compact('leavesetting', 'department'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LeaveSetting  $leaveSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'leave_name'  => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $switchDayOffCheck = LeaveSetting::whereRaw("upper(leave_name) like '%SWITCH DAY OFF%'")->where('id', '<>', $id)->first();
        if ($switchDayOffCheck && strtoupper($request->leave_name) == strtoupper($switchDayOffCheck->leave_name)) {
            DB::rollBack();
            return response()->json([
                'status'        => false,
                'message'       => 'Leave switch day off already exist. You can\' create leave switch day off more then one.'
            ], 400);
        }
        $leavesetting = LeaveSetting::find($id);
        $leavesetting->parent_id = $request->parent_id ? $request->parent_id : 0;
        $leavesetting->leave_name = $request->leave_name;
        $leavesetting->balance = $request->unlimited ? -1 : $request->balance;
        $leavesetting->reset_time = $request->reset_time;
        $leavesetting->specific_date = ($request->reset_time != 'specificdate') ? null : dbDate($request->date);
        $leavesetting->use_time = $request->use_time;
        $leavesetting->label_color = $request->label_color;
        $leavesetting->note = $request->note;
        $leavesetting->status = $request->status;
        $leavesetting->description = $request->description;
        $leavesetting->save();
        $leavesetting->path = implode(' -> ', $this->createPath($id, []));
        $leavesetting->level = count($this->createLevel($id, []));
        $leavesetting->coordinate = implode(',', $request->coordinate);
        $leavesetting->save();
        $this->updatePath($id);
        $this->updateLevel($id);
        if ($leavesetting) {
            if (isset($request->all)) {
                $departments = Department::all();
                $leave_dept_now = LeaveDepartment::where('leave_setting_id', $id);
                // $leave_sett_now = LeaveDetail::where('leavesetting_id', $id)->where('from_balance', '<=', date('Y-m-d'))->where('to_balance', '>=', date('Y-m-d'));
                // $leave_sett_now = LeaveDetail::where('leavesetting_id', $id)->where('to_balance', '>=',date('Y').'-12-30');
                // $leave_sett_now->delete();
                $leave_dept_now->delete();
                foreach ($departments as $key => $value) {
                    $leave_department = LeaveDepartment::create([
                        'leave_setting_id'  => $id,
                        'department_id'     => $value->id,
                    ]);
                    $employees = Employee::where('department_id', $value->id)->get();
                    foreach ($employees as $key => $employee) {
                        $checkResetSubYear = LeaveDetail::where('year_balance', Carbon::now()->subYear(1)->format('Y'))->where('employee_id', $employee->id)->where('leavesetting_id', $leavesetting->id)->first();
                        if (!$checkResetSubYear) {
                            $subYear = LeaveDetail::create([
                                'leavesetting_id'   => $leavesetting->id,
                                'employee_id'       => $employee->id,
                                'balance'           => $request->unlimited ? -1 : $request->balance,
                                'used_balance'      => 0,
                                'remaining_balance' => $request->unlimited ? -1 : $request->balance,
                                'over_balance'      => 0,
                                'year_balance'      => Carbon::now()->subYear(1)->format('Y'),
                            ]);
                        }
                        $checkResetNow = LeaveDetail::where('year_balance', Carbon::now()->format('Y'))->where('employee_id', $employee->id)->where('leavesetting_id', $leavesetting->id)->first();
                        if (!$checkResetNow) {
                            $leavedetail = LeaveDetail::create([
                                'leavesetting_id'   => $leavesetting->id,
                                'employee_id'       => $employee->id,
                                'balance'           => $request->unlimited ? -1 : $request->balance,
                                'used_balance'      => 0,
                                'remaining_balance' => $request->unlimited ? -1 : $request->balance,
                                'over_balance'      => 0,
                                'year_balance'      => date('Y')
                            ]);
                        }
                        $checkResetSAddYear = LeaveDetail::where('year_balance', Carbon::now()->addYear(1)->format('Y'))->where('employee_id', $employee->id)->where('leavesetting_id', $leavesetting->id)->first();
                        if (!$checkResetSAddYear) {
                            $addYear = LeaveDetail::create([
                                'leavesetting_id'   => $leavesetting->id,
                                'employee_id'       => $employee->id,
                                'balance'           => $request->unlimited ? -1 : $request->balance,
                                'used_balance'      => 0,
                                'remaining_balance' => $request->unlimited ? -1 : $request->balance,
                                'over_balance'      => 0,
                                'year_balance'      => Carbon::now()->addYear(1)->format('Y'),
                            ]);
                        }
                        if ($leavesetting->reset_time == 'beginningyear') {
                            if (!$checkResetSubYear) {
                                $subYear->from_balance = Carbon::now()->subYear(1)->startOfYear();
                                $subYear->to_balance = Carbon::now()->subYear(1)->endOfYear();
                                $subYear->save();
                            }
                            if (!$checkResetNow) {
                                $leavedetail->from_balance = Carbon::now()->startOfYear();
                                $leavedetail->to_balance = Carbon::now()->endOfYear();
                                $leavedetail->save();
                            }
                            if (!$checkResetSAddYear) {
                                $addYear->from_balance = Carbon::now()->addYear(1)->startOfYear();
                                $addYear->to_balance = Carbon::now()->addYear(1)->endOfYear();
                                $addYear->save();
                            }
                        } elseif ($leavesetting->reset_time == 'specificdate') {
                            $dateMonthArray = explode('-', $leavesetting->specific_date);
                            $date = $dateMonthArray[2];
                            $month = $dateMonthArray[1];
                            if (!$checkResetSubYear && !$checkResetNow) {
                                $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                $subYear->from_balance = Carbon::createFromDate(date('Y'), $month, $date)->subYear(1);
                                $subYear->to_balance = Carbon::parse($next_year)->subDay()->subYear(1);
                                $subYear->save();
                            }
                            if (!$checkResetNow) {
                                $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                $leavedetail->from_balance = Carbon::createFromDate(date('Y'), $month, $date);
                                $leavedetail->to_balance = Carbon::parse($next_year)->subDay();
                                $leavedetail->save();
                            }
                            if (!$checkResetSAddYear && !$checkResetNow) {
                                $addYear->from_balance = Carbon::createFromDate(date('Y'), $month, $date)->addYear(1);
                                $addYear->to_balance = Carbon::parse($next_year)->subDay()->addYear(1);
                                $addYear->save();
                            }
                        } else {
                            $dateMonthArray = explode('-', $employee->join_date);
                            $date = $dateMonthArray[2];
                            $month = $dateMonthArray[1];
                            if (!$checkResetSubYear && !$checkResetNow) {
                                $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                $subYear->from_balance = Carbon::createFromDate(date('Y'), $month, $date)->subYear(1);
                                $subYear->to_balance = Carbon::parse($next_year)->subDay()->subYear(1);
                                $subYear->save();
                            }
                            if (!$checkResetNow) {
                                $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                $leavedetail->from_balance = Carbon::createFromDate(date('Y'), $month, $date);
                                $leavedetail->to_balance = Carbon::parse($next_year)->subDay();
                                $leavedetail->save();
                            }
                            if (!$checkResetSAddYear && !$checkResetNow) {
                                $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                $addYear->from_balance = Carbon::createFromDate(date('Y'), $month, $date)->addYear(1);
                                $addYear->to_balance = Carbon::parse($next_year)->subDay()->addYear(1);
                                $addYear->save();
                            }
                        }
                    }
                }
            } else {
                $dept_choose = $request->department ?  explode(',', $request->department) : null;
                $leave_dept_now = LeaveDepartment::where('leave_setting_id', $id);
                // $leave_sett_now = LeaveDetail::where('leavesetting_id', $id)->where('year_balance', date('Y'));
                // $leave_sett_now->delete();
                $leave_dept_now->delete();
                foreach ($dept_choose as $key => $department) {
                    $leavedepartment = LeaveDepartment::create([
                        'leave_setting_id'  => $leavesetting->id,
                        'department_id'     => $department
                    ]);
                    if ($leavedepartment) {
                        $employees = Employee::where('department_id', $department)->get();
                        foreach ($employees as $key => $employee) {
                            $checkResetSubYear = LeaveDetail::where('year_balance', Carbon::now()->subYear(1)->format('Y'))->where('employee_id', $employee->id)->where('leavesetting_id', $leavesetting->id)->first();
                            if (!$checkResetSubYear) {
                                $subYear = LeaveDetail::create([
                                    'leavesetting_id'   => $leavesetting->id,
                                    'employee_id'       => $employee->id,
                                    'balance'           => $request->unlimited ? -1 : $request->balance,
                                    'used_balance'      => 0,
                                    'remaining_balance' => $request->unlimited ? -1 : $request->balance,
                                    'over_balance'      => 0,
                                    'year_balance'      => Carbon::now()->subYear(1)->format('Y'),
                                ]);
                            }
                            $checkResetNow = LeaveDetail::where('year_balance', Carbon::now()->format('Y'))->where('employee_id', $employee->id)->where('leavesetting_id', $leavesetting->id)->first();
                            if (!$checkResetNow) {
                                $leavedetail = LeaveDetail::create([
                                    'leavesetting_id'   => $leavesetting->id,
                                    'employee_id'       => $employee->id,
                                    'balance'           => $request->unlimited ? -1 : $request->balance,
                                    'used_balance'      => 0,
                                    'remaining_balance' => $request->unlimited ? -1 : $request->balance,
                                    'over_balance'      => 0,
                                    'year_balance'      => date('Y')
                                ]);
                            }
                            $checkResetSAddYear = LeaveDetail::where('year_balance', Carbon::now()->addYear(1)->format('Y'))->where('employee_id', $employee->id)->where('leavesetting_id', $leavesetting->id)->first();
                            if (!$checkResetSAddYear) {
                                $addYear = LeaveDetail::create([
                                    'leavesetting_id'   => $leavesetting->id,
                                    'employee_id'       => $employee->id,
                                    'balance'           => $request->unlimited ? -1 : $request->balance,
                                    'used_balance'      => 0,
                                    'remaining_balance' => $request->unlimited ? -1 : $request->balance,
                                    'over_balance'      => 0,
                                    'year_balance'      => Carbon::now()->addYear(1)->format('Y'),
                                ]);
                            }
                            if ($leavesetting->reset_time == 'beginningyear') {
                                if (!$checkResetSubYear) {
                                    $subYear->from_balance = Carbon::now()->subYear(1)->startOfYear();
                                    $subYear->to_balance = Carbon::now()->subYear(1)->endOfYear();
                                    $subYear->save();
                                }
                                if (!$checkResetNow) {
                                    $leavedetail->from_balance = Carbon::now()->startOfYear();
                                    $leavedetail->to_balance = Carbon::now()->endOfYear();
                                    $leavedetail->save();
                                }
                                if (!$checkResetSAddYear) {
                                    $addYear->from_balance = Carbon::now()->addYear(1)->startOfYear();
                                    $addYear->to_balance = Carbon::now()->addYear(1)->endOfYear();
                                    $addYear->save();
                                }
                            } elseif ($leavesetting->reset_time == 'specificdate') {
                                $dateMonthArray = explode('-', $leavesetting->specific_date);
                                $date = $dateMonthArray[2];
                                $month = $dateMonthArray[1];
                                if (!$checkResetSubYear && !$checkResetNow) {
                                    $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                    $subYear->from_balance = Carbon::createFromDate(date('Y'), $month, $date)->subYear(1);
                                    $subYear->to_balance = Carbon::parse($next_year)->subDay()->subYear(1);
                                    $subYear->save();
                                }
                                if (!$checkResetNow) {
                                    $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                    $leavedetail->from_balance = Carbon::createFromDate(date('Y'), $month, $date);
                                    $leavedetail->to_balance = Carbon::parse($next_year)->subDay();
                                    $leavedetail->save();
                                }
                                if (!$checkResetSAddYear && !$checkResetNow) {
                                    $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                    $addYear->from_balance = Carbon::createFromDate(date('Y'), $month, $date)->addYear(1);
                                    $addYear->to_balance = Carbon::parse($next_year)->subDay()->addYear(1);
                                    $addYear->save();
                                }
                            } else {
                                $dateMonthArray = explode('-', $employee->join_date);
                                $date = $dateMonthArray[2];
                                $month = $dateMonthArray[1];
                                if (!$checkResetSubYear && !$checkResetNow) {
                                    $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                    $subYear->from_balance = Carbon::createFromDate(date('Y'), $month, $date)->subYear(1);
                                    $subYear->to_balance = Carbon::parse($next_year)->subDay()->subYear(1);
                                    $subYear->save();
                                }
                                if (!$checkResetNow) {
                                    $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                    $leavedetail->from_balance = Carbon::createFromDate(date('Y'), $month, $date);
                                    $leavedetail->to_balance = Carbon::parse($next_year)->subDay();
                                    $leavedetail->save();
                                }
                                if (!$checkResetSAddYear && !$checkResetNow) {
                                    $next_year = Carbon::parse($leavedetail->from_balance)->addYear();
                                    $addYear->from_balance = Carbon::createFromDate(date('Y'), $month, $date)->addYear(1);
                                    $addYear->to_balance = Carbon::parse($next_year)->subDay()->addYear(1);
                                    $addYear->save();
                                }
                            }
                            if (!$leavedetail) {
                                DB::rollBack();
                                return response()->json([
                                    'status'        => false,
                                    'message'       => $leavedetail
                                ], 400);
                            }
                        }
                    } else {
                        DB::rollBack();
                        return response()->json([
                            'status'        => false,
                            'message'       => $leavedepartment
                        ], 400);
                    }
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $leavesetting
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('leavesetting.index')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LeaveSetting  $leaveSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $leave = LeaveSetting::find($id);
            $leave->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'    => false,
                'message'   => 'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }

    public function createPath($id, $path)
    {
        $leavesetting = LeaveSetting::find($id);
        array_unshift($path, $leavesetting->leave_name);
        if ($leavesetting->parent_id) {
            return $this->createPath($leavesetting->parent_id, $path);
        }
        return $path;
    }

    public function updatePath($id)
    {
        $leavesettings = LeaveSetting::where('parent_id', $id)->get();
        foreach ($leavesettings as $leavesetting) {
            $leavesetting->path = implode(' -> ', $this->createPath($leavesetting->id, []));
            $leavesetting->save();
            $this->updatePath($leavesetting->id);
        }
    }

    public function createLevel($id, $level)
    {
        $leavesetting = LeaveSetting::find($id);
        array_unshift($level, $leavesetting->leave_name);
        if ($leavesetting->parent_id) {
            return $this->createLevel($leavesetting->parent_id, $level);
        }
        return $level;
    }

    public function updateLevel($id)
    {
        $leavesettings = LeaveSetting::where('parent_id', $id)->get();
        foreach ($leavesettings as $leavesetting) {
            $leavesetting->level = count($this->createLevel($leavesetting->id, []));
            $leavesetting->save();
            $this->updateLevel($leavesetting->id);
        }
    }
}