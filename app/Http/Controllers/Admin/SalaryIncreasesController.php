<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SalaryIncreases;
use App\Models\WorkGroup;
use App\Models\Title;
use App\Models\EmployeeSalary;
use App\Models\SalaryIncreaseDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class SalaryIncreasesController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'salaryincreases'));
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
        $reff_no = $request->reff_no;
        $description = $request->description;
        $date_from = date('Y-m-d', strtotime(changeSlash($request->date_from)));
        $date_to = date('Y-m-d', strtotime(changeSlash($request->date_to)));

        //Count Data
        $query = DB::table('salary_increases');
        $query->select('salary_increases.*');
        if ($reff_no) {
            $query->whereRaw("salary_increases.ref like '%$reff_no%'");
        }
        if ($description) {
            $query->whereRaw("salary_increases.notes like '%$description%'");
        }
        
        if ($date_from) {
            $query->where('date', '>=', $date_from);
        }
        if ($date_to) {
            $query->where('date', '<=', $date_to);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('salary_increases');
        $query->select('salary_increases.*', DB::Raw('(select count(*) from salary_increase_details as sd where sd.salaryincrease_id = salary_increases.id) as total_employee'));
        if ($reff_no) {
            $query->whereRaw("salary_increases.ref like '%$reff_no%'");
        }
        if ($description) {
            $query->whereRaw("salary_increases.notes like '%$description%'");
        }
        
        if ($date_from) {
            $query->where('date', '>=', $date_from);
        }
        if ($date_to) {
            $query->where('date', '<=', $date_to);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $salaryincreases = $query->get();

        $data = [];
        foreach ($salaryincreases as $salaryincrease) {
            $salaryincrease->no = ++$start;
            $data[] = $salaryincrease;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'sort' => $sort,
            'data' => $data
        ], 200);
    }
    public function reademployee(Request $request)
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

        $sf_increases = SalaryIncreaseDetail::where('salaryincrease_id', $salaryincreases)->get();

        $dataa = [];
        foreach ($sf_increases as $sv_salary) {
            $dataa[] = $sv_salary->employee_id;
        }

        //Count Data
        $query = DB::table('employees');
        $query->select(
            'employees.name',
            'employees.nid',
            'employees.id',
            // 'employee_salarys.employee_id',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.name as title_name',
            'work_groups.id as workgroup_id',
            'work_groups.name as workgroup_name',
            DB::raw("(SELECT MAX(amount) FROM employee_salarys where employee_id = employees.id) as current_salary")
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        if ($employee_id) {
            $query->where('employees.id', $employee_id);
        }
        if ($dataa) {
            $query->whereNotIn('employees.id', $dataa);
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
        // $query->whereNotIn('employees.id', $employee_id);
        // $query->orderBy('employee_salarys.created_at', 'desc');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select(
            'employees.name',
            'employees.nid',
            'employees.id',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.name as title_name',
            'work_groups.id as workgroup_id',
            'work_groups.name as workgroup_name',
            DB::raw("(SELECT MAX(amount) FROM employee_salarys where employee_id = employees.id) as current_salary")
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        // $query->whereNotIn('employees.id', $employee_id);
        if ($employee_id) {
            $query->where('employees.id', $employee_id);
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($dataa) {
            $query->whereNotIn('employees.id', $dataa);
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
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            // $employee->current_salary = round($employee->current_salary, -3);
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
        // $employees = Employee::all();
        $sal_increase = DB::table('salary_increases');
        $sal_increase->select('salary_increases.*');
        $salaryincreases = $sal_increase->get();

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

        return view("admin.salaryincreases.index", compact('salaryincreases', 'employees', 'departments', 'workgroups', 'titles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.salaryincreases.create");
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
            'date'      => 'required',
            'value'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $salary_increases = SalaryIncreases::create([
            'ref' => '',
            'site_id' => Session::get('site_id'),
            'date' => $request->date ? dbDate($request->date) : null,
            'basic_salary' => $request->increases_type,
            'type' => $request->type,
            'value' => $request->value,
            'notes' => $request->notes,
        ]);
        if ($request->ref) {
            $salary_increases->ref = $request->ref;
            $salary_increases->save();
        } else {
            $salary_increases->ref = $salary_increases->code_system;
            $salary_increases->save();
        }
        if (!$salary_increases) {
            return response()->json([
                'status' => false,
                'message'     => 'Gagal Menyimpan Data!! Harap Periksa Kembali'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'   => route('salaryincreases.index'),
        ], 200);
    }
    public function storecheck(Request $request)
    {
        $employeeincreases = EmployeeSalary::create([
            'employee_id' => $request->ref,
            'description' => $request->date,
            'basic_salary' => $request->increases_type,
            'type' => $request->type,
            'value' => $request->value,
            'notes' => $request->notes,
        ]);
        if (!$employeeincreases) {
            return response()->json([
                'status' => false,
                'message'     => 'Gagal Menyimpan Data!! Harap Periksa Kembali'
            ], 400);
        }
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
        $salaryincreases = SalaryIncreases::find($id);
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

        if ($salaryincreases) {
            return view('admin.salaryincreases.show', compact('salaryincreases','employees', 'departments', 'workgroups', 'titles'));
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
        $salaryincreases = SalaryIncreases::find($id);
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

        if ($salaryincreases) {
            return view('admin.salaryincreases.edit', compact('salaryincreases','employees', 'departments', 'workgroups', 'titles'));
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
            'date'      => 'required',
            // 'type'      => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $salaryincreases = SalaryIncreases::find($id);
        $salaryincreases->date = $request->date;
        $salaryincreases->basic_salary = $request->increases_type;
        $salaryincreases->type = $request->type;
        $salaryincreases->value = $request->value;
        $salaryincreases->notes = $request->notes;
        $salaryincreases->save();

        if (!$salaryincreases) {
            return response()->json([
                'status' => false,
                'message' 	=> $salaryincreases
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('salaryincreases.index'),
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
            $salaryincreases = SalaryIncreases::find($id);
            $salaryincreases->delete();
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
