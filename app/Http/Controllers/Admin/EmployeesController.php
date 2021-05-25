<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\LeaveLog;
use App\Models\Grade;
use App\Models\WorkGroup;
use App\Models\Calendar;
use App\Models\Workingtime;
use App\Models\Province;
use App\Models\Region;
use App\Models\Title;
use App\Models\Department;
use App\Models\LogHistory;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\WorkgroupAllowance;
use App\Models\EmployeeSalary;
use App\Models\WorkingtimeDetail;
use App\Models\EmployeeAllowance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveDepartment;
use App\Models\LeaveDetail;
use App\Models\LeaveSetting;
use App\Models\Overtime;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;

class EmployeesController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'employees'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function readattendance(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;
        $nid = $request->nid;
        $date = date('Y-m-d', strtotime($request->date));
        $department = $request->department;
        $workgroup = $request->workgroup;
        $overtime = $request->overtime;
        $month = $request->month;
        $year = $request->year;

        //Count Data
        $query = DB::table('attendances');
        $query->select('attendances.*', 'employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'departments.name as department_name', 'titles.name as title_name', 'work_groups.name as workgroup_name');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        if ($month) {
            $query->whereMonth('attendances.attendance_date', $month);
        }
        if ($year) {
            $query->whereYear('attendances.attendance_date', $year);
        }
        if ($employee_id) {
            $query->where('attendances.employee_id', $employee_id);
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
        if ($month) {
            $query->whereMonth('attendances.attendance_date', $month);
        }
        if ($year) {
            $query->whereYear('attendances.attendance_date', $year);
        }
        if ($employee_id) {
            $query->where('attendances.employee_id', $employee_id);
        }
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
    public function selectleave(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $employee = $request->employee_id;

        //Count Data
        $query = DB::table('leave_details');
        $query->select(
            'leave_details.*',
            'leave_settings.leave_name'
        );
        $query->leftJoin('leave_details', 'leave_details.leavesetting_id', '=', 'leave_settings.id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('leave_details');
        $query->select(
            'leave_details.*',
            'leave_settings.leave_name'
        );
        $query->leftJoin('leave_details', 'leave_details.leavesetting_id', '=', 'leave_settings.id');
        $query->offset($start);
        $query->limit($length);
        $leaves = $query->get();
        dd($leaves);
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
    public function penalty(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $month = $request->montly;
        $year = $request->year;
        $employee_id = $request->employee_id;

        //Count Data
        $query = DB::table('alpha_penalties');
        $query->select(
            'alpha_penalties.*',
            'leaves.leave_setting_id',
            'leave_settings.leave_name as leave_type'
        );
        $query->leftJoin('leaves','alpha_penalties.leave_id','=','leaves.id');
        $query->leftJoin('leave_settings','leaves.leave_setting_id','=','leave_settings.id');
        $query->where('alpha_penalties.month','=', $month);
        $query->where('alpha_penalties.year','=', $year);
        $query->where('alpha_penalties.employee_id',  $employee_id);
        // $query->where('final_salary', '!=', 0);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('alpha_penalties');
        $query->select(
            'alpha_penalties.*',
            'leaves.leave_setting_id',
            'leave_settings.leave_name as leave_type'
        );
        $query->leftJoin('leaves','alpha_penalties.leave_id','=','leaves.id');
        $query->leftJoin('leave_settings','leaves.leave_setting_id','=','leave_settings.id');
        $query->where('alpha_penalties.month','=', $month);
        $query->where('alpha_penalties.year','=', $year);
        $query->where('alpha_penalties.employee_id',  $employee_id);
        // $query->where('final_salary', '!=', 0);
        if ($start) {
            $query->offset($start);
        }
        if ($length) {
            $query->limit($length);
        }
        $query->orderBy($sort, $dir);
        $penalties = $query->get();

        $data = [];
        $grand = 0;
        foreach ($penalties as $penal) {
            $penal->no = ++$start;
            $grand = $grand + $penal->penalty;
            // $penalty->basic_salary = $penalty->basic_salary;
            // $penalty->final_salary = $penalty->final_salary;
            $data[] = $penal;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    public function leave(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;
        $leave = $request->leave_id;
        $year = $request->year;
        $min_period = $request->min_period;
        $max_period = $request->max_period;

        // Count Data
        $query = LeaveDetail::select('leave_details.*', 'leave_settings.path as leave_name')->leftJoin('leave_settings', 'leave_settings.id', '=', 'leave_details.leavesetting_id')->where('leave_details.employee_id', $employee_id);
        if ($year) {
            $query->whereIn('leave_details.year_balance', $year);
        } else {
            $query->whereIn('leave_details.year_balance', [date('Y')]);
        }
        $recordsTotal = $query->count();

        // Select Pagination
        $query = LeaveDetail::select('leave_details.*', 'leave_settings.path as leave_name')->leftJoin('leave_settings', 'leave_settings.id', '=', 'leave_details.leavesetting_id')->where('leave_details.employee_id', $employee_id);
        if ($year) {
            $query->whereIn('leave_details.year_balance', $year);
        } else {
            $query->whereIn('leave_details.year_balance', [date('Y')]);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);

        $leaves = $query->get();

        $data = [];
        foreach ($leaves as $leave) {
            $leave->no      = ++$start;
            $data[]         = $leave;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    public function leavedetail(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;
        $leave = $request->leavesetting_id;

        // Count Data
        $query = Leave::select('leaves.status as status', 'leave_logs.*')->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id')->where('leaves.leave_setting_id', $leave)->where('leaves.employee_id', $employee_id);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = Leave::select('leaves.status as status', 'leave_logs.*')->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id')->where('leaves.leave_setting_id', $leave)->where('leaves.employee_id', $employee_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);

        $leaves = $query->get();

        $data = [];
        foreach ($leaves as $leave) {
            $leave->no      = ++$start;
            $data[]         = $leave;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    public function showleave(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;

        // Count Data
        $query = Leave::select(
            'leaves.*',
            'leave_settings.leave_name as leave_name',
            DB::raw("(SELECT MIN(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as start_date"),
            DB::raw("(SELECT MAX(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as finish_date")
        )->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id')->where('leaves.employee_id', $employee_id);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = Leave::select(
            'leaves.*',
            'leave_settings.leave_name as leave_name',
            DB::raw("(SELECT MIN(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as start_date"),
            DB::raw("(SELECT MAX(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as finish_date")
        )->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id')->where('leaves.employee_id', $employee_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);

        $leaves = $query->get();

        $data = [];
        foreach ($leaves as $leave) {
            $leave->no      = ++$start;
            $data[]         = $leave;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $department_id = $request->department_id;
        $list_department_multi = [69, 119, 120];
        $title_id = $request->title_id;
        $workgroup_id = $request->workgroup_id;
        $department_multi_id = $request->department_multi_id;
        $path = strtoupper($request->path);
        // $list_department_multi_id = 'Driver';
        $name = strtoupper($request->name);
        $nid = $request->nid;

        //Count Data
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employees.id as employee_id',
            'departments.id as department_id',
            'departments.name as department_name',
            'departments.path as department_path',
            'titles.id as title_id',
            'titles.name as title_name',
            'work_groups.id as workgroup_id',
            'work_groups.name as workgroup_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->where('employees.status', 1);
        if ($name) {
            $query->whereRaw("upper(employees.name) like '%$name%'");
        }
        if ($path) {
            $query->whereRaw("upper(departments.path) like '%$path%'");
        }
        if ($department_id) {
            $query->whereIn('employees.department_id', '=', $department_id);
        }
        if ($title_id) {
            $query->whereIn('employees.title_id', '=', $title_id);
        }
        if ($workgroup_id) {
            $query->whereIn('employees.workgroup_id', '=', $workgroup_id);
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employees.id as employee_id',
            'departments.id as department_id',
            'departments.name as department_name',
            'departments.path as department_path',
            'titles.id as title_id',
            'titles.name as title_name',
            'work_groups.id as workgroup_id',
            'work_groups.name as workgroup_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->where('employees.status', 1);
        if ($name) {
            $query->whereRaw("upper(employees.name) like '%$name%'");
        }
        if ($path) {
            $query->whereRaw("upper(departments.path) like '%$path%'");
        }
        if ($department_id) {
            $query->where('employees.department_id', '=', $department_id);
        }
        if ($title_id) {
            $query->where('employees.title_id', '=', $title_id);
        }
        if ($workgroup_id) {
            $query->where('employees.workgroup_id', '=', $workgroup_id);
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        $query->offset($start);
        $query->limit($length);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            $data[] = $employee;
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
        // $employee_id = DB::getPdo()->quote($request->employee_id);
        $nid = $request->nid;
        $departments = $request->department;
        $workgroup = $request->workgroup;
        $position = $request->position;
        $day = $request->day;
        $month = $request->month;
        $year = $request->year;
        $status = $request->status;
        // dd($year);

        //Count Data
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.name as title_name',
            'work_groups.id as workgroup_id',
            'work_groups.name as workgroup_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($departments) {
            $string = '';
            foreach ($departments as $department) {
                $string .= "departments.path like '%$department%'";
                if (end($departments) != $department) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($position) {
            $query->whereIn('employees.title_id', $position);
        }
        if ($status) {
            $query->whereIn('employees.status', $status);
        }
        if ($day) {
            // $query->whereDay('employees.birth_date', $day);
            $query->where(function($query3) use ($day){
                foreach ($day as $q_day) {
                  $query3->orWhereRaw("EXTRACT(DAY FROM birth_date) = $q_day");
                }
              });
        }
        if ($month) {
            // $query->whereMonth('employees.birth_date', $month);
            $query->where(function($query1) use ($month){
                foreach ($month as $q_month) {
                  $query1->orWhereRaw("EXTRACT(MONTH FROM birth_date) = $q_month");
                }
              });
        }
        if ($year) {
            // $query->whereYear('employees.birth_date', $year);
            $query->where(function($query2) use ($year){
                foreach ($year as $q_year) {
                  $query2->orWhereRaw("EXTRACT(YEAR FROM birth_date) = $q_year");
                }
              });
        } 
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.name as title_name',
            'work_groups.id as workgroup_id',
            'work_groups.name as workgroup_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($departments) {
            $string = '';
            foreach ($departments as $department) {
                $string .= "departments.path like '%$department%'";
                if (end($departments) != $department) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($position) {
            $query->whereIn('employees.title_id', $position);
        }
        if ($status) {
            $query->whereIn('employees.status', $status);
        }
        if ($day) {
            // $query->whereDay('employees.birth_date', $day);
            $query->where(function($query3) use ($day){
                foreach ($day as $q_day) {
                  $query3->orWhereRaw("EXTRACT(DAY FROM birth_date) = $q_day");
                }
              });
        }
        if ($month) {
            // $query->whereMonth('employees.birth_date', $month);
            $query->where(function($query1) use ($month){
                foreach ($month as $q_month) {
                  $query1->orWhereRaw("EXTRACT(MONTH FROM birth_date) = $q_month");
                }
              });
        }
        if ($year) {
            // $query->whereYear('employees.birth_date', $year);
            $query->where(function($query2) use ($year){
                foreach ($year as $q_year) {
                  $query2->orWhereRaw("EXTRACT(YEAR FROM birth_date) = $q_year");
                }
              });
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            $data[] = $employee;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::all();
        // $departments = Department::all();
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->orderBy('path','asc');
        $departments = $query->get();
        $workgroups = WorkGroup::all();
        $titles = Title::all();
        return view('admin.employees.index', compact('employees', 'departments', 'workgroups', 'titles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $employee = Employee::latest('id')->first();
        // $nik_system = date('Y').date('m').substr($employee->nik,6);
        // return response()->json($nik_system);
        return view('admin.employees.create');
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
            'name'                 => 'required',
            'nik'                  => 'required',
            'department_id'        => 'required',
            'title_id'             => 'required',
            'workgroup_id'         => 'required',
            'grade_id'             => 'required',
            'place_of_birth'       => 'required',
            'birth_date'           => 'required',
            'ptkp'                 => 'required',
            'phone'                => 'required',
            'address'              => 'required',
            'province_id'          => 'required',
            'region_id'            => 'required',
            'emergency_contact_no' => 'required',
            'emergency_contact_name'=> 'required',
            'calendar_id'           => 'required'
        ]);

        // $validator->sometimes('nid', 'required', function($request){
        //     return $request->status == 1;
        // });

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        // $nid = $request->nid;
        // $check = Employee::whereRaw("upper(nid) = '$nid'")->first();
        // if ($check) {
        //     return response()->json([
        //         'status'    => false,
        //         'message'   => 'NID Already Exist'
        //     ], 400);
        // }
        DB::beginTransaction();
        $employee = Employee::create([
            'name'                   => $request->name,
            'nik'                    => $request->nik,
            'department_id'          => $request->department_id,
            'title_id'               => $request->title_id,
            'workgroup_id'           => $request->workgroup_id,
            'grade_id'               => $request->grade_id,
            'npwp'                   => $request->npwp ? $request->npwp : '',
            'place_of_birth'         => $request->place_of_birth,
            'birth_date'             => $request->birth_date ? dbDate($request->birth_date) : null,
            'gender'                 => $request->gender,
            'mother_name'            => $request->mother_name ? $request->mother_name : '',
            'bpjs_tenaga_kerja'      => $request->bpjs_tenaga_kerja ? $request->bpjs_tenaga_kerja : '',
            'ptkp'                   => $request->ptkp,
            'phone'                  => $request->phone,
            'email'                  => $request->email ? $request->email : '',
            'address'                => $request->address,
            'province_id'            => $request->province_id,
            'region_id'              => $request->region_id,
            'account_no'             => $request->account_no ? $request->account_no : '',
            'account_bank'           => $request->account_bank ? $request->account_bank : '',
            'account_name'           => $request->account_name ? $request->account_name : '',
            'emergency_contact_no'   => $request->emergency_contact_no,
            'emergency_contact_name' => $request->emergency_contact_name,
            'working_time_type'      => $request->working_time_type,
            'working_time'           => $request->working_time ? $request->working_time : null,
            'calendar_id'            => $request->calendar_id,
            'tax_calculation'        => $request->tax_calculation,
            'photo'                  => '',
            'nid'                    => '',
            'status'                 => $request->status,
            'notes'                  => $request->notes ? $request->notes : '',
            'join'                   => $request->join,
            'outsourcing_id'         => $request->outsourcing_id ? $request->outsourcing_id : null,
            'overtime'               => $request->overtime,
            'spl'                    => $request->spl,
            'timeout'                => $request->timeout,
            'join_date'              => $request->join_date ? dbDate($request->join_date) : null,
            'resign_date'            => $request->resign_date ? dbDate($request->resign_date) : null,
        ]);

        if ($request->nid) {
            $employee->nid = $request->nid;
            $employee->save();
        } else {
            $employee->nid = $employee->nid_system;
            $employee->save();
        }
        // $employee->nik = $employee->nik_system;
        // $employee->save();

        if (!$employee) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message'   => $employee
            ], 400);
        }

        $query = LeaveDepartment::where('department_id', $request->department_id);
        $leavedepartment = $query->get();
        if ($leavedepartment) {
            foreach ($leavedepartment as $key => $dept) {
                $leavesetting = LeaveSetting::find($dept->leave_setting_id);
                if ($leavesetting) {
                    $leavedetail = LeaveDetail::create([
                        'leavesetting_id'   => $leavesetting->id,
                        'employee_id'       => $employee->id,
                        'balance'           => $leavesetting->balance,
                        'used_balance'      => 0,
                        'remaining_balance' => $leavesetting->balance,
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
                        DB::rollback();
                        return response()->json([
                            'status' => false,
                            'message'   => $leavedetail
                        ], 400);
                    }
                }
            }
        }
        $photo = $request->file('photo');
        if ($photo) {
            $dt = Carbon::now();
            $rd = Str::random(5);
            $path = 'assets/employee/';
            $photo->move($path, $rd . '.' . $dt->format('Y-m-d') . '.' . $photo->getClientOriginalExtension());
            $filename = $path . $rd . '.' . $dt->format('Y-m-d') . '.' . $photo->getClientOriginalExtension();
            $employee->photo = $filename;
            $employee->save();
        }
        DB::commit();
        return response()->json([
            'status' => true,
            'results' => route('employees.index'),
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
        $employee = Employee::find($id);
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $workgroup = [
            'All-In','PKWT 1','Pegawai Tetap'
        ];
        $employee = Employee::find($id);
        if ($employee) {
            // $workgroup = WorkGroup::where('id', $employee->workgroup_id)->first();
            // if ($workgroup) {
            //     $workgroup_allowance = WorkgroupAllowance::where(['workgroup_id' => $employee->workgroup_id, 'is_default' => 1])->get();
            //     if ($workgroup_allowance->count() > 0) {
            //         foreach ($workgroup_allowance as $allowance) {
            //             $allowances = array();
            //             $employeeallowance = EmployeeAllowance::where(['allowance_id' => $allowance->allowance_id, 'employee_id' => $employee->id])->get();
            //             if ($employeeallowance->count() > 0) {
            //                 foreach ($employeeallowance as $allowance) {
            //                     $allowances[] = $allowance->allowance_id;
            //                 }
            //             }
            //             if (!in_array($allowance->allowance_id, $allowances)) {
            //                 EmployeeAllowance::create([
            //                     'employee_id' => $employee->id,
            //                     'allowance_id' => $allowance->allowance_id,
            //                     'value' => $allowance->value,
            //                     'type' => $allowance->type,
            //                     'status' => 1
            //                 ]);
            //             }
            //         }
            //     }
            // }
            $grade = Grade::where('id', $employee->grade_id)->first();
            if ($grade) {
                $employeeSalary = EmployeeSalary::where('employee_id', $employee->id)->get();
                if ($employeeSalary->count() <= 0) {
                    EmployeeSalary::create([
                        'employee_id' => $id,
                        'amount' => $grade->basic_sallary,
                        'user_id' => auth()->user()->id
                    ]);
                }
            }
            return view('admin.employees.edit', compact('employee', 'workgroup'));
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
    public function update_allowances(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $employeeAllowance = EmployeeAllowance::find($request->id);
        $employeeAllowance->status = $request->status;
        $employeeAllowance->save();
        if (!$employeeAllowance) {
            return response()->json([
                'success' => false,
                'message'     => $employeeAllowance
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message'     => 'Status has been updated',
        ], 200);
    }

    /**
     * Update is penalty employee allowances
     *
     * @param Request $request
     * @return void
     */
    public function updatePenalty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'penalty'        => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $penaltyAllowance = EmployeeAllowance::find($request->id);
        $penaltyAllowance->is_penalty = $request->penalty;
        $penaltyAllowance->save();
        if (!$penaltyAllowance) {
            return response()->json([
                'success'   => false,
                'message'   => $penaltyAllowance
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Penalty has been updated'
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
            'name'                  => 'required',
            'nik'                   => 'required',
            'department_id'         => 'required',
            'title_id'              => 'required',
            'workgroup_id'          => 'required',
            'grade_id'              => 'required',
            'place_of_birth'        => 'required',
            'birth_date'            => 'required',
            'ptkp'                  => 'required',
            'phone'                 => 'required',
            'address'               => 'required',
            'province_id'           => 'required',
            'region_id'             => 'required',
            'emergency_contact_no'  => 'required',
            'emergency_contact_name'=> 'required',
            'calendar_id'           => 'required'
        ]);

        // $validator->sometimes('nid', 'required', function($request){
        //     return $request->status == 1;
        // });

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employee = Employee::with('region')
                    ->select('employees.*','employee_movements.title_id','titles.name as title_name')
                    ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                    ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                    ->whereNull('finish')
                    ->find($id);

        $user_id = Auth::user()->id;
        // account bank
        if($employee->account_bank != $request->account_bank){
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Data","Edit","Account Bank",$request->account_bank);
        }
        

        // account status
        if($employee->status != $request->status){
            $foo = $request->status;
            $status = ($foo == 0) ? "Non Active" : (($foo == 1)  ? "Active" : "other");
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Data","Edit","Status",$status);
        }

        // Join Date
        if($employee->join_date != $request->join_date){
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Data","Edit","Join Date",$request->join_date);
        }
        // Resign Date
        if($employee->resign_date != $request->resign_date){
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Data","Edit","Resign Date",$request->resign_date);
        }

        $employee = Employee::find($id);
        $employee->name                   = $request->name;
        $employee->nik                    = $request->nik;
        $employee->department_id          = $request->department_id;
        $employee->title_id               = $request->title_id;
        $employee->workgroup_id           = $request->workgroup_id;
        $employee->grade_id               = $request->grade_id;
        $employee->npwp                   = $request->npwp ? $request->npwp : '';
        $employee->place_of_birth         = $request->place_of_birth;
        $employee->birth_date             = $request->birth_date ? dbDate($request->birth_date) : null;
        $employee->gender                 = $request->gender;
        $employee->mother_name            = $request->mother_name;
        $employee->bpjs_tenaga_kerja      = $request->bpjs_tenaga_kerja;
        $employee->phone                  = $request->phone;
        $employee->email                  = $request->email ? $request->email : '';
        $employee->address                = $request->address;
        $employee->province_id            = $request->province_id;
        $employee->region_id              = $request->region_id;
        $employee->account_bank           = $request->account_bank ? $request->account_bank : '';
        $employee->account_no             = $request->account_no ? $request->account_no : '';
        $employee->account_name           = $request->account_name ? $request->account_name : '';
        $employee->emergency_contact_no   = $request->emergency_contact_no;
        $employee->emergency_contact_name = $request->emergency_contact_name;
        $employee->working_time_type      = $request->working_time_type;
        $employee->working_time           = $request->working_time ? $request->working_time : null;
        $employee->calendar_id            = $request->calendar_id;
        $employee->tax_calculation        = $request->tax_calculation;
        $employee->status                 = $request->status;
        $employee->ptkp                   = $request->ptkp;
        $employee->notes                  = $request->notes ? $request->notes : '';
        $employee->join                   = $request->join;
        $employee->outsourcing_id         = $request->outsourcing_id;
        $employee->join_date              = $request->join_date ? dbDate($request->join_date) : null;
        $employee->resign_date            = $request->resign_date ? dbDate($request->resign_date) : null;
        $employee->overtime               = $request->overtime;
        $employee->timeout                = $request->timeout;
        $employee->spl                    = $request->spl;
        $employee->save();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message'   => $employee
            ], 400);
        }

        $photo = $request->file('photo');
        if ($photo) {
            $dt = Carbon::now();
            $rd = Str::random(5);
            $path = 'assets/employee/';
            $photo->move($path, $rd . '.' . $dt->format('Y-m-d') . '.' . $photo->getClientOriginalExtension());
            $filename = $path . $rd . '.' . $dt->format('Y-m-d') . '.' . $photo->getClientOriginalExtension();
            $employee->photo = $filename ? $filename : '';
            $employee->save();
        }

        return response()->json([
            'status' => true,
            'results'   => route('employees.index'),
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
            $employee = Employee::find($id);
            $employee->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data. Data has been used in other menus. Please check the following menu references (Attendance, Leave, Salary Report)'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
    public function import(Request $request)
    {
        return view('admin.employees.import');
    }
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'         => 'required|mimes:xlsx'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $file = $request->file('file');
        try {
            $filetype     = \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch (\Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
        $data     = [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $nid = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            if ($nid) {
                $name            = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                $nocard          = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                $position_name   = strtoupper($sheet->getCellByColumnAndRow(4, $row)->getValue());
                $department_name = strtoupper($sheet->getCellByColumnAndRow(6, $row)->getValue());
                $workgroup_name  = strtoupper($sheet->getCellByColumnAndRow(7, $row)->getValue());
                $nik             = $sheet->getCellByColumnAndRow(8, $row)->getValue();
                $npwp            = $sheet->getCellByColumnAndRow(9, $row)->getValue();
                $pob             = strtoupper($sheet->getCellByColumnAndRow(10, $row)->getValue());
                if (is_numeric($sheet->getCellByColumnAndRow(11, $row)->getValue())) {
                    $birth_date = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(11, $row)->getValue()));
                } else {
                    $birth_date = date('Y-m-d', strtotime($sheet->getCellByColumnAndRow(11, $row)->getValue()));
                }
                $gender                 = strtolower($sheet->getCellByColumnAndRow(12, $row)->getValue());
                $mother_name            = $sheet->getCellByColumnAndRow(13, $row)->getValue();
                $bpjs_tenaga_kerja      = $sheet->getCellByColumnAndRow(14, $row)->getValue();
                $ptkp                   = $sheet->getCellByColumnAndRow(15, $row)->getValue();
                $phone                  = $sheet->getCellByColumnAndRow(16, $row)->getValue();
                $email                  = $sheet->getCellByColumnAndRow(17, $row)->getValue();
                $address                = $sheet->getCellByColumnAndRow(18, $row)->getValue();
                $province_name          = strtoupper($sheet->getCellByColumnAndRow(19, $row)->getValue());
                $city_name              = strtoupper($sheet->getCellByColumnAndRow(20, $row)->getValue());
                $account_bank           = $sheet->getCellByColumnAndRow(21, $row)->getValue();
                $account_no             = $sheet->getCellByColumnAndRow(22, $row)->getValue();
                $account_name           = $sheet->getCellByColumnAndRow(23, $row)->getValue();
                $emergency_contact_name = $sheet->getCellByColumnAndRow(24, $row)->getValue();
                $emergency_contact_no   = $sheet->getCellByColumnAndRow(25, $row)->getValue();
                $working_time_type      = $sheet->getCellByColumnAndRow(26, $row)->getValue();
                $working_time_name      = strtoupper($sheet->getCellByColumnAndRow(27, $row)->getValue());
                if (is_numeric($sheet->getCellByColumnAndRow(11, $row)->getValue())) {
                    $join_date = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(27, $row)->getValue()));
                } else {
                    $join_date = date('Y-m-d', strtotime($sheet->getCellByColumnAndRow(11, $row)->getValue()));
                }

                $overtime         = strtolower($sheet->getCellByColumnAndRow(29, $row)->getValue());
                $timeout          = strtolower($sheet->getCellByColumnAndRow(30, $row)->getValue());
                $join             = strtolower($sheet->getCellByColumnAndRow(31, $row)->getValue());
                $calendar_name    = strtoupper($sheet->getCellByColumnAndRow(32, $row)->getValue());
                $tax_calculation  = $sheet->getCellByColumnAndRow(33, $row)->getValue();
                $basic_salary     = $sheet->getCellByColumnAndRow(34, $row)->getValue();
                $grade_name       = strtoupper($sheet->getCellByColumnAndRow(35, $row)->getValue());
                $spl              = strtolower($sheet->getCellByColumnAndRow(36, $row)->getValue());
                $calendar         = Calendar::whereRaw("upper(name) like '%$calendar_name%'")->first();
                $workingtime      = Workingtime::whereRaw("upper(description) = '$working_time_name'")->first();
                $province         = Province::whereRaw("upper(name) like '%$province_name%'")->first();
                $city             = Region::whereRaw("upper(name) like '%$city_name%'")->first();
                $place_of_birth   = Region::whereRaw("upper(name) like '%$pob%'")->first();
                $department       = Department::whereRaw("upper(name) like '%$department_name%'")->first();
                $title            = Title::whereRaw("upper(name) like '%$position_name%'")->first();
                $grade            = Grade::whereRaw("upper(name) like '%$grade_name%'")->first();
                $workgroup        = WorkGroup::whereRaw("upper(name) like '%$workgroup_name%'")->first();
                $status           = 1;
                $error_message = '';
                if (!$calendar || !$workgroup || !$title || !$department || !$grade || !$place_of_birth || !$province || !$city || !$grade) {
                    $status = 0;
                    if (!$calendar) {
                        $error_message .= 'Calendar Not Found</br>';
                    }
                    if (!$workgroup) {
                        $error_message .= 'Work Group Not Found</br>';
                    }
                    if (!$title) {
                        $error_message .= 'Title Not Found</br>';
                    }
                    if (!$department) {
                        $error_message .= 'Department Not Found</br>';
                    }
                    if (!$place_of_birth) {
                        $error_message .= 'Place Of Birth Not Found</br>';
                    }
                    if (!$province) {
                        $error_message .= 'Province Not Found</br>';
                    }
                    if (!$city) {
                        $error_message .= 'City Not Found</br>';
                    }
                    if (!$grade) {
                        $error_message .= 'Grade Not Found</br>';
                    }
                }
                $data[] = array(
                    'index'                  => $no,
                    'nid'                    => $nid,
                    'name'                   => $name,
                    'position_name'          => $position_name,
                    'workgroup_name'         => $workgroup_name,
                    'grade_name'             => $grade_name,
                    'ptkp'                   => $ptkp,
                    'npwp'                   => $npwp,
                    'calendar_name'          => $calendar_name,
                    'department_name'        => $department_name,
                    'title_id'               => $title ? $title->id : null,
                    'nocard'                 => $nocard,
                    'department_id'          => $department ? $department->id : null,
                    'workgroup_id'           => $workgroup ? $workgroup->id : null,
                    'nik'                    => $nik,
                    'pob'                    => $pob,
                    'place_of_birth'         => $place_of_birth ? $place_of_birth->id : null,
                    'birth_date'             => $birth_date,
                    'gender'                 => $gender,
                    'mother_name'            => $mother_name,
                    'bpjs_tenaga_kerja'      => $bpjs_tenaga_kerja,
                    'phone'                  => $phone,
                    'email'                  => $email ? $email : 'anynomus@bosung.com',
                    'address'                => $address,
                    'province'               => $province_name,
                    'province_id'            => $province ? $province->id : null,
                    'city'                   => $city_name,
                    'region_id'              => $city ? $city->id : null,
                    'account_bank'           => $account_bank,
                    'account_no'             => $account_no,
                    'account_name'           => $account_name,
                    'emergency_contact_no'   => $emergency_contact_no,
                    'emergency_contact_name' => $emergency_contact_name,
                    'working_time_type'      => $working_time_type,
                    'working_time_name'      => $working_time_name,
                    'working_time'           => $workingtime ? $workingtime->id : null,
                    'join_date'              => $join_date,
                    'overtime'               => $overtime,
                    'timeout'                => $timeout,
                    'join'                   => $join,
                    'spl'                    => $spl,
                    'calendar_id'            => $calendar ? $calendar->id : null,
                    'tax_calculation'        => $tax_calculation,
                    'basic_salary'           => $basic_salary,
                    'grade_id'               => $grade ? $grade->id : null,
                    'error_message'          => $error_message,
                    'status'                 => $status
                );
                $no++;
            }
            // dd($data);
        }
        return response()->json([
            'status'     => true,
            'data'     => $data
        ], 200);
    }
    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $employees = json_decode($request->employees);
        DB::beginTransaction();
        foreach ($employees as $employee) {
            $check = Employee::where('nid', $employee->nid)->first();
            if (!$check) {
                $employeeimport = Employee::create([
                    'name'                  => $employee->name,
                    'nid'                   => '',
                    'title_id'              => $employee->title_id,
                    'department_id'         => $employee->department_id,
                    'workgroup_id'          => $employee->workgroup_id,
                    'grade_id'              => $employee->grade_id,
                    'working_time'          => $employee->working_time,
                    'npwp'                  => $employee->npwp,
                    'birth_date'            => $employee->birth_date,
                    'gender'                => $employee->gender,
                    'phone'                 => $employee->phone,
                    'email'                 => $employee->email,
                    'nik'                   => $employee->nik,
                    'address'               => $employee->address,
                    'place_of_birth'        => $employee->place_of_birth,
                    'province_id'           => $employee->province_id,
                    'account_bank'          => $employee->account_bank,
                    'account_no'            => $employee->account_no,
                    'account_name'          => $employee->account_name,
                    'emergency_contact_no'  => $employee->emergency_contact_no,
                    'emergency_contact_name'=> $employee->emergency_contact_name,
                    'working_time_type'     => $employee->working_time_type,
                    'join'                  => $employee->join,
                    'spl'                   => $employee->spl,
                    'calendar_id'           => $employee->calendar_id,
                    'join_date'             => $employee->join_date,
                    'ptkp'                  => $employee->ptkp,
                    'region_id'             => $employee->region_id,
                    'overtime'              => $employee->overtime,
                    'timeout'               => $employee->timeout,
                    'bpjs_tenaga_kerja'     => $employee->bpjs_tenaga_kerja,
                    'tax_calculation'       => $employee->tax_calculation,
                    'mother_name'           => $employee->mother_name,
                    'status'                => 1,
                    'photo'                 => 'img/no-image.png'
                ]);
                if ($employee->nid) {
                    $employeeimport->nid = $employee->nid;
                    $employeeimport->save();
                } else {
                    $employeeimport->nid = $employeeimport->nid_system;
                    $employee->save();
                }
                $query = LeaveDepartment::where('department_id', $employee->department_id);
                $leavedepartment = $query->get();
                if ($leavedepartment) {
                    foreach ($leavedepartment as $key => $dept) {
                        $leavesetting = LeaveSetting::find($dept->leave_setting_id);
                        if ($leavesetting) {
                            $leavedetail = LeaveDetail::create([
                                'leavesetting_id'   => $leavesetting->id,
                                'employee_id'       => $employeeimport->id,
                                'balance'           => $leavesetting->balance,
                                'used_balance'      => 0,
                                'remaining_balance' => $leavesetting->balance,
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
                                DB::rollback();
                                return response()->json([
                                    'status' => false,
                                    'message'   => $leavedetail
                                ], 400);
                            }
                        }
                    }
                }
                if (!$employeeimport) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'   => $employeeimport
                    ], 400);
                }
                $employeesalary = EmployeeSalary::create([
                    'employee_id' => $employeeimport->id,
                    'amount' => $employee->basic_salary,
                    'description' => 'Form Import',
                    'user_id' => auth()->user()->id
                ]);
                if (!$employeesalary) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'   => $employeesalary
                    ], 400);
                }
            }
        }
        DB::commit();
        return response()->json([
            'status' => true,
            'results' => route('employees.index'),
        ], 200);
    }

    public function export(Request $request)
    {
        $department = $request->department ? explode(',', $request->department) : null;
        $workgroup = $request->workgroup ? explode(',', $request->workgroup) : null;
        $position = $request->position ? explode(',', $request->position) : null;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $query = Employee::select('employees.*')->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
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
        if ($position) {
            $query->whereIn('employees.title_id', $position);
        }
        $employees = $query->get();

        $columns = [
            'No',
            'Nama',
            'NID',
            'Position',
            'Department',
            'Workgroup Combination',
            'No. KTP',
            'NPWP',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Nama Ibu Kandung',
            'Nomor BPJS Ketenagakerjaan',
            'PTKP',
            'No. HP',
            'E-mail',
            'Alamat Tinggal',
            'Provinsi',
            'Kota',
            'Bank',
            'No. Rekening',
            'Nama Pemegang Rekening',
            'Emergency Contact',
            'Join Date',
            'Tax',
            'Gaji Pokok',
            'Grade',
            'Insentif',
            'Jabatan',
            'Prestasi',
            'Status',
            'Overtime',
            'BPJS Hari Tua',
            'BPJS Kesehatan',
            'BPJS Pensiun',
        ];

        $header_column = 0;
        foreach ($columns as $key => $column) {
            $sheet->setCellValueByColumnAndRow($header_column, 1, $column);
            $header_column++;
        }

        $row_number = 2;
        foreach ($employees as $key => $employee) {
            $base_salary = EmployeeSalary::where('employee_id', $employee->id)->orderBy('created_at', 'desc')->first();
            $hour_salary = EmployeeAllowance::leftJoin('allowances as a', 'a.id', '=', 'employee_allowances.allowance_id')->where('a.allowance', 'like', "%Gaji Harian%")->where('employee_allowances.employee_id', $employee->id)->orderBy('employee_allowances.created_at', 'desc')->first();
            $insentif = EmployeeAllowance::leftJoin('allowances as a', 'a.id', '=', 'employee_allowances.allowance_id')->where('a.allowance', 'like', "%Insentif%")->where('employee_allowances.employee_id', $employee->id)->whereNotNull('employee_allowances.value')->orderBy('employee_allowances.created_at', 'desc')->first();
            $jabatan = EmployeeAllowance::leftJoin('allowances as a', 'a.id', '=', 'employee_allowances.allowance_id')->where('a.allowance', 'like', "%Jabatan%")->where('employee_allowances.employee_id', $employee->id)->whereNotNull('employee_allowances.value')->orderBy('employee_allowances.created_at', 'desc')->first();
            $prestasi = EmployeeAllowance::leftJoin('allowances as a', 'a.id', '=', 'employee_allowances.allowance_id')->where('a.allowance', 'like', "%Prestasi%")->where('employee_allowances.employee_id', $employee->id)->whereNotNull('employee_allowances.value')->orderBy('employee_allowances.created_at', 'desc')->first();
            $haritua = EmployeeAllowance::leftJoin('allowances as a', 'a.id', '=', 'employee_allowances.allowance_id')->where('a.allowance', 'like', "%BPJS Hari Tua%")->where('employee_allowances.employee_id', $employee->id)->orderBy('employee_allowances.created_at', 'desc')->first();
            $kesehatan = EmployeeAllowance::leftJoin('allowances as a', 'a.id', '=', 'employee_allowances.allowance_id')->where('a.allowance', 'like', "%BPJS Kesehatan%")->where('employee_allowances.employee_id', $employee->id)->orderBy('employee_allowances.created_at', 'desc')->first();
            $pensiun = EmployeeAllowance::leftJoin('allowances as a', 'a.id', '=', 'employee_allowances.allowance_id')->where('a.allowance', 'like', "%BPJS Pensiun%")->where('employee_allowances.employee_id', $employee->id)->orderBy('employee_allowances.created_at', 'desc')->first();
            // dd($haritua->status);
            // if(!$haritua){
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'error'.$haritua.'employee'.$employee->name.$employee->id
            //     ], 400);
            // }
            $sheet->setCellValue('A' . $row_number, ++$key);
            $sheet->setCellValue('B' . $row_number, $employee->name);
            $sheet->setCellValue('C' . $row_number, $employee->nid);
            $sheet->setCellValue('D' . $row_number, $employee->title->name);
            $sheet->setCellValue('E' . $row_number, $employee->department->name);
            $sheet->setCellValue('F' . $row_number, $employee->workgroup->name);
            $sheet->setCellValueExplicit('G' . $row_number, $employee->nik, PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('H' . $row_number, $employee->npwp, PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('I' . $row_number, $employee->place->name);
            $sheet->setCellValue('J' . $row_number, $employee->birth_date);
            $sheet->setCellValue('K' . $row_number, ucwords($employee->gender));
            $sheet->setCellValue('L' . $row_number, $employee->mother_name);
            $sheet->setCellValueExplicit('M' . $row_number, $employee->bpjs_tenaga_kerja, PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('N' . $row_number, $employee->ptkp);
            $sheet->setCellValueExplicit('O' . $row_number, $employee->phone, PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('P' . $row_number, $employee->email);
            $sheet->setCellValue('Q' . $row_number, $employee->address);
            $sheet->setCellValue('R' . $row_number, $employee->province->name);
            $sheet->setCellValue('S' . $row_number, $employee->region->name);
            $sheet->setCellValue('T' . $row_number, $employee->account_bank);
            $sheet->setCellValueExplicit('U' . $row_number, $employee->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('V' . $row_number, $employee->account_name);
            $sheet->setCellValue('W' . $row_number, $employee->emergency_contact_no);
            $sheet->setCellValue('X' . $row_number, $employee->join_date);
            $sheet->setCellValue('Y' . $row_number, $employee->tax_calculation);
            $salary = $base_salary ? $base_salary->amount : 0;
            if ($salary > 0) {
                $sheet->setCellValue('Z' . $row_number, $salary);
            } else {
                $sheet->setCellValue('Z' . $row_number, $hour_salary ? $hour_salary->value : 0);
            }
            $sheet->setCellValue('AA' . $row_number, $employee->grade->name);
            $sheet->setCellValue('AB' . $row_number, $insentif ? $insentif->value : 0);
            $sheet->setCellValue('AC' . $row_number, $jabatan ? $jabatan->value : 0);
            $sheet->setCellValue('AD' . $row_number, $prestasi ? $prestasi->value : 0);
            $sheet->setCellValue('AE' . $row_number, $employee->status == 1 ? 'Active':'Non Active');
            $sheet->setCellValue('AF' . $row_number, $employee->overtime);
            if($haritua){
                $sheet->setCellValue('AG' . $row_number, $haritua->status ? 'Yes': 'No');
            }else{
                $sheet->setCellValue('AG' . $row_number, 'No' );
            }
            // dd($sheet->setCellValue('AG' . $row_number, $haritua->status));
            // $sheet->setCellValue('AH' . $row_number, $kesehatan->status);
            if ($kesehatan) {
                $sheet->setCellValue('AH' . $row_number, $kesehatan->status ? 'Yes' : 'No');
            }else{
                $sheet->setCellValue('AH' . $row_number, 'No' );
            }
            // $sheet->setCellValue('AI' . $row_number, $pensiun->status);
            if ($pensiun) {
                $sheet->setCellValue('AI' . $row_number, $pensiun->status ? 'Yes' : 'No');
            }else{
                $sheet->setCellValue('AI' . $row_number, 'No' );
            }
            $row_number++;
        }

        foreach (range(0, $header_column) as $key => $value) {
            $sheet->getColumnDimensionByColumn($value)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($employees->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-employee-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Employee Data",
                'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
            ], 200);
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
        }
    }
    public function printmass(Request $request)
    {
        $id = json_decode($request->id);
        $employees = Employee::with('department')->with('workgroup')->whereIn('id', $id)->get();
        // dd($employee);
        return view('admin.employees.print', compact('employees'));
    }
}