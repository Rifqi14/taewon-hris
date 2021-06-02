<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Config;
use App\Models\AttendanceLog;
use App\Models\Department;
use App\Models\SalaryIncreases;
use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeDetailAllowance;
use App\Models\EmployeeSalary;
use App\Models\Leave;
use App\Models\OvertimeScheme;
use App\Models\LeaveDetail;
use App\Models\LeaveLog;
use App\Models\LeaveSetting;
use App\Models\Overtime;
use App\Models\Workingtime;
use App\Models\OvertimeSchemeList;
use App\Models\OvertimeschemeDepartment;
use App\Models\WorkGroup;
use App\Models\WorkingtimeDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;

class AttendanceApprovalController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'attendanceapproval'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;

        //Count Data
        $query = DB::table('attendances');
        $query->select('attendances.*', 'employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'workingtimes.start_time as start_time', 'workingtimes.finish_time as finish_time');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('attendances');
        $query->select('attendances.*', 'employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'workingtimes.start_time as start_time', 'workingtimes.finish_time as finish_time');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->offset($start);
        $query->limit($length);
        $logs = $query->get();

        $data = [];
        foreach ($logs as $log) {
            $log->no = ++$start;
            $data[] = $log;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function selectworkingtime(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;

        //Count Data
        $query = DB::table('workingtimes');
        $query->select('workingtimes.*');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('workingtimes');
        $query->select('workingtimes.*');
        $query->offset($start);
        $query->limit($length);
        $workingtimes = $query->get();

        $data = [];
        foreach ($workingtimes as $workingtime) {
            $workingtime->no = ++$start;
            $data[] = $workingtime;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function selectscheme(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;

        //Count Data
        $query = DB::table('overtime_schemes');
        $query->select('overtime_schemes.*');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('overtime_schemes');
        $query->select('overtime_schemes.*');
        $query->offset($start);
        $query->limit($length);
        $overtime_schemes = $query->get();

        $data = [];
        foreach ($overtime_schemes as $overtime_schemes) {
            $overtime_schemes->no = ++$start;
            $data[] = $overtime_schemes;
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
        $employee_id = strtoupper(str_replace("'","''",$request->employee_id));
        $nid = $request->nid;
        $department = $request->department;
        $workgroup = $request->workgroup;
        $workingtime = $request->workingtime;
        $overtime = $request->overtime;
        $checkincheckout = $request->checkincheckout;
        $month = $request->month;
        $year = $request->year;
        $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;

        //Count Data
        $query = DB::table('attendances');
        $query->select('attendances.*','employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'departments.name as department_name', 'titles.name as title_name', 'work_groups.name as workgroup_name','overtime_scheme.scheme_name as scheme_name');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->leftJoin('overtime_schemes', 'overtime_schemes.id', '=', 'attendances.overtime_scheme_id');
        $query->where('attendances.status', 0);
        $query->where('employees.status', 1);
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
        if ($overtime) {
            $query->where('attendances.adj_over_time', $overtime);
        }
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
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
        if ($workingtime) {
            $query->whereIn('attendances.workingtime_id', $workingtime);
        }
        
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('attendances');
        $query->select('attendances.*','employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'departments.name as department_name', 'titles.name as title_name', 'work_groups.name as workgroup_name','overtime_schemes.scheme_name as scheme_name');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->leftJoin('overtime_schemes', 'overtime_schemes.id', '=', 'attendances.overtime_scheme_id');
        $query->where('attendances.status', 0);
        $query->where('employees.status', 1);
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
        if ($overtime) {
            $query->where('attendances.adj_over_time', $overtime);
        }
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
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
        if ($workingtime) {
            $query->whereIn('attendances.workingtime_id', $workingtime);
        }
        
        // $query->where('employees.status', 1);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $attendances = $query->get();

        $data = [];
        foreach ($attendances as $attendance) {
            $workingtime = WorkingtimeDetail::where('workingtime_id', '=', $attendance->workingtime_id)->where('day', '=', $attendance->day)->first();
            $attendance->no = ++$start;
            $attendance->time_in = $attendance->attendance_in;
            $attendance->time_out = $attendance->attendance_out;
            $attendance->date_in = $attendance->attendance_in ? changeDateFormat('Y-m-d', $attendance->attendance_in) : null;
            $attendance->date_out = $attendance->attendance_out ? changeDateFormat('Y-m-d', $attendance->attendance_out) : null;
            $attendance->attendance_in = $attendance->attendance_in ? changeDateFormat('H:i', $attendance->attendance_in) : null;
            $attendance->attendance_out = $attendance->attendance_out ? changeDateFormat('H:i', $attendance->attendance_out) : null;
            $attendance->start_time = $workingtime ? $workingtime->start : null;
            $attendance->finish_time = $workingtime ? $workingtime->finish : null;
            if ($attendance->attendance_in) {
                $attendance->diff_in = (new Carbon(changeDateFormat('H:i', $attendance->time_in)))->diff(new Carbon(changeDateFormat('H:i', $workingtime->start)))->format('%H:%I');
            }
            if ($attendance->attendance_out) {
                if ($workingtime->start > $workingtime->finish) {
                    $attendance->diff_out = (new Carbon($attendance->time_out))->diff(new Carbon($attendance->date_out . ' ' . $workingtime->finish))->format('%H:%I');
                } else {
                    if ($attendance->date_in < $attendance->date_out) {
                        $attendance->diff_out = (new Carbon($attendance->time_out))->diff(new Carbon($attendance->date_in . ' ' . $workingtime->finish))->format('%H:%I');
                    } else {
                        $attendance->diff_out = (new Carbon($attendance->time_out))->diff(new Carbon($attendance->date_out . ' ' . $workingtime->finish))->format('%H:%I');
                    }
                }
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

    public function attendance_log(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $id = $request->id;

        //Count Data
        $query = DB::table('attendance_logs');
        $query->select('attendance_logs.*');
        $query->where('attendance_logs.attendance_id', '=', $id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('attendance_logs');
        $query->select('attendance_logs.*');
        $query->where('attendance_logs.attendance_id', '=', $id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $logs = $query->get();

        $data = [];
        foreach ($logs as $log) {
            $log->no = ++$start;
            $data[] = $log;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
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
        // $departments = Department::all();
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->orderBy('path','asc');
        $departments = $query->get();
        $workgroups = WorkGroup::all();
        $workingtimes = Workingtime::all();
        return view('admin.attendanceapproval.index', compact('employees', 'departments', 'workgroups', 'workingtimes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.attendanceapproval.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        return view('admin.attendanceapproval.import');
    }

    public function employee_calendar($id)
    {
        $query_calendar = DB::table('employees');
        $query_calendar->select('calendar_exceptions.*');
        $query_calendar->leftJoin('calendars', 'calendars.id', '=', 'employees.calendar_id');
        $query_calendar->leftJoin('calendar_exceptions', 'calendar_exceptions.calendar_id', '=', 'calendars.id');
        $query_calendar->where('employees.id', '=', $id);
        $calendar = $query_calendar->get();
        $exception_date = [];
        foreach ($calendar as $date) {
            $exception_date[] = $date->date_exception;
        }

        return $exception_date;
    }

    /**
     * Show the form for detail the specified resource.category
     * 
     */
    public function detail($id)
    {
        $approval = DB::table('attendances');
        $approval->select('attendances.*', 'employees.name as name', 'employees.id as employee_id', 'employees.nid as nid', 'workingtimes.working_time_type as working_group', 'workingtimes.description as description', 'titles.name as position');
        $approval->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $approval->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $approval->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $approval->where('attendances.id', '=', $id);
        $attendances = $approval->first();
        $update = Attendance::find($attendances->id);
        $exception_date = $this->employee_calendar($update->employee_id);
        $date = $update->attendance_date;

        $update->day = (in_array($date, $exception_date)) ? 'Off' : changeDateFormat('D', $date);
        $update->save();
        if ($attendances) {
            $attendances->attendance_in = $attendances->attendance_in ? date('H:i:s', strtotime($attendances->attendance_in)) : null;
            $attendances->attendance_out = $attendances->attendance_out ? date('H:i:s', strtotime($attendances->attendance_out)) : null;
            return view('admin.attendanceapproval.detail', compact('attendances'));
        } else {
            abort(404);
        }
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
            'time'      => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        try {
            $log = AttendanceLog::create([
                'attendance_id'   => $request->attendance_id,
                'employee_id'     => $request->employee_id,
                'device_name'     => $request->machine,
                'type'            => $request->type,
                'attendance_date' => $request->time
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            $error = explode("\n", $ex->errorInfo[2]);
            return response()->json([
                'status'    => false,
                'message'   => "Error when create data : " . $error[0]
            ], 400);
        }

        $attendance = Attendance::find($request->attendance_id);

        $worktime = WorkingtimeDetail::whereNotNull('min_workhour')->where('workingtime_id', '=', $attendance->workingtime_id)->where('day', '=', $attendance->day)->first();
        if ($worktime) {
            if (($request->type == 1 and changeDateFormat('Y-m-d H:i:s', $attendance->attendance_in) > changeDateFormat('Y-m-d H:i:s', $request->time)) || $attendance->attendance_in == null) {
                $attendance->attendance_in = changeDateFormat('Y-m-d H:i:s', $request->time);
                $breaktimes = $this->get_breaktime($attendance->employee->workgroup_id);

                $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out);

                $work_time = roundedTime(countWorkingTime($attendance->attendance_in, $attendance->attendance_out));
                if ($breaktimes->count() <= 0) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Breaktime for this workgroup ' . $attendance->employee->workgroup->name . ' not found'
                    ], 400);
                } else {
                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $worktime);
                    $getbreakovertime = getBreaktimeOvertime($breaktimes, $attendance_hour, $worktime);
                }
                $workhour = $worktime->workhour;
                $min_workhour = $worktime->min_workhour;
                $adj_over_time = roundedTime(countOverTime($worktime->finish, changeDateFormat('H:i:s', $attendance->attendance_out)));
                $adj_working_time = $work_time - $adj_over_time;

                if ($attendance->day == 'Off') {
                    if ($attendance->employee->overtime == 'yes') {
                        $attendance->adj_over_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        $attendance->adj_working_time = 0;
                    } else {
                        $attendance->adj_over_time = 0;
                        $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                    }
                } else {
                    if ($attendance->employee->overtime == 'yes') {
                        $attendance->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                        $attendance->adj_working_time = $adj_working_time - $getbreakworkingtime;
                    } else {
                        $attendance->adj_over_time = 0;
                        $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                    }
                }
                $attendance->save();
                if ($attendance && $attendance->attendance_in && $attendance->attendance_out) {
                    $overtime = calculateOvertime($attendance);
                    $allowance = calculateAllowance($attendance);
                }
            } elseif (($request->type == 0 and changeDateFormat('Y-m-d H:i:s', $attendance->attendance_out) < changeDateFormat('Y-m-d H:i:s', $request->time)) || $attendance->attendance_out == null) {
                $attendance->attendance_out = changeDateFormat('Y-m-d H:i:s', $request->time);
                $breaktimes = $this->get_breaktime($attendance->employee->workgroup_id);

                $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out);

                $work_time = roundedTime(countWorkingTime($attendance->attendance_in, $attendance->attendance_out));
                if ($breaktimes->count() <= 0) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Breaktime for this workgroup ' . $attendance->employee->workgroup->name . ' not found'
                    ], 400);
                } else {
                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $worktime);
                    $getbreakovertime = getBreaktimeOvertime($breaktimes, $attendance_hour, $worktime);
                }

                $workhour = $worktime->workhour;
                $min_workhour = $worktime->min_workhour;
                $adj_over_time = roundedTime(countOverTime($worktime->finish, changeDateFormat('H:i:s', $attendance->attendance_out)));
                $adj_working_time = $work_time - $adj_over_time;

                if ($attendance->day == 'Off') {
                    if ($attendance->employee->overtime == 'yes') {
                        $attendance->adj_over_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        $attendance->adj_working_time = 0;
                    } else {
                        $attendance->adj_over_time = 0;
                        $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                    }
                } else {
                    if ($attendance->employee->overtime == 'yes') {
                        $attendance->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                        $attendance->adj_working_time = $adj_working_time - $getbreakworkingtime;
                    } else {
                        $attendance->adj_over_time = 0;
                        $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                    }
                }
                $attendance->save();
                if ($attendance && $attendance->attendance_in && $attendance->attendance_out) {
                    $overtime = calculateOvertime($attendance);
                    $allowance = calculateAllowance($attendance);
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => 'Working shift not found'
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'message'   => 'Success add data',
            'data'      => $attendance
        ], 200);
    }

    public function setsessionfilter(Request $request)
    {
        Session::put('name', $request->name);
        Session::put('date', $request->date);
    }

    public function getLatestLeaveId()
    {
        $leave = Leave::max('id');

        return $leave + 1;
    }
    public function overtimeSchemeList($overtime_scheme_id, $allowance_id)
    {
        $query = DB::table('overtime_scheme_lists');
        $query->select('hour', 'amount');
        $query->leftJoin('overtime_schemes', 'overtime_schemes.id', '=', 'overtime_scheme_lists.overtime_scheme_id');
        $query->leftJoin('overtime_allowances', 'overtime_allowances.overtime_scheme_id', '=', 'overtime_schemes.id');
        $query->where('overtime_scheme_lists.overtime_scheme_id', $overtime_scheme_id);
        if(count($allowance_id) > 0){
            $query->whereIn('overtime_allowances.allowance_id', $allowance_id);
        }
        else{
            $query->whereIn('overtime_allowances.allowance_id', [-1]);
        }
        $query->groupBy('overtime_scheme_lists.hour', 'overtime_scheme_lists.amount');
        $scheme_lists = $query->get();

        return $scheme_lists;
    }
    public function get_additional_allowance($id, $month, $year)
    {
        $query = DB::table('employee_allowances');
        // $query->select('employee_allowances.*', 'allowances.allowance as description', 'allowances.group_allowance_id');
        $query->selectRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) as value, group_allowances.name as description, employee_allowances.is_penalty as is_penalty, allowances.group_allowance_id as group_allowance_id, employee_allowances.type as type, max(allowances.allowance) as allowance_name");
        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
        $query->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
        $query->leftJoin('group_allowances', 'group_allowances.id', 'allowances.group_allowance_id');
        $query->where('employee_allowances.employee_id', '=', $id);
        $query->where('employee_allowances.month', '=', $month);
        $query->where('employee_allowances.year', '=', $year);
        $query->where('employee_allowances.status', '=', 1);
        $query->where('allowance_categories.type', '=', 'additional');
        $query->where('employee_allowances.type', '!=', 'automatic');
        $query->where('allowances.reccurances', '=', 'monthly');
        $query->groupBy('group_allowances.name', 'employee_allowances.is_penalty', 'allowances.group_allowance_id', 'employee_allowances.type');
        $query->orderByRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) desc");
        $allowances = $query->get();

        $data = [];
        foreach ($allowances as $allowance) {
            $data[] = $allowance;
        }

        return $data;
    }
    public function approve(Request $request)
    {
        if ($request->approve) {
            $update = $request->approve;
            // $id = $this->getLatestLeaveId();
            DB::beginTransaction();
            foreach ($update as $id) {
                $approve = Attendance::find($id);
                if (!$approve->attendance_in && !$approve->attendance_out) {
                    if ($approve->day != 'Off') {
                        $employee_id = $approve->employee_id;
                        $date = $approve->attendance_date;
                        $check_alpha = Leave::with('log')->where('employee_id', $employee_id)->whereHas('log', function ($q) use ($date) {
                            $q->where('date', $date);
                        })->first();
                        if (!$check_alpha) {
                            $leave = Leave::create([
                                'employee_id'       => $approve->employee_id,
                                'status'            => -1,
                                'duration'          => 1
                            ]);
                            if ($leave) {
                                $log = LeaveLog::create([
                                    'leave_id'      => $leave->id,
                                    'reference_id'  => $approve->id,
                                    'type'          => 'fullday',
                                    'start'         => '09:00:00',
                                    'finish'        => '17:00:00',
                                    'date'          => changeDateFormat('Y-m-d', $approve->attendance_date)
                                ]);
                                if (!$log) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'     => false,
                                        'message'     => $leave
                                    ], 400);
                                }
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'status'     => false,
                                    'message'     => $leave
                                ], 400);
                            }
                        }
                    }
                    $approve->status = -1;
                    $approve->save();
                } elseif ($approve->attendance_in == null || $approve->attendance_out == null) {
                    $approve->status = -1;
                    $approve->save();
                } else {
                    $approve->status = 1;
                    $approve->save();
                    if (!$approve) {
                        DB::rollBack();
                        return response()->json([
                            'status'     => false,
                            'message'     => $approve
                        ], 400);
                    } elseif ($approve && $approve->adj_over_time > 0) {
                        $readConfigs = Config::where('option', 'cut_off')->first();
                        $cut_off = $readConfigs->value;
                        if (date('d', strtotime($approve->attendance_date)) > $cut_off) {
                            $month = date('m', strtotime($approve->attendance_date));
                            $year = date('Y', strtotime($approve->attendance_date));
                            $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                            $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                        } else {
                            $month =  date('m', strtotime($approve->attendance_date));
                            $year =  date('Y', strtotime($approve->attendance_date));
                        }
                        $overtime = Overtime::where('date', $approve->attendance_date)->where('employee_id', $approve->employee_id);
                        $overtime->delete();
                        $employee_allowance = EmployeeAllowance::where('employee_id', $approve->employee_id)->where('status',1)->where('year',$year)->where('month',$month)->get();
                        $allowance_id = [];
                        foreach($employee_allowance as $allowance){
                            array_push($allowance_id,$allowance->allowance_id);
                        }
                        $rules = $this->overtimeSchemeList($approve->overtime_scheme_id, $allowance_id);
                        // $rules = OvertimeSchemeList::select('hour', 'amount')->where('overtime_scheme_id', '=', $approve->overtime_scheme_id)->groupBy('hour','amount')->get();
                        // $schema_department = OvertimeschemeDepartment::where('overtime_scheme_id', '=', $approve->overtime_scheme_id)->first();
                        // dd($rules);
                        if ($rules) {
                            // if ($approve->day != 'Off') {
                            //     $i = 0;
                            //     $overtimes = $approve->adj_over_time;
                            //     $length = count($rules);
                            //     foreach ($rules as $key => $value) {
                            //         $sallary = EmployeeSalary::where('employee_id', '=', $approve->employee_id)->orderBy('created_at', 'desc')->first();
                            //         if ($overtimes >= 0) {
                            //             $overtime = Overtime::create([
                            //                 'employee_id'   => $approve->employee_id,
                            //                 'day'           => $value->recurrence_day,
                            //                 'scheme_rule'   => $value->hour,
                            //                 'hour'          => ($i != $length - 1) ? 1 : $overtimes,
                            //                 'amount'        => $value->amount,
                            //                 'basic_salary'  => $sallary ? $sallary->amount / 173 : 0,
                            //                 'date'          => changeDateFormat('Y-m-d', $approve->attendance_date)
                            //             ]);
                            //         } else {
                            //             continue;
                            //         }
                            //         $overtime->final_salary = $overtime->hour * $overtime->amount * $overtime->basic_salary;
                            //         $overtime->save();
                            //         $i++;
                            //         $overtimes = $overtimes - 1;
                            //         if (!$overtime) {
                            //             DB::rollBack();
                            //             return response()->json([
                            //                 'status'     => false,
                            //                 'message'     => $overtime
                            //             ], 400);
                            //         }
                            //     }
                            // } else {
                            //     $i = 0;
                            //     $n = 2;
                            //     $overtimes = $approve->adj_over_time;
                            //     $length = count($rules);
                            //     foreach ($rules as $key => $value) {
                            //         $sallary = EmployeeSalary::where('employee_id', '=', $approve->employee_id)->orderBy('created_at', 'desc')->first();
                            //         if ($overtimes >= 0) {
                            //             $overtime = Overtime::create([
                            //                 'employee_id'   => $approve->employee_id,
                            //                 'day'           => $value->recurrence_day,
                            //                 'scheme_rule'   => $value->hour,
                            //                 'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                            //                 'amount'        => $value->amount,
                            //                 'basic_salary'  => $sallary ? $sallary->amount / 173 : 0,
                            //                 'date'          => changeDateFormat('Y-m-d', $approve->attendance_date)
                            //             ]);
                            //         } else {
                            //             continue;
                            //         }
                            //         $overtime->final_salary = $overtime->hour * $overtime->amount * $overtime->basic_salary;
                            //         $overtime->save();
                            //         $i++;
                            //         $overtimes = $overtimes - 1;
                            //         if (!$overtime) {
                            //             DB::rollBack();
                            //             return response()->json([
                            //                 'status'     => false,
                            //                 'message'     => $overtime
                            //             ], 400);
                            //         }
                            //     }
                            // }
                            // if ($approve->overtime_scheme_id) {
                                $i = 0;
                                $overtimes = $approve->adj_over_time;
                                $length = count($rules);
                                foreach ($rules as $key => $value) {
                                    $date = Carbon::parse($approve->attendance_date);
                                    $sallary = SalaryIncreases::GetSalaryIncreaseDetail($approve->employee_id, $date->month, $date->year)->get();
                                    $overtimescheme = OvertimeScheme::where('id', $approve->overtime_scheme_id)->first();
                                    $allowances = $this->get_additional_allowance($approve->employee_id, $month, $year);
                                    // $emp_id = $approve->employee_id;
                                    if($overtimescheme->type == 'BASIC'){
                                        if ($approve->attendance_date >= $sallary->max('date')) {
                                            // $upcomingSalary = SalaryIncreases::whereHas('salaryIncreaseDetail', function($q) use ($emp_id){
                                            //     $q->where('employee_id', $emp_id);
                                            // })->where('date','=', $sallary->max('date'))->first();
                                            $getSallary = EmployeeSalary::where('employee_id', '=', $approve->employee_id)->orderBy('created_at', 'desc')->first();
                                            if ($overtimes >= 0) {
                                                $overtime = Overtime::create([
                                                    'employee_id'   => $approve->employee_id,
                                                    'day'           => $approve->day,
                                                    'scheme_rule'   => $value->hour,
                                                    'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                                    'amount'        => $value->amount,
                                                    'basic_salary'  => $getSallary ? $getSallary->amount / 173 : 0,
                                                    'date'          => changeDateFormat('Y-m-d', $approve->attendance_date),
                                                    'year'          => $year,
                                                    'month'         => $month,
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
                                        } else {
                                            // $query = SalaryIncreases::with(['salaryIncreaseDetail' => function ($q) use ($emp_id)
                                            // {
                                            //     $q->where('employee_id', $emp_id);
                                            // }])->whereMonth('date', $date->month)->whereYear('date', $date->year)->where('date', '<', $approve->attendance_date)->orderBy('date', 'desc');
                                            // $salary = $query->first();
                                            $getSallary = EmployeeSalary::where('employee_id', '=', $approve->employee_id)->orderBy('created_at', 'desc')->first();
                                            if ($overtimes >= 0) {
                                                $overtime = Overtime::create([
                                                    'employee_id'   => $approve->employee_id,
                                                    'day'           => $approve->day,
                                                    'scheme_rule'   => $value->hour,
                                                    'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                                    'amount'        => $value->amount,
                                                    'basic_salary'  => $getSallary ? $getSallary->amount / 173 : 0,
                                                    'date'          => changeDateFormat('Y-m-d', $approve->attendance_date),
                                                    'year'          => $year,
                                                    'month'         => $month,
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
                                        }
                                    }
                                    
                                    if($overtimescheme->type == 'BASIC & ALLOWANCE'){
                                        foreach($allowances as $key => $allowance){
                                            if ($approve->attendance_date >= $sallary->max('date')) {
                                                // $upcomingSalary = SalaryIncreases::whereHas('salaryIncreaseDetail', function($q) use ($emp_id){
                                                //     $q->where('employee_id', $emp_id);
                                                // })->where('date','=', $sallary->max('date'))->first();
                                                $getSallary = EmployeeSalary::where('employee_id', '=', $approve->employee_id)->orderBy('created_at', 'desc')->first();
                                                if ($overtimes >= 0) {
                                                    $overtime = Overtime::create([
                                                        'employee_id'   => $approve->employee_id,
                                                        'day'           => $approve->day,
                                                        'scheme_rule'   => $value->hour,
                                                        'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                                        'amount'        => $value->amount,
                                                        'basic_salary'  => $getSallary ? ($getSallary->amount + $allowance->value) / 173 : 0,
                                                        'date'          => changeDateFormat('Y-m-d', $approve->attendance_date),
                                                        'year'          => $year,
                                                        'month'         => $month,
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
                                            } else {
                                                // $query = SalaryIncreases::with(['salaryIncreaseDetail' => function ($q) use ($emp_id)
                                                // {
                                                //     $q->where('employee_id', $emp_id);
                                                // }])->whereMonth('date', $date->month)->whereYear('date', $date->year)->where('date', '<', $approve->attendance_date)->orderBy('date', 'desc');
                                                // $salary = $query->first();
                                                $getSallary = EmployeeSalary::where('employee_id', '=', $approve->employee_id)->orderBy('created_at', 'desc')->first();
                                                if ($overtimes >= 0) {
                                                    $overtime = Overtime::create([
                                                        'employee_id'   => $approve->employee_id,
                                                        'day'           => $approve->day,
                                                        'scheme_rule'   => $value->hour,
                                                        'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                                        'amount'        => $value->amount,
                                                        'basic_salary'  => $getSallary ? ($getSallary->amount + $allowance->value) / 173 : 0,
                                                        'date'          => changeDateFormat('Y-m-d', $approve->attendance_date),
                                                        'year'          => $year,
                                                        'month'         => $month,
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
                                            }
                                        }
                                    }

                                    if ($overtimescheme->type == 'ALLOWANCE') {
                                        foreach ($allowances as $key => $allowance) {
                                            if ($approve->attendance_date >= $sallary->max('date')) {
                                                // $upcomingSalary = SalaryIncreases::whereHas('salaryIncreaseDetail', function($q) use ($emp_id){
                                                //     $q->where('employee_id', $emp_id);
                                                // })->where('date','=', $sallary->max('date'))->first();
                                                $getSallary = EmployeeSalary::where('employee_id', '=', $approve->employee_id)->orderBy('created_at', 'desc')->first();
                                                if ($overtimes >= 0) {
                                                    $overtime = Overtime::create([
                                                        'employee_id'   => $approve->employee_id,
                                                        'day'           => $approve->day,
                                                        'scheme_rule'   => $value->hour,
                                                        'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                                        'amount'        => $value->amount,
                                                        'basic_salary'  => $allowance->value ? $allowance->value / 173 : 0,
                                                        'date'          => changeDateFormat('Y-m-d', $approve->attendance_date),
                                                        'year'          => $year,
                                                        'month'         => $month,
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
                                            } else {
                                                // $query = SalaryIncreases::with(['salaryIncreaseDetail' => function ($q) use ($emp_id)
                                                // {
                                                //     $q->where('employee_id', $emp_id);
                                                // }])->whereMonth('date', $date->month)->whereYear('date', $date->year)->where('date', '<', $approve->attendance_date)->orderBy('date', 'desc');
                                                // $salary = $query->first();
                                                $getSallary = EmployeeSalary::where('employee_id', '=', $approve->employee_id)->orderBy('created_at', 'desc')->first();
                                                if ($overtimes >= 0) {
                                                    $overtime = Overtime::create([
                                                        'employee_id'   => $approve->employee_id,
                                                        'day'           => $approve->day,
                                                        'scheme_rule'   => $value->hour,
                                                        'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                                        'amount'        => $value->amount,
                                                        'basic_salary'  => $allowance->value ? $allowance->value / 173 : 0,
                                                        'date'          => changeDateFormat('Y-m-d', $approve->attendance_date),
                                                        'year'          => $year,
                                                        'month'         => $month,
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
                                            }
                                        }
                                    }
                                }
                            // } else {
                                // $i = 0;
                                // $n = 2;
                                // $overtimes = $approve->adj_over_time;
                                // $length = count($rules);
                                // foreach ($rules as $key => $value) {
                                //     $sallary = EmployeeSalary::where('employee_id', '=', $approve->employee_id)->orderBy('created_at', 'desc')->first();
                                //     if ($overtimes >= 0) {
                                //         $overtime = Overtime::create([
                                //             'employee_id'   => $approve->employee_id,
                                //             'day'           => $value->recurrence_day,
                                //             'scheme_rule'   => $value->hour,
                                //             'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                //             'amount'        => $value->amount,
                                //             'basic_salary'  => $sallary ? $sallary->amount / 173 : 0,
                                //             'date'          => changeDateFormat('Y-m-d', $approve->attendance_date)
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
                            // }
                        } else {
                            DB::rollBack();
                            return response()->json([
                                'status'      => false,
                                'message'     => 'There is no overtime scheme for attendance on the relevant day'
                            ], 400);
                        }
                    }
                    if ($approve) {
                        $readConfigs = Config::where('option', 'cut_off')->first();
                        $cut_off = $readConfigs->value;
                        if (date('d', strtotime($approve->attendance_date)) > $cut_off) {
                            $month = date('m', strtotime($approve->attendance_date));
                            $year = date('Y', strtotime($approve->attendance_date));
                            $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                            $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                        } else {
                            $month =  date('m', strtotime($approve->attendance_date));
                            $year =  date('Y', strtotime($approve->attendance_date));
                        }
                        // Daily
                        $query = DB::table('attendances');
                        $query->select(
                            'attendances.employee_id as employee_id',
                            'attendances.workingtime_id as workingtime_id',
                            'attendances.attendance_date as date',
                            'allowances.reccurance as reccuran',
                            'allowances.allowance as allowance_name',
                            'employee_allowances.allowance_id as allowance_id',
                            'employee_allowances.value as value',
                            'employee_allowances.type as type',
                            'workingtime_allowances.workingtime_id as workingtime',
                            'employees.name as employee_name'
                        );
                        $query->leftJoin('employee_allowances', 'attendances.employee_id', '=', 'employee_allowances.employee_id');
                        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
                        $query->leftJoin('workingtime_allowances', 'workingtime_allowances.allowance_id', '=', 'allowances.id');
                        $query->leftJoin('employees', 'employees.id', '=', 'employee_allowances.employee_id');
                        $query->where('attendances.id', '=', $approve->id);
                        $query->where('employee_allowances.status', '=', 1);
                        $query->where('allowances.reccurance', '=', 'daily');
                        $query->where('employee_allowances.month',$month);
                        $query->where('employee_allowances.year', $year);
                        $histories = $query->get();
                        $deletedetail = EmployeeDetailAllowance::where('employee_id', $approve->employee_id)->where('tanggal_masuk', $approve->attendance_date);
                        $deletedetail->delete();
                        foreach ($histories as $history) {
                            if ($history) {
                                if ($history->workingtime) {
                                    if ($history->workingtime == $history->workingtime_id) {
                                        try {
                                            $employeedetailallowance = EmployeeDetailAllowance::create([
                                                'employee_id' => $history->employee_id,
                                                'allowance_id' => $history->allowance_id,
                                                'workingtime_id' => $history->workingtime_id,
                                                'tanggal_masuk' => $history->date,
                                                'value' => $history->value,
                                                'month' => $month,
                                                'year' => $year
                                            ]);
                                        } catch (\Illuminate\Database\QueryException $e) {
                                            return response()->json([
                                                'status'      => false,
                                                'message'     => 'There is error in employee name ' . $history->employee_name . ' when approved attendance in date ' . $history->date . ' and allowance ' . $history->allowance_name .
                                                'month' . $month . 'year' . $year . 'value' . $history->value . 'workingtime' . $history->workingtime_id
                                            ], 400);
                                        }

                                        if ($employeedetailallowance) {
                                            $query = EmployeeAllowance::select('employee_allowances.*');
                                            $query->where('employee_id', '=', $history->employee_id);
                                            $query->where('allowance_id', '=', $history->allowance_id);
                                            $query->where('month', $month);
                                            $query->where('year', $year);
                                            $updatefactor = $query->first();
                                            $updatequery = DB::table('employee_detailallowances');
                                            $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
                                            $updatequery->where('employee_detailallowances.employee_id', '=', $history->employee_id);
                                            $updatequery->where('employee_detailallowances.allowance_id', '=', $history->allowance_id);
                                            $updatequery->where('employee_detailallowances.month','=', $month);
                                            $updatequery->where('employee_detailallowances.year','=', $year);
                                            $updatequery->groupBy('employee_detailallowances.id');
                                            $updatecount = $updatequery->get()->count();
                                            if ($updatefactor) {
                                                $updatefactor->factor = $updatecount;
                                                $updatefactor->save();
                                            }
                                        }
                                    }
                                } else {
                                    try {
                                        $employeedetailallowance = EmployeeDetailAllowance::create([
                                            'employee_id' => $history->employee_id,
                                            'allowance_id' => $history->allowance_id,
                                            'workingtime_id' => $history->workingtime_id,
                                            'tanggal_masuk' => $history->date,
                                            'value' => $history->value,
                                            'month' => $month,
                                            'year' => $year
                                        ]);
                                    } catch (\Illuminate\Database\QueryException $e) {
                                        return response()->json([
                                            'status'      => false,
                                            'message'     => 'There is error in employee name ' . $history->employee_name . ' when approved attendance in date ' . $history->date . ' and allowance ' . $history-> allowance_name . 
                                            'month' . $month . 'year' . $year. 'value'. $history->value.'workingtime'.$history->workingtime_id
                                        ], 400);
                                    }

                                    if ($employeedetailallowance) {
                                        if (date('d', strtotime($approve->attendance_date)) > $cut_off) {
                                            $month = date('m', strtotime($approve->attendance_date));
                                            $year = date('Y', strtotime($approve->attendance_date));
                                            $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                                            $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                                        } else {
                                            $month =  date('m', strtotime($approve->attendance_date));
                                            $year =  date('Y', strtotime($approve->attendance_date));
                                        }
                                        $query = EmployeeAllowance::select('employee_allowances.*');
                                        $query->where('employee_id', '=', $history->employee_id);
                                        $query->where('allowance_id', '=', $history->allowance_id);
                                        $query->where('month', $month);
                                        $query->where('year', $year);
                                        $updatefactor = $query->first();
                                        $updatequery = DB::table('employee_detailallowances');
                                        $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
                                        $updatequery->where('employee_detailallowances.employee_id', '=', $history->employee_id);
                                        $updatequery->where('employee_detailallowances.allowance_id', '=', $history->allowance_id);
                                        $updatequery->where('employee_detailallowances.month','=', $month);
                                        $updatequery->where('employee_detailallowances.year','=', $year);
                                        $updatequery->groupBy('employee_detailallowances.id');
                                        $updatecount = $updatequery->get()->count();
                                        if ($updatefactor) {
                                            $updatefactor->factor = $updatecount;
                                            $updatefactor->save();
                                        }
                                    }
                                }
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'status'      => false,
                                    'message'     => $history
                                ], 400);
                            }
                        }
                        // Hourly
                        $query = DB::table('attendances');
                        $query->select(
                            'employee_allowances.*',
                            'attendances.employee_id as employee_id',
                            'attendances.workingtime_id as workingtime_id',
                            'attendances.attendance_date as date',
                            'allowances.reccurance as reccuran',
                            'allowances.allowance as allowance_name',
                            'employees.name as employee_name',
                            'employee_allowances.allowance_id as allowance_id',
                            'attendances.adj_working_time as value',
                            'employee_allowances.type as type',
                            'workingtime_allowances.workingtime_id as workingtime'
                        );
                        $query->leftJoin('employee_allowances', 'attendances.employee_id', '=', 'employee_allowances.employee_id');
                        $query->leftJoin('employees', 'employees.id', '=', 'employee_allowances.employee_id');
                        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
                        $query->leftJoin('workingtime_allowances', 'workingtime_allowances.allowance_id', '=', 'allowances.id');
                        $query->where('attendances.id', '=', $approve->id);
                        $query->where('employee_allowances.status', '=', 1);
                        $query->where('allowances.reccurance', '=', 'hourly');
                        $query->where('employee_allowances.month', $month);
                        $query->where('employee_allowances.year', $year);
                        $query->where('employee_allowances.employee_id', '=', $approve->employee_id);
                        $hourly = $query->get();
                        foreach ($hourly as $hour) {
                            if ($hour) {
                                if ($hour->workingtime) {
                                    if ($hour->workingtime == $hour->workingtime_id) {
                                        try {
                                            $employeedetailallowance = EmployeeDetailAllowance::create([
                                                'employee_id' => $hour->employee_id,
                                                'allowance_id' => $hour->allowance_id,
                                                'workingtime_id' => $hour->workingtime_id,
                                                'tanggal_masuk' => $hour->date,
                                                'value' => $hour->value,
                                                'month' => $month,
                                                'year' => $year
                                            ]);
                                        } catch (\Illuminate\Database\QueryException $e) {
                                            return response()->json([
                                                'status'      => false,
                                                'message'     => 'There is error in employee name ' . $hour->employee_name . ' when approved attendance in date ' . $hour->date . ' and allowance ' . $hour->allowance_name
                                            ], 400);
                                        }

                                        if ($employeedetailallowance) {
                                            $query = EmployeeAllowance::select('employee_allowances.*');
                                            $query->where('employee_id', '=', $hour->employee_id);
                                            $query->where('allowance_id', '=', $hour->allowance_id);
                                            $query->where('month', $month);
                                            $query->where('year', $year);
                                            $updatefactor = $query->first();
                                            $updatequery = DB::table('employee_detailallowances');
                                            $updatequery->where('employee_detailallowances.employee_id', '=', $hour->employee_id);
                                            $updatequery->where('employee_detailallowances.allowance_id', '=', $hour->allowance_id);
                                            $updatequery->where('employee_detailallowances.month', '=', $month);
                                            $updatequery->where('employee_detailallowances.year', '=', $year);
                                            $updatequery->groupBy('employee_detailallowances.id');
                                            $updatecount = $updatequery->get()->sum('value');
                                            if ($updatefactor) {
                                                $updatefactor->factor = $updatecount;
                                                $updatefactor->save();
                                            }
                                        }
                                    }
                                } else {
                                    try {
                                        $employeedetailallowance = EmployeeDetailAllowance::create([
                                            'employee_id' => $hour->employee_id,
                                            'allowance_id' => $hour->allowance_id,
                                            'workingtime_id' => $hour->workingtime_id,
                                            'tanggal_masuk' => $hour->date,
                                            'value' => $hour->value,
                                            'month' => $month,
                                            'year' => $year
                                        ]);
                                    } catch (\Illuminate\Database\QueryException $e) {
                                        return response()->json([
                                            'status'      => false,
                                            'message'     => 'There is error in employee name ' . $hour->employee_name . ' when approved attendance in date ' . $hour->date . ' and allowance ' . $hour->allowance_name
                                        ], 400);
                                    }

                                    if ($employeedetailallowance) {
                                        if (date('d', strtotime($approve->attendance_date)) > $cut_off) {
                                            $month = date('m', strtotime($approve->attendance_date));
                                            $year = date('Y', strtotime($approve->attendance_date));
                                            $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                                            $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                                        } else {
                                            $month =  date('m', strtotime($approve->attendance_date));
                                            $year =  date('Y', strtotime($approve->attendance_date));
                                        }
                                        $query = EmployeeAllowance::select('employee_allowances.*');
                                        $query->where('employee_id', '=', $hour->employee_id);
                                        $query->where('allowance_id', '=', $hour->allowance_id);
                                        $query->where('month', $month);
                                        $query->where('year', $year);
                                        $updatefactor = $query->first();
                                        $updatequery = DB::table('employee_detailallowances');
                                        // $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
                                        $updatequery->where('employee_detailallowances.employee_id', '=', $hour->employee_id);
                                        $updatequery->where('employee_detailallowances.allowance_id', '=', $hour->allowance_id);
                                        $updatequery->where('employee_detailallowances.month', '=', $month);
                                        $updatequery->where('employee_detailallowances.year', '=', $year);
                                        $updatequery->groupBy('employee_detailallowances.id');
                                        $updatecount = $updatequery->get()->sum('value');
                                        if ($updatefactor) {
                                            $updatefactor->factor = $updatecount;
                                            $updatefactor->save();
                                        }
                                    }
                                }
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'status'      => false,
                                    'message'     => $hour
                                ], 400);
                            }
                        }

                        // Breaktime
                        $query = DB::table('attendances');
                        $query->select(
                            'employee_allowances.*',
                            'attendances.employee_id as employee_id',
                            'attendances.workingtime_id as workingtime_id',
                            'attendances.attendance_date as date',
                            'allowances.reccurance as reccuran',
                            'allowances.allowance as allowance_name',
                            'employees.name as employee_name',
                            'employee_allowances.allowance_id as allowance_id',
                            'attendances.breaktime as value',
                            'employee_allowances.type as type',
                            'workingtime_allowances.workingtime_id as workingtime'
                        );
                        $query->leftJoin('employee_allowances', 'attendances.employee_id', '=', 'employee_allowances.employee_id');
                        $query->leftJoin('employees', 'employees.id', '=', 'employee_allowances.employee_id');
                        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
                        $query->leftJoin('workingtime_allowances', 'workingtime_allowances.allowance_id', '=', 'allowances.id');
                        $query->where('attendances.id', '=', $approve->id);
                        $query->where('employee_allowances.status', '=', 1);
                        $query->where('allowances.reccurance', '=', 'breaktime');
                        $query->where('employee_allowances.month', $month);
                        $query->where('employee_allowances.year', $year);
                        $query->where('employee_allowances.employee_id', '=', $approve->employee_id);
                        $breaktimes = $query->get();
                        foreach ($breaktimes as $breaktime) {
                            if ($breaktime) {
                                if ($breaktime->workingtime) {
                                    if ($breaktime->workingtime == $breaktime->workingtime_id) {
                                        try {
                                            $employeedetailallowance = EmployeeDetailAllowance::create([
                                                'employee_id' => $breaktime->employee_id,
                                                'allowance_id' => $breaktime->allowance_id,
                                                'workingtime_id' => $breaktime->workingtime_id,
                                                'tanggal_masuk' => $breaktime->date,
                                                'value' => $breaktime->value,
                                                'month' => $month,
                                                'year' => $year
                                            ]);
                                        } catch (\Illuminate\Database\QueryException $e) {
                                            return response()->json([
                                                'status'      => false,
                                                'message'     => 'There is error in employee name ' . $breaktime->employee_name . ' when approved attendance in date ' . $breaktime->date . ' and allowance ' . $breaktime->allowance_name
                                            ], 400);
                                        }

                                        if ($employeedetailallowance) {
                                            $query = EmployeeAllowance::select('employee_allowances.*');
                                            $query->where('employee_id', '=', $breaktime->employee_id);
                                            $query->where('allowance_id', '=', $breaktime->allowance_id);
                                            $query->where('month', $month);
                                            $query->where('year', $year);
                                            $updatefactor = $query->first();
                                            $updatequery = DB::table('employee_detailallowances');
                                            $updatequery->where('employee_detailallowances.employee_id', '=', $breaktime->employee_id);
                                            $updatequery->where('employee_detailallowances.allowance_id', '=', $breaktime->allowance_id);
                                            $updatequery->where('employee_detailallowances.month' ,'=', $month);
                                            $updatequery->where('employee_detailallowances.year' ,'=', $year);
                                            $updatequery->groupBy('employee_detailallowances.id');
                                            $updatecount = $updatequery->get()->sum('value');
                                            if ($updatefactor) {
                                                $updatefactor->factor = $updatecount;
                                                $updatefactor->save();
                                            }
                                        }
                                    }
                                } else {
                                    try {
                                        $employeedetailallowance = EmployeeDetailAllowance::create([
                                            'employee_id' => $breaktime->employee_id,
                                            'allowance_id' => $breaktime->allowance_id,
                                            'workingtime_id' => $breaktime->workingtime_id,
                                            'tanggal_masuk' => $breaktime->date,
                                            'value' => $breaktime->value,
                                            'month' => $month,
                                            'year' => $year
                                        ]);
                                    } catch (\Illuminate\Database\QueryException $e) {
                                        return response()->json([
                                            'status'      => false,
                                            'message'     => 'There is error in employee name ' . $breaktime->employee_name . ' when approved attendance in date ' . $breaktime->date . ' and allowance ' . $breaktime->allowance_name
                                        ], 400);
                                    }

                                    if ($employeedetailallowance) {
                                        if (date('d', strtotime($approve->attendance_date)) > $cut_off) {
                                            $month = date('m', strtotime($approve->attendance_date));
                                            $year = date('Y', strtotime($approve->attendance_date));
                                            $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                                            $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                                        } else {
                                            $month =  date('m', strtotime($approve->attendance_date));
                                            $year =  date('Y', strtotime($approve->attendance_date));
                                        }
                                        $query = EmployeeAllowance::select('employee_allowances.*');
                                        $query->where('employee_id', '=', $breaktime->employee_id);
                                        $query->where('allowance_id', '=', $breaktime->allowance_id);
                                        $query->where('month', $month);
                                        $query->where('year', $year);
                                        $updatefactor = $query->first();
                                        $updatequery = DB::table('employee_detailallowances');
                                        // $updatequery->select('employee_detailallowances.*', DB::raw('count(tanggal_masuk) as date'));
                                        $updatequery->where('employee_detailallowances.employee_id', '=', $breaktime->employee_id);
                                        $updatequery->where('employee_detailallowances.allowance_id', '=', $breaktime->allowance_id);
                                        $updatequery->where('employee_detailallowances.month', '=', $month);
                                        $updatequery->where('employee_detailallowances.year', '=', $year);
                                        $updatequery->groupBy('employee_detailallowances.id');
                                        $updatecount = $updatequery->get()->sum('value');
                                        if ($updatefactor) {
                                            $updatefactor->factor = $updatecount;
                                            $updatefactor->save();
                                        }
                                    }
                                }
                            } else {
                                DB::rollBack();
                                return response()->json([
                                    'status'      => false,
                                    'message'     => $breaktime
                                ], 400);
                            }
                        }
                        // if ($breaktimes) {
                        //     $hourly->factor = $hourly->factor + $approve->adj_working_time;
                        //     $hourly->save();
                        // }
                    }
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
            'message'     => 'Attendance was successfully approved',
        ], 200);
    }

    public function deletemass(Request $request)
    {
        if ($request->approve) {
            $update = $request->approve;
            DB::beginTransaction();
            foreach ($update as $id) {
                $log = AttendanceLog::where('attendance_id', $id);
                $log->delete();
                if ($log) {
                    $approve = Attendance::find($id);
                    if ($approve->attendance_in && $approve->attendance_out) {
                        $overtime = Overtime::where('employee_id', $approve->employee_id)->where('date', $approve->attendance_date);
                        $overtime->delete();
                        $allowance = deleteAllowance($approve);
                    }
                    $approve->delete();
                    if (!$approve) {
                        DB::rollBack();
                        return response()->json([
                            'status'      => false,
                            'message'     => $approve
                        ], 400);
                    }
                } else {
                    DB::rollBack();
                    return response()->json([
                        'status'      => false,
                        'message'     => $log
                    ], 400);
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
            'message'     => 'Attendance was successfully removed',
        ], 200);
    }

    public function get_breaktime($workgroup)
    {
        $query = DB::table('break_times');
        $query->select('break_times.*');
        $query->leftJoin('break_time_lines', 'break_time_lines.breaktime_id', '=', 'break_times.id');
        $query->where('break_time_lines.workgroup_id', '=', $workgroup);

        return $query->get();
    }

    public function quickupdate(Request $request)
    {
        if (isset($request->working_shift)) {
            // dd($request->working_shift);
            // Log History Shift
            $getworkingtimes = Workingtime::where('id',$request->working_shift)->first();
            $user_id = Auth::user()->id;
            
            $attendance = Attendance::find($request->attendance_id);
            $attendance->workingtime_id = $request->working_shift;
            $attendance->save();
            $employee = Employee::where('id',$attendance->employee_id)->first();
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Attendance Approval","Edit",date("Y-m-d")." Shift",$getworkingtimes->description);
            if (!$attendance) {
                return response()->json([
                    'status'     => false,
                    'message'    => "Error update data : "
                ], 400);
            } else {
                $allowance = calculateAllowance($attendance);
                if ($attendance->status == -1 && $attendance->attendance_in && $attendance->attendance_out) {
                    $attendance->status = 1;
                    $attendance->save();
                }
            }
        } elseif ($request->first_in) {
            $attendance = Attendance::find($request->first_in_id);
            if ($attendance->workingtime_id) {

                $employee = Employee::where('id',$attendance->employee_id)->first();
                $user_id = Auth::user()->id;
                setrecordloghistory($user_id,$employee->id,$employee->department_id,"Attendance Approval","Edit",date("Y-m-d")." Check in",$request->first_in);
                $worktime = WorkingtimeDetail::where('workingtime_id', '=', $attendance->workingtime_id)->where('day', '=', $attendance->day)->first();
                $new_time = changeDateFormat('Y-m-d', $attendance->attendance_in) . ' ' . $request->first_in;
                $attendance->attendance_in = changeDateFormat('Y-m-d H:i:s', $request->first_in);
                $breaktimes = $this->get_breaktime($attendance->employee->workgroup_id);
                $attendance->save();
                if ($attendance->attendance_out) {
                    $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out);

                    if (($worktime->start >= changeDateFormat('H:i:s', $attendance->attendance_in)) && (changeDateFormat('H:i:s', $attendance->attendance_in) >= $worktime->min_in)) {
                        $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $attendance->attendance_in) . ' ' . $worktime->start);
                        $work_time = roundedTime(countWorkingTime($start_shift, $attendance->attendance_out));
                    } else {
                        $work_time = roundedTime(countWorkingTime($attendance->attendance_in, $attendance->attendance_out));
                    }
                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $worktime);
                    $getbreakovertime = getBreaktimeOvertime($breaktimes, $attendance_hour, $worktime);
                    $workhour = $worktime->workhour;
                    $min_workhour = $worktime->min_workhour;
                    if (changeDateFormat('H:i:s', $attendance->attendance_out) < $worktime->finish) {
                        $adj_over_time = 0;
                    } else {
                        $adj_over_time = roundedTime(countOverTime($worktime->finish, changeDateFormat('H:i:s', $attendance->attendance_out)));
                    }
                    $adj_working_time = $work_time - $adj_over_time;

                    if ($attendance->day == 'Off') {
                        if ($attendance->employee->overtime == 'yes') {
                            $attendance->adj_over_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                            $attendance->adj_working_time = 0;
                        } else {
                            $attendance->adj_over_time = 0;
                            $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        }
                    } else {
                        if ($attendance->employee->overtime == 'yes') {
                            $attendance->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                            $attendance->adj_working_time = $adj_working_time - $getbreakworkingtime;
                        } else {
                            $attendance->adj_over_time = 0;
                            $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        }
                    }
                    $attendance->save();
                    if (!$attendance) {
                        return response()->json([
                            'status'     => false,
                            'message'    => $attendance
                        ], 400);
                    } else {
                        $overtime = calculateOvertime($attendance);
                        $allowance = calculateAllowance($attendance);
                        if ($attendance->status == -1 && $attendance->attendance_in && $attendance->attendance_out) {
                            $attendance->status = 1;
                            $attendance->save();
                        }
                    }
                } else {
                    $attendance->adj_over_time = 0;
                    $attendance->adj_working_time = 0;
                    $attendance->save();
                }
            } else {
                return response()->json([
                    'status'     => false,
                    'message'    => 'Please select workingtime first',
                ], 400);
            }
        } elseif ($request->last_out) {
            $attendance = Attendance::find($request->first_out_id);
            if ($attendance->workingtime_id) {
                $employee = Employee::where('id',$attendance->employee_id)->first();
                $user_id = Auth::user()->id;
                setrecordloghistory($user_id,$employee->id,$employee->department_id,"Attendance Approval","Edit",date("Y-m-d")." Check Out",$request->last_out);
                $worktime = WorkingtimeDetail::where('workingtime_id', '=', $attendance->workingtime_id)->where('day', '=', $attendance->day)->first();
                $worktime = WorkingtimeDetail::where('workingtime_id', '=', $attendance->workingtime_id)->where('day', '=', $attendance->day)->first();
                $attendance->attendance_out = changeDateFormat('Y-m-d H:i:s', $request->last_out);
                $breaktimes = $this->get_breaktime($attendance->employee->workgroup_id);
                $attendance->save();
                if ($attendance->attendance_in) {
                    $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out);

                    if (($worktime->start >= changeDateFormat('H:i:s', $attendance->attendance_in)) && (changeDateFormat('H:i:s', $attendance->attendance_in) >= $worktime->min_in)) {
                        $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $attendance->attendance_in) . ' ' . $worktime->start);
                        $work_time = roundedTime(countWorkingTime($start_shift, $attendance->attendance_out));
                    } else {
                        $work_time = roundedTime(countWorkingTime($attendance->attendance_in, $attendance->attendance_out));
                    }
                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $worktime);
                    $getbreakovertime = getBreaktimeOvertime($breaktimes, $attendance_hour, $worktime);
                    $workhour = $worktime->workhour;
                    $min_workhour = $worktime->min_workhour;
                    if (changeDateFormat('H:i:s', $attendance->attendance_out) < $worktime->finish) {
                        $adj_over_time = 0;
                    } else {
                        $adj_over_time = roundedTime(countOverTime($worktime->finish, changeDateFormat('H:i:s', $attendance->attendance_out)));
                    }
                    $adj_working_time = $work_time - $adj_over_time;

                    if ($attendance->day == 'Off') {
                        if ($attendance->employee->overtime == 'yes') {
                            $attendance->adj_over_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                            $attendance->adj_working_time = 0;
                        } else {
                            $attendance->adj_over_time = 0;
                            $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        }
                    } else {
                        if ($attendance->employee->overtime == 'yes') {
                            $attendance->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                            $attendance->adj_working_time = $adj_working_time - $getbreakworkingtime;
                        } else {
                            $attendance->adj_over_time = 0;
                            $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        }
                    }
                    $attendance->save();
                    if (!$attendance) {
                        return response()->json([
                            'status'     => false,
                            'message'    => $attendance
                        ], 400);
                    } else {
                        $overtime = calculateOvertime($attendance);
                        $allowance = calculateAllowance($attendance);
                        if ($attendance->status == -1 && $attendance->attendance_in && $attendance->attendance_out) {
                            $attendance->status = 1;
                            $attendance->save();
                        }
                    }
                } else {
                    $attendance->adj_over_time = 0;
                    $attendance->adj_working_time = 0;
                    $attendance->save();
                }
            } else {
                return response()->json([
                    'status'     => false,
                    'message'    => 'Please select workingtime first',
                ], 400);
            }
        } elseif ($request->workingtime_id) {
            $attendance = Attendance::find($request->workingtime_id);
            $attendance->adj_working_time = $request->working_time;
            $attendance->adj_over_time = $request->over_time;
            $attendance->save();

            $employee = Employee::where('id',$attendance->employee_id)->first();
            $user_id = Auth::user()->id;
            // Log History WO
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Attendance Approval","Edit",date("Y-m-d")." WT",$request->working_time);
            // Log History OT
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Attendance Approval","Edit",date("Y-m-d")." OT",$request->over_time);
            if (!$attendance) {
                return response()->json([
                    'status'     => false,
                    'message'    => $attendance
                ], 400);
            } else {
                $overtime = calculateOvertime($attendance);
                $allowance = calculateAllowance($attendance);
                if ($attendance->status == -1 && $attendance->attendance_in && $attendance->attendance_out) {
                    $attendance->status = 1;
                    $attendance->save();
                }
            }
        } elseif ($request->scheme) {
            $readConfigs = Config::where('option', 'cut_off')->first();
            $cut_off = $readConfigs->value;
            if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
                $month = date('m', strtotime($attendance->attendance_date));
                $year = date('Y', strtotime($attendance->attendance_date));
                $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
            } else {
                $month =  date('m', strtotime($attendance->attendance_date));
                $year =  date('Y', strtotime($attendance->attendance_date));
            }
            $attendance = Attendance::find($request->scheme_id);
            $employee = Employee::find($attendance->employee_id);
            $overtime_scheme = OvertimeScheme::find($request->scheme);
            $employee_allowance = EmployeeAllowance::where('employee_id', $attendance->employee_id)->where('status',1)->where('year',$year)->where('month',$month)->get();
            $allowance_id = [];
            foreach($employee_allowance as $allowance){
                array_push($allowance_id,$allowance->allowance_id);
            }
            $rules = $this->overtimeSchemeList($request->scheme, $allowance_id);
            // $rules = OvertimeSchemeList::select('hour', 'amount')->where('overtime_scheme_id', '=', $request->scheme)->groupBy('hour','amount')->orderBy('hour','asc')->get();
            // Log History scheme
            $user_id = Auth::user()->id;
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Attendance Approval","Edit",date("Y-m-d")." Scheme",$overtime_scheme->scheme_name);
            if($employee->overtime == 'yes'){
                $attendance->overtime_scheme_id = $request->scheme;
                if($request->scheme == 3){
                    $attendance->adj_over_time = $attendance->adj_working_time + $attendance->adj_over_time;
                    $attendance->adj_working_time = 0;
                }elseif($request->scheme == 2){
                    $workingtime = $attendance->adj_over_time + $attendance->adj_working_time;
                    if ($workingtime - $overtime_scheme->working_time > 0) {
                        $attendance->adj_working_time = $overtime_scheme->working_time;
                        $attendance->adj_over_time = $workingtime - $overtime_scheme->working_time;
                    }else{
                        $attendance->adj_over_time = 0;
                        $attendance->adj_working_time = $workingtime;
                    }
                }else{
                    $attendance->adj_working_time = $overtime_scheme->working_time;
                    $attendance->adj_over_time = $attendance->adj_over_time - $overtime_scheme->working_time;
                }
                if (!$attendance->save()) {
                    return response()->json([
                        'status'     => false,
                        'message'    => "Error update data"
                    ], 400);
                }
            }else{
                $attendance->overtime_scheme_id = $request->scheme;
            }
            if ($attendance->status == 1) {
                if ($rules) {
                        $readConfigs = Config::where('option', 'cut_off')->first();
                        $cut_off = $readConfigs->value;
                        if (date('d', strtotime($attendance->attendance_date)) > $cut_off) {
                            $month = date('m', strtotime($attendance->attendance_date));
                            $year = date('Y', strtotime($attendance->attendance_date));
                            $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                            $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                        } else {
                            $month =  date('m', strtotime($attendance->attendance_date));
                            $year =  date('Y', strtotime($attendance->attendance_date));
                        }
                        $i = 0;
                        $overtimes = $attendance->adj_over_time;
                        $length = count($rules);
                        $listdel = Overtime::where('employee_id','=', $attendance->employee_id)->where('date','=', $attendance->attendance_date);
                        $listdel->delete();
                    
                        foreach ($rules as $key => $value) {
                            $date = Carbon::parse($attendance->attendance_date);
                            // $sallary = SalaryIncreases::GetSalaryIncreaseDetail($attendance->employee_id, $date->month, $date->year)->get();
                            $sallary = EmployeeSalary::where('employee_id', $attendance->employee_id)->orderBy('updated_at', 'desc')->first();
                        $overtimescheme = OvertimeScheme::where('id', $request->scheme)->first();
                        $allowances = $this->get_additional_allowance($request->scheme, $month, $year);
                        if($overtimescheme->type == 'BASIC'){
                            if ($overtimes >= 0) {
                                $overtime = Overtime::create([
                                    'employee_id'   => $attendance->employee_id,
                                    'day'           => $attendance->day,
                                    'scheme_rule'   => $value->hour,
                                    'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                    'amount'        => $value->amount,
                                    'basic_salary'  => $sallary ? $sallary->amount / 173 : 0,
                                    'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date),
                                    'month'         => $month,
                                    'year'          => $year
                                ]);
                            } else {
                                continue;
                            }
                        }
                        if ($overtimescheme->type == 'BASIC & ALLOWANCE') {
                            foreach($allowances as $key => $allowance){
                                if ($overtimes >= 0) {
                                    $overtime = Overtime::create([
                                        'employee_id'   => $attendance->employee_id,
                                        'day'           => $attendance->day,
                                        'scheme_rule'   => $value->hour,
                                        'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                        'amount'        => $value->amount,
                                        'basic_salary'  => $sallary ? ($sallary->amount + $allowance->value) / 173 : 0,
                                        'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date),
                                        'month'         => $month,
                                        'year'          => $year
                                    ]);
                                } else {
                                    continue;
                                }
                            }
                            
                        }
                        if ($overtimescheme->type == 'ALLOWANCE') {
                            foreach ($allowances as $key => $allowance) {
                                if ($overtimes >= 0) {
                                    $overtime = Overtime::create([
                                        'employee_id'   => $attendance->employee_id,
                                        'day'           => $attendance->day,
                                        'scheme_rule'   => $value->hour,
                                        'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                        'amount'        => $value->amount,
                                        'basic_salary'  => $allowance ? $allowance->value / 173 : 0,
                                        'date'          => changeDateFormat('Y-m-d', $attendance->attendance_date),
                                        'month'         => $month,
                                        'year'          => $year
                                    ]);
                                } else {
                                    continue;
                                }
                            }
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
                            // $emp_id = $attendance->employee_id;
                            // if ($attendance->attendance_date >= $sallary->max('date')) {
                            //     // $upcomingSalary = SalaryIncreases::whereHas('salaryIncreaseDetail', function($q) use ($emp_id){
                            //     //     $q->where('employee_id', $emp_id);
                            //     // })->where('date','=', $sallary->max('date'))->first();
                            //     // dd($upcomingSalary->salaryIncreaseDetail);
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
                            // } else {
                            //     // $query = SalaryIncreases::with(['salaryIncreaseDetail' => function ($q) use ($emp_id)
                            //     // {
                            //     //     $q->where('employee_id', $emp_id);
                            //     // }])->whereMonth('date', $date->month)->whereYear('date', $date->year)->where('date', '<', $attendance->attendance_date)->orderBy('date', 'desc');
                            //     // $salary = $query->first();
                            //     // dd($salary->salaryIncreaseDetail);
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
                } else {
                    DB::rollBack();
                    return response()->json([
                        'status'      => false,
                        'message'     => 'There is no overtime scheme for attendance on the relevant day'
                    ], 400);
                }
            }
        } elseif (!$request->working_shift) {
            $attendance = Attendance::find($request->attendance_id);
            $attendance->workingtime_id = null;
            $attendance->attendance_in = null;
            $attendance->attendance_out = null;
            $attendance->adj_working_time = 0;
            $attendance->adj_over_time = 0;
            $attendance->save();
            $allowance = calculateAllowance($attendance);
            $overtimes = calculateOvertime($attendance);
            if ($attendance->status == -1 && $attendance->attendance_in && $attendance->attendance_out) {
                $attendance->status = 1;
                $attendance->save();
            }
        } 
        return response()->json([
            'status'    => true,
            'message'   => 'Success change date',
            'results'   => route('dailyreport.index'),
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edithistory($id)
    {
        $history = AttendanceLog::find($id);
        return response()->json([
            'status'    => true,
            'data'      => $history
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatehistory(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'time_edit'      => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $history = AttendanceLog::find($id);
        $history->attendance_id     = $request->attendance_id_edit;
        $history->employee_id       = $request->employee_id_edit;
        $history->type              = $request->type_edit;
        $history->device_name       = $request->machine_edit;
        $history->attendance_date   = $request->time_edit;
        $history->save();

        $attendance = Attendance::find($request->attendance_id_edit);

        $worktime = WorkingtimeDetail::where('workingtime_id', '=', $attendance->workingtime_id)->where('day', '=', $attendance->day)->first();

        if ($history) {
            if ($worktime) {
                if ($request->type_edit == 1 || $attendance->attendance_in == null) {
                    $attendance->attendance_in = changeDateFormat('Y-m-d H:i:s', $request->time_edit);
                    $breaktimes = $this->get_breaktime($attendance->employee->workgroup_id);

                    $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out);

                    $work_time = roundedTime(countWorkingTime($attendance->attendance_in, $attendance->attendance_out));
                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $worktime);
                    $getbreakovertime = getBreaktimeOvertime($breaktimes, $attendance_hour, $worktime);
                    $workhour = $worktime->workhour;
                    $min_workhour = $worktime->min_workhour;
                    $adj_over_time = roundedTime(countOverTime($worktime->finish, changeDateFormat('H:i:s', $attendance->attendance_out)));
                    $adj_working_time = $work_time - $adj_over_time;

                    if ($attendance->day == 'Off') {
                        if ($attendance->employee->overtime == 'yes') {
                            $attendance->adj_over_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                            $attendance->adj_working_time = 0;
                        } else {
                            $attendance->adj_over_time = 0;
                            $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        }
                    } else {
                        if ($attendance->employee->overtime == 'yes') {
                            $attendance->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                            $attendance->adj_working_time = $adj_working_time - $getbreakworkingtime;
                        } else {
                            $attendance->adj_over_time = 0;
                            $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        }
                    }
                    $attendance->save();
                    if ($attendance) {
                        $overtime = calculateOvertime($attendance);
                        $allowance = calculateAllowance($attendance);
                    }
                } elseif ($request->type == 0 || $attendance->attendance_out == null) {
                    $attendance->attendance_out = changeDateFormat('Y-m-d H:i:s', $request->time_edit);
                    $breaktimes = $this->get_breaktime($attendance->employee->workgroup_id);

                    $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out);

                    $work_time = roundedTime(countWorkingTime($attendance->attendance_in, $attendance->attendance_out));
                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $worktime);
                    $getbreakovertime = getBreaktimeOvertime($breaktimes, $attendance_hour, $worktime);
                    $workhour = $worktime->workhour;
                    $min_workhour = $worktime->min_workhour;
                    $adj_over_time = roundedTime(countOverTime($worktime->finish, changeDateFormat('H:i:s', $attendance->attendance_out)));
                    $adj_working_time = $work_time - $adj_over_time;

                    if ($attendance->day == 'Off') {
                        if ($attendance->employee->overtime == 'yes') {
                            $attendance->adj_over_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                            $attendance->adj_working_time = 0;
                        } else {
                            $attendance->adj_over_time = 0;
                            $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        }
                    } else {
                        if ($attendance->employee->overtime == 'yes') {
                            $attendance->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                            $attendance->adj_working_time = $adj_working_time - $getbreakworkingtime;
                        } else {
                            $attendance->adj_over_time = 0;
                            $attendance->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                        }
                    }
                    $attendance->save();
                    if ($attendance) {
                        $overtime = calculateOvertime($attendance);
                        $allowance = calculateAllowance($attendance);
                    }
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Set workingtime first'
                ], 400);
            }
        } elseif (!$history) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'     => $history
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'message'   => 'Success edit data',
            'data'      => $attendance
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $approval = Attendance::find($id);
        if ($approval) {
            return view('admin.attendanceapproval.detail', compact('approval'));
        } else {
            abort(404);
        }
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
            'adj_working_time'   => 'required',
            'adj_over_time'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $attendance = Attendance::find($id);
        $attendance->adj_working_time = $request->adj_working_time;
        $attendance->adj_over_time = $request->adj_over_time;
        $attendance->workingtime_id = $request->working_time;
        $attendance->note = $request->note;
        $attendance->save();
        if (!$attendance) {
            return response()->json([
                'status' => false,
                'message'     => $attendance
            ], 400);
        } else {
            $overtime = calculateOvertime($attendance);
            $allowance = calculateAllowance($attendance);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('attendanceapproval.index'),
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
            $log = AttendanceLog::where('attendance_id', $id);
            $log->delete();
            $attendance = Attendance::find($id);
            $attendance->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }

    public function getShift()
    {
        $query = Workingtime::orderBy('description');
        $shifts = $query->get();

        return $shifts;
    }

    public function getDepartmentChildId(Request $request)
    {
        $query = Department::where('path', 'like', "%$request->department_path%");
        $departmentChilds = $query->get();

        return $departmentChilds;
    }

    public function getDepartmentParent()
    {
        $query      = Department::where('departments.level', 1);
        $departments= $query->get();

        return $departments;
    }

    public function getExportData(Request $request)
    {
        $date = Carbon::parse($request->to)->toDateString();
        $departmentChilds = $this->getDepartmentChildId($request);
        $shifts = $this->getShift();
        $department_id = [];
        foreach ($departmentChilds as $key => $childs) {
            array_push($department_id, $childs->id);
        }
        $department_id = implode(',', $department_id);

        $selectRaw = "work_groups.name as workgroup_name,";
        $selectRaw .= "employees.ot as ot,";
        foreach ($shifts as $key => $shift) {
            $alias = strtolower(str_replace([" ", "-", ".", ","], "_", $shift->description));
            $selectRaw .= "attendances.$alias as $alias,";
        }
        $selectRaw .= "null";

        $selectRawJoin = '';
        foreach ($shifts as $key => $shift) {
            $alias = strtolower(str_replace([" ", "-", ".", ","], "_", $shift->description));
            $selectRawJoin .= "count(case when attendances.workingtime_id = $shift->id and employees.department_id in ($department_id) then 1 end) as $alias,";
        }
        $selectRawJoin .= "null";

        $query = WorkGroup::selectRaw("$selectRaw");
        $query->leftJoin(DB::raw("(select
            workgroup_id,
            count(id) as ot
            from employees
            where department_id in ($department_id) and employees.status = 1
            group by workgroup_id) employees"), 'employees.workgroup_id', '=', 'work_groups.id');
        $query->leftJoin(DB::raw("(select
            employees.workgroup_id as workgroup_id,
            $selectRawJoin
            from attendances left join employees on employees.id = attendances.employee_id
            where attendances.attendance_in is not null and attendances.status in (1,0) and attendances.attendance_date = '$date'
            group by employees.workgroup_id) attendances"), 'attendances.workgroup_id', '=', 'work_groups.id');
        $query->orderBy('name', 'asc');
        $datas = $query->get();

        return $datas;
    }

    public function export(Request $request)
    {
        $object         = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet          = $object->getActiveSheet();

        $shifts     = $this->getShift();
        $departments= $this->getDepartmentParent();
        // Header Columne Excel
        $column     = 0;
        $row        = 1;
        $date       = Carbon::parse($request->to);
        $title      = "Daily Attendant Report On " . $date->monthName . " " . $date->year;
        
        $styleHeader = [
            'font' => [
                'bold' => true,
                'size' => 22
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $styleHeaderData = [
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true,
            ],
            'fill' => [
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color'=> [
                    'rgb'   => 'B4C6E7',
                ],
            ],
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ]
            ]
        ];
        $styleHeaderDept = [
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true,
            ],
            'fill' => [
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color'=> [
                    'rgb'   => 'FFFF00',
                ],
            ],
        ];
        $styleData = [
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true,
            ],
        ];
        $styleDatas = [
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ],
            ],
        ];
        
        $sheet->mergeCellsByColumnAndRow($column, $row, $column + 14, $row)->setCellValueByColumnAndRow($column, $row, $title);
        $sheet->setCellValueByColumnAndRow($column, $row += 2, "DATE: " . $date->toDateString());
        $sheet->mergeCellsByColumnAndRow($column, ++$row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Dept.')->getColumnDimensionByColumn($column)->setWidth(16);
        $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Workgroup Combination')->getColumnDimensionByColumn($column)->setWidth(15);
        $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'T/O')->getColumnDimensionByColumn($column)->setWidth(6);
        /* Absent Section */
        $sheet->mergeCellsByColumnAndRow(++$column, $row, $column + 4, $row)->setCellValueByColumnAndRow($column, $row, 'Absent');
        $sheet->mergeCellsByColumnAndRow($column, $row + 1, $column, $row + 1)->setCellValueByColumnAndRow($column, $row + 1, 'Alpha');
        $sheet->mergeCellsByColumnAndRow(++$column, $row + 1, $column, $row + 1)->setCellValueByColumnAndRow($column, $row + 1, 'Sakit');
        $sheet->mergeCellsByColumnAndRow(++$column, $row + 1, $column, $row + 1)->setCellValueByColumnAndRow($column, $row + 1, 'Ijin');
        $sheet->mergeCellsByColumnAndRow(++$column, $row + 1, $column, $row + 1)->setCellValueByColumnAndRow($column, $row + 1, 'Cuti');
        $sheet->mergeCellsByColumnAndRow(++$column, $row + 1, $column, $row + 1)->setCellValueByColumnAndRow($column, $row + 1, 'Etc');
        /* .Absent Section */
        /* Workshift Section */
        $sheet->mergeCellsByColumnAndRow(++$column, $row, $column + ($shifts->count() - 1), $row)->setCellValueByColumnAndRow($column, $row, 'Shift');
        foreach ($shifts as $key => $shift) {
            $sheet->mergeCellsByColumnAndRow($column, $row + 1, $column, $row + 1)->setCellValueByColumnAndRow($column, $row + 1, $shift->description);
            $column++;
        }
        /* .Workshift Section */
        $sheet->mergeCellsByColumnAndRow($column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Total Attend');
        $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Keterangan')->getColumnDimensionByColumn($column)->setWidth(30);
        
        $column_number      = 0;
        $row_number         = $row + 2;
        $number             = 1;
        $first_col_data = $column_number + 2;
        $last_col_data = 0;

        foreach ($departments as $key => $department) {
            $sheet->setCellValueByColumnAndRow($column_number, $row_number, $department->name);
            $col_sec_iteration = $column_number + 1;
            $row_sec_iteration = $row_number;

            $request->department_id = $department->id;
            $request->department_path = $department->path;
            $data       = $this->getExportData($request);
            $totalOTDept = 0;
            $totalShift = [];
            foreach ($shifts as $key => $shift) {
                $alias = strtolower(str_replace([" ", "-", ".", ","], "_", $shift->description));
                $totalShift[$alias] = 0;
            }
            // dd($data);
            foreach ($data as $key => $value) {
                $totalOTDept += $value->ot;
                $sheet->setCellValueByColumnAndRow($col_sec_iteration, $row_sec_iteration, $value->workgroup_name);
                $sheet->setCellValueByColumnAndRow($col_sec_iteration + 1, $row_sec_iteration, $value->ot ? $value->ot : 0);
                $sheet->setCellValueByColumnAndRow($col_sec_iteration + 2, $row_sec_iteration, 0);
                $sheet->setCellValueByColumnAndRow($col_sec_iteration + 3, $row_sec_iteration, 0);
                $sheet->setCellValueByColumnAndRow($col_sec_iteration + 4, $row_sec_iteration, 0);
                $sheet->setCellValueByColumnAndRow($col_sec_iteration + 5, $row_sec_iteration, 0);
                $sheet->setCellValueByColumnAndRow($col_sec_iteration + 6, $row_sec_iteration, 0);
                $col_shift_data = $column_number + 7;
                foreach ($shifts as $key => $shift) {
                    $alias = strtolower(str_replace([" ", "-", ".", ","], "_", $shift->description));
                    $sheet->setCellValueByColumnAndRow(++$col_shift_data, $row_sec_iteration, $value->$alias);
                    $totalShift[$alias] = $totalShift[$alias] + $value->$alias;
                }
                $row_sec_iteration++;
            }
            $row_number += $data->count();
            $sheet->mergeCellsByColumnAndRow($column_number, $row_number - $data->count(), $column_number, $row_number - 1)->getStyleByColumnAndRow($column_number, $row_number - $data->count(), $column_number, $row_number - 1)->applyFromArray($styleHeaderDept);
            $sheet->mergeCellsByColumnAndRow($column_number, $row_number, $column_number + 1, $row_number)->setCellValueByColumnAndRow($column_number, $row_number, "Sub Total $department->name");
            $sheet->setCellValueByColumnAndRow($column_number + 2, $row_number, $totalOTDept);
            $sheet->setCellValueByColumnAndRow($column_number + 3, $row_number, 0);
            $sheet->setCellValueByColumnAndRow($column_number + 4, $row_number, 0);
            $sheet->setCellValueByColumnAndRow($column_number + 5, $row_number, 0);
            $sheet->setCellValueByColumnAndRow($column_number + 6, $row_number, 0);
            $sheet->setCellValueByColumnAndRow($column_number + 7, $row_number, 0);
            $col_shift_total = $column_number + 7;
            $totalAllShift = 0;
            foreach ($shifts as $key => $shift) {
                $alias = strtolower(str_replace([" ", "-", ".", ","], "_", $shift->description));
                $sheet->setCellValueByColumnAndRow(++$col_shift_total, $row_number, $totalShift[$alias] ? $totalShift[$alias] : 0);
                $totalAllShift += $totalShift[$alias];
            }
            $last_col_data = $column_number + (8 + $shifts->count());
            $sheet->mergeCellsByColumnAndRow($column_number + (8 + $shifts->count()), $row_number - $data->count(), $column_number + (8 + $shifts->count()), $row_number - 1);
            $sheet->setCellValueByColumnAndRow($column_number + (8 + $shifts->count()), $row_number, $totalAllShift);
            /* Reset Total OT per dept */
            $totalOTDept = 0;
            $row_number++;
        }

        /* Styling Section */
        $sheet->getStyle('A1:O1')->applyFromArray($styleHeader);
        $sheet->getStyle('A3')->getFont()->setBold(true);
        $sheet->getStyleByColumnAndRow(0, $row, $column, $row + 1)->applyFromArray($styleHeaderData);
        $sheet->getStyleByColumnAndRow($first_col_data, $row + 2, $last_col_data, $row_number - 1)->applyFromArray($styleData);
        $sheet->getStyleByColumnAndRow(0, $row + 2, $last_col_data + 1, $row_number - 1)->applyFromArray($styleDatas);
        /* .Styling Section */

        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($data->count() > 0) {
        return response()->json([
            'status'    => true,
            'name'      => 'daily-attendant-report-' . date('d-m-Y') . '.xlsx',
            'message'   => "Success Download Daily Attendant Report Data",
            'file'      => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
        ], 200);
        } else {
        return response()->json([
            'status'     => false,
            'message'    => "Data not found",
        ], 400);
        }
    }
}