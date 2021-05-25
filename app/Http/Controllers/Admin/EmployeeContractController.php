<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeContract;
use Illuminate\Http\Request;
use App\Models\LogHistory;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeContractController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'employeecontract'));
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
        $query = DB::table('employee_contracts');
        $query->select('employee_contracts.*');
        $query->leftJoin('employees', 'employees.id', '=', 'employee_contracts.employee_id');
        $query->where('employee_contracts.employee_id', '=', $employee_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_contracts');
        $query->select(
            'employee_contracts.*'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'employee_contracts.employee_id');
        $query->where('employee_contracts.employee_id', '=', $employee_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('id', 'asc');
        $contracts = $query->get();
        // dd($contracts);
        $data = [];
        foreach ($contracts as $contract) {
            $contract->no = ++$start;
            $contract->link = url($contract->file);
            $data[] = $contract;
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
            'code'        => 'required',
            'start_date'  => 'required',
            'end_date'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employee = Employee::where('id',$request->employee_id)->first();
        $user_id = Auth::user()->id;
        // No. Document
        setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Contract","Create","No. Document",$request->code);

        // Start Date
        setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Contract","Create","Start Date",$request->start_date);

        // End Date
        setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Contract","Create","End Date",$request->end_date);

        // Status
        $foo = $request->status;
        $status_desc = ($foo == 0) ? "Non Active" : (($foo == 1)  ? "Active" : "Expired");
        setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Contract","Create","Status",$status_desc);

        $employeecontract = EmployeeContract::create([
            'employee_id' => $request->employee_id,
            'code'        => $request->code,
            'start_date'  => $request->start_date ? dbDate($request->start_date) : null,
            'end_date'    => $request->end_date ? dbDate($request->end_date) : null,
            'status'      => $request->status,
            'description' => $request->description,
            'file'        => ''
        ]);
        $file = $request->file('file');
        if ($file) {
            $filename = 'contract.' . $request->file->getClientOriginalExtension();
            $src = 'assets/employee/contract/' . $employeecontract->id;
            if (!file_exists($src)) {
                mkdir($src, 0777, true);
            }
            $file->move($src, $filename);
            $employeecontract->file = $src . '/' . $filename;
            $employeecontract->save();
        }

        if (!$employeecontract) {
            return response()->json([
                'status' => false,
                'message'   => $employeecontract
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
     * @param  \App\EmployeeContract  $employeeContract
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeContract $employeeContract)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeContract  $employeeContract
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeecontract = EmployeeContract::find($id);
        $employeecontract->link = url($employeecontract->file);
        // $contract = [];
        // foreach ($employeecontract as $empcontract) {
            $employeecontract->start_date = date('d/m/Y',strtotime($employeecontract->start_date));
            $employeecontract->end_date = date('d/m/Y',strtotime($employeecontract->end_date));
        //     $contract[] = $empcontract;
        // }
        return response()->json([
            'status' 	=> true,
            'data'      => $employeecontract
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeContract  $employeeContract
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'code'        => 'required',
            'start_date'  => 'required',
            'end_date'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeeCont = EmployeeContract::where('id',$id)->first();
        $user_id = Auth::user()->id;
        // No. Document
        if($employeeCont->code != $request->code){
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Contract","Edit","No. Document",$request->code);
        }

        // Start Date
        if($employeeCont->start_date != date("Y-m-d",strtotime($request->start_date))){
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Contract","Edit","Start Date",$request->start_date);
        }

        // End Date
        if($employeeCont->end_date != date("Y-m-d",strtotime($request->end_date))){
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Contract","Edit","End Date",$request->end_date);
        }

        // Status
        if($employeeCont->status != $request->status){
            $foo = $request->status;
            $status_desc = ($foo == 0) ? "Non Active" : (($foo == 1)  ? "Active" : "Expired");
            setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Contract","Edit","Status",$status_desc);
        }

        $employeecontract = EmployeeContract::find($id);
        $employeecontract->code        = $request->code;
        $employeecontract->start_date  = $request->start_date ? dbDate($request->start_date) : null;
        $employeecontract->end_date    = $request->end_date ? dbDate($request->end_date) : null;
        $employeecontract->status      = $request->status;
        $employeecontract->description = $request->description;
        $employeecontract->save();

        $file = $request->file('file');
        if ($file) {
            $filename = 'contract.' . $request->file->getClientOriginalExtension();
            if (file_exists($employeecontract->file)) {
                unlink($employeecontract->file);
            }

            $src = 'assets/employee/contract/' . $employeecontract->id;
            if (!file_exists($src)) {
                mkdir($src, 0777, true);
            }
            $file->move($src, $filename);
            $employeecontract->file = $src . '/' . $filename;
            $employeecontract->save();
        }

        if (!$employeecontract) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeecontract
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $employeecontract
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeContract  $employeeContract
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $employeecontract = EmployeeContract::find($id);
            if (file_exists($employeecontract->file)) {
                unlink($employeecontract->file);
            }
            $employeecontract->delete();
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
