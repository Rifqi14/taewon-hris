<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeCareer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeCareerController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'employeecareer'));
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
        $employee_id = $request->employee_id;

        //Count Data
        $query = DB::table('employee_careers');
        $query->select('employee_careers.*');
        $query->leftJoin('employees', 'employees.id', '=', 'employee_careers.employee_id');
        $query->where('employee_careers.employee_id', '=', $employee_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_careers');
        $query->select(
            'employee_careers.*'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'employee_careers.employee_id');
        $query->where('employee_careers.employee_id', '=', $employee_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('id', 'asc');
        $roles = $query->get();
        // dd($roles);
        $data = [];
        foreach ($roles as $role) {
            $role->no = ++$start;
            $data[] = $role;
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
        $validator = Validator::make($request->all(), [
            'position'        => 'required',
            'grade'           => 'required',
            'department'      => 'required',
            'start_date'      => 'required',
            'end_date'        => 'required',
            'reference'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeecareer = EmployeeCareer::create([
            'employee_id' => $request->employee_id,
            'position'    => $request->position,
            'grade'       => $request->grade,
            'department'  => $request->department,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reference'   => $request->reference
        ]);

        if (!$employeecareer) {
            return response()->json([
                'status' => false,
                'message'   => $employeecareer
            ], 400);
        }
        return response()->json([
            'status' => true,
            'results' => 'Success Add Data!',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeCareer  $employeeCareer
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeCareer $employeeCareer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeCareer  $employeeCareer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeecareer = EmployeeCareer::find($id);
        return response()->json([
            'status' 	=> true,
            'data'      => $employeecareer
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeCareer  $employeeCareer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'position'        => 'required',
            'grade'           => 'required',
            'department'      => 'required',
            'start_date'      => 'required',
            'end_date'        => 'required',
            'reference'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        
        $employeecareer = EmployeeCareer::find($id);
        $employeecareer->employee_id = $request->employee_id;
        $employeecareer->position    = $request->position;
        $employeecareer->grade       = $request->grade;
        $employeecareer->department  = $request->department;
        $employeecareer->start_date  = $request->start_date;
        $employeecareer->end_date    = $request->end_date;
        $employeecareer->reference   = $request->reference;
        $employeecareer->save();

        if (!$employeecareer) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeecareer
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $employeecareer
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeCareer  $employeeCareer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $employeecareer = EmployeeCareer::find($id);
            $employeecareer->delete();
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
