<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeMovement;
use App\Models\SiteUser;
use App\Role;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class EmployeeController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'employee'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.employee.index');
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $department_name = strtoupper($request->department_name);
        $title_name = strtoupper($request->title_name);
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->whereRaw("upper(departments.name) like '%$department_name%'");
        $query->whereRaw("upper(titles.name) like '%$title_name%'");
        $query->whereRaw("upper(employees.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.name as title_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->whereRaw("upper(departments.name) like '%$department_name%'");
        $query->whereRaw("upper(titles.name) like '%$title_name%'");
        $query->whereRaw("upper(employees.name) like '%$name%'");
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

    public function select(Request $request){
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $department_id = $request->department_id;
        $title_id = $request->title_id;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employees.id as employee_id',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.id as title_id',
            'titles.name as title_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if ($department_id) {
            $query->where('department_id', '=', $department_id);
        }
        if ($title_id) {
            $query->where('title_id', '=', $title_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employees.id as employee_id',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.id as title_id',
            'titles.name as title_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if ($department_id) {
            $query->where('department_id', '=', $department_id);
        }
        $query->offset($start*$length);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.employee.create');
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
            'nik' => 'required',
            'name'      => 'required',
            'title_id' => 'required',
            'department_id'      => 'required',
            'workgroup_combination' => 'required',
            'grade_id'      => 'required',
            'nid'      => 'required',
            'npwp'      => 'required',
            'birth_date'      => 'required',
            'gender'      => 'required',
            'phone'      => 'required',
            'email'      => 'required',
            'address'      => 'required',
            'place_of_birth'      => 'required',
            'province_id'      => 'required',
            'account_bank'      => 'required',
            'account_no'      => 'required',
            'account_name'      => 'required',
            'emergency_contact_no'      => 'required',
            'emergency_contact_name'      => 'required',
            'working_time_type'      => 'required',
            'working_time'      => 'required',
            'status'      => 'required',
            'notes'      => 'required',
            'join'      => 'required',
            'join_date'      => 'required',
            'bpjs_tenaga_kerja'      => 'required',
            'tax_calculation'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $employee = Employee::create([
            'name' => $request->name,
            'nik' => $request->nik,
            'title_id' => $request->title_id,
            'department_id' => $request->department_id,
            'workgroup_combination' => $request->workgroup_combination,
            'grade_id' => $request->grade_id,
            'nid' => $request->nid,
            'npwp' => $request->npwp,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'place_of_birth' => $request->place_of_birth,
            'province_id' => $request->province_id,
            'account_bank' => $request->account_bank,
            'account_no' => $request->account_no,
            'photo' => '',
            'account_name' => $request->account_name,
            'emergency_contact_no' => $request->emergency_contact_no,
            'emergency_contact_name' => $request->emergency_contact_name,
            'working_time_type' => $request->working_time_type,
            'working_time' => $request->working_time,
            'status' => $request->status,
            'notes' => $request->notes,
            'join' => $request->join,
            'outsourcing' => $request->outsourcing,
            'calendar_id' => $request->calendar_id,
            'join_date' => $request->join_date,
            'resign_date' => $request->resign_date,
            'bpjs_tenaga_kerja' => $request->bpjs_tenaga_kerja,
            'ptkp' => $request->ptkp,
            'tax_calculation' => $request->tax_calculation,
        ]);

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message'   => $employee
            ], 400);
        }

        $photo = $request->file('photo');
        if ($photo) {
            $path = 'assets/employee/';
            $photo->move($path, $employee->nid . '.' . $photo->getClientOriginalExtension());
            $filename = $path . $employee->nid . '.' . $photo->getClientOriginalExtension();
            $employee->photo = $filename ? $filename : '';
            $employee->save();
        }

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
        $type = [
            'permanent'   => 'Pegawai Tetap',
            'internship'  => 'Magang'
        ];
        $employee = Employee::with('region')
                            ->select('employees.*','employee_movements.title_id','titles.name as title_name')
                            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                            ->whereNull('finish')
                            ->find($id);
        if($employee){
            $employee->type = $type[$employee->type];
            return view('admin.employee.detail',compact('employee'));
        }
        else{
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
        $employee = Employee::with('region')
                            ->select('employees.*','employee_movements.title_id','titles.name as title_name')
                            ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                            ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                            ->whereNull('finish')
                            ->find($id);
        if($employee){
            return view('admin.employee.edit',compact('employee'));
        }
        else{
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
            'title_id'       => 'required',
            'nid' 	         => 'required',
            'name'           => 'required',
            'type'           => 'required',
            'gender'         => 'required',
            'place_of_birth' => 'required',
            'birth_date'     => 'required',
            'phone'          => 'required',
            'address'        => 'required',
            'latitude'       => 'required',
            'longitude'      => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        DB::beginTransaction();
        $employee = Employee::with('region')
                    ->select('employees.*','employee_movements.title_id','titles.name as title_name')
                    ->leftJoin('employee_movements','employee_movements.employee_id','=','employees.id')
                    ->leftJoin('titles','titles.id','=','employee_movements.title_id')
                    ->whereNull('finish')
                    ->find($id);
        if($employee->nid != $request->nid){
            $user = User::where('username',$employee->nid)->first();
            $user->username = $request->nid;
            $user->save();
            if (!$user) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' 	=> $user
                ], 400);
            }
        }
        if($employee->title_id != $request->title_id){
            $employeemovement = EmployeeMovement::where('employee_id',$employee->id)
                                                ->where('title_id',$employee->title_id)
                                                ->whereNull('finish')
                                                ->first();
            $employeemovement->finish = date('Y-m-d H:i:s');
            $employeemovement->save();
            if (!$employeemovement) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' 	=> $employeemovement
                ], 400);
            }
            $employeemovement = EmployeeMovement::create([
                'employee_id' => $employee->id,
                'title_id'    => $request->title_id,
                'start'       => date('Y-m-d H:i:s'),
                'finish'      => null,
                'reason'      => 'Manual Update Jabatan',
                'status'      => 1
            ]);
            if (!$employeemovement) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message' 	=> $employeemovement
                ], 400);
            }
        }
        $employee->nid            = $request->nid;
        $employee->name           = $request->name;
        $employee->type           = $request->type;
        $employee->gender         = $request->gender;
        $employee->place_of_birth = $request->place_of_birth;
        $employee->birth_date     = $request->birth_date;
        $employee->phone          = $request->phone;
        $employee->address        = $request->address;
        $employee->latitude       = $request->latitude;
        $employee->longitude      = $request->longitude;
        $employee->save();
        if (!$employee) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $employee
            ], 400);
        }
        DB::commit();
        // return response()->json([
        // 	'status' 	=> true,
        // 	'results' 	=> route('employee.index'),
        // ], 200);
        // return view('employee.index');
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
            $title = Employee::find($id);
            $title->delete();
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
}
