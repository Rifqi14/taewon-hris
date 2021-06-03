<?php

namespace App\Http\Controllers\Admin;

use App\Models\Employee;
use App\Models\EmployeeAllowance;
use App\Models\Allowance;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\WorkGroup;
use App\Models\Title;
use App\Models\AllowanceReport;
use App\Models\AllowanceRule;
use App\Models\EmployeeSalary;
use App\Models\AllowanceReportDetail;
use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Font;

class AllowanceReportController extends Controller
{
  function __construct()
  {
    View::share('menu_active', url('admin/' . 'allowancereport'));
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
    $search = strtoupper($request->search['value']);
    $sort = $request->columns[$request->order[0]['column']]['data'];
    $dir = $request->order[0]['dir'];
    $employee_id = $request->employee_id;
    $departments = $request->department;
    $position = $request->position;
    $workgroup = $request->workgroup;
    $month = $request->month;
    $year = $request->year;
    $nid = $request->nid;
    $status = $request->status;
    $type = $request->type;

    //Count Data
    $query = DB::table('allowance_reports');
    $query->select(
      'allowance_reports.*',
      'employees.name as employee_name',
      'employees.id as employee_id',
      'work_groups.name as workgroup'
    );
    $query->leftJoin('employees', 'employees.id', '=', 'allowance_reports.employee_id');
    $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
    $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
    $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
    if ($employee_id) {
      $query->whereIn('allowance_reports.employee_id', $employee_id);
    }
    if ($nid) {
      $query->whereRaw("employees.nid like '%$nid%'");
    }
    if ($month) {
      // $query->whereMonth('allowance_reports.period', '=', $month);
      $query->where(function($query1) use ($month){
        foreach ($month as $q_month) {
          $query1->orWhereRaw("EXTRACT(MONTH FROM period) = $q_month");
        }
      });
    }
    if ($year) {
      // $query->whereYear('allowance_reports.period', '=', $year);
      $query->where(function($query2) use ($year){
        foreach ($year as $q_year) {
          $query2->orWhereRaw("EXTRACT(YEAR FROM period) = $q_year");
        }
      });
    }
    if ($departments) {
      $string = '';
      $uniqdepartments = [];
      foreach($departments as $department){
          if(!in_array($department,$uniqdepartments)){
              $uniqdepartments[] = $department;
          }
      }
      $departments = $uniqdepartments;
      foreach ($departments as $department) {
        $string .= "departments.path like '%$department%'";
        if (end($departments) != $department) {
          $string .= ' or ';
        }
      }
      $query->whereRaw('(' . $string . ')');
    }
    if ($position != "") {
      $query->where('employees.title_id', '=', $position);
    }
    if ($workgroup != "") {
      $query->where('employees.workgroup_id', '=', $workgroup);
    }
    $recordsTotal = $query->count();

    //Select Pagination
    $query = DB::table('allowance_reports');
    $query->select(
      'allowance_reports.*',
      'employees.name as employee_name',
      'employees.id as employee_id',
      'work_groups.name as workgroup'
    );
    $query->leftJoin('employees', 'employees.id', '=', 'allowance_reports.employee_id');
    $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
    $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
    $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
    if ($employee_id) {
      $query->whereIn('allowance_reports.employee_id', $employee_id);
    }
    if ($nid) {
      $query->whereRaw("employees.nid like '%$nid%'");
    }
    if ($month) {
      // $query->whereMonth('allowance_reports.period', '=', $month);
      $query->where(function($query1) use ($month){
        foreach ($month as $q_month) {
          $query1->orWhereRaw("EXTRACT(MONTH FROM period) = $q_month");
        }
      });
    }
    if ($year) {
      // $query->whereYear('allowance_reports.period', '=', $year);
      $query->where(function($query2) use ($year){
        foreach ($year as $q_year) {
          $query2->orWhereRaw("EXTRACT(YEAR FROM period) = $q_year");
        }
      });
    }
    if ($departments) {
      $string = '';
      $uniqdepartments = [];
      foreach($departments as $department){
          if(!in_array($department,$uniqdepartments)){
              $uniqdepartments[] = $department;
          }
      }
      $departments = $uniqdepartments;
      foreach ($departments as $department) {
        $string .= "departments.path like '%$department%'";
        if (end($departments) != $department) {
          $string .= ' or ';
        }
      }
      $query->whereRaw('(' . $string . ')');
    }
    if ($position != "") {
      $query->where('employees.title_id', '=', $position);
    }
    if ($workgroup != "") {
      $query->where('employees.workgroup_id', '=', $workgroup);
    }
    $query->offset($start);
    $query->limit($length);
    $query->orderBy($sort, $dir);
    $reports = $query->get();
    // dd($reports);
    $data = [];
    foreach ($reports as $report) {
      $report->no = ++$start;
      $report->total = number_format($report->total, 0, ',', '.');
      $report->period = changeDateFormat('F - Y', $report->period);
      $data[] = $report;
    }
    // dd($data);
    return response()->json([
      'draw' => $request->draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsTotal,
      'data' => $data
    ], 200);
  }
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
    return view('admin/allowancereport/index', compact('employees', 'departments', 'workgroups','titles'));
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
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function check_periode($month, $year, $employee)
  {
    $exists = AllowanceReport::whereMonth('period', '=', $month)->whereYear('period', '=', $year)->where('employee_id', '=', $employee)->first();

    return $exists;
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
  public function get_employee_salary($id)
  {
    $basesalary = EmployeeSalary::where('employee_id', '=', $id)->orderBy('created_at', 'desc')->first();

    return $basesalary;
  }

  public function get_additional_allowance($id, $month, $year)
  {
    $query = DB::table('employee_allowances');
    $query->select('employee_allowances.*', 'allowances.allowance as description', 'allowances.id as allowance_id');
    $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    $query->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    $query->where('employee_allowances.employee_id', '=', $id);
    $query->where('employee_allowances.month', '=', $month);
    $query->where('employee_allowances.year', '=', $year);
    $query->where('employee_allowances.status', '=', 1);
    $query->where('allowance_categories.type', '=', 'additional');
    $query->where('employee_allowances.type', '!=', 'automatic');
    $allowances = $query->get();

    $data = [];
    foreach ($allowances as $allowance) {
      $data[] = $allowance;
    }

    return $data;
  }
  public function get_overtime($id, $month, $year)
  {
    $query = DB::table('overtimes');
    $query->select('overtimes.*');
    $query->where('overtimes.employee_id', '=', $id);
    $query->where('overtimes.final_salary', '>', 0);
    $query->whereMonth('date', '=', $month);
    $query->whereYear('date', '=', $year);
    $salaries = $query->sum('final_salary');

    return $salaries;
  }

  public function get_attendance($id, $month, $year)
  {
    $query = DB::table('attendances');
    $query->select('attendances.*');
    $query->where('attendances.employee_id', '=', $id);
    $query->where('attendances.status', '=', 1);
    $query->whereMonth('attendance_date', '=', $month);
    $query->whereYear('attendance_date', '=', $year);
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

    $qty_absent = abs(count($work_date) - count($attendance));
    $allowance = AllowanceRule::with('allowance')->where('qty_absent', '=', $qty_absent >= 2 ? 2 : $qty_absent)->first();
    $attendance_allowance = 0;
    $basesalary = $this->get_employee_salary($id);
    if ($allowance->qty_allowance > 0) {
      $attendance_allowance = $allowance->qty_allowance * ($basesalary->amount / 30);
    }

    $employee_allowance = EmployeeAllowance::with('allowance')->where('status', 1)->where('month', $month)->where('year', $year)->where('employee_id', $id)->whereHas('allowance', function ($q) {
      $q->where('category', 'like', 'tunjanganKehadiran');
    })->first();
    return $employee_allowance ? $attendance_allowance : 0;
  }

  public function allowance($id)
  {
    $query = DB::table('allowance_reports');
    $query->select('allowance_reports.*');
    $query->leftJoin('allowance_report_details', 'allowance_report_details.allowance_report_id', '=', 'allowance_reports.id');
    $query->where('allowance_report_details.allowance_report_id', '=', $id);
    // $query->where('allowance_report_details.type', '=', 1);
    $allowance = $query->sum('allowance_report_details.value');

    return $allowance;
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
    $query->whereMonth('date', $month);
    $query->whereYear('date', $year);

    return $query->sum('value');
  }

  public function getLatestId()
  {
    $read = AllowanceReport::max('id');
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

  public function generateByDepartment($department, $month, $year, $user)
  {
    $dept_id = Department::where('name', $department)->first();
    $employees = Employee::select('employees.*')->where('department_id', $dept_id->id)->where('employees.status', 1)->get();
    if (!$employees->isEmpty()) {
      foreach ($employees as $employee) {
        $exists = $this->check_periode($month, $year, $employee->id);
        if ($exists) {
          $delete = $exists->delete();
        }

        $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);
        $id = $this->getLatestId();
        $allowancereport = AllowanceReport::create([
          'id'            => $this->getLatestId(),
          'employee_id'   => $employee->id,
          'period'        => $period
        ]);

        if ($allowancereport) {
          $allowance = $this->get_additional_allowance($employee->id, $month, $year);
          if ($allowance) {
            foreach ($allowance as $key => $value) {
              if ($value->value) {
                AllowanceReportDetail::create([
                  'allowance_report_id'  => $id,
                  'employee_id'       => $employee->id,
                  'allowance'       => $value->description,
                  'factor'           => $value->factor,
                  'total'             => $value->value,
                  'value'             => $value->factor * $value->value,
                ]);
              } else {
                continue;
              }
            }
          }
          $allowancereport->total = $this->allowance($id) ? $this->allowance($id) : 0;
          $allowancereport->save();
        } else {
          return array(
            'status'    => false,
            'message'   => $allowancereport
          );
        }
      }
      return array(
        'status'    => true,
        'message'   => "Allowance report generated successfully"
      );
    } else {
      return array(
        'status'    => false,
        'message'   => "This department has no employees"
      );
    }
  }

  public function generateByPosition($position, $month, $year, $user)
  {
    $employees = Employee::select('employees.*')->where('title_id', $position)->where('employees.status', 1)->get();
    if (!$employees->isEmpty()) {
      foreach ($employees as $employee) {
        $exists = $this->check_periode($month, $year, $employee->id);
        if ($exists) {
          $delete = $exists->delete();
        }

        $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);
        $id = $this->getLatestId();
        $allowancereport = AllowanceReport::create([
          'id'            => $this->getLatestId(),
          'employee_id'   => $employee->id,
          'period'        => $period
        ]);

        if ($allowancereport) {
          $allowance = $this->get_additional_allowance($employee->id, $month, $year);
          if ($allowance) {
            foreach ($allowance as $key => $value) {
              if ($value->value) {
                AllowanceReportDetail::create([
                  'allowance_report_id'  => $id,
                  'employee_id'       => $employee->id,
                  'allowance'       => $value->description,
                  'factor'           => $value->factor,
                  'total'             => $value->value,
                  'value'             => $value->factor * $value->value,
                ]);
              } else {
                continue;
              }
            }
          }
          $allowancereport->total = $this->allowance($id) ? $this->allowance($id) : 0;
          $allowancereport->save();
        } else {
          return $allowancereport;
        }
      }
      return array(
        'status'    => true,
        'message'   => "Allowance report generated successfully"
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
    $employees = Employee::select('employees.*')->where('employees.status', 1)->where('workgroup_id', $workgroup)->get();
    if (!$employees->isEmpty()) {
      foreach ($employees as $employee) {
        $exists = $this->check_periode($month, $year, $employee->id);
        if ($exists) {
          $delete = $exists->delete();
        }

        $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);
        $id = $this->getLatestId();
        $allowancereport = AllowanceReport::create([
          'id'            => $this->getLatestId(),
          'employee_id'   => $employee->id,
          'period'        => $period
        ]);

        if ($allowancereport) {
          $allowance = $this->get_additional_allowance($employee->id, $month, $year);
          if ($allowance) {
            foreach ($allowance as $key => $value) {
              if ($value->value) {
                AllowanceReportDetail::create([
                  'allowance_report_id'  => $id,
                  'employee_id'       => $employee->id,
                  'allowance'       => $value->description,
                  'factor'           => $value->factor,
                  'total'             => $value->value,
                  'value'             => $value->factor * $value->value,
                ]);
              } else {
                continue;
              }
            }
          }
          $allowancereport->total = $this->allowance($id) ? $this->allowance($id) : 0;
          $allowancereport->save();
        } else {
          return $allowancereport;
        }
      }
      return array(
        'status'    => true,
        'message'   => "Allowance report generated successfully"
      );
    } else {
      return array(
        'status'    => false,
        'message'   => "This workgroup has no employees"
      );
    }
  }
  public function store(Request $request)
  {
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
      foreach ($request->employee_name as $emp) {
      $exists = $this->check_periode($request->montly, $request->year, $emp);
      if ($exists) {
        $delete = $exists->delete();
      }

      $period = changeDateFormat('Y-m-d', 01 . '-' . $request->montly . '-' . $request->year);

      $id = $this->getLatestId();
        $allowancereport = AllowanceReport::create([
          'id'            => $id,
          'employee_id'   => $emp,
          'period'        => $period
        ]);
        if ($allowancereport) {
          $allowance = $this->get_additional_allowance($emp, $request->montly, $request->year);
          if ($allowance) {
            foreach ($allowance as $key => $value) {
              if ($value->value && $value->factor) {
                AllowanceReportDetail::create([
                  'allowance_report_id'  => $id,
                  'employee_id'       => $emp,
                  'allowance'       => $value->description,
                  'allowance_id'    => $value->allowance_id,
                  'factor'           => $value->factor,
                  'total'             => $value->value,
                  'value'             => $value->factor * $value->value,
                ]);
              } else {
                continue;
              }
            }
          }
          $allowancereport->total = $this->allowance($id) ? $this->allowance($id) : 0;
          $allowancereport->save();
        } elseif (!$allowancereport) {
          DB::rollBack();
          return response()->json([
            'status'    => false,
            'message'   => $allowancereport
          ], 400);
        }
      }
      DB::commit();
      return response()->json([
        'status'    => true,
        'message'   => 'Allowance report generated successfully',
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
    $allowance_detail = AllowanceReport::with('allowancedetail')->with('employee')->find($id);
    $employee = Employee::with('department')->with('title')->find($allowance_detail->employee_id);
    if ($allowance_detail) {
      return view('admin.allowancereport.detail', compact('allowance_detail', 'employee'));
    } else {
      abort(404);
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function detail($id)
  {
    $employee = Employee::find($id);
    if ($employee) {
      // $employeeallowance = EmployeeAllowance::where('id', $employee->allowance_id)->get();
      // if ($employeeallowance > 0) {
      //     foreach ($employeeallowance as $allowancereport) {
      //             $allowancereport = array();
      //             $allowancereport = AllowanceReport::where(['allowance_id' => $allowancereport->allowance_id, 'employee_id' => $employee->id])->get();
      //             if ($allowancereport->count() > 0) {
      //                 foreach ($allowancereport as $allowance_rp) {
      //                     $allowance_rp[] = $allowancereport->allowance_id;
      //                 }
      //             }
      //             if (!in_array($allowancereport->allowance_id, $allowance_rp)) {
      //                 AllowanceReport::create([
      //                     'employee_id' => $employee->id,
      //                     'allowance_id' => $allowance_rp->allowance_id,
      //                     'value' => $allowance_rp->value,
      //                 ]);
      //             }
      //         }
      // }
      // return view('admin.allowancereport.show', compact('employee'));
      $employeeAllowance = EmployeeAllowance::where('id', $employee->allowance_id)->first();
      if ($employeeAllowance) {
        dd($employeeAllowance);
        $allowanceReport = AllowanceReport::where('employee_id', $employee->id)->get();
        if ($allowanceReport->count() > 0) {
          AllowanceReport::create([
            'employee_id' => $id,
            'allowance_id' => $employeeAllowance->allowance_id,
            'value' => $employeeAllowance->value
          ]);
        }
      }
      return view('admin.allowancereport.show', compact('employee'));
    } else {
      Abort(404);
    }
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
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    try {
      $report = AllowanceReport::find($id);
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
  public function export(Request $request)
  {
    $employee = $request->name;
    $department = $request->department ? explode(',', $request->department) : null;
    $workgroup = $request->workgroup_id ? explode(',', $request->workgroup_id) : null;
    $month = $request->montly;
    $year = $request->year;

    $object = new \PHPExcel();
    $object->getProperties()->setCreator('Bosung Indonesia');
    $object->setActiveSheetIndex(0);
    $sheet = $object->getActiveSheet();

    $allowance = AllowanceReport::select(
      'allowance_reports.*',
      'work_groups.name as workgroup_name',
      'departments.name as department_name',
      'employees.nid as nik',
      'employees.name as employee_name'
    );
    $allowance->leftJoin('employees', 'employees.id', '=', 'allowance_reports.employee_id');
    $allowance->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
    $allowance->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
    $allowance->whereMonth('allowance_reports.period', $month)->whereYear('allowance_reports.period', $year);
    if ($department) {
      $string = '';
      foreach ($department as $dept) {
        $string .= "departments.path like '%$dept%'";
        if (end($department) != $dept) {
          $string .= ' or ';
        }
      }
      $allowance->whereRaw('(' . $string . ')');
    }
    if ($workgroup) {
      $allowance->whereIn('employees.workgroup_id', $workgroup);
    }
    $allowance_reports = $allowance->get();
    // dd($allowance_reports); 

    // Get Additional Allowance
    $query = Allowance::select('allowances.*')->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    $query->where('allowance_categories.type', '=', 'additional');
    $query->orderBy('allowances.allowance', 'asc');
    $additionals = $query->get();
    $addition_name = [];

    // Header Columne Excel
    $sheet->mergeCells('A1:D1');
    $sheet->setCellValue('A1', 'Personal Data')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->setCellValue('A2', 'Workgroup Combination');
    $sheet->setCellValue('B2', 'Department');
    $sheet->setCellValue('C2', 'NIK');
    $sheet->setCellValue('D2', 'Nama');
    $column_number = 4;
    $column_number_field = 4;

    foreach ($additionals as $key => $additional) {
      $sheet->setCellValueByColumnAndRow($column_number_field, 3, 'Factor');
      $sheet->setCellValueByColumnAndRow(++$column_number_field, 3, 'Value');
      $sheet->setCellValueByColumnAndRow(++$column_number_field, 3, 'Total');
      $sheet->setCellValueByColumnAndRow($column_number, 2, $additional->allowance);
      $sheet->mergeCellsByColumnAndRow($column_number, 2, $column_number + 2, 2);
      $column_number++;
      $column_number++;
      $column_number++;
      $column_number_field++;
    }
    $sheet->setCellValueByColumnAndRow($column_number, 1, 'Allowance Total')->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->mergeCellsByColumnAndRow($column_number, 1, $column_number, 2);

    $row_number = 4;

    foreach ($allowance_reports as $reports) {
      $sheet->setCellValue('A' . $row_number, $reports->workgroup_name);
      $sheet->setCellValue('B' . $row_number, $reports->department_name);
      $sheet->setCellValue('C' . $row_number, $reports->nik);
      $sheet->setCellValue('D' . $row_number, $reports->employee_name);
      // $additional = AllowanceReportDetail::select('allowance_report_details.*', 'allowances.allowance')->leftjoin('allowances', 'allowances.id', '=', 'allowance_report_details.allowance_id')->where('allowance_report_details.allowance_report_id', 2)->get();
      // foreach ($additionals as $key => $name) {
      // foreach ($additional as $detail_report) {
      //   foreach ($additionals as $key => $additional) {
      //     if ($detail_report->allowance == $additional->allowance) {
      //       $sheet->setCellValueByColumnAndRow($col, $row_number, round($detail_report->factor));
      //       $sheet->setCellValueByColumnAndRow(++$col, $row_number, round($detail_report->value));
      //       $sheet->setCellValueByColumnAndRow(++$col, $row_number, round($detail_report->total));
      //     } else {
      //       $sheet->setCellValueByColumnAndRow($col, $row_number, 0);
      //       $sheet->setCellValueByColumnAndRow(++$col, $row_number, 0);
      //       $sheet->setCellValueByColumnAndRow(++$col, $row_number, 0);
      //     }
      //   }
      // }
      // }
      $col = 4;
      $col_field = 4;
      foreach ($additionals as $key => $additional) {
        $nilai = AllowanceReportDetail::where('allowance_report_id', $reports->id)->where('allowance_id', $additional->id)->first();
        if ($nilai) {
          $sheet->setCellValueByColumnAndRow($col_field, $row_number, $nilai->factor);
          $sheet->setCellValueByColumnAndRow(++$col_field, $row_number, $nilai->total);
          $sheet->setCellValueByColumnAndRow(++$col_field, $row_number, $nilai->value);
          $col_field++;
        } else {
          $sheet->setCellValueByColumnAndRow($col_field, $row_number, 0);
          $sheet->setCellValueByColumnAndRow(++$col_field, $row_number, 0);
          $sheet->setCellValueByColumnAndRow(++$col_field, $row_number, 0);
          $col_field++;
        }
      }
      $sheet->setCellValueByColumnAndRow($col_field, $row_number, round($reports->total));
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
    if ($allowance_reports->count() > 0) {
      return response()->json([
        'status'     => true,
        'name'        => 'allowance-report-' . date('d-m-Y') . '.xlsx',
        'message'    => "Success Download Allowance Report Data",
        'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
      ], 200);
    } else {
      return response()->json([
        'status'     => false,
        'message'    => "Data not found",
      ], 400);
    }
  }
}