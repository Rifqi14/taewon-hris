<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeEducation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeEducationController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'employeeeducation'));
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
        $query = DB::table('employee_education');
        $query->select('employee_education.*');
        $query->leftJoin('employees', 'employees.id', '=', 'employee_education.employee_id');
        $query->where('employee_education.employee_id', '=', $employee_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_education');
        $query->select(
            'employee_education.*'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'employee_education.employee_id');
        $query->where('employee_education.employee_id', '=', $employee_id);
        $query->offset($start);
        $query->limit($length);
        // $query->orderBy('id', 'asc');
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
            'institution'  => 'required',
            'stage'        => 'required',
            'period'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeeeducation = EmployeeEducation::create([
            'employee_id'   => $request->employee_id,
            'institution'   => $request->institution,
            'stage'         => $request->stage,
            'period'        => $request->period
        ]);

        if (!$employeeeducation) {
            return response()->json([
                'status' => false,
                'message'   => $employeeeducation
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
     * @param  \App\EmployeeEducation  $employeeEducation
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeEducation $employeeEducation)
    {
        // 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeEducation  $employeeEducation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeeeducation = EmployeeEducation::find($id);
        return response()->json([
            'status' 	=> true,
            'data'      => $employeeeducation
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeEducation  $employeeEducation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'institution'  => 'required',
            'stage'        => 'required',
            'period'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeeeducation = EmployeeEducation::find($id);
        $employeeeducation->employee_id = $request->employee_id;
        $employeeeducation->institution = $request->institution;
        $employeeeducation->stage       = $request->stage;
        $employeeeducation->period      = $request->period;
        $employeeeducation->save();

        if (!$employeeeducation) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeeeducation
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $employeeeducation
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeEducation  $employeeEducation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $employeeeducation = EmployeeEducation::find($id);
            $employeeeducation->delete();
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
