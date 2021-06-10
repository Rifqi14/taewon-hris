<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\AllowanceDetail;
use App\Models\AllowanceRule;
use App\Models\AlphaPenalty;
use App\Models\DeliveryOrder;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Title;
use App\Models\Overtime;
use App\Models\Attendance;
use App\Models\WorkGroup;
use App\Models\LeaveSetting;
use App\Models\PphReport;
use App\Models\PphReportDetail;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeSalary;
use App\Models\GroupAllowance;
use App\Models\Leave;
use App\Models\OvertimeSchemeList;
use App\Models\SalaryReport;
use App\Models\SalaryReportDetail;
use App\Models\ThrReport;
use App\Models\Config;
// use App\Models\Title;
// use App\Models\WorkGroup;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\Rule;
use PDF;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Style;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Font;

const LABEL_BASIC_SALARY          = 'Basic Salary';
const LABEL_BASIC_ALLOWANCE       = 'Basic Salary + Allowance';
const LABEL_OVERTIME              = 'Overtime';
const LABEL_SPSI                  = 'Potongan SPSI';
const LABEL_DRIVER_ALLOWANCE      = 'Driver Allowance';
const LABEL_ALPHA_PENALTY         = 'Potongan absen';
const LABEL_ATTENDANCE_ALLOWANCE  = 'Premi Hadir';
const LABEL_POSITION_ALLOWANCE    = 'Biaya Jabatan';
const LABEL_NET_SALARY_YEAR       = 'Net Salary (Yearly)';
const LABEL_PPH_YEARLY            = 'PPh 21 (Yearly)';
const LABEL_PPH_MONTHLY           = 'Potongan PPh 21';
const LABEL_PPH_TOTAL             = 'PPH Total';
const LABEL_PPH_THR               = 'PPH 21 THR';
class SalaryReportController extends Controller
{
  function __construct()
  {
    View::share('menu_active', url('admin/' . 'salaryreport'));
  }

  /**
   * Get data from salary_reports table in database to preview in datatable
   *
   * @param Request $request
   * @return void
   */
  public function read(Request $request)
  {
    $start = $request->start;
    $length = $request->length;
    $search = strtoupper($request->search['value']);
    $sort = $request->columns[$request->order[0]['column']]['data'];
    $dir = $request->order[0]['dir'];
    $employee_id = $request->employee_id;
    $department_ids = $request->department_id ? $request->department_id : null;
    $position = $request->position ? $request->position : null;
    $workgroup_id = $request->workgroup_id ? $request->workgroup_id : null;
    $month = $request->month;
    // dd($month);
    $year = $request->year;
    $nid = $request->nid;
    $status = $request->status;
    $type = $request->type;

    //Count Data
    $query = DB::table('salary_reports');
    $query->select('salary_reports.*', 'employees.name as employee_name', 'employees.nid as nik', 'titles.name as title_name', 'departments.name as department_name', 'work_groups.name as workgroup_name', 'employees.department_id as department_id', 'employees.title_id as title_id', 'employees.workgroup_id as workgroup_id');
    $query->leftJoin('employees', 'employees.id', '=', 'salary_reports.employee_id');
    $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
    $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
    $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
    if ($employee_id) {
      $query->whereIn('salary_reports.employee_id', $employee_id);
    }
    if ($nid) {
      $query->whereRaw("employees.nid like '%$nid%'");
    }
    if ($month) {
      // $query->whereMonth('salary_reports.period', '=', $month);
      $query->where(function($query1) use ($month){
        foreach ($month as $q_month) {
          $query1->orWhereRaw("EXTRACT(MONTH FROM period) = $q_month");
        }
      });
    }
    if ($year) {
      // $query->whereYear('salary_reports.period', '=', $year);
      $query->where(function($query2) use ($year){
        foreach ($year as $q_year) {
          $query2->orWhereRaw("EXTRACT(YEAR FROM period) = $q_year");
        }
      });
    }
    if ($department_ids) {
      $string = '';
      $uniqdepartments = [];
      foreach($department_ids as $dept){
          if(!in_array($dept,$uniqdepartments)){
              $uniqdepartments[] = $dept;
          }
      }
      $department_ids = $uniqdepartments;
      foreach ($department_ids as $dept) {
        $string .= "departments.path like '%$dept%'";
        if (end($department_ids) != $dept) {
          $string .= ' or ';
        }
      }
      $query->whereRaw('(' . $string . ')');
    }
    if ($position != "") {
      $query->whereIn('employees.title_id', $position);
    }
    if ($workgroup_id != "") {
      $query->whereIn('employees.workgroup_id', $workgroup_id);
    }
    if ($status) {
      $query->whereIn('salary_reports.status', $status);
    }
    if ($type) {
      $query->whereIn('salary_reports.salary_type', $type);
    }
    $recordsTotal = $query->count();

    //Select Pagination
    $query = DB::table('salary_reports');
    $query->select('salary_reports.*', 'employees.name as employee_name', 'employees.nid as nik', 'titles.name as title_name', 'departments.name as department_name', 'work_groups.name as workgroup_name', 'employees.department_id as department_id', 'employees.title_id as title_id', 'employees.workgroup_id as workgroup_id');
    $query->leftJoin('employees', 'employees.id', '=', 'salary_reports.employee_id');
    $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
    $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
    $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
    if ($employee_id) {
      $query->whereIn('salary_reports.employee_id', $employee_id);
    }
    if ($nid) {
      $query->whereRaw("employees.nid like '%$nid%'");
    }
    if ($month) {
      // $query->whereMonth('salary_reports.period', '=', $month);
      $query->where(function($query1) use ($month){
        foreach ($month as $q_month) {
          $query1->orWhereRaw("EXTRACT(MONTH FROM period) = $q_month");
        }
      });
    }
    if ($year) {
      // $query->whereYear('salary_reports.period', '=', $year);
      $query->where(function($query2) use ($year){
        foreach ($year as $q_year) {
          $query2->orWhereRaw("EXTRACT(YEAR FROM period) = $q_year");
        }
      });
    }
    if ($department_ids) {
      $string = '';
      $uniqdepartments = [];
      foreach($department_ids as $dept){
          if(!in_array($dept,$uniqdepartments)){
              $uniqdepartments[] = $dept;
          }
      }
      $department_ids = $uniqdepartments;
      foreach ($department_ids as $dept) {
        $string .= "departments.path like '%$dept%'";
        if (end($department_ids) != $dept) {
          $string .= ' or ';
        }
      }
      $query->whereRaw('(' . $string . ')');
    }
    if ($position != "") {
      $query->whereIn('employees.title_id', $position);
    }
    if ($workgroup_id != "") {
      $query->whereIn('employees.workgroup_id', $workgroup_id);
    }
    if ($status) {
      $query->whereIn('salary_reports.status', $status);
    }
    if ($type) {
      $query->whereIn('salary_reports.salary_type', $type);
    }
    $query->offset($start);
    $query->limit($length);
    $query->orderBy('period', $dir);
    $query->orderBy($sort, $dir);
    $reports = $query->get();
    $data = [];
    foreach ($reports as $report) {
      $report->no = ++$start;
      $report->net_salary = number_format($report->net_salary, 0, ',', '.');
      $report->period = changeDateFormat('F - Y', $report->period);
      $data[] = $report;
    }
    return response()->json([
      'draw' => $request->draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsTotal,
      'data' => $data
    ], 200);
  }

  /**
   * Get data from salary_reports where is approved table in database to preview in datatable
   *
   * @param Request $request
   * @return void
   */
  public function readapproval(Request $request)
  {
    $start = $request->start;
    $length = $request->length;
    $search = strtoupper($request->search['value']);
    $sort = $request->columns[$request->order[0]['column']]['data'];
    $dir = $request->order[0]['dir'];
    $employee_id = $request->employee_id;
    $department_id = $request->department_id;
    $position = $request->position;
    $workgroup_id = $request->workgroup_id;
    $month = $request->month;
    $year = $request->year;
    $nid = $request->nid;
    $status = $request->status;
    $type = $request->type;

    //Count Data
    $query = DB::table('salary_reports');
    $query->select('salary_reports.*', 'employees.name as employee_name', 'employees.nid as nik', 'titles.name as title_name', 'departments.name as department_name', 'work_groups.name as workgroup_name', 'employees.department_id as department_id', 'employees.title_id as title_id', 'employees.workgroup_id as workgroup_id');
    $query->leftJoin('employees', 'employees.id', '=', 'salary_reports.employee_id');
    $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
    $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
    $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
    $query->where('salary_reports.status', 0);

    $recordsTotal = $query->count();

    //Select Pagination
    $query = DB::table('salary_reports');
    $query->select('salary_reports.*', 'employees.name as employee_name', 'employees.nid as nik', 'titles.name as title_name', 'departments.name as department_name', 'work_groups.name as workgroup_name', 'employees.department_id as department_id', 'employees.title_id as title_id', 'employees.workgroup_id as workgroup_id');
    $query->leftJoin('employees', 'employees.id', '=', 'salary_reports.employee_id');
    $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
    $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
    $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
    $query->where('salary_reports.status', 0);


    $query->offset($start);
    $query->limit($length);
    $query->orderBy('id', 'asc');
    $reports = $query->get();
    $data = [];
    foreach ($reports as $report) {
      $report->no = ++$start;
      $report->net_salary = number_format($report->net_salary, 0, ',', '.');
      $report->period = changeDateFormat('F - Y', $report->period);
      $data[] = $report;
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
    $titles = Title::all();

    return view('admin.salaryreport.index', compact('employees', 'departments', 'workgroups', 'titles'));
  }

  /**
   * Display a listing of the approved salary report
   *
   * @return void
   */
  public function indexapproval()
  {
    return view('admin.salaryreport.indexapproval');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Apporve salary report
   *
   * @param Request $request
   * @return void
   */
  public function approve(Request $request)
  {
    if ($request->checksalary) {
      $approves = $request->checksalary;
      DB::beginTransaction();
      foreach ($approves as $id) {
        $salary = SalaryReport::find($id);
        $salary->status = 1;
        $salary->save();
        if (!$salary) {
          DB::rollBack();
          return response()->json([
            'status'      => false,
            'message'     => $salary
          ], 400);
        }
      }
      DB::commit();
      return response()->json([
        'status'     => true,
        'message'     => 'Salary report was successfully approved',
      ], 200);
    }
  }

  /**
   * Change salary_report status to waiting approval
   *
   * @param Request $request
   * @return void
   */
  public function waitingapproval(Request $request)
  {
    $salary = SalaryReport::find($request->id);
    DB::beginTransaction();
    $salary->status = 0;
    $salary->save();
    if (!$salary) {
      DB::rollBack();
      return response()->json([
        'status'      => false,
        'message'     => $salary
      ], 400);
    }
    DB::commit();
    return response()->json([
      'status'     => true,
      'results'   => route('salaryreport.index'),
    ], 200);
  }

  /**
   * Check salary report already exists or not with give param
   *
   * @param int $month
   * @param int $year
   * @param unsignedBigInt $employee
   * @return object
   */
  public function check_periode($month, $year, $employee)
  {
    $exists = SalaryReport::whereMonth('period', '=', $month)->whereYear('period', '=', $year)->where('employee_id', '=', $employee)->first();

    return $exists;
  }

  /**
   * Get employee calendar from employee_calendars table
   *
   * @param int $id
   * @return array
   */
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
   * Get base salary from choosen employee
   *
   * @param int $id
   * @return object
   */
  public function get_employee_salary($id)
  {
    $basesalary = EmployeeSalary::where('employee_id', '=', $id)->orderBy('created_at', 'desc')->first();

    return $basesalary;
  }

  /**
   * Get employee allowance where is additional
   *
   * @param int $id
   * @param int $month
   * @param int $year
   * @return array
   */
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
    $query->groupBy('group_allowances.name', 'employee_allowances.is_penalty', 'allowances.group_allowance_id', 'employee_allowances.type');
    $query->orderByRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) desc");
    $allowances = $query->get();

    $data = [];
    foreach ($allowances as $allowance) {
      $data[] = $allowance;
    }
    
    return $data;
  }
  public function get_detail_allowance($id, $month, $year, $allowance)
  {
    $allowance_id = [];
    $allowancedetails = AllowanceDetail::where('allowance_id', $allowance)->get();
    foreach($allowancedetails as $allowancedetail){
      $allowance_id[] = $allowancedetail->allowancedetail_id;
    }
    $query = DB::table('employee_allowances');
    // $query->select('employee_allowances.*', 'allowances.allowance as description', 'allowances.group_allowance_id');
    $query->selectRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) as value_deduction, group_allowances.name as description, employee_allowances.is_penalty as is_penalty, allowances.group_allowance_id as group_allowance_id, employee_allowances.type as type, max(allowances.allowance) as allowance_name");
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    $query->leftJoin('group_allowances', 'group_allowances.id', 'allowances.group_allowance_id');
    $query->where('employee_allowances.employee_id', '=', $id);
    $query->where('employee_allowances.month', '=', $month);
    $query->where('employee_allowances.year', '=', $year);
    $query->whereIn('employee_allowances.allowance_id', $allowance_id);
    $query->where('employee_allowances.status', '=', 1);
    $query->where('allowance_categories.type', '=', 'additional');
    $query->where('employee_allowances.type', '!=', 'automatic');
    $query->groupBy('group_allowances.name', 'employee_allowances.is_penalty', 'allowances.group_allowance_id', 'employee_allowances.type');
    $query->orderByRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) desc");
    $allowances = $query->get();

    $data = [];
    foreach ($allowances as $allowance) {
      $data[] = $allowance;
    }

    return $data;
  }
  

  /**
   * Get employee allowance where is deduction
   *
   * @param int $id
   * @param int $month
   * @param int $year
   * @return array
   */
  public function get_deduction($id, $month, $year)
  {
    $query = DB::table('employee_allowances');
    // $query->select('employee_allowances.*', 'allowances.allowance as description', 'allowances.group_allowance_id');
    $query->selectRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) as value, group_allowances.name as description, employee_allowances.is_penalty as is_penalty, allowances.group_allowance_id as group_allowance_id,allowances.id as allowance_id, employee_allowances.type as type, max(allowances.allowance) as allowance_name, allowances.formula_bpjs as bpjs");
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    $query->leftJoin('group_allowances', 'group_allowances.id', 'allowances.group_allowance_id');
    $query->where('employee_allowances.employee_id', '=', $id);
    $query->where('employee_allowances.month', '=', $month);
    $query->where('employee_allowances.year', '=', $year);
    $query->where('employee_allowances.status', '=', 1);
    $query->where('allowance_categories.type', '=', 'deduction');
    $query->where('employee_allowances.type', '!=', 'automatic');
    $query->groupBy('group_allowances.name', 'employee_allowances.is_penalty', 'allowances.group_allowance_id', 'employee_allowances.type','allowances.id', 'allowances.formula_bpjs');
    $allowances = $query->get();

    $data = [];
    foreach ($allowances as $allowance) {
      $data[] = $allowance;
    }

    return $data;
  }

  /**
   * Get employee overtime
   *
   * @param int $id
   * @param int $month
   * @param int $year
   */
  public function get_overtime($id, $month, $year)
  {
    $query = DB::table('overtimes');
    $query->selectRaw("SUM(hour) as hour, sum(final_salary) as final_salary, amount");
    $query->where('overtimes.employee_id', '=', $id);
    $query->where('overtimes.final_salary', '>', 0);
    $query->where('month', '=', $month);
    $query->where('year', '=', $year);
    $query->groupBy('overtimes.amount');
    $salaries = $query->get();

    $data = [];
    foreach ($salaries as $salary) {
      $data[] = $salary;
    }

    return $data;
  }
  public function get_salarydeduction($id, $month, $year)
  {
    $query = DB::table('salary_deductions');
    $query->select('salary_deductions.*');
    $query->where('employee_id', '=', $id);
    $query->whereMonth('date', '=', $month);
    $query->whereYear('date', '=', $year);
    $salary_deductions = $query->get();

    $data = [];
    foreach ($salary_deductions as $salary_deduction) {
      $data[] = $salary_deduction;
    }

    return $data;
  }
  // public function getAllowanceProrate($id, $month, $year)
  // {
  //   $query = DB::table('employee_allowances');
  //   $query->selectRaw("sum(employee_allowances.value::numeric) as allowance_value");
  //   $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
  //   $query->where('employee_allowances.employee_id', '=', $id);
  //   $query->where('allowances.prorate','=', 'Yes');
  //   $query->where('employee_allowances.month', '=', $month);
  //   $query->where('employee_allowances.year', '=', $year);
  //    $query->where('employee_allowances.type', '!=', 'automatic');
  //   $allowances = $query->get();

  //   $data = [];
  //   foreach ($allowances as $allowance) {
  //     $data[] = $allowance;
  //   }

  //   return $data;
  // }

  public function getAllowanceProrate($id, $month, $year)
  {
    $query = DB::table('employee_allowances');
    // $query->select('employee_allowances.*', 'allowances.allowance as description', 'allowances.group_allowance_id');
    $query->selectRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) as allowance_value, group_allowances.name as description, employee_allowances.is_penalty as is_penalty, allowances.group_allowance_id as group_allowance_id, employee_allowances.type as type, max(allowances.allowance) as allowance_name");
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    $query->leftJoin('group_allowances', 'group_allowances.id', 'allowances.group_allowance_id');
    $query->where('employee_allowances.employee_id', '=', $id);
    $query->where('employee_allowances.month', '=', $month);
    $query->where('employee_allowances.year', '=', $year);
    $query->where('allowances.prorate', '=', 'Yes');
    $query->where('employee_allowances.status', '=', 1);
    $query->where('employee_allowances.type', '!=', 'automatic');
    $query->groupBy('group_allowances.name', 'employee_allowances.is_penalty', 'allowances.group_allowance_id', 'employee_allowances.type');
    $allowances = $query->get();

    $data = [];
    foreach ($allowances as $allowance) {
      $data[] = $allowance;
    }

    return $data;
  }
  public function getAllowanceDetail($id, $month, $year)
  {
    $query = DB::table('employee_allowances');
    // $query->select('employee_allowances.*', 'allowances.allowance as description', 'allowances.group_allowance_id');
    $query->selectRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) as allowance_value, group_allowances.name as description, employee_allowances.is_penalty as is_penalty, allowances.group_allowance_id as group_allowance_id, employee_allowances.type as type, max(allowances.allowance) as allowance_name");
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    $query->leftJoin('group_allowances', 'group_allowances.id', 'allowances.group_allowance_id');
    $query->where('employee_allowances.employee_id', '=', $id);
    $query->where('employee_allowances.month', '=', $month);
    $query->where('employee_allowances.year', '=', $year);
    // $query->where('allowances.prorate', '=', 'Yes');
    $query->where('employee_allowances.status', '=', 1);
    $query->where('employee_allowances.type', '!=', 'automatic');
    $query->groupBy('group_allowances.name', 'employee_allowances.is_penalty', 'allowances.group_allowance_id', 'employee_allowances.type');
    $allowances = $query->get();

    $data = [];
    foreach ($allowances as $allowance) {
      $data[] = $allowance;
    }

    return $data;
  }
  /**
   * Get employee attendance
   *
   * @param int $id
   * @param int $month
   * @param int $year
   * @return array
   */
  public function get_attendance($id, $month, $year)
  {
    $query = DB::table('attendances');
    $query->select('attendances.*');
    $query->where('attendances.employee_id', '=', $id);
    $query->where('attendances.status', '=', 1);
    $query->where('month', '=', $month);
    $query->where('year', '=', $year);
    $query->where('day', '!=', 'Off');
    $attendances = $query->get();

    $data = [];
    foreach ($attendances as $attendance) {
      $data[] = $attendance;
    }

    return $data;
  }

  public function get_attendance_allowance($id, $month, $year)
  {
    $employee_calendar = $this->employee_calendar($id);
    $amonth = dateInAMonth($month, $year);
    $work_date = [];
    // To Create Workdate
    foreach ($amonth as $key => $value) {
      if (!in_array($value, $employee_calendar)) {
        $work_date[] = $value;
      }
    }

    $attendance = $this->get_attendance($id, $month, $year);

    $employee_allowance = EmployeeAllowance::with('allowance')->where('status', 1)->where('month', $month)->where('year', $year)->where('employee_id', $id)->whereHas('allowance', function ($q) {
      $q->where('category', 'like', 'tunjanganKehadiran');
    })->first();

    if($employee_allowance){
      $qty_absent = abs(count($work_date) - count($attendance));
      $allowance = AllowanceRule::with('allowance')->where('qty_absent', '=', $qty_absent >= 2 ? 2 : $qty_absent)->first();
      $attendance_allowance = 0;
      $basesalary = $this->get_employee_salary($id);
      if ($allowance->qty_allowance > 0 && $basesalary) {
        $attendance_allowance = $allowance->qty_allowance * ($basesalary->amount / 30);
      }
      return $attendance_allowance;
    }else{
      return 0;
    }
  }

  public function get_leave($id, $month, $year)
  {
    $query = DB::table('leaves');
    $query->select('leaves.*');
    $query->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id');
    $query->where('leaves.employee_id', '=', $id);
    $query->where('leaves.status', '=', 1);
    $query->where('leave_logs.type', '=', 'fullday');
    $query->whereMonth('leave_logs.date', '=', $month);
    $query->whereYear('leave_logs.date', '=', $year);
    $leaves = $query->get();

    $data = [];
    foreach ($leaves as $leave) {
      $data[] = $leave;
    }

    return $data;
  }

  public function get_alpha($id, $month, $year)
  {
    $query = DB::table('leaves');
    $query->select('leaves.*');
    $query->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id');
    $query->leftJoin('leave_logs', 'leaves.id', '=', 'leave_logs.leave_id');
    $query->whereMonth('leave_logs.date', $month);
    $query->whereYear('leave_logs.date', $year);
    $query->where('leave_settings.description', 0);
    $query->where('leaves.employee_id', $id);
    $query->where('leaves.status', 1);
    $leaves = $query->get();

    return $leaves->count();
  }

  public function getAlphaData($id, $month, $year)
  {
    $query    = AlphaPenalty::where('employee_id', $id)->where('month', $month)->where('year', $year);

    return $query;
  }

  public function gross_salary($id)
  {
    $query = DB::table('salary_reports');
    $query->select('salary_reports.*');
    $query->leftJoin('salary_report_details', 'salary_report_details.salary_report_id', '=', 'salary_reports.id');
    $query->where('salary_report_details.salary_report_id', '=', $id);
    $query->where('salary_report_details.type', '=', 1);
    $gross = $query->sum('salary_report_details.total');

    return $gross;
  }

  public function deduction_salary($id)
  {
    $query = DB::table('salary_reports');
    $query->select('salary_reports.*');
    $query->leftJoin('salary_report_details', 'salary_report_details.salary_report_id', '=', 'salary_reports.id');
    $query->where('salary_report_details.salary_report_id', '=', $id);
    $query->where('salary_report_details.type', '=', 0);
    $deduction = $query->sum('salary_report_details.total');

    return $deduction;
  }

  public function get_pkp($id)
  {
    $query = DB::table('ptkps');
    $query->select('ptkps.*');
    $query->where('ptkps.key', '=', $id);
    $ptkp = $query->first();

    return $ptkp;
  }

  public function get_driver_allowance($id, $month, $year)
  {
    $query = DB::table('driver_allowance_lists');
    $query->select('driver_allowance_lists.*');
    $query->where('driver_id', $id);
    $query->where('month', $month);
    $query->where('year', $year);

    return $query->sum('total_value');
  }

  public function getLatestId()
  {
    $read = SalaryReport::max('id');
    return $read + 1;
  }

  public function getAllDeliveryOrder($driver, $month, $year)
  {
    $deliveryOrder = DeliveryOrder::whereMonth('date', '=', $month)->whereYear('date', '=', $year)->where('driver_id', '=', $driver)->get();

    return $deliveryOrder;
  }

  public function getPPhAllowance($id, $month, $year)
  {
    $employee_allowance = EmployeeAllowance::with('allowance')->where('status', 1)->where('month', $month)->where('year', $year)->where('employee_id', $id)->whereHas('allowance', function ($q) {
      $q->where('category', 'like', 'potonganPph');
    })->first();

    return $employee_allowance ? true : false;
  }

  public function getThrReport($month, $year, $id){
    $thr = ThrReport::where('month', $month)->where('year', $year)->where('employee_id', $id)->first();

    return $thr;
  }

  public function getPenaltyAllowance($id, $month, $year)
  {
    $query = EmployeeAllowance::select('employee_allowances.*', 'allowances.allowance as description');
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJOin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    $query->where('employee_allowances.employee_id', $id);
    $query->where('employee_allowances.month', $month);
    $query->where('employee_allowances.year', $year);
    $query->where('employee_allowances.status', 1);
    $query->where('employee_allowances.is_penalty', 0);
    $query->where('allowance_categories.type', 'additional');
    $query->where('employee_allowances.type', '!=', 'automatic');
    $allowances = $query->get();

    return $allowances->sum('value');
  }

  public function generateByDepartment($department, $month, $year, $user)
  {
    $employee = Employee::select('employees.*')->leftJoin('departments', 'departments.id', '=', 'employees.department_id')->where('employees.status', 1);
    $string = '';
    foreach ($department as $dept) {
      $string .= "departments.path like '%$dept%'";
      if (end($department) != $dept) {
        $string .= ' or ';
      }
    }
    $employee->whereRaw('(' . $string . ')');
    $employees = $employee->get();
    if ($employees->count() > 0) {
      foreach ($employees as $employee) {
        $exists = $this->check_periode($month, $year, $employee->id);
        if ($exists) {
          $delete = $exists->delete();
        }
        $dt            = Carbon::createFromFormat('Y-m', $year . '-' . $month);
        $checkDate     = changeDateFormat('Y-m-d', $dt->endOfMonth()->toDateString() . '-' . $month . '-' . $year);
        $checkJoinDate = Employee::where('employees.status', 1)->where('employees.join_date', '<=', $checkDate)->find($employee->id);
        if ($checkJoinDate) {
          $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);
  
          $salaryreport = SalaryReport::create([
            'id'            => $this->getLatestId(),
            'employee_id'   => $employee->id,
            'created_by'    => $user,
            'period'        => $period,
            'status'        => -1
          ]);
  
          if ($salaryreport) {
            $basesalary           = $this->get_employee_salary($employee->id);
            $penaltyallowance     = $this->getPenaltyAllowance($employee->id, $month, $year);
            $allowance            = $this->get_additional_allowance($employee->id, $month, $year);
            $alphaPenalty         = $this->getAlphaData($employee->id, $month, $year);
            $deduction            = $this->get_deduction($employee->id, $month, $year);
            $overtime             = $this->get_overtime($employee->id, $month, $year);
            $attendance           = $this->get_attendance($employee->id, $month, $year);
            $driverallowance      = $this->get_driver_allowance($employee->id, $month, $year);
            $leave                = $this->get_leave($employee->id, $month, $year);
            $ptkp                 = $this->get_pkp($employee->ptkp);
            $alpha                = $this->get_alpha($employee->id, $month, $year);
            $attendance_allowance = $this->get_attendance_allowance($employee->id, $month, $year);
            $pph                  = $this->getPPhAllowance($employee->id, $month, $year);
            $allowance_prorates   = $this->getAllowanceProrate($employee->id, $month, $year);
            $salary_deduction     = $this->get_salarydeduction($employee->id, $month, $year);
            if ($basesalary) {
              $periode_salary   = changeDateFormat('Y-m', $year . '-' . $month);
              $join_date        = changeDateFormat('Y-m', $employee->join_date);
              $resign_date      = changeDateFormat('Y-m', $employee->resign_date);
              $daily_salary     = $basesalary->amount / 30;
              $attendance_count = count($attendance);
              $readConfigs      = Config::where('option', 'setting_prorate')->first();
              $prorate_type     = Config::where('option', 'type_prorate')->first();

              /**Jika Config setting prorate sama dengan full */
              if ($readConfigs->value == 'full') {

                $date1 = $employee->join_date;
                $date2 = $employee->resign_date;

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years  = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days   = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

                /**Jika tipe prorate sama dengan basic_allowance */
                if ($prorate_type == 'basic_allowance') {
                  foreach ($allowance_prorates as $key => $allowance) {
                    /**Jika Join date sama dengan priode salary  */
                    if ($join_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika Join date sama dengan priode salary  */
                    /**Jika join date dan resign date sama dengan priode salary*/
                    if ($join_date && $resign_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika join date dan resign date sama dengan priode salary*/
                    /** Jika join date dan resign date ada*/
                    if ($join_date && $resign_date) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /** End Jika join date dan resign date ada*/
                  }
                  /**End Jika tipe prorate sama dengan basic_allowance */
                } else {
                  /**Jika join date sama dengan periode_salary*/
                  if ($join_date == $periode_salary) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /**End Jika join date sama dengan periode_salary*/
                  /** Jika ada join date dan resign date sama dengan priode salary*/
                  if ($join_date && $resign_date == $periode_salary) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /**End Jika ada join date dan resign date sama dengan priode salary*/
                  /** Jika ada join date dan resign date*/
                  if ($join_date && $resign_date) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /** End Jika ada join date dan resign date*/
                }
                /**End Jika Config setting prorate sama dengan full */
              } else {
                SalaryReportDetail::create([
                  'salary_report_id' => $salaryreport->id,
                  'employee_id'      => $employee->id,
                  'description'      => LABEL_BASIC_SALARY,
                  'total'            => $resign_date == $periode_salary ? (date("d", strtotime($employee->resign_date . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                  'type'             => 1,
                  'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                  'is_added'         => 'NO'
                ]);
              }
              $salaryreport->salary_type = $basesalary->amount == 0 ? 'Hourly' : 'Monthly';
              $salaryreport->save();
            }
            if ($allowance) {
              foreach ($allowance as $key => $value) {
                if ($value->group_allowance_id) {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->description,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 1,
                    'status'            => 'Additional Allowance',
                    'group_allowance_id'=> $value->group_allowance_id,
                    'is_added'          => 'NO'
                  ]);
                } else {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->allowance_name,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 1,
                    'status'            => 'Additional Allowance',
                    'is_added'          => 'NO'
                  ]);
                }
              }
            }
            if ($deduction) {
              foreach ($deduction as $key => $value) {
                if ($value->group_allowance_id) {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->description,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 0,
                    'status'            => 'Deduction Allowance',
                    'group_allowance_id'=> $value->group_allowance_id,
                    'is_added'          => 'NO'
                  ]);
                } else {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->allowance_name,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 0,
                    'status'            => 'Deduction Allowance',
                    'is_added'          => 'NO'
                  ]);
                }
              }
            }
            if($salary_deduction){
              foreach ($salary_deduction as $key => $value) {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->description,
                    'total'             => $value->nominal,
                    'type'              => 0,
                    'status'            => 'Salary Deduction',
                    'is_added'          => 'NO'
                  ]);
              }
            }
            if ($overtime && $employee->overtime == 'yes') {
              foreach ($overtime as $key => $over) {
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_OVERTIME . " " . $over->amount * 100 . "%",
                  'total'             => $over->final_salary,
                  'type'              => 1,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
              }
            }
            if ($employee->join == 'yes') {
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_SPSI,
                'total'             => 20000,
                'type'              => 0,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            if ($employee->department->driver == 'yes' && $driverallowance > 0) {
              $spsi = SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_DRIVER_ALLOWANCE,
                'total'             => $driverallowance,
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            if ($attendance_allowance) {
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_ATTENDANCE_ALLOWANCE,
                'total'             => $attendance_allowance,
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            if ($alphaPenalty->sum('penalty') > 0 && $employee->workgroup->penalty == 'Basic' && $basesalary) {
              $alpha_penalty = SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_ALPHA_PENALTY,
                'total'             => -1 * $alphaPenalty->sum('penalty'),
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            
            $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
            $salaryreport->deduction    = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
            $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
            $salaryreport->save();
            if ($alphaPenalty->count() > 0 && $employee->workgroup->penalty == 'Gross' && $basesalary) {
              $alpha_penalty = SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_ALPHA_PENALTY,
                'total'             => -1 * ($alphaPenalty->count() * (($salaryreport->gross_salary - $penaltyallowance) / 30)),
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
              $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
              $salaryreport->save();
            }
            if ($pph) {
              if (!$ptkp) {
                return array(
                  'status'    => false,
                  'message'   => 'PTKP for this employee name ' . $employee->name . ' not found. Please set PTKP or uncheck PPh 21 allowance for this generate month.'
                );
              }
              $gross                             = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
              $deduction                         = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
              $positionAllowance                 = getPositionAllowance($gross);
              $grossSalaryAfterPositionAllowance = getGrossSalaryAfterPositionAllowance($gross, $positionAllowance);
              $multiplierMonth                   = getMultiplierMonth($employee->join_date);
              $grossSalaryPerYear                = getGrossSalaryPerYear($grossSalaryAfterPositionAllowance, $multiplierMonth);
              $pkps                              = getPKP($grossSalaryPerYear, $ptkp->value);
              $pph21Yearly                       = getPPH21Yearly($pkps, $employee->npwp);

              //PPH Gaji + THR
              $grossSalaryJoinMonth   = getGrossSalaryJoinMonth($gross, $multiplierMonth);
              $getThr                 = $this->getThrReport($month, $year, $employee->id);
              

              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_POSITION_ALLOWANCE,
                'total'             => ($positionAllowance) > 0 ? $positionAllowance : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_NET_SALARY_YEAR,
                'total'             => ($grossSalaryPerYear) > 0 ? $grossSalaryPerYear : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_PPH_YEARLY,
                'total'             => ($pph21Yearly) > 0 ? $pph21Yearly : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_PPH_MONTHLY,
                'total'             => ($pph21Yearly / $multiplierMonth) > 0 ? $pph21Yearly / $multiplierMonth : 0,
                'type'              => 0,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              if($getThr){
                $total                  = getTotal($grossSalaryJoinMonth, $getThr->amount);
                $totalPositionAllowance = getTotalPositionAllowance($total);
                $netSalaryThr           = getNetSalaryThr($total, $totalPositionAllowance);
                $pkpThr                 = getPkpThr($netSalaryThr, $ptkp->value);
                $tarifThr               = getTarifThr($pkpThr);

                SalaryReportDetail::create([
                  'salary_report_id' => $salaryreport->id,
                  'employee_id'      => $employee->id,
                  'description'      => LABEL_PPH_THR,
                  'total'            => $tarifThr > 0 ? $tarifThr - $pph21Yearly : 0,
                  'type'             => 0,
                  'status'           => 'Draft',
                  'is_added'         => 'NO'
                ]);
              }
              
            }
            $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
            $salaryreport->deduction    = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
            $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
            $salaryreport->save();
          } else {
            return array(
              'status'    => false,
              'message'   => $salaryreport
            );
          }
        }
      }
    } else {
      return array(
        'status'    => true,
        'message'   => "Data not found"
      );
    }
    return array(
      'status'    => true,
      'message'   => "salary report generated by department successfully"
    );
  }

  public function generateByPosition($position, $month, $year, $user)
  {
    $employees = Employee::select('employees.*')->whereIn('title_id', $position)->where('employees.status', 1)->get();
    if (!$employees->isEmpty()) {
      foreach ($employees as $employee) {
        $exists = $this->check_periode($month, $year, $employee->id);
        if ($exists) {
          $delete = $exists->delete();
        }

        $dt            = Carbon::createFromFormat('Y-m', $year . '-' . $month);
        $checkDate     = changeDateFormat('Y-m-d', $dt->endOfMonth()->toDateString() . '-' . $month . '-' . $year);
        $checkJoinDate = Employee::where('employees.status', 1)->where('employees.join_date', '<=', $checkDate)->find($employee->id);
        if ($checkJoinDate) {
          $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);
  
          $salaryreport = SalaryReport::create([
            'id'            => $this->getLatestId(),
            'employee_id'   => $employee->id,
            'created_by'    => $user,
            'period'        => $period,
            'status'        => -1
          ]);
          if ($salaryreport) {
            $basesalary           = $this->get_employee_salary($employee->id);
            $penaltyallowance     = $this->getPenaltyAllowance($employee->id, $month, $year);
            $allowance            = $this->get_additional_allowance($employee->id, $month, $year);
            $alphaPenalty         = $this->getAlphaData($employee->id, $month, $year);
            $deduction            = $this->get_deduction($employee->id, $month, $year);
            $overtime             = $this->get_overtime($employee->id, $month, $year);
            $attendance           = $this->get_attendance($employee->id, $month, $year);
            $leave                = $this->get_leave($employee->id, $month, $year);
            $driverallowance      = $this->get_driver_allowance($employee->id, $month, $year);
            $ptkp                 = $this->get_pkp($employee->ptkp);
            $alpha                = $this->get_alpha($employee->id, $month, $year);
            $attendance_allowance = $this->get_attendance_allowance($employee->id, $month, $year);
            $pph                  = $this->getPPhAllowance($employee->id, $month, $year);
            $allowance_prorates   = $this->getAllowanceProrate($employee->id, $month, $year);
            $salary_deduction     = $this->get_salarydeduction($employee->id, $month, $year);
            if ($basesalary) {
              $periode_salary   = changeDateFormat('Y-m', $year . '-' . $month);
              $join_date        = changeDateFormat('Y-m', $employee->join_date);
              $resign_date      = changeDateFormat('Y-m', $employee->resign_date);
              $daily_salary     = $basesalary->amount / 30;
              $attendance_count = count($attendance);
              $readConfigs      = Config::where('option', 'setting_prorate')->first();
              $prorate_type = Config::where('option', 'type_prorate')->first();

              /**Jika Config setting prorate sama dengan full */
              if ($readConfigs->value == 'full') {

                $date1 = $employee->join_date;
                $date2 = $employee->resign_date;

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years  = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days   = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

                /**Jika tipe prorate sama dengan basic_allowance */
                if ($prorate_type == 'basic_allowance') {
                  foreach ($allowance_prorates as $key => $allowance) {
                    /**Jika Join date sama dengan priode salary  */
                    if ($join_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika Join date sama dengan priode salary  */
                    /**Jika join date dan resign date sama dengan priode salary*/
                    if ($join_date && $resign_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika join date dan resign date sama dengan priode salary*/
                    /** Jika join date dan resign date ada*/
                    if ($join_date && $resign_date) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /** End Jika join date dan resign date ada*/
                  }
                  /**End Jika tipe prorate sama dengan basic_allowance */
                } else {
                  /**Jika join date sama dengan periode_salary*/
                  if ($join_date == $periode_salary) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /**End Jika join date sama dengan periode_salary*/
                  /** Jika ada join date dan resign date sama dengan priode salary*/
                  if ($join_date && $resign_date == $periode_salary) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /**End Jika ada join date dan resign date sama dengan priode salary*/
                  /** Jika ada join date dan resign date*/
                  if ($join_date && $resign_date) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /** End Jika ada join date dan resign date*/
                }
                /**End Jika Config setting prorate sama dengan full */
              } else {
                SalaryReportDetail::create([
                  'salary_report_id' => $salaryreport->id,
                  'employee_id'      => $employee->id,
                  'description'      => LABEL_BASIC_SALARY,
                  'total'            => $resign_date == $periode_salary ? (date("d", strtotime($employee->resign_date . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                  'type'             => 1,
                  'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                  'is_added'         => 'NO'
                ]);
              }
              $salaryreport->salary_type = $basesalary->amount == 0 ? 'Hourly' : 'Monthly';
              $salaryreport->save();
            }
            if ($allowance) {
              foreach ($allowance as $key => $value) {
                if ($value->group_allowance_id) {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->description,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 1,
                    'status'            => 'Additional Allowance',
                    'group_allowance_id'=> $value->group_allowance_id,
                    'is_added'          => 'NO'
                  ]);
                } else {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->allowance_name,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 1,
                    'status'            => 'Additional Allowance',
                    'is_added'          => 'NO'
                  ]);
                }
              }
            }
            if ($deduction) {
              foreach ($deduction as $key => $value) {
                if ($value->group_allowance_id) {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->description,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 0,
                    'status'            => 'Deduction Allowance',
                    'group_allowance_id'=> $value->group_allowance_id,
                    'is_added'          => 'NO'
                  ]);
                } else {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->allowance_name,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 0,
                    'status'            => 'Deduction Allowance',
                    'is_added'          => 'NO'
                  ]);
                }
              }
            }
            if ($salary_deduction) {
              foreach ($salary_deduction as $key => $value) {
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => $value->description,
                  'total'             => $value->nominal,
                  'type'              => 0,
                  'status'            => 'Salary Deduction',
                  'is_added'          => 'NO'
                ]);
              }
            }
            if ($overtime && $employee->overtime == 'yes') {
              foreach ($overtime as $key => $over) {
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_OVERTIME . " " . $over->amount * 100 . "%",
                  'total'             => $over->final_salary,
                  'type'              => 1,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
              }
            }
            if ($employee->join == 'yes') {
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_SPSI,
                'total'             => 20000,
                'type'              => 0,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            if ($employee->department->driver == 'yes' && $driverallowance > 0) {
              $spsi = SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_DRIVER_ALLOWANCE,
                'total'             => $driverallowance,
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            if ($attendance_allowance) {
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_ATTENDANCE_ALLOWANCE,
                'total'             => $attendance_allowance,
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            if ($alphaPenalty->sum('penalty') > 0 && $employee->workgroup->penalty == 'Basic' && $basesalary) {
              $alpha_penalty = SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_ALPHA_PENALTY,
                'total'             => -1 * $alphaPenalty->sum('penalty'),
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            
            $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
            $salaryreport->deduction    = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
            $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
            $salaryreport->save();
            if ($alphaPenalty->count() > 0 && $employee->workgroup->penalty == 'Gross' && $basesalary) {
              $alpha_penalty = SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_ALPHA_PENALTY,
                'total'             => -1 * ($alphaPenalty->count() * (($salaryreport->gross_salary - $penaltyallowance) / 30)),
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
              $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
              $salaryreport->save();
            }
            if ($pph) {
              if (!$ptkp) {
                return array(
                  'status'    => false,
                  'message'   => 'PTKP for this employee name ' . $employee->name . ' not found. Please set PTKP or uncheck PPh 21 allowance for this generate month.'
                );
              }
              $gross                             = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
              $deduction                         = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
              $positionAllowance                 = getPositionAllowance($gross);
              $grossSalaryAfterPositionAllowance = getGrossSalaryAfterPositionAllowance($gross, $positionAllowance);
              $multiplierMonth                   = getMultiplierMonth($employee->join_date);
              $grossSalaryPerYear                = getGrossSalaryPerYear($grossSalaryAfterPositionAllowance, $multiplierMonth);
              $pkps                              = getPKP($grossSalaryPerYear, $ptkp->value);
              $pph21Yearly                       = getPPH21Yearly($pkps, $employee->npwp);

              //PPH Gaji + THR
              $grossSalaryJoinMonth             = getGrossSalaryJoinMonth($gross, $multiplierMonth);
              $getThr                           = $this->getThrReport($month, $year, $employee->id);
              

              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_POSITION_ALLOWANCE,
                'total'             => ($positionAllowance) > 0 ? $positionAllowance : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_NET_SALARY_YEAR,
                'total'             => ($grossSalaryPerYear) > 0 ? $grossSalaryPerYear : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_PPH_YEARLY,
                'total'             => ($pph21Yearly) > 0 ? $pph21Yearly : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_PPH_MONTHLY,
                'total'             => ($pph21Yearly / $multiplierMonth) > 0 ? $pph21Yearly / $multiplierMonth : 0,
                'type'              => 0,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              if($getThr){
                $total                            = getTotal($grossSalaryJoinMonth, $getThr->amount);
                $totalPositionAllowance           = getTotalPositionAllowance($total);
                $netSalaryThr                     = getNetSalaryThr($total, $totalPositionAllowance);
                $pkpThr                           = getPkpThr($netSalaryThr, $ptkp->value);
                $tarifThr                         = getTarifThr($pkpThr);

                SalaryReportDetail::create([
                  'salary_report_id' => $salaryreport->id,
                  'employee_id'      => $employee->id,
                  'description'      => LABEL_PPH_THR,
                  'total'            => $tarifThr > 0 ? $tarifThr - $pph21Yearly : 0,
                  'type'             => 0,
                  'status'           => 'Draft',
                  'is_added'         => 'NO'
                ]);
              }
              
            }
            $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
            $salaryreport->deduction    = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
            $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
            $salaryreport->save();
          } else {
            return $salaryreport;
          }
        }
      }
      return array(
        'status'    => true,
        'message'   => "salary report generated successfully"
      );
    } else {
      return array(
        'status'    => false,
        'message'   => "This position has no employees"
      );
    }
  }

  public function generateByWorkgroup($workgroup, $month, $year, $user)
  {
    $employees = Employee::select('employees.*')->whereIn('workgroup_id', $workgroup)->where('employees.status', 1)->get();
    if (!$employees->isEmpty()) {
      foreach ($employees as $employee) {
        $exists = $this->check_periode($month, $year, $employee->id);
        if ($exists) {
          $delete = $exists->delete();
        }

        $dt            = Carbon::createFromFormat('Y-m', $year . '-' . $month);
        $checkDate     = changeDateFormat('Y-m-d', $dt->endOfMonth()->toDateString() . '-' . $month . '-' . $year);
        $checkJoinDate = Employee::where('employees.status', 1)->where('employees.join_date', '<=', $checkDate)->find($employee->id);
        if ($checkJoinDate) {
          $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);
  
          $salaryreport = SalaryReport::create([
            'id'            => $this->getLatestId(),
            'employee_id'   => $employee->id,
            'created_by'    => $user,
            'period'        => $period,
            'status'        => -1
          ]);
          if ($salaryreport) {
            $basesalary           = $this->get_employee_salary($employee->id);
            $penaltyallowance     = $this->getPenaltyAllowance($employee->id, $month, $year);
            $allowance            = $this->get_additional_allowance($employee->id, $month, $year);
            $alphaPenalty         = $this->getAlphaData($employee->id, $month, $year);
            $deduction            = $this->get_deduction($employee->id, $month, $year);
            $overtime             = $this->get_overtime($employee->id, $month, $year);
            $attendance           = $this->get_attendance($employee->id, $month, $year);
            // $leave = $this->get_leave($employee->id, $month, $year);
            // $alpha = $this->get_alpha($employee->id, $month, $year);
            $driverallowance      = $this->get_driver_allowance($employee->id, $month, $year);
            $ptkp                 = $this->get_pkp($employee->ptkp);
            $attendance_allowance = $this->get_attendance_allowance($employee->id, $month, $year);
            $pph                  = $this->getPPhAllowance($employee->id, $month, $year);
            $allowance_prorates   = $this->getAllowanceProrate($employee->id, $month, $year);
            $salary_deduction     = $this->get_salarydeduction($employee->id, $month, $year);

            if ($basesalary) {
              $periode_salary   = changeDateFormat('Y-m', $year . '-' . $month);
              $join_date        = changeDateFormat('Y-m', $employee->join_date);
              $resign_date      = changeDateFormat('Y-m', $employee->resign_date);
              $daily_salary     = $basesalary->amount / 30;
              $attendance_count = count($attendance);
              $readConfigs      = Config::where('option', 'setting_prorate')->first();
              $prorate_type     = Config::where('option', 'type_prorate')->first();

              /**Jika Config setting prorate sama dengan full */
              if ($readConfigs->value == 'full') {

                $date1 = $employee->join_date;
                $date2 = $employee->resign_date;

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years  = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days   = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

                /**Jika tipe prorate sama dengan basic_allowance */
                if ($prorate_type == 'basic_allowance') {
                  foreach ($allowance_prorates as $key => $allowance) {
                    /**Jika Join date sama dengan priode salary  */
                    if ($join_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika Join date sama dengan priode salary  */
                    /**Jika join date dan resign date sama dengan priode salary*/
                    if ($join_date && $resign_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika join date dan resign date sama dengan priode salary*/
                    /** Jika join date dan resign date ada*/
                    if ($join_date && $resign_date) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /** End Jika join date dan resign date ada*/
                  }
                  /**End Jika tipe prorate sama dengan basic_allowance */
                } else {
                  /**Jika join date sama dengan periode_salary*/
                  if ($join_date == $periode_salary) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /**End Jika join date sama dengan periode_salary*/
                  /** Jika ada join date dan resign date sama dengan priode salary*/
                  if ($join_date && $resign_date == $periode_salary) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /**End Jika ada join date dan resign date sama dengan priode salary*/
                  /** Jika ada join date dan resign date*/
                  if ($join_date && $resign_date) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /** End Jika ada join date dan resign date*/
                }
                /**End Jika Config setting prorate sama dengan full */
              } else {
                SalaryReportDetail::create([
                  'salary_report_id' => $salaryreport->id,
                  'employee_id'      => $employee->id,
                  'description'      => LABEL_BASIC_SALARY,
                  'total'            => $resign_date == $periode_salary ? (date("d", strtotime($employee->resign_date . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                  'type'             => 1,
                  'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                  'is_added'         => 'NO'
                ]);
              }
              $salaryreport->salary_type = $basesalary->amount == 0 ? 'Hourly' : 'Monthly';
              $salaryreport->save();
            }
            if ($allowance) {
              foreach ($allowance as $key => $value) {
                if ($value->group_allowance_id) {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->description,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 1,
                    'status'            => 'Additional Allowance',
                    'group_allowance_id'=> $value->group_allowance_id,
                    'is_added'          => 'NO'
                  ]);
                } else {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->allowance_name,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 1,
                    'status'            => 'Additional Allowance',
                    'is_added'          => 'NO'
                  ]);
                }
              }
            }

            if ($deduction) {
              foreach ($deduction as $key => $value) {
                if ($value->group_allowance_id) {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->description,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 0,
                    'status'            => 'Deduction Allowance',
                    'group_allowance_id'=> $value->group_allowance_id,
                    'is_added'          => 'NO'
                  ]);
                } else {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->allowance_name,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 0,
                    'status'            => 'Deduction Allowance',
                    'is_added'          => 'NO'
                  ]);
                }
              }
            }

            if ($salary_deduction) {
              foreach ($salary_deduction as $key => $value) {
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => $value->description,
                  'total'             => $value->nominal,
                  'type'              => 0,
                  'status'            => 'Salary Deduction',
                  'is_added'          => 'NO'
                ]);
              }
            }
            if ($overtime && $employee->overtime == 'yes') {
              foreach ($overtime as $key => $over) {
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_OVERTIME . " " . $over->amount * 100 . "%",
                  'total'             => $over->final_salary,
                  'type'              => 1,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
              }
            }
            if ($employee->join == 'yes') {
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_SPSI,
                'total'             => 20000,
                'type'              => 0,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            if ($employee->department->driver == 'yes' && $driverallowance > 0) {
              $spsi = SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_DRIVER_ALLOWANCE,
                'total'             => $driverallowance,
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            if ($attendance_allowance) {
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_ATTENDANCE_ALLOWANCE,
                'total'             => $attendance_allowance,
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            
            if ($alphaPenalty->sum('penalty') > 0 && $employee->workgroup->penalty == 'Basic' && $basesalary) {
              $alpha_penalty = SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_ALPHA_PENALTY,
                'total'             => -1 * $alphaPenalty->sum('penalty'),
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            
            $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
            $salaryreport->deduction    = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
            $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
            $salaryreport->save();
            if ($alphaPenalty->count() > 0 && $employee->workgroup->penalty == 'Gross' && $basesalary) {
              $alpha_penalty = SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_ALPHA_PENALTY,
                'total'             => -1 * ($alphaPenalty->count() * (($salaryreport->gross_salary - $penaltyallowance) / 30)),
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
              $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
              $salaryreport->save();
            }
            if ($pph) {
              if (!$ptkp) {
                return array(
                  'status'    => false,
                  'message'   => 'PTKP for this employee name ' . $employee->name . ' not found. Please set PTKP or uncheck PPh 21 allowance for this generate month.'
                );
              }
              $gross                             = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
              $deduction                         = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
              $positionAllowance                 = getPositionAllowance($gross);
              $grossSalaryAfterPositionAllowance = getGrossSalaryAfterPositionAllowance($gross, $positionAllowance);
              $multiplierMonth                   = getMultiplierMonth($employee->join_date);
              $grossSalaryPerYear                = getGrossSalaryPerYear($grossSalaryAfterPositionAllowance, $multiplierMonth);
              $pkps                              = getPKP($grossSalaryPerYear, $ptkp->value);
              $pph21Yearly                       = getPPH21Yearly($pkps, $employee->npwp);

              //PPH Gaji + THR
              $grossSalaryJoinMonth   = getGrossSalaryJoinMonth($gross, $multiplierMonth);
              $getThr                 = $this->getThrReport($month, $year, $employee->id);

              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_POSITION_ALLOWANCE,
                'total'             => ($positionAllowance) > 0 ? $positionAllowance : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_NET_SALARY_YEAR,
                'total'             => ($grossSalaryPerYear) > 0 ? $grossSalaryPerYear : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_PPH_YEARLY,
                'total'             => ($pph21Yearly) > 0 ? $pph21Yearly : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_PPH_MONTHLY,
                'total'             => ($pph21Yearly / $multiplierMonth) > 0 ? $pph21Yearly / $multiplierMonth : 0,
                'type'              => 0,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              if($getThr){
                $total                  = getTotal($grossSalaryJoinMonth, $getThr->amount);
                $totalPositionAllowance = getTotalPositionAllowance($total);
                $netSalaryThr           = getNetSalaryThr($total, $totalPositionAllowance);
                $pkpThr                 = getPkpThr($netSalaryThr, $ptkp->value);
                $tarifThr               = getTarifThr($pkpThr);

                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_PPH_THR,
                  'total'             => $tarifThr > 0 ? $tarifThr - $pph21Yearly : 0,
                  'type'              => 0,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
              }
              
            }
            $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
            $salaryreport->deduction    = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
            $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
            $salaryreport->save();
          } else {
            return $salaryreport;
          }
        }
      }
      return array(
        'status'    => true,
        'message'   => "salary report generated successfully"
      );
    } else {
      return array(
        'status'    => false,
        'message'   => "This workgroup has no employees"
      );
    }
  }

  public function generateSalary($month,$year,$employees,$created_by){
    foreach ($employees as $employee) {
        $dt            = Carbon::createFromFormat('Y-m', $year . '-' . $month);
        $checkDate     = changeDateFormat('Y-m-d', $dt->endOfMonth()->toDateString() . '-' . $month . '-' . $year);
        $exists        = $this->check_periode($month, $year, $employee->id);
        if ($exists) {
            $delete = $exists->delete();
        }
        if ($employee->join_date <= $checkDate && $employee->status == 1) {
            $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);
            $salaryreport = SalaryReport::create([
              'employee_id'   => $employee->id,
              'created_by'    => $created_by,
              'period'        => $period,
              'status'        => -1
            ]); 
            if ($salaryreport) {
              $basesalary           = $this->get_employee_salary($employee->id);
              $alphaPenalty         = $this->getAlphaData($employee->id, $month, $year);
              $allowance            = $this->get_additional_allowance($employee->id, $month, $year);
              $deduction            = $this->get_deduction($employee->id, $month, $year);
              $overtime             = $this->get_overtime($employee->id, $month, $year);
              $driverallowance      = $this->get_driver_allowance($employee->id, $month, $year);
              $penaltyallowance     = $this->getPenaltyAllowance($employee->id, $month, $year);
              $ptkp                 = $this->get_pkp($employee->ptkp);
              $attendance_allowance = $this->get_attendance_allowance($employee->id, $month, $year);
              $pph                  = $this->getPPhAllowance($employee->id, $month, $year);
              $allowance_prorates   = $this->getAllowanceProrate($employee->id, $month, $year);
              $salary_deduction     = $this->get_salarydeduction($employee->id, $month, $year);

              /* Base Salary*/
              if ($basesalary) {
                $periode_salary = changeDateFormat('Y-m', $year . '-' . $month);
                $join_date      = changeDateFormat('Y-m', $employee->join_date);
                $resign_date    = changeDateFormat('Y-m', $employee->resign_date);
                $readConfigs = Config::where('option', 'setting_prorate')->first();
                $prorate_type= Config::where('option', 'type_prorate')->first();
  
                /**Jika Config setting prorate sama dengan full */
                if ($readConfigs->value == 'full') {
  
                  $date1 = $employee->join_date;
                  $date2 = $employee->resign_date;
  
                  $diff = abs(strtotime($date2) - strtotime($date1));
  
                  $years  = floor($diff / (365 * 60 * 60 * 24));
                  $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                  $days   = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
  
                  /**Jika tipe prorate sama dengan basic_allowance */
                  if ($prorate_type == 'basic_allowance') {
                    foreach ($allowance_prorates as $key => $allowance) {
                      /**Jika Join date sama dengan priode salary  */
                      if ($join_date == $periode_salary) {
                        SalaryReportDetail::create([
                          'salary_report_id' => $salaryreport->id,
                          'employee_id'      => $employee->id,
                          'description'      => LABEL_BASIC_ALLOWANCE,
                          'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                          'type'             => 1,
                          'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                          'is_added'         => 'NO'
                        ]);
                      }
                      /**End Jika Join date sama dengan priode salary  */
                      /**Jika join date dan resign date sama dengan priode salary*/
                      if ($join_date && $resign_date == $periode_salary) {
                        SalaryReportDetail::create([
                          'salary_report_id' => $salaryreport->id,
                          'employee_id'      => $employee->id,
                          'description'      => LABEL_BASIC_ALLOWANCE,
                          'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                          'type'             => 1,
                          'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                          'is_added'         => 'NO'
                        ]);
                      }
                      /**End Jika join date dan resign date sama dengan priode salary*/
                      /** Jika join date dan resign date ada*/
                      if ($join_date && $resign_date) {
                        SalaryReportDetail::create([
                          'salary_report_id' => $salaryreport->id,
                          'employee_id'      => $employee->id,
                          'description'      => LABEL_BASIC_ALLOWANCE,
                          'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                          'type'             => 1,
                          'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                          'is_added'         => 'NO'
                        ]);
                      }
                      /** End Jika join date dan resign date ada*/
                    }
                    /**End Jika tipe prorate sama dengan basic_allowance */
                  } else {
                    /**Jika join date sama dengan periode_salary*/
                    if ($join_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_SALARY,
                        'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * $basesalary->amount) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika join date sama dengan periode_salary*/
                    /** Jika ada join date dan resign date sama dengan priode salary*/
                    if ($join_date && $resign_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_SALARY,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika ada join date dan resign date sama dengan priode salary*/
                    /** Jika ada join date dan resign date*/
                    if ($join_date && $resign_date) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_SALARY,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /** End Jika ada join date dan resign date*/
                  }
                  /**End Jika Config setting prorate sama dengan full */
                } else {
                  SalaryReportDetail::create([
                    'salary_report_id' => $salaryreport->id,
                    'employee_id'      => $employee->id,
                    'description'      => LABEL_BASIC_SALARY,
                    'total'            => $resign_date == $periode_salary ? (date("d", strtotime($employee->resign_date . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                    'type'             => 1,
                    'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                    'is_added'         => 'NO'
                  ]);
                }
                
                $salaryreport->salary_type = $basesalary->amount == 0 ? 'Hourly' : 'Monthly';
                $salaryreport->save();
              } else {
                DB::rollBack();
                return response()->json([
                  'status'    => false,
                  'message'   => 'Base salary for this employee not found'
                ], 400);
              }
              /*End Base Salary*/


              /*Allowance*/
              if ($allowance) {
                foreach ($allowance as $key => $value) {
                  if ($value->group_allowance_id) {
                    SalaryReportDetail::create([
                      'salary_report_id'  => $salaryreport->id,
                      'employee_id'       => $employee->id,
                      'description'       => $value->description,
                      'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                      'type'              => 1,
                      'status'            => 'Additional Allowance',
                      'group_allowance_id'=> $value->group_allowance_id,
                      'is_added'          => 'NO'
                    ]);
                  } else {
                    SalaryReportDetail::create([
                      'salary_report_id'  => $salaryreport->id,
                      'employee_id'       => $employee->id,
                      'description'       => $value->allowance_name,
                      'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                      'type'              => 1,
                      'status'            => 'Additional Allowance',
                      'is_added'          => 'NO'
                    ]);
                  }
                }
              }
              /*End Allowance*/

              /*Deduction*/
              if ($deduction) {
                foreach ($deduction as $key => $value) {
                    $decutionvalue = 0;
                    $basic_ammount = $basesalary->amount ;
                    $allowances = $this->get_detail_allowance($employee->id, $month, $year,$value->allowance_id );
                    $totalallowance = 0;
                    foreach($allowances as $allowance){
                      $totalallowance += $allowance->value_deduction;
                    }
                    $deductionvalue = $basic_ammount;
                    if ($value->bpjs == 'BASIC') {
                      $deductionvalue = $basic_ammount;
                    }
                    if ($value->bpjs == 'ALLOWANCE') {
                      $deductionvalue = $totalallowance;
                    }
                    if ($value->bpjs == 'BASIC & ALLOWANCE') {
                      $deductionvalue = $basic_ammount + $totalallowance;
                    }
                  if ($value->group_allowance_id) {
                    $salaryreportdetail = SalaryReportDetail::where('salary_report_id', $salaryreport->id)->where('group_allowance_id', $value->group_allowance_id)->first();
                    if ($salaryreportdetail) {
                        $salaryreportdetail->total =  $salaryreportdetail->total + (($value->type == 'percentage') ? $deductionvalue * ($value->value / 100) : $value->value);
                        $salaryreportdetail->save();
                    } else {
                      SalaryReportDetail::create([
                        'salary_report_id'  => $salaryreport->id,
                        'employee_id'       => $employee->id,
                        'description'       => $value->description,
                        'total'             => ($value->type == 'percentage') ? $deductionvalue * ($value->value / 100) : $value->value,
                        'type'              => 0,
                        'status'            => 'Deduction Allowance',
                        'group_allowance_id' => $value->group_allowance_id,
                        'is_added'          => 'NO'
                      ]);
                    }
                  } else {
                    SalaryReportDetail::create([
                      'salary_report_id'  => $salaryreport->id,
                      'employee_id'       => $employee->id,
                      'description'       => $value->description,
                      'total'             => ($value->type == 'percentage') ? $deductionvalue * ($value->value / 100) : $value->value,
                      'type'              => 0,
                      'status'            => 'Deduction Allowance',
                      'is_added'          => 'NO'
                    ]);
                  }
                }
              }
              /*End Deduction*/

              /*Salary Deduction*/
              if ($salary_deduction) {
                foreach ($salary_deduction as $key => $value) {
                  SalaryReportDetail::create([
                    'salary_report_id' => $salaryreport->id,
                    'employee_id'      => $employee->id,
                    'description'      => $value->description,
                    'total'            => $value->nominal,
                    'type'             => 0,
                    'status'           => 'Salary Deduction',
                    'is_added'         => 'NO'
                  ]);
                }
              }
              /*End Salary Deduction*/

              /*Overtime Yes*/
              if ($overtime && $employee->overtime == 'yes') {
                foreach ($overtime as $key => $over) {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => LABEL_OVERTIME . " " . $over->amount * 100 . "%",
                    'total'             => $over->final_salary,
                    'type'              => 1,
                    'status'            => 'Draft',
                    'is_added'          => 'NO'
                  ]);
                }
              }
              /*End Overtime Yes*/

              /*SPSI*/
              if ($employee->join == 'yes') {
                $spsi = SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_SPSI,
                  'total'             => 20000,
                  'type'              => 0,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);

              }
              /*SPSI*/

              /*Driver Alllowance*/
              if ($employee->department->driver == 'yes' && $driverallowance > 0) {
                $salaryreportdetail = SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_DRIVER_ALLOWANCE,
                  'total'             => $driverallowance,
                  'type'              => 1,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
                if(!$salaryreportdetail){
                  DB::rollBack();
                  return response()->json([
                    'status'    => false,
                    'message'   => $salaryreportdetail
                  ],
                    400
                  );
                } 
              }
              /*End Driver Allowance*/
              
              /* Alpha Penalty Allowance*/
              if ($alphaPenalty->sum('penalty') > 0 && $employee->workgroup->penalty == 'Basic') {
                $alpha_penalty = SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_ALPHA_PENALTY,
                  'total'             => -1 * $alphaPenalty->sum('penalty'),
                  'type'              => 1,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
              }
              /*End Alpha Penalty Allowance*/

              /* Attendance Allowance*/
              if ($attendance_allowance) {
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_ATTENDANCE_ALLOWANCE,
                  'total'             => $attendance_allowance,
                  'type'              => 1,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
              }
              /* End Attendance Allowance */

              $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
              $salaryreport->deduction    = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
              $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
              $salaryreport->save();

              /* Alpha Penalty*/
              if ($alphaPenalty->count() > 0 && $employee->workgroup->penalty == 'Gross') {
                $alpha_penalty = SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_ALPHA_PENALTY,
                  'total'             => -1 * ($alphaPenalty->count() * (($salaryreport->gross_salary - $penaltyallowance) / 30)),
                  'type'              => 1,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
                $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
                $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
                $salaryreport->save();
              }
              /*End Alpha Penalty*/

              /* PPH True */
              if ($pph) {
                if (!$ptkp) {
                  DB::rollBack();
                  return response()->json([
                    'status'    => false,
                    'message'   => 'PTKP for this employee name ' . $employee->name . ' not found. Please set PTKP or uncheck PPh 21 allowance for this generate month.'
                  ], 400);
                }
                $gross                             = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
                $deduction                         = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
                $positionAllowance                 = getPositionAllowance($gross);
                $grossSalaryAfterPositionAllowance = getGrossSalaryAfterPositionAllowance($gross, $positionAllowance);
                $multiplierMonth                   = getMultiplierMonth($employee->join_date);
                $grossSalaryPerYear                = getGrossSalaryPerYear($grossSalaryAfterPositionAllowance, $multiplierMonth);
                $pkps                              = getPKP($grossSalaryPerYear, $ptkp->value);
                $pph21Yearly                       = getPPH21Yearly($pkps, $employee->npwp);
                
                //PPH Gaji + THR
                $grossSalaryJoinMonth   = getGrossSalaryJoinMonth($gross, $multiplierMonth);
                $getThr                 = $this->getThrReport($month, $year, $employee->id);
                
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_POSITION_ALLOWANCE,
                  'total'             => $positionAllowance > 0 ? $positionAllowance : 0,
                  'type'              => 2,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_NET_SALARY_YEAR,
                  'total'             => $grossSalaryPerYear > 0 ? $grossSalaryPerYear : 0,
                  'type'              => 2,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_PPH_YEARLY,
                  'total'             => ($pph21Yearly) > 0 ? $pph21Yearly : 0,
                  'type'              => 2,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_PPH_MONTHLY,
                  'total'             => ($pph21Yearly / $multiplierMonth) > 0 ? $pph21Yearly / $multiplierMonth : 0,
                  'type'              => 0,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
                if ($getThr) {
                  $total                  = getTotal($grossSalaryJoinMonth, $getThr->amount);
                  $totalPositionAllowance = getTotalPositionAllowance($total);
                  $netSalaryThr           = getNetSalaryThr($total, $totalPositionAllowance);
                  $pkpThr                 = getPkpThr($netSalaryThr, $ptkp->value);
                  $tarifThr               = getTarifThr($pkpThr);
  
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => LABEL_PPH_THR,
                    'total'             => $tarifThr > 0 ? $tarifThr - $pph21Yearly : 0,
                    'type'              => 0,
                    'status'            => 'Draft',
                    'is_added'          => 'NO'
                  ]);
                }
                
              }
              /*End PPH*/

              $salaryreport->gross_salary = $this->gross_salary($salaryreport->id) ? $this->gross_salary($salaryreport->id) : 0;
              $salaryreport->deduction    = $this->deduction_salary($salaryreport->id) ? $this->deduction_salary($salaryreport->id) : 0;
              $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
              $salaryreport->save();
            } elseif (!$salaryreport) {
              DB::rollBack();
              return response()->json([
                'status'    => false,
                'message'   => $salaryreport
              ], 400);
            }
        }
        else {
          DB::rollBack();
          return response()->json([
            'status'    => false,
            'message'   => 'This employee join date is greater than generate month and year',
          ], 400);
        }
    }
  } 
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){
    if ($request->department && !$request->position && !$request->workgroup_id && !$request->employee_name) {
      DB::beginTransaction();
      $employee = Employee::select('employees.*')->leftJoin('departments', 'departments.id', '=', 'employees.department_id')->where('employees.status', 1);
      $string = '';
      $department = $request->department;
      $uniqdepartments = [];
      foreach($department as $dept){
          if(!in_array($dept,$uniqdepartments)){
              $uniqdepartments[] = $dept;
          }
      }
      $department = $uniqdepartments;
      foreach ($department as $dept) {
        $string .= "departments.path like '%$dept%'";
        if (end($department) != $dept) {
          $string .= ' or ';
        }
      }
      $employee->whereRaw('(' . $string . ')');
      $employees = $employee->get();
      if (!$employees->isEmpty()) {
        $this->generateSalary($request->montly,$request->year,$employees,$request->user);
      }
      else{
        return array(
          'status'    => true,
          'message'   => "Data not found"
        );
      }
      DB::commit();
    }elseif (!$request->department && $request->position && !$request->workgroup_id && !$request->employee_name) {
      DB::beginTransaction();
      $employees = Employee::select('employees.*')->whereIn('title_id', $request->position)->where('employees.status', 1)->get();
      if (!$employees->isEmpty()) {
        $this->generateSalary($request->montly,$request->year,$employees,$request->user);
      }
      else{
        return array(
          'status'    => false,
          'message'   => "This position has no employees"
        );
      }
      DB::commit();
    } elseif (!$request->department && !$request->position && $request->workgroup_id && !$request->employee_name) {
      DB::beginTransaction();
      $employees = Employee::select('employees.*')->whereIn('workgroup_id', $request->workgroup_id)->where('employees.status', 1)->get();
      if (!$employees->isEmpty()) {
        $this->generateSalary($request->montly,$request->year,$employees,$request->user);
      }
      else{
        return array(
          'status'    => false,
          'message'   => "This workgroup has no employees"
        );
      }
      DB::commit();
    } elseif (!$request->department && !$request->position && !$request->workgroup_id && $request->employee_name) {
      DB::beginTransaction();
      $employee_id = [];
      foreach ($request->employee_name as $view_employee) {
        $employee_id[] = $view_employee;
      }
      if(count($employee_id) >0){
        $employees = Employee::whereIn('id',$employee_id)->get();
      }
      else{
        $employees = Employee::whereIn('id',[-1])->get();
      }
      $this->generateSalary($request->montly,$request->year,$employees,$request->user);
      DB::commit();
      return response()->json([
        'status'    => true,
        'message'   => 'salary report generated successfully',
      ], 200);
    } else {
      return response()->json([
        'status'    => false,
        'message'   => 'Please select one parameter from position, department or workgroup to generate mass'
      ], 400);
    }
  }

  public function store2(Request $request)
  {
    // dd($request->all());
    if ($request->department && !$request->position && !$request->workgroup_id && !$request->employee_name) {
      $departments = $this->generateByDepartment($request->department, $request->montly, $request->year, $request->user);
      if (!$departments['status']) {
        return response()->json([
          'status'    => false,
          'message'   => $departments['message']
        ], 400);
      } else {
        return response()->json([
          'status'    => true,
          'message'   => $departments['message']
        ], 200);
      }
    } elseif (!$request->department && $request->position && !$request->workgroup_id && !$request->employee_name) {
      $position = $this->generateByPosition($request->position, $request->montly, $request->year, $request->user);
      if (!$position['status']) {
        return response()->json([
          'status'    => false,
          'message'   => $position['message']
        ], 400);
      } else {
        return response()->json([
          'status'    => true,
          'message'   => $position['message']
        ], 200);
      }
    } elseif (!$request->department && !$request->position && $request->workgroup_id && !$request->employee_name) {
      $workgroup = $this->generateByWorkgroup($request->workgroup_id, $request->montly, $request->year, $request->user);
      if (!$workgroup['status']) {
        return response()->json([
          'status'    => false,
          'message'   => $workgroup['message']
        ], 400);
      } else {
        return response()->json([
          'status'    => true,
          'message'   => $workgroup['message']
        ], 200);
      }
    } elseif (!$request->department && !$request->position && !$request->workgroup_id && $request->employee_name) {
      DB::beginTransaction();
      
      foreach ($request->employee_name as $view_employee) {
        $dt            = Carbon::createFromFormat('Y-m', $request->year . '-' . $request->montly);
        $checkDate     = changeDateFormat('Y-m-d', $dt->endOfMonth()->toDateString() . '-' . $request->montly . '-' . $request->year);
        $checkJoinDate = Employee::select('employees.*')->where('employees.status', 1)->where('employees.join_date', '<=', $checkDate)->find($view_employee);
        $exists        = $this->check_periode($request->montly, $request->year, $view_employee);
        if ($exists) {
          $delete = $exists->delete();
        }
        if ($checkJoinDate) {
          $period = changeDateFormat('Y-m-d', 01 . '-' . $request->montly . '-' . $request->year);
          $id = $this->getLatestId();
          $salaryreport = SalaryReport::create([
            'id'            => $id,
            'employee_id'   => $view_employee,
            'created_by'    => $request->user,
            'period'        => $period,
            'status'        => -1
          ]);
          if ($salaryreport) {
            $basesalary           = $this->get_employee_salary($view_employee);
            $alphaPenalty         = $this->getAlphaData($view_employee, $request->montly, $request->year);
            $allowance            = $this->get_additional_allowance($view_employee, $request->montly, $request->year);
            $deduction            = $this->get_deduction($view_employee, $request->montly, $request->year);
            $overtime             = $this->get_overtime($view_employee, $request->montly, $request->year);
            // $attendance = $this->get_attendance($view_employee, $request->montly, $request->year);
            // $leave = $this->get_leave($view_employee, $request->montly, $request->year);
            // $alpha = $this->get_alpha($view_employee, $request->montly, $request->year);
            $driverallowance      = $this->get_driver_allowance($view_employee, $request->montly, $request->year);
            $penaltyallowance     = $this->getPenaltyAllowance($view_employee, $request->montly, $request->year);
            $employee             = Employee::with('department')->with('title')->find($view_employee);
            $ptkp                 = $this->get_pkp($employee->ptkp);
            $attendance_allowance = $this->get_attendance_allowance($view_employee, $request->montly, $request->year);
            $pph                  = $this->getPPhAllowance($view_employee, $request->montly, $request->year);
            $allowance_prorates   = $this->getAllowanceProrate($view_employee, $request->montly, $request->year);
            $salary_deduction     = $this->get_salarydeduction($view_employee, $request->montly, $request->year);


            if ($basesalary) {
              $periode_salary = changeDateFormat('Y-m', $request->year . '-' . $request->montly);
              $join_date      = changeDateFormat('Y-m', $employee->join_date);
              $resign_date    = changeDateFormat('Y-m', $employee->resign_date);
              // $daily_salary = $basesalary->amount / 30;
              // $attendance_count = count($attendance);
              $readConfigs = Config::where('option', 'setting_prorate')->first();
              $prorate_type= Config::where('option', 'type_prorate')->first();

              /**Jika Config setting prorate sama dengan full */
              if ($readConfigs->value == 'full') {

                $date1 = $employee->join_date;
                $date2 = $employee->resign_date;

                $diff = abs(strtotime($date2) - strtotime($date1));

                $years  = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $days   = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

                /**Jika tipe prorate sama dengan basic_allowance */
                if ($prorate_type == 'basic_allowance') {
                  foreach ($allowance_prorates as $key => $allowance) {
                    /**Jika Join date sama dengan priode salary  */
                    if ($join_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika Join date sama dengan priode salary  */
                    /**Jika join date dan resign date sama dengan priode salary*/
                    if ($join_date && $resign_date == $periode_salary) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /**End Jika join date dan resign date sama dengan priode salary*/
                    /** Jika join date dan resign date ada*/
                    if ($join_date && $resign_date) {
                      SalaryReportDetail::create([
                        'salary_report_id' => $salaryreport->id,
                        'employee_id'      => $employee->id,
                        'description'      => LABEL_BASIC_ALLOWANCE,
                        'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * ($basesalary->amount + $allowance->allowance_value)) / 30 : $basesalary->amount,
                        'type'             => 1,
                        'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                        'is_added'         => 'NO'
                      ]);
                    }
                    /** End Jika join date dan resign date ada*/
                  }
                  /**End Jika tipe prorate sama dengan basic_allowance */
                } else {
                  /**Jika join date sama dengan periode_salary*/
                  if ($join_date == $periode_salary) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $join_date == $periode_salary ? (date("d", strtotime($employee->join_date)) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /**End Jika join date sama dengan periode_salary*/
                  /** Jika ada join date dan resign date sama dengan priode salary*/
                  if ($join_date && $resign_date == $periode_salary) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /**End Jika ada join date dan resign date sama dengan priode salary*/
                  /** Jika ada join date dan resign date*/
                  if ($join_date && $resign_date) {
                    SalaryReportDetail::create([
                      'salary_report_id' => $salaryreport->id,
                      'employee_id'      => $employee->id,
                      'description'      => LABEL_BASIC_SALARY,
                      'total'            => $days > 0 ? (date("d", strtotime($days . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                      'type'             => 1,
                      'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                      'is_added'         => 'NO'
                    ]);
                  }
                  /** End Jika ada join date dan resign date*/
                }
                /**End Jika Config setting prorate sama dengan full */
              } else {
                SalaryReportDetail::create([
                  'salary_report_id' => $salaryreport->id,
                  'employee_id'      => $employee->id,
                  'description'      => LABEL_BASIC_SALARY,
                  'total'            => $resign_date == $periode_salary ? (date("d", strtotime($employee->resign_date . '-1 days')) * $basesalary->amount) / 30 : $basesalary->amount,
                  'type'             => 1,
                  'status'           => $basesalary->amount == 0 ? 'Hourly' : 'Monthly',
                  'is_added'         => 'NO'
                ]);
              }
              
              $salaryreport->salary_type = $basesalary->amount == 0 ? 'Hourly' : 'Monthly';
              $salaryreport->save();
            } else {
              DB::rollBack();
              return response()->json([
                'status'    => false,
                'message'   => 'Base salary for this employee not found'
              ], 400);
            }
            if ($allowance) {
              foreach ($allowance as $key => $value) {
                if ($value->group_allowance_id) {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->description,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 1,
                    'status'            => 'Additional Allowance',
                    'group_allowance_id'=> $value->group_allowance_id,
                    'is_added'          => 'NO'
                  ]);
                } else {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $salaryreport->id,
                    'employee_id'       => $employee->id,
                    'description'       => $value->allowance_name,
                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->value,
                    'type'              => 1,
                    'status'            => 'Additional Allowance',
                    'is_added'          => 'NO'
                  ]);
                }
              }
            }
            if ($deduction) {
              foreach ($deduction as $key => $value) {
                  $decutionvalue = 0;
                  $basic_ammount = $basesalary->amount ;
                  $allowances = $this->get_detail_allowance($view_employee, $request->montly, $request->year,$value->allowance_id );
                  $totalallowance = 0;
                  foreach($allowances as $allowance){
                    $totalallowance += $allowance->value_deduction;
                  }
                  $deductionvalue = $basic_ammount;
                  if ($value->bpjs == 'BASIC') {
                    $deductionvalue = $basic_ammount;
                  }
                  if ($value->bpjs == 'ALLOWANCE') {
                    $deductionvalue = $totalallowance;
                  }
                  if ($value->bpjs == 'BASIC & ALLOWANCE') {
                    $deductionvalue = $basic_ammount + $totalallowance;
                  }
                if ($value->group_allowance_id) {
                  $salaryreportdetail = SalaryReportDetail::where('salary_report_id', $id)->where('group_allowance_id', $value->group_allowance_id)->first();
                  if ($salaryreportdetail) {
                      $salaryreportdetail->total =  $salaryreportdetail->total + (($value->type == 'percentage') ? $deductionvalue * ($value->value / 100) : $value->value);
                      //$salaryreportdetail->total =  $salaryreportdetail->total + $decutionvalue;
                      $salaryreportdetail->save();
                  } else {
                    SalaryReportDetail::create([
                      'salary_report_id'  => $id,
                      'employee_id'       => $view_employee,
                      'description'       => $value->description,
                      'total'             => ($value->type == 'percentage') ? $deductionvalue * ($value->value / 100) : $value->value,
                      //'total'             => $decutionvalue,
                      'type'              => 0,
                      'status'            => 'Deduction Allowance',
                      'group_allowance_id' => $value->group_allowance_id,
                      'is_added'          => 'NO'
                    ]);
                  }
                } else {
                  SalaryReportDetail::create([
                    'salary_report_id'  => $id,
                    'employee_id'       => $view_employee,
                    'description'       => $value->description,
                    'total'             => ($value->type == 'percentage') ? $deductionvalue * ($value->value / 100) : $value->value,
                    //'total'             => $decutionvalue,
                    'type'              => 0,
                    'status'            => 'Deduction Allowance',
                    'is_added'          => 'NO'
                  ]);
                }
              }
            }
            if ($salary_deduction) {
              foreach ($salary_deduction as $key => $value) {
                SalaryReportDetail::create([
                  'salary_report_id' => $salaryreport->id,
                  'employee_id'      => $employee->id,
                  'description'      => $value->description,
                  'total'            => $value->nominal,
                  'type'             => 0,
                  'status'           => 'Salary Deduction',
                  'is_added'         => 'NO'
                ]);
              }
            }
            if ($overtime && $employee->overtime == 'yes') {
              foreach ($overtime as $key => $over) {
                SalaryReportDetail::create([
                  'salary_report_id'  => $id,
                  'employee_id'       => $view_employee,
                  'description'       => LABEL_OVERTIME . " " . $over->amount * 100 . "%",
                  'total'             => $over->final_salary,
                  'type'              => 1,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
              }
            }
            if ($employee->join == 'yes') {
              $spsi = SalaryReportDetail::create([
                'salary_report_id'  => $id,
                'employee_id'       => $view_employee,
                'description'       => LABEL_SPSI,
                'total'             => 20000,
                'type'              => 0,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);

            }
            if ($employee->department->driver == 'yes' && $driverallowance > 0) {
              $spsi = SalaryReportDetail::create([
                'salary_report_id'  => $id,
                'employee_id'       => $view_employee,
                'description'       => LABEL_DRIVER_ALLOWANCE,
                'total'             => $driverallowance,
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              if(!$spsi){
                DB::rollBack();
                return response()->json([
                  'status'    => false,
                  'message'   => $spsi
                ],
                  400
                );
              }
              
            }
            if ($alphaPenalty->sum('penalty') > 0 && $employee->workgroup->penalty == 'Basic') {
              $alpha_penalty = SalaryReportDetail::create([
                'salary_report_id'  => $id,
                'employee_id'       => $view_employee,
                'description'       => LABEL_ALPHA_PENALTY,
                'total'             => -1 * $alphaPenalty->sum('penalty'),
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            if ($attendance_allowance) {
              SalaryReportDetail::create([
                'salary_report_id'  => $id,
                'employee_id'       => $view_employee,
                'description'       => LABEL_ATTENDANCE_ALLOWANCE,
                'total'             => $attendance_allowance,
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
            }
            
            $salaryreport->gross_salary = $this->gross_salary($id) ? $this->gross_salary($id) : 0;
            $salaryreport->deduction    = $this->deduction_salary($id) ? $this->deduction_salary($id) : 0;
            $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
            $salaryreport->save();
            if ($alphaPenalty->count() > 0 && $employee->workgroup->penalty == 'Gross') {
              $alpha_penalty = SalaryReportDetail::create([
                'salary_report_id'  => $id,
                'employee_id'       => $view_employee,
                'description'       => LABEL_ALPHA_PENALTY,
                'total'             => -1 * ($alphaPenalty->count() * (($salaryreport->gross_salary - $penaltyallowance) / 30)),
                'type'              => 1,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              $salaryreport->gross_salary = $this->gross_salary($id) ? $this->gross_salary($id) : 0;
              $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
              $salaryreport->save();
            }
            if ($pph) {
              if (!$ptkp) {
                DB::rollBack();
                return response()->json([
                  'status'    => false,
                  'message'   => 'PTKP for this employee name ' . $employee->name . ' not found. Please set PTKP or uncheck PPh 21 allowance for this generate month.'
                ], 400);
              }
              $gross                             = $this->gross_salary($id) ? $this->gross_salary($id) : 0;
              $deduction                         = $this->deduction_salary($id) ? $this->deduction_salary($id) : 0;
              $positionAllowance                 = getPositionAllowance($gross);
              $grossSalaryAfterPositionAllowance = getGrossSalaryAfterPositionAllowance($gross, $positionAllowance);
              $multiplierMonth                   = getMultiplierMonth($employee->join_date);
              $grossSalaryPerYear                = getGrossSalaryPerYear($grossSalaryAfterPositionAllowance, $multiplierMonth);
              $pkps                              = getPKP($grossSalaryPerYear, $ptkp->value);
              $pph21Yearly                       = getPPH21Yearly($pkps, $employee->npwp);
              
              //PPH Gaji + THR
              $grossSalaryJoinMonth   = getGrossSalaryJoinMonth($gross, $multiplierMonth);
              $getThr                 = $this->getThrReport($request->montly, $request->year, $employee->id);
              
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_POSITION_ALLOWANCE,
                'total'             => $positionAllowance > 0 ? $positionAllowance : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_NET_SALARY_YEAR,
                'total'             => $grossSalaryPerYear > 0 ? $grossSalaryPerYear : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_PPH_YEARLY,
                'total'             => ($pph21Yearly) > 0 ? $pph21Yearly : 0,
                'type'              => 2,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              SalaryReportDetail::create([
                'salary_report_id'  => $salaryreport->id,
                'employee_id'       => $employee->id,
                'description'       => LABEL_PPH_MONTHLY,
                'total'             => ($pph21Yearly / $multiplierMonth) > 0 ? $pph21Yearly / $multiplierMonth : 0,
                'type'              => 0,
                'status'            => 'Draft',
                'is_added'          => 'NO'
              ]);
              if ($getThr) {
                $total                  = getTotal($grossSalaryJoinMonth, $getThr->amount);
                $totalPositionAllowance = getTotalPositionAllowance($total);
                $netSalaryThr           = getNetSalaryThr($total, $totalPositionAllowance);
                $pkpThr                 = getPkpThr($netSalaryThr, $ptkp->value);
                $tarifThr               = getTarifThr($pkpThr);

                SalaryReportDetail::create([
                  'salary_report_id'  => $salaryreport->id,
                  'employee_id'       => $employee->id,
                  'description'       => LABEL_PPH_THR,
                  'total'             => $tarifThr > 0 ? $tarifThr - $pph21Yearly : 0,
                  'type'              => 0,
                  'status'            => 'Draft',
                  'is_added'          => 'NO'
                ]);
              }
              
            }
            $salaryreport->gross_salary = $this->gross_salary($id) ? $this->gross_salary($id) : 0;
            $salaryreport->deduction    = $this->deduction_salary($id) ? $this->deduction_salary($id) : 0;
            $salaryreport->net_salary   = $salaryreport->gross_salary - $salaryreport->deduction;
            $salaryreport->save();
          } elseif (!$salaryreport) {
            DB::rollBack();
            return response()->json([
              'status'    => false,
              'message'   => $salaryreport
            ], 400);
          }
        } else {
          DB::rollBack();
          return response()->json([
            'status'    => false,
            'message'   => 'This employee join date is greater than generate month and year',
          ], 400);
        }
      }
      DB::commit();
      return response()->json([
        'status'    => true,
        'message'   => 'salary report generated successfully',
      ], 200);
    } else {
      return response()->json([
        'status'    => false,
        'message'   => 'Please select one parameter from position, department or workgroup to generate mass'
      ], 400);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $salary_detail = SalaryReport::with('salarydetail')->with('employee')->find($id);
    $employee = Employee::with('department')->with('title')->find($salary_detail->employee_id);
    if ($salary_detail) {
      return view('admin.salaryreport.detail', compact('salary_detail', 'employee'));
    } else {
      abort(404);
    }
  }

  public function editapproval($id)
  {
    $salary_detail = SalaryReport::with('salarydetail')->with('employee')->find($id);
    $employee = Employee::with('department')->with('title')->find($salary_detail->employee_id);
    if ($salary_detail) {
      return view('admin.salaryreport.editapproval', compact('salary_detail', 'employee'));
    } else {
      abort(404);
    }
  }

  public function updateapprove(Request $request, $id)
  {
    $salaryreport = SalaryReport::find($id);
    $salaryreport->status = $request->status;
    $salaryreport->save();

    if (!$salaryreport) {
      return response()->json([
        'status'    => false,
        'message'   => $salaryreport
      ], 400);
    }

    // return view('admin.salaryreport.indexapproval');
    return response()->json([
      'status'    => true,
      'results'   => route('salaryreport.indexapproval')
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
    //
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
    //
  }

  /**
   * Print mass all salary that checked
   */
  public function printmass(Request $request)
  {
    $id = json_decode($request->id);
    $salaries = SalaryReport::with('employee')->with('salarydetail')->whereIn('id', $id)->get();

    $coordinate12 = GroupAllowance::where('coordinate', '1.2')->first();
    $coordinate13 = GroupAllowance::where('coordinate', '1.3')->first();
    $coordinate14 = GroupAllowance::where('coordinate', '1.4')->first();
    $coordinate43 = GroupAllowance::where('coordinate', '4.3')->first();
    $coordinate44 = GroupAllowance::where('coordinate', '4.4')->first();
    $coordinate45 = GroupAllowance::where('coordinate', '4.5')->first();
    $coordinate46 = GroupAllowance::where('coordinate', '4.6')->first();
    $coordinate54 = GroupAllowance::where('coordinate', '5.4')->first();
    $coordinate55 = GroupAllowance::where('coordinate', '5.5')->first();
    $coordinate56 = GroupAllowance::where('coordinate', '5.6')->first();

    $coordinate33 = LeaveSetting::where('coordinate', 'like', '%3.3%')->first();
    $coordinate34 = LeaveSetting::where('coordinate', 'like', '%3.4%')->first();
    $coordinate35 = LeaveSetting::where('coordinate', 'like', '%3.5%')->first();
    $coordinate36 = LeaveSetting::where('coordinate', 'like', '%3.6%')->first();
    $coordinate51 = LeaveSetting::where('coordinate', 'like', '%5.1%')->get();
    $coordinate52 = LeaveSetting::where('coordinate', 'like', '%5.2%')->get();
    $coordinate53 = LeaveSetting::where('coordinate', 'like', '%5.3%')->get();
    
    $overtimes = [];
    $coordinate12values = [];
    $coordinate13values = [];
    $coordinate14values = [];
    $coordinate43values = [];
    $coordinate44values = [];
    $coordinate45values = [];
    $coordinate46values = [];
    $coordinate54values = [];
    $coordinate55values = [];
    $coordinate56values = [];
    $coordinate33values = [];
    $coordinate34values = [];
    $coordinate35values = [];
    $coordinate36values = [];
    $coordinate51values = [];
    $coordinate52values = [];
    $coordinate53values = [];
    $basic_salaries = [];
    $total_deductions= [];
    foreach ($salaries as $salary) {

      $overtime = Overtime::selectRaw("
        sum(case when amount = 1 then hour else 0 end) ot_1,
        sum(case when amount = 1.5 then hour else 0 end) ot_15,
        sum(case when amount = 2 then hour else 0 end) ot_20,
        sum(case when amount = 3 then hour else 0 end) ot_30,
        sum(case when amount = 4 then hour else 0 end) ot_40,
        sum(case when amount = 1 then final_salary::numeric else 0 end) value_1,
        sum(case when amount = 1.5 then final_salary::numeric else 0 end) value_15,
        sum(case when amount = 2 then final_salary::numeric else 0 end) value_20,
        sum(case when amount = 3 then final_salary::numeric else 0 end) value_30,
        sum(case when amount = 4 then final_salary::numeric else 0 end) value_40")
        ->where('overtimes.employee_id', $salary->employee_id)
        ->where('overtimes.month', '=', date('m', strtotime($salary->period)))
        ->where('overtimes.year', '=', date('Y', strtotime($salary->period)))
        ->first();
    
      $overtimes[$salary->id] = $overtime;
      // Jumlah Jam Overtime
      if($overtimes[$salary->id]){
        $total_jam = $overtimes[$salary->id]->ot_1 + ($overtimes[$salary->id]->ot_15 * 1.5) + ($overtimes[$salary->id]->ot_20 * 2) + ($overtimes[$salary->id]->ot_30 * 3) + ($overtimes[$salary->id]->ot_40 * 4);
      }else{
        $total_jam = 0;
      }
      // Total Overtime
      if ($overtimes[$salary->id]) {
        $value_overtime = $overtimes[$salary->id]->value_1 + $overtimes[$salary->id]->value_15 + $overtimes[$salary->id]->value_20 + $overtimes[$salary->id]->value_30 + $overtimes[$salary->id]->value_40;
      } else {
        $value_overtime = 0;
      }
      // Price Overtime
      if ($value_overtime > 0) {
        $everage_overtime = $value_overtime / $total_jam;
      } else {
        $everage_overtime = 0;
      }
      // Basic Salary
      $basic_salary = SalaryReportDetail::whereRaw("(description = 'Basic Salary' or description = 'Basic Salary + Allowance')")->where('salary_report_id', $salary->id)->first();
      $basic_salaries[$salary->id] = $basic_salary;
      
      // coordinate12
      if ($coordinate12) {
        $coordinate12value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('group_allowance_id', $coordinate12->id)->get()->sum('total');
      } else {
        $coordinate12value = 0.0;
      }
      $coordinate12values[$salary->id] = $coordinate12value;

      // coordinate13
      if ($coordinate13) {
        $coordinate13value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('group_allowance_id', $coordinate13->id)->get()->sum('total');
      } else {
        $coordinate13value = 0.0;
      }
      $coordinate13values[$salary->id] = $coordinate13value;

      // coordinate14
      if ($coordinate14) {
        $coordinate14value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('group_allowance_id', $coordinate14->id)->get()->sum('total');
      } else {
        $coordinate14value = 0.0;
      }
      $coordinate14values[$salary->id] = $coordinate14value;

      // coordinate43
      if ($coordinate43) {
        $coordinate43value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('group_allowance_id', $coordinate43->id)->get()->sum('total');
      } else {
        $coordinate43value = 0.0;
      }
      $coordinate43values[$salary->id] = $coordinate43value;

      // coordinate44
      if ($coordinate44) {
        $coordinate44value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('group_allowance_id', $coordinate44->id)->get()->sum('total');
      } else {
        $coordinate44value = 0.0;
      }
      $coordinate44values[$salary->id] = $coordinate44value;

      // coordinate45
      if ($coordinate45) {
        $coordinate45value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('group_allowance_id', $coordinate45->id)->get()->sum('total');
      } else {
        $coordinate45value = 0.0;
      }
      $coordinate45values[$salary->id] = $coordinate45value;

      // coordinate46
      $driverallowance = SalaryReportDetail::where('salary_report_id', $salary->id)->where('description', 'Driver Allowance')->get()->sum('total');
      $mealallowance = SalaryReportDetail::where('salary_report_id', $salary->id)->where('description', 'Tunjangan Makan')->get()->sum('total');
      if ($coordinate46) {
        $coordinate46value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('group_allowance_id', $coordinate46->id)->get()->sum('total');
      } else {
        $coordinate46value = 0.0;
      }
      $coordinate46values[$salary->id] = $coordinate46value + $driverallowance + $mealallowance;

      // coordinate54
      if ($coordinate54) {
        $coordinate54value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('description', '=','Potongan PPh 21')->get()->sum('total');
      } else {
        $coordinate54value = 0.0;
      }
      $coordinate54values[$salary->id] = $coordinate54value;

      // coordinate55
      if ($coordinate55) {
        $coordinate55value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('group_allowance_id', $coordinate55->id)->get()->sum('total');
      } else {
        $coordinate55value = 0.0;
      }
      $coordinate55values[$salary->id] = $coordinate55value;
      // coordinate55
      if ($coordinate56) {
        $coordinate56value = SalaryReportDetail::where('salary_report_id', $salary->id)->where('group_allowance_id', $coordinate56->id)->get()->sum('total');
      } else {
        $coordinate56value = 0.0;
      }
      $coordinate56values[$salary->id] = $coordinate56value;

      // deduction
      $deduction = SalaryReportDetail::where('salary_report_id', $salary->id)->where('status', 'Salary Deduction')->get()->sum('total');

      if ($basic_salaries[$salary->id]) {
        $jumlah_month = $coordinate12values[$salary->id] + $coordinate13values[$salary->id] + $coordinate14values[$salary->id] + $basic_salaries[$salary->id]->total;
      }else{
        $jumlah_month = 0;
      }
      // Coordinate33
      if($coordinate33){
        $coordinate33value = Leave::where('leave_setting_id', $coordinate33->id)->where('employee_id', $salary->employee_id)->where('status', 1)->get()->sum('duration');
      }else{
        $coordinate33value = 0;
      }
      $coordinate33values[$salary->id] = $coordinate33value;
      // Coordinate34
      if ($coordinate34) {
        $coordinate34value = Leave::where('leave_setting_id', $coordinate34->id)->where('employee_id', $salary->employee_id)->where('status', 1)->get()->sum('duration');
      } else {
        $coordinate34value = 0;
      }
      $coordinate34values[$salary->id] = $coordinate34value;
      // Coordinate35
      if ($coordinate35) {
        $coordinate35value = Leave::where('leave_setting_id', $coordinate35->id)->where('employee_id', $salary->employee_id)->where('status', 1)->get()->sum('duration');
      } else {
        $coordinate35value = 0;
      }
      $coordinate35values[$salary->id] = $coordinate35value;
      // Coordinate34
      if ($coordinate36) {
        $coordinate36value = Leave::where('leave_setting_id', $coordinate36->id)->where('employee_id', $salary->employee_id)->where('status', 1)->get()->sum('duration');
      } else {
        $coordinate36value = 0;
      }
      $coordinate36values[$salary->id] = $coordinate36value;

      // Jumlah pendapatan
      if($jumlah_month){
        $jumlah_pendapatan = $jumlah_month + $value_overtime + $coordinate43values[$salary->id] + $coordinate44values[$salary->id] + $coordinate45values[$salary->id] + $coordinate46values[$salary->id];
      }else{
        $jumlah_pendapatan = 0;
      }
      
      // Coordinate51
      if($coordinate51){
        $coordinate51value = 0;
        foreach($coordinate51 as $row){
          $value = AlphaPenalty::select('alpha_penalties.*')
            ->leftJoin('leaves', 'leaves.id', '=', 'alpha_penalties.leave_id')
            ->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id')
            ->where('leaves.employee_id', $salary->employee_id)
            ->where('leaves.status', 1)
            ->where('leaves.leave_setting_id', $row->id)
            ->get()->sum('penalty');
          $coordinate51value += $value;
        }
      }else{
        $coordinate51value =0;
      }
      $coordinate51values[$salary->id] = $coordinate51value;

      // Coordinate52
      if ($coordinate52) {
        $coordinate52value = 0;
        foreach($coordinate52 as $row){
          $value = AlphaPenalty::select('alpha_penalties.*')
          ->leftJoin('leaves', 'leaves.id', '=', 'alpha_penalties.leave_id')
          ->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id')
          ->where('leaves.employee_id', $salary->employee_id)
          ->where('leaves.status', 1)
          ->where('leaves.leave_setting_id', $row->id)
          ->get()->sum('penalty');
          $coordinate52value += $value;
        }
      } else {
        $coordinate52value = 0;
      }
      $coordinate52values[$salary->id] = $coordinate52value;
      // Coordinate53
      if ($coordinate53) {
        $coordinate53value = 0;
        foreach($coordinate53 as $row){
          $value = AlphaPenalty::select('alpha_penalties.*')
          ->leftJoin('leaves', 'leaves.id', '=', 'alpha_penalties.leave_id')
          ->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id')
          ->where('leaves.employee_id', $salary->employee_id)
          ->where('leaves.status', 1)
          ->where('leaves.leave_setting_id', $row->id)
          ->get()->sum('penalty');
          $coordinate53value += $value;
        }
      } else {
        $coordinate53value = 0;
      }
      $coordinate53values[$salary->id] = $coordinate53value;

      // Jumlah Potongan
      $jumlah_potongan = $coordinate51values[$salary->id] + $coordinate52values[$salary->id] + $coordinate53values[$salary->id] + $coordinate54values[$salary->id] + $coordinate55values[$salary->id] + $coordinate56values[$salary->id];
      $grand_total = $jumlah_pendapatan - $jumlah_potongan - $deduction ;
      // dd($coordinate51values[$salary->id]);
    }
    // dd($overtimes);
    // $overtime->where('month', date('m', strtotime($salary->period)));
    // $overtime->where('year', date('Y', strtotime($salary->period)));
    //$overtime->get();
    // $leavesetting = LeaveSetting::get();
    // $leavesetting->coordinate = explode(',', $leavesetting->coordinate);
   

    // $coordinate12 = DB::table('salary_reports');
    // $coordinate12->select('salary_reports.*', 'group_allowances.name as allowance_name');
    // $coordinate12->leftJoin('salary_report_details', 'salary_report_details.salary_report_id', '=', 'salary_reports.id');
    // $coordinate12->leftJoin('group_allowances', 'group_allowances.id', '=', 'salary_report_details.group_allowance_id');
    // // $coordinate12->where('group_allowances.coordinate', '1.2');
    // $coordinate12->whereIn('salary_reports.id', $id);
    // $data = $coordinate12->get();

    // $coordinate12 = SalaryReport::with('employee')->with(['salarydetail' => function ($q) {
    //   $q->with(['groupAllowance' => function ($q1) {
    //     $q1->where('name','Insentif');
    //   }]);
    // }])->whereIn('id', $id)->first();

    // $coordinate13 = SalaryReport::with('employee')->with(['salarydetail' => function ($q) {
    //   $q->with(['groupAllowance'])->where('coordinate', '1.3');
    // }])->whereIn('id', $id)->first();
    foreach ($salaries as $salary) {
      $salary->print_status = 1;
      $salary->save();
    }
    // return response()->json($coordinate12);
    // dd($coordinate13);
    
    
    
    return view('admin.salaryreport.newprint', compact('salaries','overtimes', 'coordinate12', 'coordinate13', 'coordinate14',
  'coordinate43', 'coordinate44', 'coordinate45', 'coordinate46', 'coordinate54', 'coordinate55' , 'coordinate56', 'coordinate33',
  'coordinate34', 'coordinate35', 'coordinate36', 'coordinate51', 'coordinate52', 'coordinate53', 'coordinate12values',
  'coordinate13values','coordinate14values','coordinate43values','coordinate44values','coordinate45values','coordinate46values',
  'coordinate54values','coordinate55values','coordinate56values','basic_salaries','jumlah_month', 'total_jam', 'value_overtime', 'everage_overtime',
  'coordinate33values','coordinate34values','coordinate35values','coordinate36values','jumlah_pendapatan','coordinate51values',
  'coordinate52values','coordinate53values','jumlah_potongan', 'grand_total'));
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
      $report = SalaryReport::find($id);
      $report->delete();
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

  public function print_pdf()
  {
    return view('admin.salaryreport.report');
  }

  public function pdf($id)
  {
    $report = SalaryReport::with('salarydetail')->with('employee')->find($id);
    $employee = Employee::with('department')->with('title')->find($report->employee_id);
    return view('admin.salaryreport.report', compact('employee', 'report'));
    // $data = ['title' => 'Slip Gaji PT.Bosung Indonesia'];

    // $pdf = PDF::loadView('admin/salaryreport/report', compact('report', 'employee'));
    // $pdf->setPaper('A4', 'landscape');
    // set_time_limit(5000);
    // return $pdf->download('Salary-Report.pdf');
  }

  public function getGroupAllowance(string $type = 'ADDITIONAL')
  {
    $query    = GroupAllowance::where('status', 1)->where('group_type', $type);
    $groups   = $query->get();

    return $groups;
  }
  public function getLeaveSetting()
  {
    $leavesetting = LeaveSetting::where('status',1)->get();

    return $leavesetting;
  }

  public function getExportData(Request $request)
  {
    $department = $request->department;
    $workgroup  = $request->workgroup_id;
    $month      = $request->montly;
    $year       = $request->year;

    $additionals= $this->getGroupAllowance();
    $deductions = $this->getGroupAllowance('DEDUCTION');
    $leaveSettings = $this->getLeaveSetting();
    
    $select = '';
    $select .= "employees.nid as nik, employees.name as name, departments.name as department_name,employees.account_no as account_no,
    employees.join_date as join_date, employees.ptkp as st, employees.npwp as npwp,";
    $select .= "max(details.basic_salary) as basic_salary,";
    $select .= "attendances.wt as wt,";
    $select .= "max(details.daily_salary) as daily_salary,";
    $select .= "max(overtimes.ot_1) as ot_1,";
    $select .= "max(overtimes.otn_1) as otn_1,";
    $select .= "max(overtimes.ot_15) as ot_15,";
    $select .= "max(overtimes.otn_15) as otn_15,";
    $select .= "max(overtimes.ot_20) as ot_20,";
    $select .= "max(overtimes.otn_20) as otn_20,";
    $select .= "max(overtimes.ot_30) as ot_30,";
    $select .= "max(overtimes.otn_30) as otn_30,";
    $select .= "max(overtimes.ot_40) as ot_40,";
    $select .= "max(overtimes.otn_40) as otn_40,";
    foreach ($additionals as $key => $value) {
      $alias = strtolower(str_replace([" ", "/", "+", "-"], "_", $value->name));
      $select .= "max(details.$alias) as $alias,";
    }
    $select .= "min(details.alpha_penalty) as alpha_penalty,";
    $select .= "max(details.spsi) as spsi,";
    $select .= "max(details.pph) as pph,";
    $select .= "sum(details.de_non_pph) as de_non_pph,";
    $select .= "sum(details.add_non_pph) as add_non_pph,";
    foreach ($deductions as $key => $value) {
      $alias = strtolower(str_replace([" ", "/", "+", "-"], "_", $value->name));
      $select .= "max(details.$alias) as $alias,";
    }
    $select .= " null";

    $selectJoin = '';
    foreach ($additionals as $key => $value) {
      $alias = strtolower(str_replace([" ", "/", "+", "-"], "_", $value->name));
      $selectJoin .= "case when description = '$value->name' then total else 0 end as $alias,";
    }
    foreach ($deductions as $key => $value) {
      $alias = strtolower(str_replace([" ", "/", "+", "-"], "_", $value->name));
      $selectJoin .= "case when description = '$value->name' then total else 0 end as $alias,";
    }
    // foreach ($leaveSettings as $key => $value) {
    //   $alias = strtolower(str_replace([" ", "/", "+", "-"], "_", $value->name));
    //   $selectJoin .= "case when leave_name = '$value->leave_name' then duration else 0 end as $alias,";
    // }
    $selectJoin .= " null";

    $salary = SalaryReport::SelectRaw("$select");
    $salary->leftJoin('employees', 'employees.id', '=', 'salary_reports.employee_id');
    $salary->leftJoin(DB::raw("(select
        employee_id,
        salary_report_id,
        description,
        case when description = 'Basic Salary' then total else 0 end as basic_salary,
        case when description = 'Gaji Harian' then total else 0 end as daily_salary,
        case when description = 'Potongan absen' then total else 0 end as alpha_penalty,
        case when description = 'Potongan SPSI' then total else 0 end as spsi,
        case when description = 'Potongan PPh 21' then total else 0 end as pph,
        case when is_added = 'YES' and type = '1' then total else 0 end as add_non_pph,
        case when is_added = 'YES' and type = '0' then total else 0 end as de_non_pph,
        $selectJoin
        from salary_report_details) details"), function($join){
      $join->on('details.employee_id', '=', 'employees.id');
      $join->on('details.salary_report_id', '=', 'salary_reports.id');
    });
    $salary->leftJoin(DB::raw("(select 
        employee_id,
        sum(case when amount = 1 then hour else 0 end) ot_1,
        sum(case when amount = 1 then final_salary else 0 end) otn_1,
        sum(case when amount = 1.5 then hour else 0 end) ot_15,
        sum(case when amount = 1.5 then final_salary else 0 end) otn_15,
        sum(case when amount = 2 then hour else 0 end) ot_20,
        sum(case when amount = 2 then final_salary else 0 end) otn_20,
        sum(case when amount = 3 then hour else 0 end) ot_30,
        sum(case when amount = 3 then final_salary else 0 end) otn_30,
        sum(case when amount = 4 then hour else 0 end) ot_40,
        sum(case when amount = 4 then final_salary else 0 end) otn_40
        from overtimes where extract(month from date) = $month and extract(year from date) = $year group by employee_id) overtimes"), 'employees.id', '=', 'overtimes.employee_id');
    // $salary->leftJoin(DB::raw("(select employee_id, 
    //   sum(case when leave_setting_id = leave_settings.id then duration else 0 end) as leave_duration 
    //   from leaves where status = 1) leave"), function($join){
    //     $join->on('leave.employee_id', '=', 'employees.id');
    //     $join->on('leave_settings.id', '=', 'leave.leave_setting_id');
    //   });
    $salary->leftJoin(DB::raw("(select employee_id, sum(adj_working_time) as wt from attendances where status = 1 and extract(month from attendance_date) = $month and extract(year from attendance_date) = $year group by employee_id) as attendances"), 'attendances.employee_id', '=', 'salary_reports.employee_id');
    $salary->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
    $salary->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
    $salary->whereMonth('salary_reports.period', $month)->whereYear('salary_reports.period', $year);
    if ($department) {
      $string = '';
      foreach ($department as $dept) {
        $string .= "departments.path like '%$dept%'";
        if (end($department) != $dept) {
          $string .= ' or ';
        }
      }
      $salary->whereRaw('(' . $string . ')');
    }
    if ($workgroup) {
      $salary->whereIn('employees.workgroup_id', $workgroup);
    }
    // $salary->where('salary_reports.id', 27692);
    $salary->orderBy('departments.name', 'asc');
    $salary->groupBy('employees.nid', 'employees.name', 'departments.name', 'attendances.wt', 'employees.account_no','employees.join_date', 'employees.ptkp', 'employees.npwp');
    $salary_reports = $salary->get();

    return $salary_reports;
  }

  public function getOvertimeScheme()
  {
    $query  = OvertimeSchemeList::select('amount')->groupBy('amount')->orderBy('amount');

    return $query->get();
  }

  public function exportsalary(Request $request)
  {
    $object           = new \PHPExcel();
    $object->getProperties()->setCreator('Bosung Indonesia');
    $object->setActiveSheetIndex(0);
    $sheet            = $object->getActiveSheet();

    $salaries         = $this->getExportData($request);
    $additionals      = $this->getGroupAllowance();
    $deductions       = $this->getGroupAllowance('DEDUCTION');
    $overtimeScheme   = $this->getOvertimeScheme();

    // Header Column Excel
    $column = 0;
    $row    = 2;
    $sheet->mergeCellsByColumnAndRow($column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'No')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'NIK')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Name')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Department')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Bank Account Number')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Basic Salary')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column + 1, $row)->setCellValueByColumnAndRow($column, $row, '100%')->getStyleByColumnAndRow($column, $row, $column + 1, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->setCellValueByColumnAndRow($column, $row + 1, 'WT')->getStyleByColumnAndRow($column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValueByColumnAndRow(++$column, $row + 1, 'Rp')->getStyleByColumnAndRow($column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    ++$column;
    foreach ($overtimeScheme as $key => $value) {
      $sheet->mergeCellsByColumnAndRow($column, $row, $column + 1, $row)->setCellValueByColumnAndRow($column, $row, $value->amount * 100 . '%')->getStyleByColumnAndRow($column, $row, $column + 1, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $sheet->setCellValueByColumnAndRow($column, $row + 1, 'OT')->getStyleByColumnAndRow($column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $sheet->setCellValueByColumnAndRow(++$column, $row + 1, 'Rp')->getStyleByColumnAndRow($column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $column++;
    }
    $sheet->mergeCellsByColumnAndRow($column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Total Overtime')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    ++$column;
    foreach ($additionals as $key => $value) {
      $sheet->mergeCellsByColumnAndRow($column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, $value->name)->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $column++;
    }
    $sheet->mergeCellsByColumnAndRow($column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Potongan Absen')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Tambahan Non PPh')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Gaji Bruto')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    ++$column;
    foreach ($deductions as $key => $value) {
      $sheet->mergeCellsByColumnAndRow($column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, $value->name)->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $column++;
    }
    $sheet->mergeCellsByColumnAndRow($column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'SPSI')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Potongan Non PPH')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'PPh 21')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'Gaji Net')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    $column_number  = 0;
    $row_number     = $row + 2;
    $number         = 1;
    $bruto          = 0;
    $gross          = 0;
    $net            = 0;
    $last_col       = 0;

    foreach ($salaries as $key => $value) {
      $totalOvertime = $value->otn_15 + $value->otn_20 + $value->otn_30 + $value->otn_40;
      $bruto += $totalOvertime + $value->basic_salary + $value->daily_salary;
      $sheet->setCellValueByColumnAndRow($column_number, $row_number, $number);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->nik);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->name);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->department_name);
      $sheet->setCellValueExplicitByColumnAndRow(++$column_number, $row_number, $value->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->basic_salary ? $value->basic_salary : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->wt ? $value->wt : '-');
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->daily_salary ? $value->daily_salary : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->ot_15 ? $value->ot_15 : 0);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->otn_15 ? $value->otn_15 : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->ot_20 ? $value->ot_20 : 0);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->otn_20 ? $value->otn_20 : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->ot_30 ? $value->ot_30 : 0);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->otn_30 ? $value->otn_30 : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->ot_40 ? $value->ot_40 : 0);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->otn_40 ? $value->otn_40 : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $totalOvertime ? $totalOvertime : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      foreach ($additionals as $key => $additional) {
        $alias = strtolower(str_replace([" ", "/", "+", "-"], "_", $additional->name));
        $bruto += $value->{$alias};
        $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->{$alias} ? $value->{$alias} : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      }
      $bruto += $value->alpha_penalty;
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->alpha_penalty ? $value->alpha_penalty : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $bruto += $value->add_non_pph;
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->add_non_pph ? $value->add_non_pph : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $bruto ? $bruto : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");

      foreach ($deductions as $key => $deduction) {
        $alias = strtolower(str_replace([" ", "/", "+", "-"], "_", $deduction->name));
        $gross += $value->{$alias};
        $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->{$alias} ? $value->{$alias} : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      }
      $gross += $value->spsi;
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->spsi ? $value->spsi : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $gross += $value->de_non_pph;
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->de_non_pph ? $value->de_non_pph : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $gross += $value->pph;
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->pph ? $value->pph : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $net = $bruto - $gross;
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $net ? $net : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");

      $bruto = 0;
      $net  = 0;
      $gross  = 0;
      $last_col = $column_number;
      $column_number = 0;
      $row_number++;
      $number++;
    }

    $sheet->getStyleByColumnAndRow(0, 2, $last_col, 3)->getAlignment()->setWrapText(true);
    for ($i=0; $i <= $last_col; $i++) { 
      $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
    }
    $sheet->calculateColumnWidths();
    for ($i=0; $i <= $last_col; $i++) { 
      $sheet->getColumnDimensionByColumn($i)->setAutoSize(false);
    }
    for ($i=16; $i <= $last_col; $i++) { 
      $sheet->getColumnDimensionByColumn($i)->setWidth(12);
    }

    $sheet->getPageSetup()->setFitToWidth(1);
    $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
    ob_start();
    $objWriter->save('php://output');
    $export = ob_get_contents();
    ob_end_clean();
    header('Content-Type: application/json');
    if ($salaries->count() > 0) {
      return response()->json([
        'status'    => true,
        'name'      => 'salary-report-' . date('d-m-Y') . '.xlsx',
        'message'   => "Success Download Salary Report Data",
        'file'      => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
      ], 200);
    } else {
      return response()->json([
        'status'     => false,
        'message'    => "Data not found",
      ], 400);
    }
  }

  public function exportsalary2(Request $request)
  {
    $employee = $request->name; 
    $department = $request->department;
    $workgroup = $request->workgroup_id;
    $month = $request->montly;
    $year = $request->year;

    $object = new \PHPExcel();
    $object->getProperties()->setCreator('Bosung Indonesia');
    $object->setActiveSheetIndex(0);
    $sheet = $object->getActiveSheet();

    $salary = SalaryReport::select(
      'salary_reports.*',
      'salary_reports.employee_id',
      'work_groups.name as workgroup_name',
      'departments.name as department_name',
      'employees.nid as nik',
      'employees.name as employee_name',
      'employees.account_bank as account_name',
      'employees.account_no as account_no'
    );
    $salary->leftJoin('employees', 'employees.id', '=', 'salary_reports.employee_id');
    $salary->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
    $salary->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
    $salary->whereMonth('salary_reports.period', $month)->whereYear('salary_reports.period', $year);
    if ($department) {
      $string = '';
      foreach ($department as $dept) {
        $string .= "departments.path like '%$dept%'";
        if (end($department) != $dept) {
          $string .= ' or ';
        }
      }
      $salary->whereRaw('(' . $string . ')');
    }
    if ($workgroup) {
      $salary->whereIn('employees.workgroup_id', $workgroup);
    }
    $salary_reports = $salary->get();

    // Get Additional Allowance
    $query = Allowance::leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    $query->where('allowance_categories.type', '=', 'additional');
    $query->orderBy('allowances.allowance', 'asc');
    $additionals = $query->get();
    $addition_name = [];

    // Get Deduction Allowance
    $query = Allowance::leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    $query->where('allowance_categories.type', '=', 'deduction');
    $query->orderBy('allowances.allowance', 'asc');
    $deductions = $query->get();
    $deduction_name = [];

    // Header Columne Excel
    $sheet->mergeCells('A1:F1');
    $sheet->setCellValue('A1', 'Personal Data')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A2', 'Workgroup Combination');
    $sheet->setCellValue('B2', 'Department');
    $sheet->setCellValue('C2', 'NIK');
    $sheet->setCellValue('D2', 'Nama');
    $sheet->setCellValue('E2', 'Bank Account Name');
    $sheet->setCellValue('F2', 'Bank Account Number');
    $column_number = 7;
    $sheet->setCellValue('G1', 'Additional')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('G2', 'Basic Salary');
    foreach ($additionals as $key => $additional) {
      $sheet->setCellValueByColumnAndRow($column_number, 2, $additional->allowance);
      $column_number++;
    }
    $sheet->setCellValueByColumnAndRow($column_number, 2, 'Overtime');
    $sheet->setCellValueByColumnAndRow(++$column_number, 2, 'Driver Allowance');
    $sheet->setCellValueByColumnAndRow(++$column_number, 2, 'Potongan absen');
    $sheet->setCellValueByColumnAndRow(++$column_number, 2, 'Tunjangan Kehadiran');
    $sheet->mergeCellsByColumnAndRow(6, 1, $column_number, 1);
    $sheet->setCellValueByColumnAndRow(++$column_number, 1, 'Gross Salary')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCellsByColumnAndRow($column_number, 1, $column_number, 2);
    $sheet->setCellValueByColumnAndRow(++$column_number, 1, 'Deduction')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $col_deduction = $column_number;
    foreach ($deductions as $key => $deduction) {
      $sheet->setCellValueByColumnAndRow($column_number, 2, $deduction->allowance);
      $column_number++;
    }
    $sheet->setCellValueByColumnAndRow($column_number, 2, 'Potongan SPSI');
    $sheet->mergeCellsByColumnAndRow($col_deduction, 1, $column_number, 1);
    $sheet->setCellValueByColumnAndRow(++$column_number, 1, 'Deduction Salary')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCellsByColumnAndRow($column_number, 1, $column_number, 2);
    $sheet->setCellValueByColumnAndRow(++$column_number, 1, 'Net Salary')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCellsByColumnAndRow($column_number, 1, $column_number, 2);
    $sheet->setCellValueByColumnAndRow(++$column_number, 1, 'WT')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCellsByColumnAndRow($column_number, 1, $column_number, 2);
    $sheet->setCellValueByColumnAndRow(++$column_number, 1, 'OT')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCellsByColumnAndRow($column_number, 1, $column_number, 2);
    // $sheet->setCellValue('AM1', 'WT')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    // $sheet->mergeCellsByColumnAndRow($column_number, 1, $column_number, 2);
    // $sheet->setCellValue('AN1', 'OT')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    // $sheet->mergeCellsByColumnAndRow($column_number, 1, $column_number, 2);
    $row_number = 3;

    foreach ($salary_reports as $reports) {
      $wt = Attendance::where('employee_id', $reports->employee_id)->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)->where('status', 1)->sum('adj_working_time');
      $ot = Attendance::where('employee_id', $reports->employee_id)->whereMonth('attendance_date', $month)->whereYear('attendance_date', $year)->where('status', 1)->sum('adj_over_time');
      $basic_salary = SalaryReportDetail::where('salary_report_id', $reports->id)->where('description', 'Basic Salary')->first();
      $overtime = SalaryReportDetail::where('salary_report_id', $reports->id)->where('description', 'Overtime')->first();
      $driver_allowance = SalaryReportDetail::where('salary_report_id', $reports->id)->where('description', 'Driver Allowance')->first();
      $penalty = SalaryReportDetail::where('salary_report_id', $reports->id)->where('description', 'Potongan absen')->first();
      $kehadiran = SalaryReportDetail::where('salary_report_id', $reports->id)->where('description', 'Tunjangan Kehadiran')->first();
      $spsi = SalaryReportDetail::where('salary_report_id', $reports->id)->where('description', 'Potongan SPSI')->first();

      $sheet->setCellValue('A' . $row_number, $reports->workgroup_name);
      $sheet->setCellValue('B' . $row_number, $reports->department_name);
      $sheet->setCellValue('C' . $row_number, $reports->nik);
      $sheet->setCellValue('D' . $row_number, $reports->employee_name);
      $sheet->setCellValue('E' . $row_number, $reports->account_name ? $reports->account_name : '-');
      $sheet->setCellValueExplicit('F' . $row_number, $reports->account_no ? $reports->account_no : '-', PHPExcel_Cell_DataType::TYPE_STRING);
      if ($basic_salary) {
        $sheet->setCellValue('G' . $row_number, $basic_salary->total);
      } else {
        $sheet->setCellValue('G' . $row_number, 0);
      }
      $col = 7;
      foreach ($additionals as $key => $name) {
        $additional = SalaryReportDetail::where('salary_report_id', $reports->id)->where('type', 1)->where('description', $name->allowance)->first();
        if ($additional) {
          $sheet->setCellValueByColumnAndRow($col, $row_number, round($additional->total));
        } else {
          $sheet->setCellValueByColumnAndRow($col, $row_number, 0);
        }
        $col++;
      }
      if ($overtime) {
        $sheet->setCellValueByColumnAndRow($col, $row_number, round($overtime->total));
      } else {
        $sheet->setCellValueByColumnAndRow($col, $row_number, 0);
      }
      if ($driver_allowance) {
        $sheet->setCellValueByColumnAndRow(++$col, $row_number, round($driver_allowance->total));
      } else {
        $sheet->setCellValueByColumnAndRow(++$col, $row_number, 0);
      }
      if ($penalty) {
        $sheet->setCellValueByColumnAndRow(++$col, $row_number, round($penalty->total));
      } else {
        $sheet->setCellValueByColumnAndRow(++$col, $row_number, 0);
      }
      if ($kehadiran) {
        $sheet->setCellValueByColumnAndRow(++$col, $row_number, round($kehadiran->total));
      } else {
        $sheet->setCellValueByColumnAndRow(++$col, $row_number, 0);
      }
      $sheet->setCellValueByColumnAndRow(++$col, $row_number, round($reports->gross_salary));
      ++$col;
      foreach ($deductions as $key => $name) {
        $deduction = SalaryReportDetail::where('salary_report_id', $reports->id)->where('type', 0)->where('description', $name->allowance)->first();
        if ($deduction) {
          $sheet->setCellValueByColumnAndRow($col, $row_number, round($deduction->total));
        } else {
          $sheet->setCellValueByColumnAndRow($col, $row_number, 0);
        }
        $col++;
      }
      if ($spsi) {
        $sheet->setCellValueByColumnAndRow($col, $row_number, $spsi->total);
      } else {
        $sheet->setCellValueByColumnAndRow($col, $row_number, 0);
      }
      $sheet->setCellValueByColumnAndRow(++$col, $row_number, round($reports->deduction));
      $sheet->setCellValueByColumnAndRow(++$col, $row_number, round($reports->net_salary));
      $sheet->setCellValueByColumnAndRow(++$col, $row_number, $wt);
      $sheet->setCellValueByColumnAndRow(++$col, $row_number, $ot ? $ot : 0);
      $row_number++;
    }
    foreach (range(0, $column_number) as $column) {
      $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
      $sheet->getCellByColumnAndRow($column, 1)->getStyle()->getFont()->setBold(true);
      $sheet->getCellByColumnAndRow($column, 2)->getStyle()->getFont()->setBold(true);
      $sheet->getCellByColumnAndRow($column, 1)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }
    $sheet->getPageSetup()->setFitToWidth(1);
    $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
    ob_start();
    $objWriter->save('php://output');
    $export = ob_get_contents();
    ob_end_clean();
    header('Content-Type: application/json');
    if ($salary_reports->count() > 0) {
      return response()->json([
        'status'     => true,
        'name'        => 'salary-report-' . date('d-m-Y') . '.xlsx',
        'message'    => "Success Download Salary Report Data",
        'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
      ], 200);
    } else {
      return response()->json([
        'status'     => false,
        'message'    => "Data not found",
      ], 400);
    }
  }

  public function newExport(Request $request)
  {
    $object           = new \PHPExcel();
    $object->getProperties()->setCreator('Taewon');
    $object->setActiveSheetIndex(0);
    $sheet            = $object->getActiveSheet();

    $salaries         = $this->getExportData($request);
    $additionals      = $this->getGroupAllowance();
    $deductions       = $this->getGroupAllowance('DEDUCTION');
    $overtimeScheme   = $this->getOvertimeScheme();

    // Header Column Excel
    $column = 0;
    $row    = 2;
    $sheet->mergeCellsByColumnAndRow($column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'No')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'STATUS')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'DEPT.')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'NIK')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'NAMA')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'TGL.' . "\n" . 'MASUK')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'ST.')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'NO.REK')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'NPWP')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'GAJI POKOK')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column + 3, $row)->setCellValueByColumnAndRow($column, $row, 'O.T.')->getStyleByColumnAndRow($column, $row, $column + 1, $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->setCellValueByColumnAndRow($column, $row + 1, '150%')->getStyleByColumnAndRow($column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValueByColumnAndRow(++$column, $row + 1, '200%')->getStyleByColumnAndRow($column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValueByColumnAndRow(++$column, $row + 1, '300%')->getStyleByColumnAndRow($column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValueByColumnAndRow(++$column, $row + 1, '400%')->getStyleByColumnAndRow($column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    // ++$column;

    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'ACT. JAM O.T')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'TOTAL JAM O.T')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'O.T / JAM')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'TOTAL OVERTIME')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'HARI KERJA')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'HARI LIBUR')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'HARI CUTI')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'TOTAL CUTI')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'HARI IJIN')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'HARI ALPA')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'TOTAL ALPA')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'HARI S.D')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'TTL S.D')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    ++$column;
    foreach ($additionals as $key => $value) {
      $sheet->mergeCellsByColumnAndRow($column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, $value->name)->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
      $column++;
    }
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'TUNJ RITASI')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'GAJI KOTOR 1')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'ptkp')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'pkp program')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'PPH21 program')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'GAJI KOTOR 2')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'jamsostek 1')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'BPJS KET.KARY.(2%)')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'T.PENSIUN KARY.(1%)')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'BPJS KES.KARY.(1%)')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'GAJI KOTOR 3')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'PINJ ACC')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'GAJI BERSIH')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'jamsostek 2')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'BPJS KET.TAEWON(3,7119%)')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'T.PENSIUN TAEWON(2%)')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->mergeCellsByColumnAndRow(++$column, $row, $column, $row + 1)->setCellValueByColumnAndRow($column, $row, 'BPJS KES. TAEWON(4%)')->getStyleByColumnAndRow($column, $row, $column, $row + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

    

    $column_number  = 0;
    $row_number     = $row + 2;
    $number         = 1;
    $bruto          = 0;
    $gross          = 0;
    $net            = 0;
    $last_col       = 0;

    foreach ($salaries as $key => $value) {
      $totalOvertime = $value->otn_1 + $value->otn_15 + $value->otn_20 + $value->otn_30 + $value->otn_40;
      $acttotalJamOt = $value->ot_1 + $value->ot_15 + $value->ot_20 + $value->ot_30 + $value->ot_40;
      // dd($totalOvertime, $acttotalJamOt); 
      if($totalOvertime && $acttotalJamOt > 0){
        $otJam = $totalOvertime / $acttotalJamOt;
      }else{
        $otJam = 0;
      }
      $totalJamOt = $value->ot_1 + $value->ot_15 * 1.5 + $value->ot_20 * 2 + $value->ot_30 * 3 + $value->ot_40 * 4;
      $bruto += $totalOvertime + $value->basic_salary + $value->daily_salary;
      $sheet->setCellValueByColumnAndRow($column_number, $row_number, $number);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->department_name);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->nik);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->name);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->join_date);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->ptkp);
      $sheet->setCellValueExplicitByColumnAndRow(++$column_number, $row_number, $value->account_no, PHPExcel_Cell_DataType::TYPE_STRING);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->npwp ? $value->npwp : '-');
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->basic_salary ? $value->basic_salary : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->ot_15 ? $value->ot_15 : 0);
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->otn_15 ? $value->otn_15 : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->ot_20 ? $value->ot_20 : 0);
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->otn_20 ? $value->otn_20 : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->ot_30 ? $value->ot_30 : 0);
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->otn_30 ? $value->otn_30 : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->ot_40 ? $value->ot_40 : 0);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $acttotalJamOt? $acttotalJamOt : 0);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $totalJamOt ? $totalJamOt : 0);
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $otJam ? $otJam : 0)->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $totalOvertime ? $totalOvertime : 0)->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      foreach ($additionals as $key => $additional) {
        $alias = strtolower(str_replace([" ", "/", "+", "-"], "_", $additional->name));
        $bruto += $value->{$alias};
        $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->{$alias} ? $value->{$alias} : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      }
      // $bruto += $value->alpha_penalty;
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->alpha_penalty ? $value->alpha_penalty : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      // $bruto += $value->add_non_pph;
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->add_non_pph ? $value->add_non_pph : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $bruto ? $bruto : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");

      // foreach ($deductions as $key => $deduction) {
      //   $alias = strtolower(str_replace([" ", "/", "+", "-"], "_", $deduction->name));
      //   $gross += $value->{$alias};
      //   $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->{$alias} ? $value->{$alias} : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      // }
      // $gross += $value->spsi;
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->spsi ? $value->spsi : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      // $gross += $value->de_non_pph;
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->de_non_pph ? $value->de_non_pph : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      // $gross += $value->pph;
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $value->pph ? $value->pph : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");
      // $net = $bruto - $gross;
      // $sheet->setCellValueByColumnAndRow(++$column_number, $row_number, $net ? $net : '-')->getStyleByColumnAndRow($column_number, $row_number)->getNumberFormat()->setFormatCode("#,##0");

      $bruto = 0;
      $net  = 0;
      $gross  = 0;
      $last_col = $column_number;
      $column_number = 0;
      $row_number++;
      $number++;
    }

    $sheet->getStyleByColumnAndRow(0, 2, $last_col, 3)->getAlignment()->setWrapText(true);
    for ($i = 0; $i <= $last_col; $i++) {
      $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
    }
    $sheet->calculateColumnWidths();
    for ($i = 0; $i <= $last_col; $i++) {
      $sheet->getColumnDimensionByColumn($i)->setAutoSize(false);
    }
    for ($i = 16; $i <= $last_col; $i++) {
      $sheet->getColumnDimensionByColumn($i)->setWidth(12);
    }

    $sheet->getPageSetup()->setFitToWidth(1);
    $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
    ob_start();
    $objWriter->save('php://output');
    $export = ob_get_contents();
    ob_end_clean();
    header('Content-Type: application/json');
    if ($salaries->count() > 0) {
      return response()->json([
        'status'    => true,
        'name'      => 'salary-report-' . date('d-m-Y') . '.xlsx',
        'message'   => "Success Download Salary Report Data",
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