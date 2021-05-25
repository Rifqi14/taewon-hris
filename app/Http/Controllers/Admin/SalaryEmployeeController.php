<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeSalary;
use App\Models\OutsourcingDocument;
use Illuminate\Http\Request;
use App\Models\LogHistory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class SalaryEmployeeController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'employee'));
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $amount = strtoupper($request->amount);

        //Count Data
        $query = DB::table('employee_salarys');
        $query->select('employee_salarys.*');
        $query->where('employee_id', '=', $request->employee_id);
        $query->whereRaw("upper(employee_salarys.amount) like '%$amount%'");
        $recordsTotal = $query->count(); 

        //Select Pagination
        $query = DB::table('employee_salarys');
        $query->select('employee_salarys.*','users.name as user_name');
        $query->leftJoin('users', 'users.id', '=', 'employee_salarys.user_id');
        $query->where('employee_id', '=', $request->employee_id);
        $query->whereRaw("upper(employee_salarys.amount) like '%$amount%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employeesalarys = $query->get();

        $data = [];
        foreach($employeesalarys as $employeesalary){
            $employeesalary->no = ++$start;
            // $employeesalary->amount = round($employeesalary->amount, -3);
            $data[] = $employeesalary;
        }
        return response()->json([
            'draw'=>$request->draw,
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsTotal,
            'data'=>$data
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
            'user_id' => 'required',
            'amount'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employee = Employee::where('id',$request->employee_id)->first();
        $user_id = Auth::user()->id;
        // Basic Salary Amount
        setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Salary","Add","Basic Salary Amount",$request->amount);

        $salaryemployee = EmployeeSalary::create([
            'user_id' => Auth::guard('admin')->user()->id,
            'employee_id'   => $request->employee_id,
            'description' => $request->description,
            'amount' => $request->amount
        ]);

        if (!$salaryemployee) {
            return response()->json([
                'status' => false,
                'message'   => $salaryemployee
            ], 400);
        }

        return response()->json([
            'status' => true,
            'results' => 'Success add data',
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
        $salaryemployee = EmployeeSalary::find($id);
        return response()->json([
            'status' 	=> true,
            'data'      => $salaryemployee
        ], 200);
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
            'outsourcing_id' 	=> 'required',
            'phone'        => 'required',
            'name'          => 'required',
            'category'      => 'required',
            
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $outsourcingdocument = OutsourcingDocument::find($id);
        $outsourcingdocument->outsourcing_id = $request->outsourcing_id;
        $outsourcingdocument->phone      = $request->phone;
        $outsourcingdocument->name        = $request->name;
        $outsourcingdocument->category    = $request->category;
        $outsourcingdocument->description = $request->description;
        $outsourcingdocument->file        = 'data-foto.jpg';
        $outsourcingdocument->save();

        $file = $request->file('file');
        if($file){  
            $filename = 'foto.'. $request->file->getClientOriginalExtension(); 
            if(file_exists($outsourcingdocument->file)){
                unlink($outsourcingdocument->file);
            } 
            
            $src = 'assets/ousourcing/document/'.$outsourcingdocument->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $file->move($src,$filename);
            $outsourcingdocument->file = $src.'/'.$filename;
            $outsourcingdocument->save();
        }
        if (!$outsourcingdocument) {
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcingdocument
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $outsourcingdocument
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
            $salaryemployee = EmployeeSalary::find($id); 
            $salaryemployee->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     =>  'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
