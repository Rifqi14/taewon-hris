<?php

namespace App\Http\Controllers\Admin;

use App\Models\SalaryDeduction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class SalaryDeductionController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'salarydeduction'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $description = strtoupper($request->description);

        //Count Data
        $query = DB::table('salary_deductions');
        $query->select('salary_deductions.*', 'employees.name');
        $query->leftJoin('employees', 'employees.id', '=', 'salary_deductions.employee_id');
        $query->whereRaw("upper(salary_deductions.description) like '%$description%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('salary_deductions');
        $query->select('salary_deductions.*', 'employees.name');
        $query->leftJoin('employees', 'employees.id', '=', 'salary_deductions.employee_id');
        $query->whereRaw("upper(salary_deductions.description) like '%$description%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $salarydeductions = $query->get();

        $data = [];
        foreach ($salarydeductions as $salarydeduction) {
            $salarydeduction->no = ++$start;
            // $employeesalary->amount = round($employeesalary->amount, -3);
            $data[] = $salarydeduction;
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
        return view('admin.salarydeduction.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.salarydeduction.create');
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
            'employee_id' => 'required',
            'date'        => 'required',
            'nominal'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $salarydeduction = SalaryDeduction::create([
            'employee_id' => $request->employee_id,
            'date'        => date('Y-m-d', strtotime($request->date)),
            'nominal'     => $request->nominal,
            'description' => $request->description
        ]);

        if (!$salarydeduction) {
            return response()->json([
                'status' => false,
                'message'   => $salarydeduction
            ], 400);
        }

        return response()->json([
            'status'     => true,
            'results'     => route('salarydeduction.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SalaryDeduction  $salaryDeduction
     * @return \Illuminate\Http\Response
     */
    public function show(SalaryDeduction $salaryDeduction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SalaryDeduction  $salaryDeduction
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $salarydeduction = SalaryDeduction::find($id);

        return view('admin.salarydeduction.edit', compact('salarydeduction'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SalaryDeduction  $salaryDeduction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required',
            'date'        => 'required',
            'nominal'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        
        $salarydeduction = SalaryDeduction::find($id);
        $salarydeduction->employee_id = $request->employee_id;
        $salarydeduction->date        = $request->date;
        $salarydeduction->nominal     = $request->nominal;
        $salarydeduction->description = $request->description;
        $salarydeduction->save();

        if (!$salarydeduction) {
            return response()->json([
                'status' => false,
                'message'     => $salarydeduction
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('salarydeduction.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SalaryDeduction  $salaryDeduction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $salarydeduction = SalaryDeduction::find($id);
            $salarydeduction->delete();
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
