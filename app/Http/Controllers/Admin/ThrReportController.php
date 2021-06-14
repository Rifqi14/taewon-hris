<?php

namespace App\Http\Controllers\Admin;

use App\Models\ThrReport;
use App\Models\ThrReportDetail;
use App\Models\WorkGroup;
use App\Models\Title;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Attendance;
use App\Models\Config;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class ThrReportController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'thrreport'));
    }

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
        $period = $request->period;

        //Count Data
        $query = DB::table('thr_reports');
        $query->select('thr_reports.*', 'employees.name as employee_name', 'employees.nid as nik', 'titles.name as title_name', 'departments.name as department_name', 'work_groups.name as workgroup_name', 'employees.department_id as department_id', 'employees.title_id as title_id', 'employees.workgroup_id as workgroup_id');
        $query->leftJoin('employees', 'employees.id', '=', 'thr_reports.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        if ($employee_id) {
            $query->whereIn('thr_reports.employee_id', $employee_id);
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($year) {
            $query->whereIn('thr_reports.year', $year);
        }
        if ($month) {
            $query->whereIn('thr_reports.month', $month);
        }
        // if ($month) {
        //     // $query->whereMonth('salary_reports.period', '=', $month);
        //     $query->where(function ($query1) use ($month) {
        //         foreach ($month as $q_month) {
        //             $query1->where("thr_trports.month", '=', $q_month);
        //         }
        //     });
        // }
        // if ($year) {
        //     // $query->whereYear('salary_reports.period', '=', $year);
        //     $query->where(function ($query2) use ($year) {
        //         foreach ($year as $q_year) {
        //             $query2->where("thr_trports.year",'=' ,$q_year);
        //         }
        //     });
        // }
        if ($department_ids) {
            $string = '';
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
            $query->whereIn('thr_reports.status', $status);
        }
        if ($period) {
            $query->whereIn('thr_reports.period', $period);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('thr_reports');
        $query->select('thr_reports.*', 'employees.name as employee_name', 'employees.nid as nik', 'titles.name as title_name', 'departments.name as department_name', 'work_groups.name as workgroup_name', 'employees.department_id as department_id', 'employees.title_id as title_id', 'employees.workgroup_id as workgroup_id');
        $query->leftJoin('employees', 'employees.id', '=', 'thr_reports.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        if ($employee_id) {
            $query->whereIn('thr_reports.employee_id', $employee_id);
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        // if ($month) {
        //     // $query->whereMonth('salary_reports.period', '=', $month);
        //     $query->where(function ($query1) use ($month) {
        //         foreach ($month as $q_month) {
        //             $query1->where("thr_trports.month", '=', $q_month);
        //         }
        //     });
        // }
        // if ($year) {
        //     // $query->whereYear('salary_reports.period', '=', $year);
        //     $query->where(function ($query2) use ($year) {
        //         foreach ($year as $q_year) {
        //             $query2->where("thr_trports.year", '=', $q_year);
        //         }
        //     });
        // }
        if ($year) {
            $query->whereIn('thr_reports.year', $year);
        }
        if ($month) {
            $query->whereIn('thr_reports.month', $month);
        }
        if ($department_ids) {
            $string = '';
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
            $query->whereIn('thr_reports.status', $status);
        }
        if ($period) {
            $query->whereIn('thr_reports.period', $period);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('working_periode', $dir);
        $query->orderBy($sort, $dir);
        $reports = $query->get();
        $data = [];
        foreach ($reports as $report) {
            $report->no = ++$start;
            $report->amount = number_format($report->amount, 0, ',', '.');
            $report->working_periode = changeDateFormat('F - Y', $report->working_periode);
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
        $query->orderBy('path', 'asc');
        $departments = $query->get();
        $workgroups = WorkGroup::all();
        $titles = Title::all();

        return view('admin.thrreport.index', compact('employees', 'departments', 'workgroups', 'titles'));
    }

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
   
    public function get_employee_salary($id)
    {
        $basesalary = EmployeeSalary::where('employee_id', '=', $id)->orderBy('created_at', 'desc')->first();

        return $basesalary;
    }
    public function check_periode($month, $year, $employee)
    {
        $exists = ThrReport::where('month', '=', $month)->where('year', '=', $year)->where('employee_id', '=', $employee)->first();

        return $exists;
    }
    public function getAllowanceThr($id, $month, $year)
    {
        $query = DB::table('employee_allowances');
        $query->selectRaw("sum(employee_allowances.value::numeric) as allowance_value");
        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
        $query->where('employee_allowances.employee_id', '=', $id);
        $query->where('allowances.thr', '=', 'Yes');
        $query->where('employee_allowances.month', '=', $month);
        $query->where('employee_allowances.year', '=', $year);
        $allowances = $query->get();

        $data = [];
        foreach ($allowances as $allowance) {
            $data[] = $allowance;
        }

        return $data;
    }
    public function get_additional_allowance($id, $month, $year)
    {
        $query = DB::table('employee_allowances');
        // $query->select('employee_allowances.*', 'allowances.allowance as description', 'allowances.group_allowance_id');
        $query->selectRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) as value, group_allowances.name as description, employee_allowances.is_penalty as is_penalty, allowances.group_allowance_id as group_allowance_id, employee_allowances.type as type, max(allowances.allowance) as allowance_name");
        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
        $query->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
        $query->leftJoin('group_allowances', 'group_allowances.id', 'allowances.group_allowance_id');
        $query->where('allowances.thr', '=', 'Yes');
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
    public function get_allowance_thr($id, $month, $year)
    {
        $query = DB::table('employee_allowances');
        // $query->select('employee_allowances.*', 'allowances.allowance as description', 'allowances.group_allowance_id');
        $query->selectRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) as value,group_allowances.name as description, employee_allowances.is_penalty as is_penalty, allowances.group_allowance_id as group_allowance_id, employee_allowances.type as type, max(allowances.allowance) as allowance_name,allowances.allowance");
        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
        $query->leftJoin('allowance_categories', 'allowance_categories.key', '=', 'allowances.category');
        $query->leftJoin('group_allowances', 'group_allowances.id', 'allowances.group_allowance_id');
        $query->where('allowances.thr', '=', 'Yes');
        $query->where('employee_allowances.employee_id', '=', $id);
        $query->where('employee_allowances.month', '=', $month);
        $query->where('employee_allowances.year', '=', $year);
        $query->where('employee_allowances.status', '=', 1);
        $query->where('allowance_categories.type', '=', 'additional');
        $query->where('employee_allowances.type', '!=', 'automatic');
        $query->groupBy('group_allowances.name', 'employee_allowances.is_penalty', 'allowances.group_allowance_id', 'employee_allowances.type','allowances.allowance');
        // $query->orderByRaw("sum(case when employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) desc");
        $allowances = $query->get();

        $data = [];
        foreach ($allowances as $allowance) {
            $data[] = $allowance;
        }

        return $data;
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
        if($employees->count() > 0) {
            foreach ($employees as $employee) {
                $exists = $this->check_periode($month, $year, $employee->id);
                if ($exists) {
                    $delete = $exists->delete();
                }
                $dt = Carbon::createFromFormat('Y-m', $year . '-' . $month);
                $checkDate = changeDateFormat('Y-m-d', $dt->endOfMonth()->toDateString() . '-' . $month . '-' . $year);
                $checkJoinDate = Employee::select('employees.*')->where('employees.status', 1)->where('employees.join_date', '<=', $checkDate)->find($employee->id);
                if ($exists) {
                    $delete = $exists->delete();
                }
                if ($checkJoinDate) {
                    $date1 = date("Y-m", strtotime($checkJoinDate->join_date));
                    $date1 = $date1 . "-01";
                    $date2 = Carbon::createFromFormat('Y-m', $year . '-' . $month);
                    $date2 = $date2 . "-01";

                    $diff = abs(strtotime($date2) - strtotime($date1));

                    $years = floor($diff / (365 * 60 * 60 * 24));
                    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    $total_month = ($years * 12) + $months + 1;

                    if($total_month < 12){
                        $thrreport = ThrReport::create([
                            'employee_id'       => $employee->id,
                            'created_by'        => $user,
                            'working_periode'   => $checkJoinDate->join_date,
                            'period'            => $total_month,
                            'year'              => $year,
                            'month'             => $month,
                            'status'            => -1
                        ]);
                    }else{
                        $thrreport = ThrReport::create([
                            'employee_id'       => $employee->id,
                            'created_by'        => $user,
                            'working_periode'   => $checkJoinDate->join_date,
                            'period'            => 12,
                            'year'              => $year,
                            'month'             => $month,
                            'status'            => -1
                        ]);
                    }
                   
                    if ($thrreport) {
                        $basesalary = $this->get_employee_salary($employee->id);
                        $allowance = $this->get_additional_allowance($employee->id, $month, $year);
                        $employee = Employee::with('department')->with('title')->find($employee->id);
                        if ($basesalary && $allowance) {
                            foreach ($allowance as $key => $value) {
                                if ($thrreport->period < 12) {
                                    $thrdetail = ThrReportDetail::create([
                                        'thr_report_id'        => $thrreport->id,
                                        'employee_id'          => $employee->id,
                                        'description'          => 'THR Basic + Allowance',
                                        'total'                => number_format((float)(($basesalary->amount + $value->value) / 12 * $thrreport->period), 2, '.', ''),
                                        'is_added'             => 'No'
                                    ]);
                                    $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                    $thrreport->save();
                                    if (!$thrdetail) {
                                        DB::rollBack();
                                        return response()->json([
                                            'status'    => false,
                                            'message'   => $thrdetail
                                        ], 400);
                                    }
                                } else {
                                    $thrdetail = ThrReportDetail::create([
                                        'thr_report_id'        => $thrreport->id,
                                        'employee_id'          => $employee->id,
                                        'description'          => 'THR Basic + Allowance',
                                        'total'                => number_format((float)(($basesalary->amount + $value->value) / 12 * 12), 2, '.', ''),
                                        'is_added'             => 'No'
                                    ]);
                                    $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                    $thrreport->save();

                                    if (!$thrdetail) {
                                        DB::rollBack();
                                        return response()->json([
                                            'status'    => false,
                                            'message'   => $thrdetail
                                        ], 400);
                                    }
                                }
                            }
                        } else {
                            if ($thrreport->period < 12) {
                                $thrdetail = ThrReportDetail::create([
                                    'thr_report_id'        => $thrreport->id,
                                    'employee_id'          => $employee->id,
                                    'description'          => 'THR Basic',
                                    'total'                => number_format((float)($basesalary->amount / 12 * $thrreport->period), 2, '.', ''),
                                    'is_added'             => 'No'
                                ]);

                                $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                $thrreport->save();


                                if (!$thrdetail) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $thrdetail
                                    ], 400);
                                }
                            } else {
                                $thrdetail = ThrReportDetail::create([
                                    'thr_report_id'        => $thrreport->id,
                                    'employee_id'          => $employee->id,
                                    'description'          => 'THR Basic',
                                    'total'                => number_format((float)($basesalary->amount / 12 * 12), 2, '.', ''),
                                    'is_added'             => 'No'
                                ]);
                                $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                $thrreport->save();

                                if (!$thrdetail) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $thrdetail
                                    ], 400);
                                }
                            }
                        }
                    } elseif (!$thrreport) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => 'This employee join date is greater than generate month and year',
                        ], 400);
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
        if(!$employees->isEmpty()){
            foreach ($employees as $employee) {
                $exists = $this->check_periode($month, $year, $employee->id);
                if ($exists) {
                    $delete = $exists->delete();
                }
                $dt = Carbon::createFromFormat('Y-m', $year . '-' . $month);
                $checkDate = changeDateFormat('Y-m-d', $dt->endOfMonth()->toDateString() . '-' . $month . '-' . $year);
                $checkJoinDate = Employee::select('employees.*')->where('employees.status', 1)->where('employees.join_date', '<=', $checkDate)->find($employee->id);
                if ($exists) {
                    $delete = $exists->delete();
                }
                if ($checkJoinDate) {
                    $date1 = date("Y-m", strtotime($checkJoinDate->join_date));
                    $date1 = $date1 . "-01";
                    $date2 = Carbon::createFromFormat('Y-m', $year . '-' . $month);
                    $date2 = $date2 . "-01";

                    $diff = abs(strtotime($date2) - strtotime($date1));

                    $years = floor($diff / (365 * 60 * 60 * 24));
                    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                    $months = $months + 1;
                    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

                    $total_month = ($years * 12) + $months + 1;

                    if ($total_month < 12) {
                        $thrreport = ThrReport::create([
                            'employee_id'       => $employee->id,
                            'created_by'        => $user,
                            'working_periode'   => $checkJoinDate->join_date,
                            'period'            => $total_month,
                            'year'              => $year,
                            'month'             => $month,
                            'status'            => -1
                        ]);
                    } else {
                        $thrreport = ThrReport::create([
                            'employee_id'       => $employee->id,
                            'created_by'        => $user,
                            'working_periode'   => $checkJoinDate->join_date,
                            'period'            => 12,
                            'year'              => $year,
                            'month'             => $month,
                            'status'            => -1
                        ]);
                    }
                    if ($thrreport) {
                        $basesalary = $this->get_employee_salary($employee->id);
                        $allowance = $this->get_additional_allowance($employee->id, $month, $year);
                        $employee = Employee::with('department')->with('title')->find($employee->id);
                        if ($basesalary && $allowance) {
                            foreach ($allowance as $key => $value) {
                                if ($thrreport->period < 12) {
                                    $thrdetail = ThrReportDetail::create([
                                        'thr_report_id'        => $thrreport->id,
                                        'employee_id'          => $employee->id,
                                        'description'          => 'THR Basic + Allowance',
                                        'total'                => number_format((float)(($basesalary->amount + $value->value) / 12 * $thrreport->period), 2, '.', ''),
                                        'is_added'             => 'No'
                                    ]);
                                    $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                    $thrreport->save();
                                    if (!$thrdetail) {
                                        DB::rollBack();
                                        return response()->json([
                                            'status'    => false,
                                            'message'   => $thrdetail
                                        ], 400);
                                    }
                                } else {
                                    $thrdetail = ThrReportDetail::create([
                                        'thr_report_id'        => $thrreport->id,
                                        'employee_id'          => $employee->id,
                                        'description'          => 'THR Basic + Allowance',
                                        'total'                => number_format((float)(($basesalary->amount + $value->value) / 12 * 12), 2, '.', ''),
                                        'is_added'             => 'No'
                                    ]);
                                    $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                    $thrreport->save();

                                    if (!$thrdetail) {
                                        DB::rollBack();
                                        return response()->json([
                                            'status'    => false,
                                            'message'   => $thrdetail
                                        ], 400);
                                    }
                                }
                            }
                        } else {
                            if ($thrreport->period < 12) {
                                $thrdetail = ThrReportDetail::create([
                                    'thr_report_id'        => $thrreport->id,
                                    'employee_id'          => $employee->id,
                                    'description'          => 'THR Basic',
                                    'total'                => number_format((float)($basesalary->amount / 12 * $thrreport->period), 2, '.', ''),
                                    'is_added'             => 'No'
                                ]);

                                $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                $thrreport->save();


                                if (!$thrdetail) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $thrdetail
                                    ], 400);
                                }
                            } else {
                                $thrdetail = ThrReportDetail::create([
                                    'thr_report_id'        => $thrreport->id,
                                    'employee_id'          => $employee->id,
                                    'description'          => 'THR Basic',
                                    'total'                => number_format((float)($basesalary->amount / 12 * 12), 2, '.', ''),
                                    'is_added'             => 'No'
                                ]);
                                $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                $thrreport->save();

                                if (!$thrdetail) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $thrdetail
                                    ], 400);
                                }
                            }
                        }
                    } elseif (!$thrreport) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => 'This employee join date is greater than generate month and year',
                        ], 400);
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
                $dt = Carbon::createFromFormat('Y-m', $year . '-' . $month);
                $checkDate = changeDateFormat('Y-m-d', $dt->endOfMonth()->toDateString() . '-' . $month . '-' . $year);
                $checkJoinDate = Employee::select('employees.*')->where('employees.status', 1)->where('employees.join_date', '<=', $checkDate)->find($employee->id);
                if ($exists) {
                    $delete = $exists->delete();
                }
                if ($checkJoinDate) {
                    $date1 = date("Y-m", strtotime($checkJoinDate->join_date));
                    $date1 = $date1 . "-01";
                    $date2 = Carbon::createFromFormat('Y-m', $year . '-' . $month);
                    $date2 = $date2 . "-01";

                    $diff = abs(strtotime($date2) - strtotime($date1));

                    $years = floor($diff / (365 * 60 * 60 * 24));
                    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                    $months = $months + 1;
                    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

                    $total_month = ($years * 12) + $months + 1;

                    if ($total_month < 12) {
                        $thrreport = ThrReport::create([
                            'employee_id'       => $employee->id,
                            'created_by'        => $user,
                            'working_periode'   => $checkJoinDate->join_date,
                            'period'            => $total_month,
                            'year'              => $year,
                            'month'             => $month,
                            'status'            => -1
                        ]);
                    } else {
                        $thrreport = ThrReport::create([
                            'employee_id'       => $employee->id,
                            'created_by'        => $user,
                            'working_periode'   => $checkJoinDate->join_date,
                            'period'            => 12,
                            'year'              => $year,
                            'month'             => $month,
                            'status'            => -1
                        ]);
                    }
                    if ($thrreport) {
                        $basesalary = $this->get_employee_salary($employee->id);
                        $allowance = $this->get_additional_allowance($employee->id, $month, $year);
                        $employee = Employee::with('department')->with('title')->find($employee->id);
                        if ($basesalary && $allowance) {
                            foreach ($allowance as $key => $value) {
                                if ($thrreport->period < 12) {
                                    $thrdetail = ThrReportDetail::create([
                                        'thr_report_id'        => $thrreport->id,
                                        'employee_id'          => $employee->id,
                                        'description'          => 'THR Basic + Allowance',
                                        'total'                => number_format((float)(($basesalary->amount + $value->value) / 12 * $thrreport->period), 2, '.', ''),
                                        'is_added'             => 'No'
                                    ]);
                                    $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                    $thrreport->save();
                                    if (!$thrdetail) {
                                        DB::rollBack();
                                        return response()->json([
                                            'status'    => false,
                                            'message'   => $thrdetail
                                        ], 400);
                                    }
                                } else {
                                    $thrdetail = ThrReportDetail::create([
                                        'thr_report_id'        => $thrreport->id,
                                        'employee_id'          => $employee->id,
                                        'description'          => 'THR Basic + Allowance 02',
                                        'total'                => number_format((float)($basesalary->amount + $value->value / 12 * 12), 2, '.', ''),
                                        'is_added'             => 'No'
                                    ]);
                                    $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                    $thrreport->save();

                                    if (!$thrdetail) {
                                        DB::rollBack();
                                        return response()->json([
                                            'status'    => false,
                                            'message'   => $thrdetail
                                        ], 400);
                                    }
                                }
                            }
                        } else {
                            if ($thrreport->period < 12) {
                                $thrdetail = ThrReportDetail::create([
                                    'thr_report_id'        => $thrreport->id,
                                    'employee_id'          => $employee->id,
                                    'description'          => 'THR Basic 03',
                                    'total'                => number_format((float)($basesalary->amount / 12 * $thrreport->period), 2, '.', ''),
                                    'is_added'             => 'No'
                                ]);

                                $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                $thrreport->save();


                                if (!$thrdetail) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $thrdetail
                                    ], 400);
                                }
                            } else {
                                $thrdetail = ThrReportDetail::create([
                                    'thr_report_id'        => $thrreport->id,
                                    'employee_id'          => $employee->id,
                                    'description'          => 'THR Basic 04',
                                    'total'                => number_format((float)($basesalary->amount / 12 * 12), 2, '.', ''),
                                    'is_added'             => 'No'
                                ]);
                                $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                $thrreport->save();

                                if (!$thrdetail) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $thrdetail
                                    ], 400);
                                }
                            }
                        }
                    } elseif (!$thrreport) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => 'This employee join date is greater than generate month and year',
                        ], 400);
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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
            foreach ($request->employee_name as $view_employee){
                $dt = Carbon::createFromFormat('Y-m', $request->year . '-' . $request->montly);
                $checkDate = changeDateFormat('Y-m-d', $dt->endOfMonth()->toDateString() . '-' . $request->montly . '-' . $request->year);
                $checkJoinDate = Employee::select('employees.*')->where('employees.status', 1)->where('employees.join_date', '<=', $checkDate)->find($view_employee);
                $exists = $this->check_periode($request->montly, $request->year, $view_employee);
                if ($exists) {
                    $delete = $exists->delete();
                }
                if($checkJoinDate){
                    $date1 = date("Y-m", strtotime($checkJoinDate->join_date));
                    $date1 = $date1."-01";
                    $date2 = Carbon::createFromFormat('Y-m', $request->year . '-' . $request->montly);
                    $date2 = $date2 . "-01";

                    $diff = abs(strtotime($date2) - strtotime($date1));

                    $years = floor($diff / (365 * 60 * 60 * 24));
                    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                    $total_month = ($years * 12) + $months + 1;
                    // dd($total_month);
                    // dd($date1, $date2,$diff,$months);
                    if($total_month < 12)
                    {
                        $thrreport = ThrReport::create([
                            'employee_id'       => $view_employee,
                            'created_by'        => $request->user,
                            'working_periode'   => $checkJoinDate->join_date,
                            'period'            => $total_month,
                            'year'              => $request->year,
                            'month'             => $request->montly,
                            'status'            => -1
                        ]);
                    }else{
                        $thrreport = ThrReport::create([
                            'employee_id'       => $view_employee,
                            'created_by'        => $request->user,
                            'working_periode'   => $checkJoinDate->join_date,
                            'period'            => 12,
                            'year'              => $request->year,
                            'month'             => $request->montly,
                            'status'            => -1
                        ]);
                    }
                    
                    if($thrreport){
                        $basesalary = $this->get_employee_salary($view_employee);
                        $allowance = $this->get_additional_allowance($view_employee, $request->montly, $request->year);
                        $allowance_thr = $this->get_allowance_thr($view_employee, $request->montly, $request->year);
                        $configThr = Config::where('option', 'thr')->first();
                        $employee = Employee::with('department')->with('title')->find($view_employee);

                        if($configThr->value == 'basic_allowance'){
                            if ($basesalary) {
                                // $amount_allowance = 0;
                                // foreach ($allowance as $key => $value) {
                                //    $amount_allowance = $amount_allowance +  $value->value;
                                // }
                                // Rumus : 'total'                => number_format((float)(($basesalary->amount + $amount_allowance) / 12 * $thrreport->period), 2, '.', ''),
                                // Insert Basic Salary
                                $thrdetail = ThrReportDetail::create([
                                    'thr_report_id'        => $thrreport->id,
                                    'employee_id'          => $employee->id,
                                    'description'          => 'Basic Salary',
                                    'total'                => $basesalary->amount,
                                    'is_added'             => 'No'
                                ]);
                                // $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                // $thrreport->save();
                                $thrreport->amount = $basesalary->amount;
                                $thrreport->save();
                                if (!$thrdetail) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $thrdetail
                                    ], 400);
                                }
                                // Insert Looping Allowance
                                $subTotal = 0;
                                foreach ($allowance_thr as $key => $value) {
                                   if ($thrreport->period < 12) {
                                        $thrdetail = ThrReportDetail::create([
                                            'thr_report_id'        => $thrreport->id,
                                            'employee_id'          => $employee->id,
                                            'description'          => $value->allowance,
                                            'total'                => $value->value,
                                            'is_added'             => 'No'
                                        ]);
                                        $subTotal = $subTotal + $basesalary->amount + $amount_allowance;
                                        $grandTotal = ($subTotal / 12) * $thrreport->period;
                                        $thrreport->amount = $grandTotal;
                                        $thrreport->save();
                                        if (!$thrdetail) {
                                            DB::rollBack();
                                            return response()->json([
                                                'status'    => false,
                                                'message'   => $thrdetail
                                            ], 400);
                                        }
                                    } else {
                                        $thrdetail = ThrReportDetail::create([
                                            'thr_report_id'        => $thrreport->id,
                                            'employee_id'          => $employee->id,
                                            'description'          => $value->allowance,
                                            'total'                => $value->value,
                                            'is_added'             => 'No'
                                        ]);
                                        $subTotal = $subTotal + $basesalary->amount + $amount_allowance;
                                        $grandTotal = ($subTotal / 12) * 12;
                                        $thrreport->amount = $grandTotal;
                                        $thrreport->save();
                                        if (!$thrdetail) {
                                            DB::rollBack();
                                            return response()->json([
                                                'status'    => false,
                                                'message'   => $thrdetail
                                            ], 400);
                                        }
                                    }
                                }
                                // End Insert Looping Allowance
                            }
                        } else {
                            if ($thrreport->period < 12) {
                                $thrdetail = ThrReportDetail::create([
                                    'thr_report_id'        => $thrreport->id,
                                    'employee_id'          => $employee->id,
                                    'description'          => 'THR',
                                    'total'                => number_format((float)($basesalary->amount / 12 * $thrreport->period), 2, '.', ''),
                                    'is_added'             => 'No'
                                ]);

                                $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                $thrreport->save();


                                if (!$thrdetail) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $thrdetail
                                    ], 400);
                                }
                            } else {
                                $thrdetail = ThrReportDetail::create([
                                    'thr_report_id'        => $thrreport->id,
                                    'employee_id'          => $employee->id,
                                    'description'          => 'THR Basic',
                                    'total'                => number_format((float)($basesalary->amount / 12 * 12), 2, '.', ''),
                                    'is_added'             => 'No'
                                ]);
                                $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                                $thrreport->save();

                                if (!$thrdetail) {
                                    DB::rollBack();
                                    return response()->json([
                                        'status'    => false,
                                        'message'   => $thrdetail
                                    ], 400);
                                }
                            }
                        }
                        
                        
                    }elseif (!$thrreport){
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => 'This employee join date is greater than generate month and year',
                        ], 400);
                    }
                }
            }
            DB::commit();
            // dd($thrreport->amount, $thrdetail->total);
            return response()->json([
                'status'    => true,
                'message'   => 'salary report generated successfully',
            ], 200);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Please select one parameter from position, department or workgroup to generate mass'
            ],
                400
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ThrReport  $thrReport
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $thrreport = ThrReport::with('thrdetail')->with('employee')->find($id);
        $employee = Employee::with('department')->with('title')->find($thrreport->employee_id);
        if ($thrreport) {
            return view('admin.thrreport.detail', compact('thrreport', 'employee'));
        } else {
            abort(404);
        }
    }

    public function quickupdate(Request $request)
    {
        if($request->month_periode){
            $thrreport = ThrReport::find($request->periode_id);
            if ($thrreport) {
                $basesalary = $this->get_employee_salary($thrreport->employee_id);
                $allowance = $this->get_additional_allowance($thrreport->employee_id, $thrreport->month, $thrreport->year);
                // dd($allowance);
                $employee = Employee::with('department')->with('title')->find($thrreport->employee_id);
                $thrdetail = ThrReportDetail::where('thr_report_id', $thrreport->id)->first();
                if ($basesalary && $allowance) {
                    foreach ($allowance as $key => $value) {
                        $thrdetail->total = number_format((float)(($basesalary->amount + $value->value) / 12 * $request->month_periode), 2, '.', '');
                        $thrdetail->save();

                        $thrreport->period = $request->month_periode;
                        $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                        $thrreport->save();

                        if (!$thrdetail) {
                            DB::rollBack();
                            return response()->json([
                                'status'    => false,
                                'message'   => $thrdetail
                            ], 400);
                        }
                       
                    }
                } else {
                    $thrdetail->total = number_format((float)($basesalary->amount / 12 * $request->month_periode), 2, '.', '');
                    $thrdetail->save();

                    $thrreport->period = $request->month_periode;
                    $thrreport->amount = number_format((float)($thrdetail->total), 2, '.', '');
                    $thrreport->save();

                    if (!$thrdetail) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $thrdetail
                        ], 400);
                    }
                    
                }
            } elseif (!$thrreport) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Chane Month Error!'
                ], 400);
            }
        }
        return redirect()->back();
        // return response()->json([
        //     'status'    => true,
        //     'message'   => 'Success change Month',
        // ], 200);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ThrReport  $thrReport
     * @return \Illuminate\Http\Response
     */
    public function edit(ThrReport $thrReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ThrReport  $thrReport
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ThrReport $thrReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ThrReport  $thrReport
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $report = ThrReport::find($id);
            $report->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'    => false,
                'message'   => 'Data has been used to another page'
            ],
                400
            );
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }

    public function printmass(Request $request)
    {
        $id = json_decode($request->id);
        
        $thrReports = ThrReport::with('employee')->with('thrdetail')->whereIn('id', $id)->get();
        return view('admin.thrreport.print', compact('thrReports'));
    }
}
