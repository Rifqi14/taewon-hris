<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeTraining;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeTrainingController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'employeetraining'));
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $employee_id = $request->employee_id;

        //Count Data
        $query = DB::table('employee_trainings');
        $query->select('employee_trainings.*');
        $query->leftJoin('employees', 'employees.id', '=', 'employee_trainings.employee_id');
        $query->where('employee_trainings.employee_id', '=', $employee_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_trainings');
        $query->select(
            'employee_trainings.*'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'employee_trainings.employee_id');
        $query->where('employee_trainings.employee_id', '=', $employee_id);
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            'code'           => 'required',
            'issued'         => 'required',
            'start_date'     => 'required',
            'end_date'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeetraining = EmployeeTraining::create([
            'employee_id'      => $request->employee_id,
            'code'             => $request->code,
            'issued'           => $request->issued,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'description'      => $request->description
        ]);

        if (!$employeetraining) {
            return response()->json([
                'status' => false,
                'message'   => $employeetraining
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
     * @param  \App\EmployeeTraining  $employeeTraining
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeTraining $employeeTraining)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeTraining  $employeeTraining
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeetraining= EmployeeTraining::find($id);
        return response()->json([
            'status' 	=> true,
            'data'      => $employeetraining
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeTraining  $employeeTraining
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $validator = Validator::make($request->all(), [
            'code'           => 'required',
            'issued'         => 'required',
            'start_date'     => 'required',
            'end_date'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeetraining = EmployeeTraining::find($id);
        $employeetraining->code          = $request->code;
        $employeetraining->issued        = $request->issued;
        $employeetraining->start_date    = $request->start_date;
        $employeetraining->end_date      = $request->end_date;
        $employeetraining->description   = $request->description;
        $employeetraining->save();

        if (!$employeetraining) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeetraining
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $employeetraining
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeTraining  $employeeTraining
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $employeetraining = EmployeeTraining::find($id);
            $employeetraining->delete();
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
