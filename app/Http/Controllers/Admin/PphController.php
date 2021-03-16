<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Title;
use App\Models\WorkGroup;
use App\Models\AllowanceRule;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeSalary;
use App\Models\PphReport;
use App\Models\PphReportDetail;
use App\Models\PTKP;
use App\Models\SalaryReport;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use PDF;

class PphController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'pph'));
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
        $employee_id = strtoupper(str_replace("'","''",$request->employee_id));
        $departments = $request->department;
        $position = $request->position;
        $workgroup = $request->workgroup;
        $month = $request->month;
        $year = $request->year;
        $nid = $request->nid;
        $status = $request->status;
        $type = $request->type;

        //Count Data
        $query = DB::table('salary_reports');
        $query->select(
            'salary_reports.*',
            'employees.name as employee_name',
            'employees.nid as nik',
            'employees.npwp as npwp',
            'employees.tax_calculation as metode',
            'titles.name as title_name',
            'departments.name as department_name',
            'work_groups.name as workgroup_name',
            'employees.department_id as department_id',
            'employees.title_id as title_id',
            'employees.workgroup_id as workgroup_id',
            DB::raw("(SELECT total FROM salary_report_details WHERE description = 'Basic Salary' and salary_report_id = salary_reports.id) as basic_salary"),
            DB::raw("(SELECT total FROM salary_report_details WHERE description = 'Potongan PPh 21' and salary_report_id = salary_reports.id) as tax")
        );
        $query->leftJoin('employees', 'employees.id', '=', 'salary_reports.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
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
        if ($departments) {
            $string = '';
            foreach ($departments as $dept) {
                $string .= "departments.path like '%$dept%'";
                if (end($departments) != $dept) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($position != "") {
            $query->whereIn('employees.title_id', $position);
        }
        if ($workgroup != "") {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('salary_reports');
        $query->select(
            'salary_reports.*',
            'employees.name as employee_name',
            'employees.nid as nik',
            'employees.npwp as npwp',
            'employees.tax_calculation as metode',
            'titles.name as title_name',
            'departments.name as department_name',
            'work_groups.name as workgroup_name',
            'employees.department_id as department_id',
            'employees.title_id as title_id',
            'employees.workgroup_id as workgroup_id',
            DB::raw("(SELECT total FROM salary_report_details WHERE description = 'Basic Salary' and salary_report_id = salary_reports.id) as basic_salary"),
            DB::raw("(SELECT total FROM salary_report_details WHERE description = 'Potongan PPh 21' and salary_report_id = salary_reports.id) as tax")
        );
        $query->leftJoin('employees', 'employees.id', '=', 'salary_reports.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
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
        if ($departments) {
            $string = '';
            foreach ($departments as $dept) {
                $string .= "departments.path like '%$dept%'";
                if (end($departments) != $dept) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($position != "") {
            $query->whereIn('employees.title_id', $position);
        }
        if ($workgroup != "") {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        $query->offset($start);
        $query->limit($length);
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
    public function index()
    {
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
        return view('admin/pph/index', compact('employees', 'departments', 'workgroups','titles'));
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
        $exists = PphReport::whereMonth('period', '=', $month)->whereYear('period', '=', $year)->where('employee_id', '=', $employee)->first();

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
        $query->select('employee_allowances.*', 'allowances.allowance as description');
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

    // public function get_deduction($id, $month, $year)
    // {
    //     $query = DB::table('employee_allowances');
    //     $query->select('employee_allowances.*', 'allowances.allowance as description');
    //     $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
    //     $query->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
    //     $query->where('employee_allowances.employee_id', '=', $id);
    //     $query->where('employee_allowances.month', '=', $month);
    //     $query->where('employee_allowances.year', '=', $year);
    //     $query->where('employee_allowances.status', '=', 1);
    //     $query->where('allowance_categories.type', '=', 'deduction');
    //     $query->where('employee_allowances.type', '!=', 'automatic');
    //     $allowances = $query->get();

    //     $data = [];
    //     foreach ($allowances as $allowance) {
    //     $data[] = $allowance;
    //     }

    //     return $data;
    // }

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
        // $query = Leave::with('leavesetting')->with('log')->where('employee_id', $id)->whereHas('log', function ($q) use ($month, $year) {
        //   $q->whereMonth('date', $month);
        //   $q->whereYear('date', $year);
        // })->whereHas('leavesetting', function ($q2) {
        //   $q2->where('description', 0);
        // });
        $leaves = $query->sum('leaves.duration');

        return $leaves;
    }

    public function gross_salary($id)
    {
        $query = DB::table('pph_reports');
        $query->select('pph_reports.*');
        $query->leftJoin('pph_report_details', 'pph_report_details.pph_report_id', '=', 'pph_reports.id');
        $query->where('pph_report_details.pph_report_id', '=', $id);
        $query->where('pph_report_details.type', '=', 1);
        $gross = $query->sum('pph_report_details.total');

        return $gross;
    }

    public function deduction_salary($id)
    {
        $query = DB::table('pph_reports');
        $query->select('pph_reports.*');
        $query->leftJoin('pph_report_details', 'pph_report_details.pph_report_id', '=', 'pph_reports.id');
        $query->where('pph_report_details.pph_report_id', '=', $id);
        $query->where('pph_report_details.type', '=', 0);
        $deduction = $query->sum('pph_report_details.total');

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
        $query->whereMonth('date', $month);
        $query->whereYear('date', $year);

        return $query->sum('value');
    }

    public function getLatestId()
    {
        $read = PphReport::max('id');
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
        $employees = Employee::where('department_id', $department)->get();
        if (!$employees->isEmpty()) {
            foreach ($employees as $employee) {
                $exists = $this->check_periode($month, $year, $employee->id);
                if ($exists) {
                    $delete = $exists->delete();
                }

                $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);

                $pphreport = PphReport::create([
                    'id'            => $this->getLatestId(),
                    'employee_id'   => $employee->id,
                    'created_by'    => $user,
                    'period'        => $period,
                    'status'        => -1
                ]);

                if ($pphreport) {
                    $basesalary = $this->get_employee_salary($employee->id);
                    $allowance = $this->get_additional_allowance($employee->id, $month, $year);
                    $deduction = $this->get_deduction($employee->id, $month, $year);
                    $overtime = $this->get_overtime($employee->id, $month, $year);
                    $attendance = $this->get_attendance($employee->id, $month, $year);
                    $driverallowance = $this->get_driver_allowance($employee->id, $month, $year);
                    $leave = $this->get_leave($employee->id, $month, $year);
                    $ptkp = $this->get_pkp($employee->ptkp);
                    $alpha = $this->get_alpha($employee->id, $month, $year);
                    $attendance_allowance = $this->get_attendance_allowance($employee->id, $month, $year);
                    $pph = $this->getPPhAllowance($employee->id, $month, $year);
                    if ($basesalary) {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Basic Salary',
                            'total'             => $basesalary->amount,
                            'type'              => 1,
                            'status'            => $basesalary->amount == 0 ? 'Hourly' : 'Monthly'
                        ]);
                        $pphreport->pph_type = $basesalary->amount == 0 ? 'Hourly' : 'Monthly';
                        $pphreport->save();
                    }
                    if ($allowance) {
                        foreach ($allowance as $key => $value) {
                            if ($value->value) {
                                PphReportDetail::create([
                                    'pph_report_id'  => $pphreport->id,
                                    'employee_id'       => $employee->id,
                                    'description'       => $value->description,
                                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->factor * $value->value,
                                    'type'              => 1,
                                    'status'            => 'Draft'
                                ]);
                            } else {
                                continue;
                            }
                        }
                    }
                    if ($deduction) {
                        foreach ($deduction as $key => $value) {
                            if ($value->value) {
                                PphReportDetail::create([
                                    'pph_report_id'  => $pphreport->id,
                                    'employee_id'       => $employee->id,
                                    'description'       => $value->description,
                                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->factor * $value->value,
                                    'type'              => 0,
                                    'status'            => 'Draft'
                                ]);
                            } else {
                                continue;
                            }
                        }
                    }
                    if ($overtime && $employee->overtime == 'yes') {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Overtime',
                            'total'             => $overtime,
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($employee->join == 'yes') {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Potongan SPSI',
                            'total'             => 20000,
                            'type'              => 0,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($employee->department->name == 'Driver Team' && $driverallowance > 0) {
                        $spsi = PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Driver Allowance',
                            'total'             => $driverallowance,
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($attendance_allowance) {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->employee_name,
                            'description'       => 'Tunjangan Kehadiran',
                            'total'             => $attendance_allowance,
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($alpha > 0) {
                        $alpha_penalty = PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->employee_name,
                            'description'       => 'Alpha penalty',
                            'total'             => -1 * ($alpha * ($basesalary->amount / 30)),
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($pph) {
                        $gross = $this->gross_salary($pphreport->id) ? $this->gross_salary($pphreport->id) : 0;
                        $deduction = $this->deduction_salary($pphreport->id) ? $this->deduction_salary($pphreport->id) : 0;
                        $position_allowance = $gross * (5 / 100);
                        $new_net = $gross - $position_allowance;
                        $salary_year = $new_net * 12;
                        $pkp = $salary_year - $ptkp->value;
                        $pkp_left = $pkp;
                        $pph21 = 0;
                        $iteration = 4;

                        for ($i = 1; $i <= $iteration; $i++) {
                            if ($i == 1) {
                                if ($pkp_left > 0) {
                                    if ($pkp_left <= 50000000) {
                                        $pph21 = $pkp_left * (5 / 100);
                                        $pkp_left = ($pkp_left - 50000000) <= 0 ? 0 : $pkp_left - 50000000;
                                    } else {
                                        $pph21 = 50000000 * (5 / 100);
                                        $pkp_left = ($pkp_left - 50000000) <= 0 ? 0 : $pkp_left - 50000000;
                                    }
                                } else {
                                    break;
                                }
                            }
                            if ($i == 2) {
                                if ($pkp_left > 0) {
                                    if ($pkp_left >= 250000000) {
                                        $pph21 = $pph21 + (250000000 * (15 / 100));
                                        $pkp_left = ($pkp_left - 250000000) <= 0 ? 0 : $pkp_left - 250000000;
                                    } else {
                                        $pph21 = $pph21 + ($pkp_left * (15 / 100));
                                        $pkp_left = ($pkp_left - 250000000) <= 0 ? 0 : $pkp_left - 250000000;
                                    }
                                } else {
                                    break;
                                }
                            }
                            if ($i == 3) {
                                if ($pkp_left > 0) {
                                    if ($pkp_left >= 500000000) {
                                        $pph21 = $pph21 + (500000000 * (25 / 100));
                                        $pkp_left = ($pkp_left - 500000000) <= 0 ? 0 : $pkp_left - 500000000;
                                    } else {
                                        $pph21 = $pph21 + ($pkp_left * (25 / 100));
                                        $pkp_left = ($pkp_left - 500000000) <= 0 ? 0 : $pkp_left - 500000000;
                                    }
                                } else {
                                    break;
                                }
                            }
                            if ($i == 4) {
                                if ($pkp_left > 0) {
                                    $pph21 = $pph21 + ($pkp_left * (30 / 100));
                                } else {
                                    break;
                                }
                            }
                        }
                        // switch ($pkp) {
                        //   case $pkp <= 50000000:
                        //     $pph21 = $pkp * (5 / 100);
                        //     break;
                        //   case (50000000 <= $pkp && $pkp <= 250000000):
                        //     $pph21 = $pkp * (15 / 100);
                        //     break;
                        //   case (250000000 <= $pkp && $pkp <= 500000000):
                        //     $pph21 = $pkp * (25 / 100);
                        //     break;
                        //   default:
                        //     $pph21 = $pkp * (30 / 100);
                        //     break;
                        // }
                        if ($pph21) {
                            PphReportDetail::create([
                                'pph_report_id'  => $pphreport->id,
                                'employee_id'       => $employee->id,
                                'description'       => 'Potongan PPh21',
                                'total'             => ($pph21 / 12) > 0 ? $pph21 / 12 : 0,
                                'type'              => 0,
                                'status'            => 'Draft'
                            ]);
                        }
                    }
                    $pphreport->gross_salary = $this->gross_salary($pphreport->id) ? $this->gross_salary($pphreport->id) : 0;
                    $pphreport->deduction    = $this->deduction_salary($pphreport->id) ? $this->deduction_salary($pphreport->id) : 0;
                    $pphreport->net_salary   = $pphreport->gross_salary - $pphreport->deduction;
                    $pphreport->save();
                } else {
                    return array(
                        'status'    => false,
                        'message'   => $pphreport
                    );
                }
            }
            return array(
                'status'    => true,
                'message'   => "Pph report generated successfully"
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
        $employees = Employee::where('title_id', $position)->get();
        if (!$employees->isEmpty()) {
            foreach ($employees as $employee) {
                $exists = $this->check_periode($month, $year, $employee->id);
                if ($exists) {
                    $delete = $exists->delete();
                }

                $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);

                $pphreport = PphReport::create([
                    'id'            => $this->getLatestId(),
                    'employee_id'   => $employee->id,
                    'created_by'    => $user,
                    'period'        => $period,
                    'status'        => -1
                ]);

                if ($pphreport) {
                    $basesalary = $this->get_employee_salary($employee->id);
                    $allowance = $this->get_additional_allowance($employee->id, $month, $year);
                    $deduction = $this->get_deduction($employee->id, $month, $year);
                    $overtime = $this->get_overtime($employee->id, $month, $year);
                    $attendance = $this->get_attendance($employee->id, $month, $year);
                    $leave = $this->get_leave($employee->id, $month, $year);
                    $driverallowance = $this->get_driver_allowance($employee->id, $month, $year);
                    $ptkp = $this->get_pkp($employee->ptkp);
                    $alpha = $this->get_alpha($employee->id, $month, $year);
                    $attendance_allowance = $this->get_attendance_allowance($employee->id, $month, $year);
                    $pph = $this->getPPhAllowance($employee->id, $month, $year);
                    if ($basesalary) {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Basic Salary',
                            'total'             => $basesalary->amount,
                            'type'              => 1,
                            'status'            => $basesalary->amount == 0 ? 'Hourly' : 'Monthly'
                        ]);
                        $pphreport->pph_type = $basesalary->amount == 0 ? 'Hourly' : 'Monthly';
                        $pphreport->save();
                    }
                    if ($allowance) {
                        foreach ($allowance as $key => $value) {
                            if ($value->value) {
                                PphReportDetail::create([
                                    'pph_report_id'  => $pphreport->id,
                                    'employee_id'       => $employee->id,
                                    'description'       => $value->description,
                                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->factor * $value->value,
                                    'type'              => 1,
                                    'status'            => 'Draft'
                                ]);
                            } else {
                                continue;
                            }
                        }
                    }
                    if ($deduction) {
                        foreach ($deduction as $key => $value) {
                            if ($value->value) {
                                PphReportDetail::create([
                                    'pph_report_id'  => $pphreport->id,
                                    'employee_id'       => $employee->id,
                                    'description'       => $value->description,
                                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->factor * $value->value,
                                    'type'              => 0,
                                    'status'            => 'Draft'
                                ]);
                            } else {
                                continue;
                            }
                        }
                    }
                    if ($overtime && $employee->overtime == 'yes') {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Overtime',
                            'total'             => $overtime,
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($employee->join == 'yes') {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Potongan SPSI',
                            'total'             => 20000,
                            'type'              => 0,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($employee->department->name == 'Driver Team' && $driverallowance > 0) {
                        $spsi = PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Driver Allowance',
                            'total'             => $driverallowance,
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($attendance_allowance) {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->employee_name,
                            'description'       => 'Tunjangan Kehadiran',
                            'total'             => $attendance_allowance,
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($alpha > 0) {
                        $alpha_penalty = PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->employee_name,
                            'description'       => 'Alpha penalty',
                            'total'             => -1 * ($alpha * ($basesalary->amount / 30)),
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($pph) {
                        $gross = $this->gross_salary($pphreport->id) ? $this->gross_salary($pphreport->id) : 0;
                        $deduction = $this->deduction_salary($pphreport->id) ? $this->deduction_salary($pphreport->id) : 0;
                        $position_allowance = $gross * (5 / 100);
                        $new_net = $gross - $position_allowance;
                        $salary_year = $new_net * 12;
                        $pkp = $salary_year - $ptkp->value;
                        $pkp_left = $pkp;
                        $pph21 = 0;
                        $iteration = 4;

                        for ($i = 1; $i <= $iteration; $i++) {
                            if ($i == 1) {
                                if ($pkp_left > 0) {
                                    if ($pkp_left <= 50000000) {
                                        $pph21 = $pkp_left * (5 / 100);
                                        $pkp_left = ($pkp_left - 50000000) <= 0 ? 0 : $pkp_left - 50000000;
                                    } else {
                                        $pph21 = 50000000 * (5 / 100);
                                        $pkp_left = ($pkp_left - 50000000) <= 0 ? 0 : $pkp_left - 50000000;
                                    }
                                } else {
                                    break;
                                }
                            }
                            if ($i == 2) {
                                if ($pkp_left > 0) {
                                    if ($pkp_left >= 250000000) {
                                        $pph21 = $pph21 + (250000000 * (15 / 100));
                                        $pkp_left = ($pkp_left - 250000000) <= 0 ? 0 : $pkp_left - 250000000;
                                    } else {
                                        $pph21 = $pph21 + ($pkp_left * (15 / 100));
                                        $pkp_left = ($pkp_left - 250000000) <= 0 ? 0 : $pkp_left - 250000000;
                                    }
                                } else {
                                    break;
                                }
                            }
                            if ($i == 3) {
                                if ($pkp_left > 0) {
                                    if ($pkp_left >= 500000000) {
                                        $pph21 = $pph21 + (500000000 * (25 / 100));
                                        $pkp_left = ($pkp_left - 500000000) <= 0 ? 0 : $pkp_left - 500000000;
                                    } else {
                                        $pph21 = $pph21 + ($pkp_left * (25 / 100));
                                        $pkp_left = ($pkp_left - 500000000) <= 0 ? 0 : $pkp_left - 500000000;
                                    }
                                } else {
                                    break;
                                }
                            }
                            if ($i == 4) {
                                if ($pkp_left > 0) {
                                    $pph21 = $pph21 + ($pkp_left * (30 / 100));
                                } else {
                                    break;
                                }
                            }
                        }
                        // switch ($pkp) {
                        //   case $pkp <= 50000000:
                        //     $pph21 = $pkp * (5 / 100);
                        //     break;
                        //   case (50000000 <= $pkp && $pkp <= 250000000):
                        //     $pph21 = $pkp * (15 / 100);
                        //     break;
                        //   case (250000000 <= $pkp && $pkp <= 500000000):
                        //     $pph21 = $pkp * (25 / 100);
                        //     break;
                        //   default:
                        //     $pph21 = $pkp * (30 / 100);
                        //     break;
                        // }
                        if ($pph21) {
                            PphReportDetail::create([
                                'pph_report_id'  => $pphreport->id,
                                'employee_id'       => $employee->id,
                                'description'       => 'Potongan PPh21',
                                'total'             => ($pph21 / 12) > 0 ? $pph21 / 12 : 0,
                                'type'              => 0,
                                'status'            => 'Draft'
                            ]);
                        }
                    }
                    $pphreport->gross_salary = $this->gross_salary($pphreport->id) ? $this->gross_salary($pphreport->id) : 0;
                    $pphreport->deduction    = $this->deduction_salary($pphreport->id) ? $this->deduction_salary($pphreport->id) : 0;
                    $pphreport->net_salary   = $pphreport->gross_salary - $pphreport->deduction;
                    $pphreport->save();
                } else {
                    return $pphreport;
                }
            }
            return array(
                'status'    => true,
                'message'   => "Pph report generated successfully"
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
        $employees = Employee::where('workgroup_id', $workgroup)->get();
        if (!$employees->isEmpty()) {
            foreach ($employees as $employee) {
                $exists = $this->check_periode($month, $year, $employee->id);
                if ($exists) {
                    $delete = $exists->delete();
                }

                $period = changeDateFormat('Y-m-d', 01 . '-' . $month . '-' . $year);

                $pphreport = PphReport::create([
                    'id'            => $this->getLatestId(),
                    'employee_id'   => $employee->id,
                    'created_by'    => $user,
                    'period'        => $period,
                    'status'        => -1
                ]);

                if ($pphreport) {
                    $basesalary = $this->get_employee_salary($employee->id);
                    $allowance = $this->get_additional_allowance($employee->id, $month, $year);
                    $deduction = $this->get_deduction($employee->id, $month, $year);
                    $overtime = $this->get_overtime($employee->id, $month, $year);
                    $attendance = $this->get_attendance($employee->id, $month, $year);
                    $leave = $this->get_leave($employee->id, $month, $year);
                    $alpha = $this->get_alpha($employee->id, $month, $year);
                    $driverallowance = $this->get_driver_allowance($employee->id, $month, $year);
                    $ptkp = $this->get_pkp($employee->ptkp);
                    $attendance_allowance = $this->get_attendance_allowance($employee->id, $month, $year);
                    $pph = $this->getPPhAllowance($employee->id, $month, $year);
                    if ($basesalary) {
                        SalaryReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Basic Salary',
                            'total'             => $basesalary->amount,
                            'type'              => 1,
                            'status'            => $basesalary->amount == 0 ? 'Hourly' : 'Monthly'
                        ]);
                        $pphreport->pph_type = $basesalary->amount == 0 ? 'Hourly' : 'Monthly';
                        $pphreport->save();
                    }
                    if ($allowance) {
                        foreach ($allowance as $key => $value) {
                            if ($value->value) {
                                PphReportDetail::create([
                                    'pph_report_id'  => $pphreport->id,
                                    'employee_id'       => $employee->id,
                                    'description'       => $value->description,
                                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->factor * $value->value,
                                    'type'              => 1,
                                    'status'            => 'Draft'
                                ]);
                            } else {
                                continue;
                            }
                        }
                    }
                    if ($deduction) {
                        foreach ($deduction as $key => $value) {
                            if ($value->value) {
                                PphReportDetail::create([
                                    'pph_report_id'  => $pphreport->id,
                                    'employee_id'       => $employee->id,
                                    'description'       => $value->description,
                                    'total'             => ($value->type == 'percentage') ? $basesalary->amount * ($value->value / 100) : $value->factor * $value->value,
                                    'type'              => 0,
                                    'status'            => 'Draft'
                                ]);
                            } else {
                                continue;
                            }
                        }
                    }
                    if ($overtime && $employee->overtime == 'yes') {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Overtime',
                            'total'             => $overtime,
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($employee->join == 'yes') {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Potongan SPSI',
                            'total'             => 20000,
                            'type'              => 0,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($employee->department->name == 'Driver Team' && $driverallowance > 0) {
                        $spsi = PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->id,
                            'description'       => 'Driver Allowance',
                            'total'             => $driverallowance,
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($attendance_allowance) {
                        PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->employee_name,
                            'description'       => 'Tunjangan Kehadiran',
                            'total'             => $attendance_allowance,
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($alpha > 0) {
                        $alpha_penalty = PphReportDetail::create([
                            'pph_report_id'  => $pphreport->id,
                            'employee_id'       => $employee->employee_name,
                            'description'       => 'Alpha penalty',
                            'total'             => -1 * ($alpha * ($basesalary->amount / 30)),
                            'type'              => 1,
                            'status'            => 'Draft'
                        ]);
                    }
                    if ($pph) {
                        $gross = $this->gross_salary($pphreport->id) ? $this->gross_salary($pphreport->id) : 0;
                        $deduction = $this->deduction_salary($pphreport->id) ? $this->deduction_salary($pphreport->id) : 0;
                        $position_allowance = $gross * (5 / 100);
                        $new_net = $gross - $position_allowance;
                        $salary_year = $new_net * 12;
                        $pkp = $salary_year - $ptkp->value;
                        $pkp_left = $pkp;
                        $pph21 = 0;
                        $iteration = 4;

                        for ($i = 1; $i <= $iteration; $i++) {
                            if ($i == 1) {
                                if ($pkp_left > 0) {
                                    if ($pkp_left <= 50000000) {
                                        $pph21 = $pkp_left * (5 / 100);
                                        $pkp_left = ($pkp_left - 50000000) <= 0 ? 0 : $pkp_left - 50000000;
                                    } else {
                                        $pph21 = 50000000 * (5 / 100);
                                        $pkp_left = ($pkp_left - 50000000) <= 0 ? 0 : $pkp_left - 50000000;
                                    }
                                } else {
                                    break;
                                }
                            }
                            if ($i == 2) {
                                if ($pkp_left > 0) {
                                    if ($pkp_left >= 250000000) {
                                        $pph21 = $pph21 + (250000000 * (15 / 100));
                                        $pkp_left = ($pkp_left - 250000000) <= 0 ? 0 : $pkp_left - 250000000;
                                    } else {
                                        $pph21 = $pph21 + ($pkp_left * (15 / 100));
                                        $pkp_left = ($pkp_left - 250000000) <= 0 ? 0 : $pkp_left - 250000000;
                                    }
                                } else {
                                    break;
                                }
                            }
                            if ($i == 3) {
                                if ($pkp_left > 0) {
                                    if ($pkp_left >= 500000000) {
                                        $pph21 = $pph21 + (500000000 * (25 / 100));
                                        $pkp_left = ($pkp_left - 500000000) <= 0 ? 0 : $pkp_left - 500000000;
                                    } else {
                                        $pph21 = $pph21 + ($pkp_left * (25 / 100));
                                        $pkp_left = ($pkp_left - 500000000) <= 0 ? 0 : $pkp_left - 500000000;
                                    }
                                } else {
                                    break;
                                }
                            }
                            if ($i == 4) {
                                if ($pkp_left > 0) {
                                    $pph21 = $pph21 + ($pkp_left * (30 / 100));
                                } else {
                                    break;
                                }
                            }
                        }
                        // switch ($pkp) {
                        //   case $pkp <= 50000000:
                        //     $pph21 = $pkp * (5 / 100);
                        //     break;
                        //   case (50000000 <= $pkp && $pkp <= 250000000):
                        //     $pph21 = $pkp * (15 / 100);
                        //     break;
                        //   case (250000000 <= $pkp && $pkp <= 500000000):
                        //     $pph21 = $pkp * (25 / 100);
                        //     break;
                        //   default:
                        //     $pph21 = $pkp * (30 / 100);
                        //     break;
                        // }
                        if ($pph21) {
                            PphReportDetail::create([
                                'pph_report_id'  => $pphreport->id,
                                'employee_id'       => $employee->id,
                                'description'       => 'Potongan PPh21',
                                'total'             => ($pph21 / 12) > 0 ? $pph21 / 12 : 0,
                                'type'              => 0,
                                'status'            => 'Draft'
                            ]);
                        }
                    }
                    $pphreport->gross_salary = $this->gross_salary($pphreport->id) ? $this->gross_salary($pphreport->id) : 0;
                    $pphreport->deduction    = $this->deduction_salary($pphreport->id) ? $this->deduction_salary($pphreport->id) : 0;
                    $pphreport->net_salary   = $pphreport->gross_salary - $pphreport->deduction;
                    $pphreport->save();
                } else {
                    return $pphreport;
                }
            }
            return array(
                'status'    => true,
                'message'   => "Pph report generated successfully"
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
            $exists = $this->check_periode($request->montly, $request->year, $request->employee_name);
            if ($exists) {
                $delete = $exists->delete();
            }

            $period = changeDateFormat('Y-m-d', 01 . '-' . $request->montly . '-' . $request->year);

            $id = $this->getLatestId();

            $pphreport = PphReport::create([
                'id'            => $id,
                'employee_id'   => $request->employee_name,
                'created_by'    => $request->user,
                'period'        => $period,
                'status'        => -1
            ]);
            if ($pphreport) {
                $basesalary = $this->get_employee_salary($request->employee_name);
                $allowance = $this->get_additional_allowance($request->employee_name, $request->montly, $request->year);
                $overtime = $this->get_overtime($request->employee_name, $request->montly, $request->year);
                $leave = $this->get_leave($request->employee_name, $request->montly, $request->year);
                $alpha = $this->get_alpha($request->employee_name, $request->montly, $request->year);
                $driverallowance = $this->get_driver_allowance($request->employee_name, $request->montly, $request->year);
                $employee = Employee::with('department')->with('title')->find($request->employee_name);
                $ptkp = $this->get_pkp($employee->ptkp);
                $attendance_allowance = $this->get_attendance_allowance($request->employee_name, $request->montly, $request->year);
                $pph = $this->getPPhAllowance($request->employee_name, $request->montly, $request->year);
                if ($basesalary) {
                    PphReportDetail::create([
                        'pph_report_id'  => $id,
                        'employee_id'       => $request->employee_name,
                        'description'       => 'Basic Salary',
                        'total'             => $basesalary->amount,
                        'type'              => 1,
                        'status'            => $basesalary->amount == 0 ? 'Hourly' : 'Monthly'
                    ]);
                    $pphreport->pph_type = $basesalary->amount == 0 ? 'Hourly' : 'Monthly';
                    $pphreport->save();
                } else {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Base salary for this employee not found'
                    ], 400);
                }
                if ($allowance) {
                    foreach ($allowance as $key => $value) {
                        if ($value->value && $value->factor) {
                            PphReportDetail::create([
                                'pph_report_id'  => $id,
                                'employee_id'       => $request->employee_name,
                                'description'       => $value->description,
                                'total'             => $value->factor * $value->value,
                                'type'              => 1,
                                'status'            => 'Draft'
                            ]);
                        } else {
                            continue;
                        }
                    }
                }
                if ($overtime && $employee->overtime == 'yes') {
                    PphReportDetail::create([
                        'pph_report_id'  => $id,
                        'employee_id'       => $request->employee_name,
                        'description'       => 'Overtime',
                        'total'             => $overtime,
                        'type'              => 1,
                        'status'            => 'Draft'
                    ]);
                }
                if ($employee->department->name == 'Driver Team' && $driverallowance > 0) {
                    $spsi = PphReportDetail::create([
                        'pph_report_id'  => $id,
                        'employee_id'       => $request->employee_name,
                        'description'       => 'Driver Allowance',
                        'total'             => $driverallowance,
                        'type'              => 1,
                        'status'            => 'Draft'
                    ]);
                }
                if ($alpha > 0) {
                    $alpha_penalty = PphReportDetail::create([
                        'pph_report_id'  => $id,
                        'employee_id'       => $request->employee_name,
                        'description'       => 'Alpha penalty',
                        'total'             => -1 * ($alpha * ($basesalary->amount / 30)),
                        'type'              => 1,
                        'status'            => 'Draft'
                    ]);
                }
                if ($attendance_allowance) {
                    PphReportDetail::create([
                        'pph_report_id'  => $id,
                        'employee_id'       => $request->employee_name,
                        'description'       => 'Tunjangan Kehadiran',
                        'total'             => $attendance_allowance,
                        'type'              => 1,
                        'status'            => 'Draft'
                    ]);
                }
                $gross = $this->gross_salary($id) ? $this->gross_salary($id) : 0;
                if ($gross) {
                    PphReportDetail::create([
                        'pph_report_id' => $id,
                        'employee_id' => $request->employee_name,
                        'description' => 'Gross Salary',
                        'total' => $gross,
                        'type' => 1,
                        'status' => 'Draft'
                    ]);
                }
                $position_allowance = $gross * (5 / 100);
                if ($position_allowance) {
                    PphReportDetail::create([
                        'pph_report_id' => $id,
                        'employee_id' => $request->employee_name,
                        'description' => 'Biaya Jabatan',
                        'total' => $position_allowance,
                        'type' => 0,
                        'status' => 'Draft'
                    ]);
                }
                $net_salary = $gross - $position_allowance;
                if ($net_salary) {
                    PphReportDetail::create([
                        'pph_report_id' => $id,
                        'employee_id' => $request->employee_name,
                        'description' => 'Net Salary (Month)',
                        'total' => $net_salary,
                        'type' => 1,
                        'status' => 'Draft'
                    ]);
                }
                $net_salary_yearly = $net_salary * 12;
                if ($net_salary_yearly) {
                    PphReportDetail::create([
                        'pph_report_id' => $id,
                        'employee_id' => $request->employee_name,
                        'description' => 'Net Salary (Yearly)',
                        'total' => $net_salary_yearly,
                        'type' => 1,
                        'status' => 'Draft'
                    ]);
                }
                if ($ptkp->value) {
                    PphReportDetail::create([
                        'pph_report_id' => $id,
                        'employee_id' => $request->employee_name,
                        'description' => 'PTKP',
                        'total' => $ptkp->value,
                        'type' => 1,
                        'status' => 'Draft'
                    ]);
                }
                $pkp_yearly = $net_salary_yearly - $ptkp->value;
                if ($pkp_yearly) {
                    PphReportDetail::create([
                        'pph_report_id' => $id,
                        'employee_id' => $request->employee_name,
                        'description' => 'PKP (Yearly)',
                        'total' => $pkp_yearly,
                        'type' => 1,
                        'status' => 'Draft'
                    ]);
                }

                if ($pph) {
                    // $gross = $this->gross_salary($id) ? $this->gross_salary($id) : 0;
                    // $position_allowance = $gross * (5 / 100);
                    // $net_salary = $gross - $position_allowance;
                    // $net_salary_yearly = $net_salary * 12;
                    $pkp = $net_salary_yearly - $ptkp->value;
                    $pkp_left = $pkp;
                    $pph21 = 0;
                    $iteration = 4;

                    for ($i = 1; $i <= $iteration; $i++) {
                        if ($i == 1) {
                            if ($pkp_left > 0) {
                                if ($pkp_left <= 50000000) {
                                    $pph21 = $pkp_left * (5 / 100);
                                    $pkp_left = ($pkp_left - 50000000) <= 0 ? 0 : $pkp_left - 50000000;
                                } else {
                                    $pph21 = 50000000 * (5 / 100);
                                    $pkp_left = ($pkp_left - 50000000) <= 0 ? 0 : $pkp_left - 50000000;
                                }
                            } else {
                                break;
                            }
                        }
                        if ($i == 2) {
                            if ($pkp_left > 0) {
                                if ($pkp_left >= 250000000) {
                                    $pph21 = $pph21 + (250000000 * (15 / 100));
                                    $pkp_left = ($pkp_left - 250000000) <= 0 ? 0 : $pkp_left - 250000000;
                                } else {
                                    $pph21 = $pph21 + ($pkp_left * (15 / 100));
                                    $pkp_left = ($pkp_left - 250000000) <= 0 ? 0 : $pkp_left - 250000000;
                                }
                            } else {
                                break;
                            }
                        }
                        if ($i == 3) {
                            if ($pkp_left > 0) {
                                if ($pkp_left >= 500000000) {
                                    $pph21 = $pph21 + (500000000 * (25 / 100));
                                    $pkp_left = ($pkp_left - 500000000) <= 0 ? 0 : $pkp_left - 500000000;
                                } else {
                                    $pph21 = $pph21 + ($pkp_left * (25 / 100));
                                    $pkp_left = ($pkp_left - 500000000) <= 0 ? 0 : $pkp_left - 500000000;
                                }
                            } else {
                                break;
                            }
                        }
                        if ($i == 4) {
                            if ($pkp_left > 0) {
                                $pph21 = $pph21 + ($pkp_left * (30 / 100));
                            } else {
                                break;
                            }
                        }
                    }
                    //Cek Salary
                    // DB::rollBack();
                    // return response()->json([
                    //   'status'    => false,
                    //   'message'   => [
                    //     'pkp'=>$pkp,
                    //     'salary_year'=>$salary_year,
                    //     'position_allowance'=>$position_allowance,
                    //     'new_net'=>$new_net,
                    //     'gross'=>$gross,
                    //     'ptkp'=>$ptkp->value,
                    //   ]
                    // ], 400);
                    // switch ($pkp) {
                    //   case $pkp <= 50000000:
                    //     $pph21 = $pkp * (5 / 100);
                    //     break;
                    //   case (50000000 <= $pkp && $pkp <= 250000000):
                    //     $pph21 = $pkp * (15 / 100);
                    //     break;
                    //   case (250000000 <= $pkp && $pkp <= 500000000):
                    //     $pph21 = $pkp * (25 / 100);
                    //     break;
                    //   default:
                    //     $pph21 = $pkp * (30 / 100);
                    //     break;
                    // }
                    PphReportDetail::create([
                        'pph_report_id' => $id,
                        'employee_id' => $request->employee_name,
                        'description' => 'PPh 21 (Yearly)',
                        'total' => ($pph21) > 0 ? $pph21 : 0,
                        'type' => 0,
                        'status' => 'Draft'
                    ]);
                    PphReportDetail::create([
                        'pph_report_id' => $id,
                        'employee_id' => $request->employee_name,
                        'description' => 'PPh 21 (Monthly)',
                        'total' => ($pph21 / 12) > 0 ? $pph21 / 12 : 0,
                        'type' => 0,
                        'status' => 'Draft'
                    ]);

                    // if ($pph21) {
                    //     PphReportDetail::create([
                    //     'pph_report_id'  => $pphreport->id,
                    //     'employee_id'       => $employee->id,
                    //     'description'       => 'Potongan PPh21',
                    //     'total'             => ($pph21 / 12) > 0 ? $pph21 / 12 : 0,
                    //     'type'              => 0,
                    //     'status'            => 'Draft'
                    //     ]);
                    // }
                }
                $pphreport->gross_salary = $this->gross_salary($id) ? $this->gross_salary($id) : 0;
                $pphreport->deduction    = $this->deduction_salary($id) ? $this->deduction_salary($id) : 0;
                $pphreport->net_salary   = $pphreport->gross_salary - $pphreport->deduction;
                $pphreport->tax = $pph21 / 12;
                $pphreport->save();
            } elseif (!$pphreport) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $pphreport
                ], 400);
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Pph report generated successfully',
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
        $pph_detail = SalaryReport::with('salarydetail')->with('employee')->find($id);
        $ptkp = PTKP::where('key', $pph_detail->employee->ptkp)->first();
        $employee = Employee::find($pph_detail->employee->id);
        $multipleMonth = getMultiplierMonth($employee->join_date);
        if ($pph_detail) {
            return view('admin.pph.detail', compact('pph_detail', 'ptkp', 'multipleMonth'));
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
            $report = PphReport::find($id);
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
}