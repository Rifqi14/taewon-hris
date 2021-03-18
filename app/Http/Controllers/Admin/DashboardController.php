<?php

namespace App\Http\Controllers\admin;

use App\Models\Attendance;
use App\Models\Leave;
use App\Models\DocumentManagement;
use App\Models\EmployeeContract;
use App\Models\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\SalaryReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'dashboard'));
    }

    public function departmentdetail(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $department_name = strtoupper($request->department_name);

        //Count Data
        $query = Department::select(
            'path as department_name',
            DB::raw("(SELECT COUNT(a.id) FROM attendances a LEFT JOIN employees e ON e.id = a.employee_id LEFT JOIN departments d ON e.department_id = d.id WHERE a.attendance_date = '" . date('Y-m-d', strtotime('-1 day')) . "' and a.status = 1 and d.id = departments.id) as attend"),
            DB::raw("(SELECT COUNT(a.id) FROM attendances a LEFT JOIN employees e ON e.id = a.employee_id LEFT JOIN departments d ON e.department_id = d.id WHERE a.attendance_date = '" . date('Y-m-d', strtotime('-1 day')) . "' and a.status = -1 and d.id = departments.id) as not_attend")
        )->whereRaw("upper(path) like '%$department_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Department::select(
            'path as department_name',
            DB::raw("(SELECT COUNT(a.id) FROM attendances a LEFT JOIN employees e ON e.id = a.employee_id LEFT JOIN departments d ON e.department_id = d.id WHERE a.attendance_date = '" . date('Y-m-d', strtotime('-1 day')) . "' and a.status = 1 and d.id = departments.id) as attend"),
            DB::raw("(SELECT COUNT(a.id) FROM attendances a LEFT JOIN employees e ON e.id = a.employee_id LEFT JOIN departments d ON e.department_id = d.id WHERE a.attendance_date = '" . date('Y-m-d', strtotime('-1 day')) . "' and a.status = -1 and d.id = departments.id) as not_attend")
        )->whereRaw("upper(path) like '%$department_name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $departments = $query->get();

        $data = [];
        foreach ($departments as $department) {
            $department->no = ++$start;
            $data[] = $department;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function readcontract(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;
        $nid = $request->nid;
        $department = $request->department ? explode(',', $request->department) : null;
        $workgroup = $request->workgroup ? explode(',', $request->workgroup) : null;
        $position = $request->position ? explode(',', $request->position) : null;
        // dd($position);
        $day = $request->day;
        $month = $request->month;
        $year = $request->year;
        // dd($year);
        $config = Config::where('option', 'expired_contract')->get()->first();
        $todata = date('Y-m-d', strtotime('+' . $config->value . " Days"));
        //Count Data
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employee_contracts.description as employee_desc',
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
        $query->where('employees.status', 1);

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employee_contracts.description as employee_desc',
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
        // $query->where('employee_contracts.end_date', '>' , date('Y-m-d', strtotime('-7 day')));
        $query->where('employee_contracts.end_date', '<=', $todata);
        $query->where('employees.status', 1);
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

    public function readdocument(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d');
        $start  = $request->start;
        $length = $request->length;
        $query  = $request->search['value'];
        $sort   = $request->columns[$request->order[0]['column']]['data'];
        $dir    = $request->order[0]['dir'];
        $name   = strtoupper($request->name);

        //$config_document = Config::where('option', 'expired_document')->get()->first();
        // $todata_document = date('Y-m-d', strtotime('+' . $config_document->value . " Days"));

        //Count Data
        $query = DB::table('document_management');
        $query->select('document_management.*');
        $query->whereRaw("upper(document_management.name) like '%$name%'");
        $query->whereRaw("DATE_PART('day',expired_date::timestamp - '$now'::timestamp) <= nilai");
        $query->where('expired_date', '>=', $now);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('document_management');
        $query->select('document_management.*');
        $query->whereRaw("upper(document_management.name) like '%$name%'");
        $query->whereRaw("DATE_PART('day',expired_date::timestamp - '$now'::timestamp) <= nilai");
        $query->where('expired_date', '>=', $now);
        // $query->orderBy('expired_date', 'asc');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $document_managements = $query->get();

        $data = [];
        foreach ($document_managements as $document_management) {
            $document_management->no = ++$start;
            $document_management->link = url($document_management->file);
            $data[] = $document_management;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function index()
    {
        $config = Config::where('option', 'expired_contract')->get()->first();
        $config_document = Config::where('option', 'expired_document')->get()->first();
        $todata = date('Y-m-d', strtotime('+' . $config->value . " Days"));
        $todata_document = date('Y-m-d', strtotime('+' . $config_document->value . " Days"));
        // dd($todata);

        // $attendances = Attendance::with(['employee' => function($query){
        //     $query->where('employees.status', 1);
        // }])->where('attendances.status', 0)->count('id');
        $query = DB::table('attendances');
        $query->select('attendances.*','employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'departments.name as department_name', 'titles.name as title_name', 'work_groups.name as workgroup_name', 'overtime_schemes.scheme_name as scheme_name');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->where('attendances.status', 0);
        $query->where('employees.status', 1);
        $attendances = $query->count('attendances.id');
        $leaves      = Leave::where('status', 0)->count('id');

        $contracts = Employee::whereHas('employee_contracts', function ($q) use ($todata) {
            $q->where('end_date', '<=', $todata);
        })->where('status', 1)->count();
        // $contracts = EmployeeContract::with(['employee' => function($query){ $query->where('employees.status', 1);}])->distinct()->where('end_date', '<=', $todata)->get()->count();

        $now = Carbon::now()->format('Y-m-d');
        $documents   = DocumentManagement::whereRaw("DATE_PART('day',expired_date::timestamp - '$now'::timestamp) <= nilai")->where('expired_date', '>=', $now)->count('id');
        $yesterdayAttendance = Attendance::where('attendance_date', date('Y-m-d', strtotime('-1 day')))->where('status', 1)->count('id');
        $yesterdayNotAttend = Attendance::where('attendance_date', date('Y-m-d', strtotime('-1 day')))->where('status', -1)->count('id');
        $employeeTotal = Employee::all()->count('id');
        $dayBeforeAttend = Attendance::where('attendance_date', date('Y-m-d', strtotime('-2 day')))->where('status', 1)->count('id');
        $dayBeforeNotAttend = Attendance::where('attendance_date', date('Y-m-d', strtotime('-2 day')))->where('status', -1)->count('id');
        if (($yesterdayAttendance + $dayBeforeAttend) > 0) {
            $dayBeforeCount = round((($yesterdayAttendance - $dayBeforeAttend) / ($yesterdayAttendance + $dayBeforeAttend)) * 100);
        } else {
            $dayBeforeCount = 0;
        }
        if (($yesterdayNotAttend + $dayBeforeNotAttend) > 0) {
            $dayBeforeNotCount = round((($yesterdayNotAttend - $dayBeforeNotAttend) / ($yesterdayNotAttend + $dayBeforeNotAttend)) * 100);
        } else {
            $dayBeforeNotCount = 0;
        }
        $yesterdayOvertime = $this->yesterdayOvertimeReport();
        $yesterdayAttendancebyDept = $this->yesterdayAttendanceByDept();
        $estimateSalary = $this->estimateSalary();
        $estimateSalaryHourly = $this->estimateSalaryHourly();
        $grossSalaryYear = $this->grossSalaryInYear();
        $donutChart = $this->donutChart();

        return view('admin.dashboard', compact('attendances', 'leaves', 'contracts', 'documents', 'yesterdayAttendance', 'dayBeforeCount', 'dayBeforeNotCount', 'yesterdayAttendancebyDept', 'yesterdayOvertime', 'estimateSalary', 'grossSalaryYear', 'donutChart', 'estimateSalaryHourly'));
    }

    public function department()
    {
        $department = Department::where('parent_id', 0)->get();

        return $department;
    }

    public function yesterdayAttendanceByDept()
    {
        $departments = $this->department();

        $result = [];
        $notAttend = [];
        foreach ($departments as $value) {
            $value->attend = 0;
            $employee = Employee::leftJoin('departments', 'departments.id', '=', 'employees.department_id')->whereRaw("departments.path like '%$value->name%'")->count();
            $attend = Attendance::leftJoin('employees', 'employees.id', '=', 'attendances.employee_id')->leftJoin('departments', 'departments.id', '=', 'employees.department_id')->where('attendances.attendance_date', date('Y-m-d', strtotime('-1 day')))->whereRaw("departments.path like '%$value->name%'")->where('attendances.status', 1)->count();
            $notattend = Attendance::leftJoin('employees', 'employees.id', '=', 'attendances.employee_id')->leftJoin('departments', 'departments.id', '=', 'employees.department_id')->where('attendances.attendance_date', date('Y-m-d', strtotime('-1 day')))->whereRaw("departments.path like '%$value->name%'")->where('attendances.status', -1)->count();
            $value->attend = $attend;
            $value->notAttend = $notattend * -1;
            array_push($result, $value);
        }

        return $result;
    }

    public function grossSalaryInYear()
    {
        $result = [];
        for ($i = 1; $i <= 12; $i++) {
            $salary = SalaryReport::whereMonth('period', $i)->whereYear('period', date('Y'))->sum('gross_salary');
            array_push($result, $salary);
        }
        return $result;
    }

    public function estimateSalary()
    {
        $salary = SalaryReport::whereMonth('period', date('m'))->whereYear('period', date('Y'))->where('salary_type', 'Monthly')->sum('net_salary');

        return $salary;
    }

    public function estimateSalaryHourly()
    {
        $salary = SalaryReport::whereMonth('period', date('m'))->whereYear('period', date('Y'))->where('salary_type', 'Hourly')->sum('net_salary');

        return $salary;
    }

    public function donutChart()
    {
        $employees = Employee::all()->count();
        $attendance = Attendance::where('attendance_date', date('Y-m-d', strtotime('-1 day')))->where('status', 1)->count();
        $notattendance = Attendance::where('attendance_date', date('Y-m-d', strtotime('-1 day')))->where('status', -1)->count();
        $leave = Leave::leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id')->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id')->whereRaw("upper(leave_settings.leave_name) not like 'ALPHA'")->where('leave_logs.date', date('Y-m-d', strtotime('-1 day')))->where('leaves.status', 1)->count();
        $alpha = Leave::leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id')->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id')->where('leaves.status', 1)->whereRaw("upper(leave_settings.leave_name) like 'ALPHA'")->where('leave_logs.date', date('Y-m-d', strtotime('-1 day')))->count();
        $off = $notattendance - ($leave + $alpha) > 0 ? $notattendance - ($leave + $alpha) : 0;
        $label = ['Attendances (' . $attendance . ')', 'Alpha (' . $alpha . ')', 'Leave (' . $leave . ')', 'Off (' . $off . ')'];
        $data = [$attendance, $alpha, $leave, $off];
        $result = array('label' => $label, 'data' => $data);
        return $result;
    }

    public function yesterdayOvertimeReport()
    {
        $department = $this->department();
        $result = [];
        foreach ($department as $value) {
            $value->person = 0;
            $value->total = 0;
            $value->average = 0;

            $overtime = Attendance::leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
            $overtime->leftJoin('workingtime_details', function ($join) {
                $join->on('workingtime_details.workingtime_id', '=', 'attendances.workingtime_id');
                $join->on('workingtime_details.day', '=', 'attendances.day');
            });
            $overtime->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
            $overtime->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
            $overtime->leftJoin('workgroup_masters', 'workgroup_masters.id', '=', 'work_groups.workgroupmaster_id');
            $overtime->whereRaw("departments.path like '%$value->name%'");
            $overtime->where('attendances.attendance_date', date('Y-m-d', strtotime('-1 day')));
            $overtime->where('attendances.status', 1);
            $overtime->whereRaw("(attendances.adj_over_time > 0 or (((attendances.adj_working_time + attendances.adj_over_time) - workingtime_details.min_workhour) > 0 and (workgroup_masters.name = 'PKWT 2' or workgroup_masters.name = 'Outsourcing')))");
            $value->total = $overtime->sum(DB::raw('(attendances.adj_over_time + attendances.adj_working_time) - workingtime_details.min_workhour'));
            $value->person = $overtime->count();
            $value->average = $value->person > 0 ? $value->total / $value->person : 0;
            array_push($result, $value);
        }

        return $result;
    }
}