<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeExperience;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeExperienceController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'employeeexperience'));
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
        $query = DB::table('employee_experiences');
        $query->select('employee_experiences.*');
        $query->leftJoin('employees', 'employees.id', '=', 'employee_experiences.employee_id');
        $query->where('employee_experiences.employee_id', '=', $employee_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_experiences');
        $query->select(
            'employee_experiences.*'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'employee_experiences.employee_id');
        $query->where('employee_experiences.employee_id', '=', $employee_id);
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
            'last_position'  => 'required',
            'company'        => 'required',
            'start_date'     => 'required',
            'end_date'       => 'required',
            'duration'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeeexperience = EmployeeExperience::create([
            'employee_id'   => $request->employee_id,
            'last_position' => $request->last_position,
            'company'       => $request->company,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'duration'      => $request->duration
        ]);

        if (!$employeeexperience) {
            return response()->json([
                'status' => false,
                'message'   => $employeeexperience
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
     * @param  \App\EmployeeExperience  $employeeExperience
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeExperience $employeeExperience)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeExperience  $employeeExperience
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeeexperience = EmployeeExperience::find($id);
        return response()->json([
            'status' 	=> true,
            'data'      => $employeeexperience
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeExperience  $employeeExperience
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $validator = Validator::make($request->all(), [
            'last_position'  => 'required',
            'company'        => 'required',
            'start_date'     => 'required',
            'end_date'       => 'required',
            'duration'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeeexperience = EmployeeExperience::find($id);
        $employeeexperience->last_position = $request->last_position;
        $employeeexperience->company       = $request->company;
        $employeeexperience->start_date    = $request->start_date;
        $employeeexperience->end_date      = $request->end_date;
        $employeeexperience->duration      = $request->duration;
        $employeeexperience->save();

        if (!$employeeexperience) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeeexperience
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $employeeexperience
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeExperience  $employeeExperience
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $employeeexperience = EmployeeExperience::find($id);
            $employeeexperience->delete();
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
