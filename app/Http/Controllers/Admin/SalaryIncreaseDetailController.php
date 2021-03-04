<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SalaryIncreaseDetail;
use App\Models\EmployeeSalary;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\OvertimeSchemeList;
use App\Models\SalaryIncreases;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class SalaryIncreaseDetailController extends Controller
{
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
        $departments = $request->departments;
        $workgroup = $request->workgroup;
        $position = $request->position;
        $salaryincreases = $request->salaryincreases_id;

        //Count Data
        $query = DB::table('salary_increase_details');
        $query->select(
            'salary_increase_details.*',
            'employees.name as employee_name',
            'employees.nid as nid',
            'titles.name as position',
            'departments.name as department',
            'salary_increases.ref as reff_no',
            // DB::raw("(SELECT MIN(amount) FROM employee_salarys where employee_id = employees.id) as current_salary"),
            'salary_increases.date as date',
            'salary_increases.value as increases_amount',
            'salary_increases.type as type'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'salary_increase_details.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('salary_increases', 'salary_increases.id', '=', 'salary_increase_details.salaryincrease_id');
        if ($employee_id) {
            $query->where('employees.id', $employee_id);
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($departments) {
            foreach($departments as $department){
                $query->whereRaw("departments.path like '%$department%'");
            }
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($position) {
            $query->whereIn('employees.title_id', $position);
        }
        if ($salaryincreases) {
            $query->where('salary_increases.id', $salaryincreases);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('salary_increase_details');
        $query->select(
            'salary_increase_details.*',
            'employees.name as employee_name',
            'employees.nid as nid',
            'titles.name as position',
            'departments.name as department',
            // DB::raw("(SELECT MAX(amount) FROM employee_salarys where employee_id = employees.id) as current_salary"),
            'salary_increases.ref as reff_no',
            'salary_increases.date as date',
            'salary_increases.value as increases_amount',
            'salary_increases.type as type'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'salary_increase_details.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('salary_increases', 'salary_increases.id', '=', 'salary_increase_details.salaryincrease_id');
        if ($employee_id) {
            $query->where('employees.id', $employee_id);
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($departments) {
            foreach($departments as $department){
                $query->whereRaw("departments.path like '%$department%'");
            }
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($position) {
            $query->whereIn('employees.title_id', $position);
        }
        if ($salaryincreases) {
            $query->where('salary_increases.id', $salaryincreases);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            // $employee->upcoming_amount = round($employee->upcoming_amount, -3);
            // $employee->current_Salary = round($employee->current_Salary, -3);
            // dd($employee->upcoming_amount);
            $data[] = $employee;
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
        //
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
    public function store(Request $request)
    {
        // dd($request->all());


        // dd($cok);
        DB::beginTransaction();
        if(count($request->employee_id) > 0){

            foreach($request->employee_id as $item => $v){
                foreach ($request->employess as $e => $s) {
                    if($s == $v){
                        // dd($request->current_salary[$e]);
                        $cok = $request->employee_id ? 1:0;
                        if($cok == 1){
                            $salaryincrease = SalaryIncreases::where('id',$request->salaryincrease_id)->first();
                            // dd($salaryincrease);

                            // echo $salaryincrease->type;

                            $upcoming_amount = 0;
                            if($salaryincrease->type == 'Percentage'){
                                $upcoming_amount = (int)$request->current_salary[$e] + ($request->current_salary[$e] * ($salaryincrease->value / 100));
                            }else{
                                $upcoming_amount = (int)$request->current_salary[$e] + $salaryincrease->value;
                            }

                            $salaryincreasesdetail = SalaryIncreaseDetail::create([
                                'employee_id' => $request->employee_id[$item],
                                'salaryincrease_id' => $request->salaryincrease_id,
                                'current_Salary' => $request->current_salary[$e],
                                'upcoming_amount' => $upcoming_amount
                            ]);

                            $employees = EmployeeSalary::create([
                                'employee_id' => $request->employee_id[$item],
                                'description' => $salaryincrease->notes,
                                'amount' => $upcoming_amount,
                                'user_id' => auth()->user()->id,
                                'created_date'  => $salaryincrease->date,
                            ]);
                            
                            
                            if (!$salaryincreasesdetail) {
                                DB::rollback();
                                return response()->json([
                                    'status' => false,
                                    'message'     => 'Gagal Menyimpan Data!! Harap Periksa Kembali'
                                ], 400);
                            }
                            else{
                                $now = $salaryincrease->date;
                                $firstMonth = Attendance::where('attendance_date', '>=', $now)->where('employee_id', $request->employee_id[$item])
                                ->where('status', 1)->where('adj_over_time', '>', 0)->get();
                                    foreach($firstMonth as $attendanceMonth){
                                        $overtime = Overtime::where('date', $attendanceMonth->attendance_date)->where('employee_id', $attendanceMonth->employee_id);
                                        $overtime->delete();
                                        $rules = OvertimeSchemeList::where('recurrence_day', '=', $attendanceMonth->day)->get();
                                        if ($rules) {
                                            if ($attendanceMonth->day != 'Off') {
                                                $i = 0;
                                                $overtimes = $attendanceMonth->adj_over_time;
                                                $length = count($rules);
                                                foreach ($rules as $key => $value) {
                                                    $sallary = EmployeeSalary::where('employee_id', '=', $attendanceMonth->employee_id)->orderBy('created_at', 'desc')->first();
                                                    if ($overtimes >= 0) {
                                                        $overtime = Overtime::create([
                                                            'employee_id'   => $attendanceMonth->employee_id,
                                                            'day'           => $value->recurrence_day,
                                                            'scheme_rule'   => $value->hour,
                                                            'hour'          => ($i != $length - 1) ? 1 : $overtimes,
                                                            'amount'        => $value->amount,
                                                            'basic_salary'  => $sallary ? $sallary->amount / 173 : 0,
                                                            'date'          => changeDateFormat('Y-m-d', $attendanceMonth->attendance_date)
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
                                            } else {
                                                $i = 0;
                                                $n = 2;
                                                $overtimes = $attendanceMonth->adj_over_time;
                                                $length = count($rules);
                                                foreach ($rules as $key => $value) {
                                                    $sallary = EmployeeSalary::where('employee_id', '=', $attendanceMonth->employee_id)->orderBy('created_at', 'desc')->first();
                                                    if ($overtimes >= 0) {
                                                        $overtime = Overtime::create([
                                                            'employee_id'   => $attendanceMonth->employee_id,
                                                            'day'           => $value->recurrence_day,
                                                            'scheme_rule'   => $value->hour,
                                                            'hour'          => ($i != $length - 1 && $overtimes >= 1) ? 1 : $overtimes,
                                                            'amount'        => $value->amount,
                                                            'basic_salary'  => $sallary ? $sallary->amount / 173 : 0,
                                                            'date'          => changeDateFormat('Y-m-d', $attendanceMonth->attendance_date)
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
                                        } else {
                                            DB::rollBack();
                                            return response()->json([
                                                'status'      => false,
                                                'message'     => 'There is no overtime scheme for attendance on the relevant day'
                                            ], 400);
                                        }
                                    }
                                }
                            }
                        }
                }
                
            }

            // die();
        }
        // dd($salaryincreasesdetail);

        // if()
        
        DB::commit();
        return response()->json([
            'status'     => true,
            'message'   => 'Salary Increases successfully',
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
    function destroychild($employee_id)
    {
        try {
            $employeeSalarys = EmployeeSalary::where('employee_id', '=', $employee_id)->orderBy('created_at', 'desc')->first();
            $employeeSalarys->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'    => false,
                'message'   => 'Data has been used to another page'
            ], 400);
        }
    }
    public function destroy($id)
    {
        try {
            $salaryincreasedetail = SalaryIncreaseDetail::find($id);
            $salaryincreasedetail->delete();
            // dd($salaryincreasedetail->employee_id);
            $this->destroychild($salaryincreasedetail->employee_id);
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