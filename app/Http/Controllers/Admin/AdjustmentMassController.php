<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdjustmentMass;
use App\Models\AdjustmentMassLines;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeDetailAllowance;
use App\Models\EmployeeSalary;
use App\Models\Leave;
use App\Models\LeaveDetail;
use App\Models\LeaveLog;
use App\Models\LeaveSetting;
use App\Models\Overtime;
use App\Models\Workingtime;
use App\Models\OvertimeSchemeList;
use App\Models\WorkingtimeDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\WorkGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class AdjustmentMassController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'adjustmentmass'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;
        $nid = $request->nid;
        $department = $request->department;
        $workgroup = $request->workgroup;
        $overtime = $request->overtime;
        $workingtime = $request->workingtime;
        $checkincheckout = $request->checkincheckout;
        $month = $request->month;
        $year = $request->year;
        $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;

        //Count Data
        $query = DB::table('attendances');
        $query->select('attendances.*', 'employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'departments.name as department_name', 'titles.name as title_name', 'work_groups.name as workgroup_name');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->where('attendances.status', '!=', -1);
        $query->whereNotNull('attendances.workingtime_id');
        if ($month) {
            $query->whereMonth('attendances.attendance_date', $month);
        }
        if ($year) {
            $query->whereYear('attendances.attendance_date', $year);
        }
        if ($from && $to) {
            $query->whereBetween('attendances.attendance_date', [$from, $to]);
        }
        if (!$from && $to) {
            $query->where('attendances.attendance_date', '<=', $to);
        }
        if ($employee_id) {
            $query->where('attendances.employee_id', $employee_id);
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($department) {
            $string = '';
            foreach ($department as $dept) {
                $string .= "departments.path like '%$dept%'";
                if (end($department) != $dept) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($overtime) {
            $query->where('attendances.adj_over_time', $overtime);
        }
        if ($checkincheckout == 'checkin') {
            $query->where("attendances.attendance_in", '!=', null)->where("attendances.attendance_out", null);
        }
        if ($checkincheckout == 'checkout') {
            $query->where("attendances.attendance_out", '!=', null)->where("attendances.attendance_in", null);
        }
        if ($checkincheckout == 'checkin_checkout') {
            $query->where("attendances.attendance_in", '!=', null)->where("attendances.attendance_out", '!=', null);
        }
        if ($checkincheckout == '!checkin_checkout') {
            $query->where("attendances.attendance_in", null)->where("attendances.attendance_out", null);
        }
        if ($workingtime) {
            $query->whereIn('attendances.workingtime_id', $workingtime);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('attendances');
        $query->select('attendances.*', 'employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'departments.name as department_name', 'titles.name as title_name', 'work_groups.name as workgroup_name');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->where('attendances.status', '!=', -1);
        $query->whereNotNull('attendances.workingtime_id');
        if ($month) {
            $query->whereMonth('attendances.attendance_date', $month);
        }
        if ($year) {
            $query->whereYear('attendances.attendance_date', $year);
        }
        if ($from && $to) {
            $query->whereBetween('attendances.attendance_date', [$from, $to]);
        }
        if (!$from && $to) {
            $query->where('attendances.attendance_date', '<=', $to);
        }
        if ($employee_id) {
            $query->where('attendances.employee_id', $employee_id);
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($department) {
            $string = '';
            foreach ($department as $dept) {
                $string .= "departments.path like '%$dept%'";
                if (end($department) != $dept) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($overtime) {
            $query->where('attendances.adj_over_time', $overtime);
        }
        if ($checkincheckout == 'checkin') {
            $query->where("attendances.attendance_in", '!=', null)->where("attendances.attendance_out", null);
        }
        if ($checkincheckout == 'checkout') {
            $query->where("attendances.attendance_out", '!=', null)->where("attendances.attendance_in", null);
        }
        if ($checkincheckout == 'checkin_checkout') {
            $query->where("attendances.attendance_in", '!=', null)->where("attendances.attendance_out", '!=', null);
        }
        if ($checkincheckout == '!checkin_checkout') {
            $query->where("attendances.attendance_in", null)->where("attendances.attendance_out", null);
        }
        if ($workingtime) {
            $query->whereIn('attendances.workingtime_id', $workingtime);
        }
        // $query->offset($start);
        // $query->limit($length);
        $query->orderBy($sort, $dir);
        $attendances = $query->get();

        $data = [];
        foreach ($attendances as $attendance) {
            $workingtime = WorkingtimeDetail::where('workingtime_id', '=', $attendance->workingtime_id)->where('day', '=', $attendance->day)->first();
            $attendance->no = ++$start;
            $attendance->attendance_in = $attendance->attendance_in ? changeDateFormat('H:i', $attendance->attendance_in) : null;
            $attendance->attendance_out = $attendance->attendance_out ? changeDateFormat('H:i', $attendance->attendance_out) : null;
            $attendance->start_time = $workingtime ? $workingtime->start : null;
            $attendance->finish_time = $workingtime ? $workingtime->finish : null;
            if ($attendance->attendance_in) {
                $attendance->diff_in = (new Carbon(changeDateFormat('H:i:s', $attendance->attendance_in)))->diff(new Carbon($workingtime->start))->format('%H:%I');
            }
            if ($attendance->attendance_out) {
                $attendance->diff_out = (new Carbon(changeDateFormat('H:i:s', $attendance->attendance_out)))->diff(new Carbon($workingtime->finish))->format('%H:%I');
            }
            $data[] = $attendance;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function index()
    { {
            $employees = Employee::all();
            $departments = Department::all();
            $workgroups = WorkGroup::all();
            $workingtimes = WorkingTime::all();
            return view('admin.adjustmentmass.index', compact('employees', 'departments', 'workgroups', 'workingtimes'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // return view('admin.adjustmentmass.create');
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
            'adjustment_workingtime'     => 'required',
            'adjustment_overtime'     => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $expD  = explode('/', $request->date);
        $Year  = $expD[2];
        $Month = $expD[1];
        $Day   = $expD[0];
        $Adj_date = $Year . '-' . $Month . '-' . $Day;
        $employeeall = explode(',', $request->employee_id);
        foreach ($employeeall as $key => $value) {
            $get_day = DB::table('attendances')
                ->where('employee_id', $value)
                ->where('attendance_date', '=', $Adj_date)
                ->where('attendance_in', '!=', null)
                ->where('attendance_out', '!=', null)
                ->first()->day;

            if ($get_day == 'off') {
            }
        }


        $get_data = DB::table('attendances')
            ->where('employee_id', 81)
            ->where('attendance_out', '!=', null)
            ->where('attendance_in', '!=', null)
            ->where('attendance_out', '!=', null)
            ->first();
        dd($get_data->attendance_date);
        if ($get_data != "off") {
        }
        $attendance                       = Attendance::find($id);
        $attendance->adj_working_time       = $request->notes;
        $attendance->adj_over_time           = $request->dailyreportdriver_id;
        $attendance->save();

        // $adjustmentmass = AdjustmentMassLines::insert($adjustmentmassline);
        if (!$adjustmentmass) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $adjustmentmass
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('adjustmentmass.index'),
        ], 200);
    }

    public function updatemass(Request $request)
    {
        if ($request->approve) {
            $update = $request->approve;
            DB::beginTransaction();
            foreach ($update as $val) {
                // print_r($request->working_time);
                // print_r($request->working_time);
                // print_r($request->working_time);
                // die();
                // dd($val['get_working_time']);
                $attendance                            = Attendance::find($val['id']);
                $attendance->adj_working_time          = $val['get_working_time'] + $request->working_time;
                $attendance->adj_over_time             = $val['get_over_time'] + $request->over_time;
                $attendance->save();
                if ($attendance) {
                    $overtime = calculateOvertime($attendance);
                    $allowance = calculateAllowance($attendance);
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'      => false,
                'message'     => 'Need to check at least one data'
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'message'     => 'Attendance was successfully Updated',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $adjustmentmass = AdjustmentMass::with("adjustmentmasslines")->find($id);
        if ($adjustmentmass) {
            $employee = [];
            foreach ($adjustmentmass->adjustmentmasslines as $adj) {
                $employee[] = $adj->employee->name;
            }
            return view('admin.adjustmentmass.edit', compact('adjustmentmass', 'employee'));
        } else {
            abort(404);
        }
    }
    public function multi(Request $request)
    {
        $data = $request->data;
        $adjustmentmass = AdjustmentMass::with("adjustmentmasslines")->where('id', $request->id)->first();

        $employee = [];
        foreach ($adjustmentmass->adjustmentmasslines as $adj) {
            $adj->name = $adj->employee->name;
            $employee[] = $adj;
        }

        return response()->json([
            'status'     => true,
            'results'     => $employee,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'adjustment_workingtime'     => 'required',
            'adjustment_overtime'     => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $adjustmentmass                 = AdjustmentMass::find($id);
        $adjustmentmass->date                        = date('Y-m-d', strtotime($request->date));
        $adjustmentmass->adjustment_workingtime    = $request->adjustment_workingtime;
        $adjustmentmass->adjustment_overtime       = $request->adjustment_overtime;
        $adjustmentmass->save();

        if ($adjustmentmass) {
            $employeeall = explode(',', $request->employee_id);
            $lists = AdjustmentMassLines::where('adjustmentmass_id', '=', $id);
            $lists->delete();
            foreach ($employeeall as $key => $value) {
                $adjustmentmassline[] = array(
                    'adjustmentmass_id'  => $adjustmentmass->id,
                    'employee_id'  => $value,
                    'created_at'    => Carbon::now()->toDateTimeString(),
                    'updated_at'    => Carbon::now()->toDateTimeString(),
                );
            }
            $lines = AdjustmentMassLines::insert($adjustmentmassline);
            if (!$lines) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $lines
                ], 400);
            }
        }
        if (!$adjustmentmass) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $adjustmentmass
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('adjustmentmass.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $list = AdjustmentMassLines::where('adjustmentmass_id', '=', $id);
            $list->delete();
            $deliveryorder = AdjustmentMass::find($id);
            $deliveryorder->delete();
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