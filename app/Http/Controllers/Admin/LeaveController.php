<?php

namespace App\Http\Controllers\Admin;

use App\Models\Leave;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CalendarException;
use App\Models\LeaveDetail;
use App\Models\LeaveLog;
use App\Models\Employee;
use App\Models\LeaveSetting;
use App\Models\Workingtime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\This;

class LeaveController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'leave'));
    }

    public function selectemployee(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employees.id as employee_id',
            'employees.nid as nid',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.id as title_id',
            'titles.name as title_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        $query->where('employees.status', '=', 1);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employees.id as employee_id',
            'employees.nid as nid',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.id as title_id',
            'titles.name as title_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        $query->where('employees.status', '=', 1);
        $query->offset($start);
        $query->limit($length);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            $employee->first_month = Carbon::parse($employee->join_date)->addMonth()->format('Y-m-d');
            $employee->third_month = Carbon::parse($employee->join_date)->addMonths(3)->format('Y-m-d');
            $employee->sixth_month = Carbon::parse($employee->join_date)->addMonths(6)->format('Y-m-d');
            $employee->one_year = Carbon::parse($employee->join_date)->addYear()->format('Y-m-d');
            $data[] = $employee;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function selectleave(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $employee = $request->employee_id;
        $type = $request->type;

        //Count Data
        $query = LeaveDetail::leftJoin('leave_settings', 'leave_settings.id', '=', 'leave_details.leavesetting_id')->where('leave_settings.status', 1)->where('leave_details.employee_id', $employee);
        $query->where('from_balance', '<=', date('Y-m-d'));
        $query->where('to_balance', '>=', date('Y-m-d'));
        if ($name) {
            $query->where('leave_settings.leave_name', 'like', "'%$name%'");
        }
        if ($type == 'create') {
            $query->where('leave_settings.path', 'not like', '%Attendance%');
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = LeaveDetail::leftJoin('leave_settings', 'leave_settings.id', '=', 'leave_details.leavesetting_id')->where('leave_settings.status', 1)->where('leave_details.employee_id', $employee);
        $query->where('from_balance', '<=', date('Y-m-d'));
        $query->where('to_balance', '>=', date('Y-m-d'));
        if ($name) {
            $query->where('leave_settings.leave_name', 'like', "'%$name%'");
        }
        if ($type == 'create') {
            $query->where('leave_settings.path', 'not like', '%Attendance%');
        }
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
        $query->where('leaves.status', -1);
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
            DB::raw("(SELECT MIN(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as start_date"),
            DB::raw("(SELECT MAX(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as finish_date")
        );
        $query->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id');
        $query->leftJoin('employees', 'employees.id', '=', 'leaves.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id');
        $query->where('leaves.status', -1);
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
            $leave->min     = LeaveLog::where('leave_id', $leave->id)->min('date');
            $leave->max     = LeaveLog::where('leave_id', $leave->id)->max('date');
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


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $employees = Employee::all();
        $emp = DB::table('employees');
        $emp->select('employees.*');
        $emp->where('status', 1);
        $employees = $emp->get();
        return view('admin.leave.index', compact('employees'));
    }
    public function indexapproval()
    {
        return view('admin.leave.indexapproval');
    }

    public function getLatestId()
    {
        $read = Leave::max('id');
        return $read + 1;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.leave.create');
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
            'employee'      => 'required',
            'leave_type'    => 'required',
            // 'document'      => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        if ($request->total_days == 0) {
            return response()->json([
                'status'    => false,
                'message'   => 'Please select the date you are applying for leave.'
            ], 400);
        }
        $id = $this->getLatestId();
        DB::beginTransaction();
        $leave = Leave::create([
            'id'                    => $id,
            'employee_id'           => $request->employee_id,
            'leave_setting_id'      => $request->leave_type,
            'status'                => 0,
            // 'supporting_document'   => $file_name,
            'notes'                 => $request->notes,
            'duration'              => $request->total_days
        ]);
        if ($file = $request->file('document')) {
            $file_name = $id . "_" . time() . "_" . $file->getClientOriginalName();
            $src = 'media/leavedocument';
            if (!file_exists($src)) {
                mkdir($src, 0777, true);
            }
            $file->move($src, $file_name);
            $leave->suporting_documnet = $file_name;
            $leave->save();
        }
        if ($leave) {
            // $detail = LeaveDetail::where('leavesetting_id', '=', $request->leave_type)->where('employee_id', $request->employee_id)->where('from_balance', '<=', date('Y-m-d'))->where('to_balance', '>=', date('Y-m-d'))->first();
            // if ($detail) {
                
                if (isset($request->date)) {
                    $i = 1;
                    foreach ($request->date as $key => $date) {
                        $detail = LeaveDetail::where('leavesetting_id', '=', $request->leave_type)->where('employee_id', $request->employee_id)->where('from_balance', '<=', changeDateFormat('Y-m-d', $date))->where('to_balance', '>=', changeDateFormat('Y-m-d', $date))->first();
                        if ($detail) {
                            $detail->used_balance = $detail->used_balance + $i;
                            $over = abs($detail->remaining_balance - $i);
                            if ($detail->balance != -1) {
                                $detail->over_balance = (($detail->remaining_balance - $i) <= 0) ? $detail->over_balance + $over : 0;
                                $detail->remaining_balance = (($detail->remaining_balance - $i) <= 0) ? 0 : $detail->remaining_balance - $i;
                            }
                            $detail->save();
                            $leave_logs = LeaveLog::create([
                                'leave_id'  => $id,
                                'date'      => changeDateFormat('Y-m-d', $date),
                                'start'     => $request->time_start[$key],
                                'finish'    => $request->time_finish[$key],
                                'type'      => $request->type[$key]
                            ]);
                        }else{
                            DB::rollBack();
                            return response()->json([
                                'status'    => false,
                                'message'   => 'Leave balance for this employee and this date '.$date.' not found, please check leave setting.'
                            ], 400);
                        }
                    }
                }
            
        } else if (!$leave) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $leave
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('leave.index')
        ], 200);
    }

    public function quickupdate(Request $request)
    {
        $leavedetail = LeaveDetail::find($request->balance_leavesetting_id);
        DB::beginTransaction();
        if ($leavedetail) {
            $leavedetail->balance = $request->leave_balance;
            if ($leavedetail->balance != -1) {
                $remaining_balance = $request->leave_balance - $leavedetail->used_balance;
                if ($remaining_balance > 0) {
                    $leavedetail->remaining_balance = $remaining_balance;
                    $leavedetail->over_balance = 0;
                } else {
                    $leavedetail->remaining_balance = 0;
                    $leavedetail->over_balance = abs($remaining_balance);
                }
            } else {
                $leavedetail->remaining_balance = -1;
            }
            $leavedetail->save();
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success change data'
            ], 200);
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $leavedetail
            ], 400);
        }
    }

    /**
     * Create date to leave
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createdate(Request $request)
    {
        $start = dbDate($request->range_date_start);
        $finish = dbDate($request->range_date_finish);
        $routine = '1 day';
        $calendar = $request->calendar_id ? $request->calendar_id : 1;
        $dates = getDatesFromRange($start, $finish, 'Y-m-d', $routine);

        /**
         * Section to get employee calendar and to check exception date from employee want to leave
         */
        $calendarexc = CalendarException::where('calendar_id', '=', $calendar)->get();
        $exception_date = [];
        foreach ($calendarexc as $date) {
            $exception_date[] = $date->date_exception;
        }

        /**
         * Section to check the date already using or not
         */
        $already_use = DB::table('leave_logs');
        $already_use->select('leave_logs.*');
        $already_use->leftJoin('leaves', 'leaves.id', '=', 'leave_logs.leave_id');
        $already_use->where('leaves.employee_id', '=', $request->employee_id);
        $already_use->where('leaves.status', '<>', 2);
        $use = $already_use->get();
        $using_date = [];
        foreach ($use as $date) {
            $using_date[] = $date->date;
        }

        $data = $request->availableDates;
        foreach ($dates as $key => $date) {
            /* Remove Exception Date
            if (in_array($date, $exception_date)) {
                continue;
            } else if ($request->status == 'create' && in_array($date, $using_date)) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'You have applied for leave on the same date. Please check previous submissions.'
                ], 400);
            } else {
                $data[] = array(
                    'date'  => $date,
                    'start_time'    => $request->range_time_start,
                    'finish_time'   => $request->range_time_finish,
                    'type'          => $request->type
                );
            }
            */
            if ($request->status == 'create' && in_array($date, $using_date)) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'You have applied for leave on the same date. Please check previous submissions.'
                ], 400);
            } else {
                $data[] = array(
                    'date'  => $date,
                    'start_time'    => $request->range_time_start,
                    'finish_time'   => $request->range_time_finish,
                    'type'          => $request->type
                );
            }
        }
        return json_encode($data);
    }

    function removedate(Request $request)
    {
        $dates = $request->dates;

        foreach ($dates as $key => $date) {
            if ($date['date'] == $request->selected_date) {
                unset($dates[$key]);
            }
        }

        return json_encode(array_values($dates));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
        //
    }

    public function getList($id)
    {
        $logs = DB::table('leave_logs')->where('leave_logs.leave_id', '=', $id)->get();
        $data = [];
        foreach ($logs as $log) {
            $log->start_time = changeDateFormat('H:i', $log->start);
            $log->finish_time = changeDateFormat('H:i', $log->finish);
            $data[] = $log;
        }
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $leave = Leave::find($id);
        if ($leave) {
            return view('admin.leave.edit', compact('leave'));
        } else {
            abort(404);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee'      => 'required',
            'leave_type'    => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $leave = Leave::find($id);
        $leave->employee_id = $request->employee_id;
        $leave->status = 0;
        $leave->notes = $request->note;
        $leave->duration = $request->total_days;
        $leave->leave_setting_id = $request->leave_type;
        $leave->save();
        // Log History Leave
        $employee = Employee::where('id',$request->employee_id)->first();
        $user_id = Auth::user()->id;
        $LeaveSettings = LeaveSetting::where('id',$request->leave_type)->first();
        setrecordloghistory($user_id,$employee->id,$employee->department_id,"Leave Application","Edit",date("Y-m-d")." Leave Type",$LeaveSettings->leave_name);
        if ($request->file('document') != null) {
            $file = $request->file('document');
            $file_name = $id . "_" . time() . "_" . $file->getClientOriginalName();
            $src = 'media/leavedocument';
            if (!file_exists($src)) {
                mkdir($src, 0777, true);
            }
            $file->move($src, $file_name);
            $leave->supporting_document = $file_name;
            $leave->save();
        }
        if ($leave) {
            $detail = LeaveDetail::where('leavesetting_id', '=', $request->leave_type)->where('employee_id', $request->employee_id)->where('from_balance', '<=', date('Y-m-d'))->where('to_balance', '>=', date('Y-m-d'))->first();
            if ($detail) {
                $detail->used_balance = $detail->used_balance + $request->total_days;
                $over = abs($detail->remaining_balance - $request->total_days);
                if ($detail->balance != -1) {
                    $detail->over_balance = (($detail->remaining_balance - $request->total_days) <= 0) ? $detail->over_balance + $over : 0;
                    $detail->remaining_balance = (($detail->remaining_balance - $request->total_days) <= 0) ? 0 : $detail->remaining_balance - $request->total_days;
                }
                $detail->save();
                if (isset($request->date)) {
                    $lists = LeaveLog::where('leave_id', '=', $id);
                    $lists->delete();
                    foreach ($request->date as $key => $date) {
                        $existingBalance = LeaveDetail::where('leavesetting_id', '=', $request->leave_type)->where('employee_id', $request->employee_id)->where('from_balance', '<=', changeDateFormat('Y-m-d', $date))->where('to_balance', '>=', changeDateFormat('Y-m-d', $date))->first();
                        if ($existingBalance) {
                            $leave_logs = LeaveLog::create([
                                'leave_id'      => $id,
                                'date'          => changeDateFormat('Y-m-d', $date),
                                'start'         => $request->time_start[$key],
                                'finish'        => $request->time_finish[$key],
                                'type'          => $request->type[$key],
                                'reference_id'  => $request->reference_id[$key]
                            ]);
                            if ($request->reference_id[$key]) {
                                $leavesetting = LeaveSetting::find($request->leave_type);
                                $updateAttend = Attendance::find($request->reference_id[$key]);
                                if ($leavesetting->leave_name == 'Attendance') {
                                    $workingtime = Workingtime::where('working_time_type', 'Non-Shift')->first();
                                    $updateAttend->attendance_in = changeDateFormat('Y-m-d H:i:s', $date . ' ' . $request->time_start[$key]);
                                    $updateAttend->attendance_out = changeDateFormat('Y-m-d H:i:s', $date . ' ' . $request->time_finish[$key]);
                                    $updateAttend->workingtime_id = $workingtime->id;
                                    $updateAttend->status = 0;
                                } elseif ($leavesetting->leave_name == 'Switch Day Off') {
                                    $updateAttend->day = 'Off';
                                }
                                $updateAttend->save();
                            }
                            if (!$leave_logs) {
                                DB::rollBack();
                                return response()->json([
                                    'status'    => false,
                                    'message'   => $leave_logs
                                ], 400);
                            }
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'status'    => false,
                                'message'   => "Leave balance for this employee and this date " . changeDateFormat('Y-m-d', $date) . " not found, please check leave setting."
                            ], 400);
                        }
                    }
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Leave balance for this employee not found, please check leave setting.'
                ], 400);
            }
        } else if (!$leave) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $leave
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('leave.index')
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $leave = Leave::find($id);
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
}