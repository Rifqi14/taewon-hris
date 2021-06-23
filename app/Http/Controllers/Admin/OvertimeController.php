<?php

namespace App\Http\Controllers\Admin;

use App\Models\Overtime;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OvertimeController extends Controller
{
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
     * Display the specified resource.
     */
    public function read_overtime(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $montly = $request->montly;
        $year = $request->year;
        $employee_id = $request->employee_id;

        //Count Data
        $query = DB::table('overtimes');
        $query->select('overtimes.*');
        if ($montly != '') {
            $query->where('month', '=', $montly);
        }
        if ($year != '') {
            $query->where('year', '=', $year);
        }
        $query->where('employee_id',  $employee_id);
        $query->where('final_salary', '!=', 0);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('overtimes');
        $query->select('overtimes.*');
        if ($montly != '') {
            $query->where('month', '=', $montly);
        }
        if ($year != '') {
            $query->where('year', '=', $year);
        }
        $query->where('employee_id',  $employee_id);
        $query->where('final_salary', '!=', 0);
        if ($start) {
            $query->offset($start);
        }
        if ($length) {
            $query->limit($length);
        }
        $query->orderBy('date', 'asc');
        $query->orderBy('scheme_rule', 'asc');
        $overtimes = $query->get();

        $data = [];
        $grand = 0;
        foreach ($overtimes as $overtime) {
            $overtime->no = ++$start;
            $grand = $grand + $overtime->final_salary;
            $overtime->basic_salary = $overtime->basic_salary;
            $overtime->final_salary = $overtime->final_salary;
            $data[] = $overtime;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Overtime  $overtime
     * @return \Illuminate\Http\Response
     */
    public function show(Overtime $overtime)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Overtime  $overtime
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $overtime = Overtime::find($id);
        return response()->json([
            'success' => true,
            'data'     => $overtime
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Overtime  $overtime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $overtime = Overtime::find($id);
        $overtime->hour = $request->hour;
        $overtime->final_salary = $request->hour * $overtime->amount * $overtime->basic_salary;
        $overtime->save();

        $employee = Employee::where('id',$request->employee_id)->first();
        $user_id = Auth::user()->id;
        // Employee Overtimes 
        setrecordloghistory($user_id,$employee->id,$employee->department_id,"Employee Overtime","Edit",date('Y-m-d',strtotime($overtime->date))." ".$overtime->day,$request->hour);

        if (!$overtime) {
            return response()->json([
                'success' => false,
                'message'     => $overtime
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message'     => 'Status has been updated',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Overtime  $overtime
     * @return \Illuminate\Http\Response
     */
    public function destroy(Overtime $overtime)
    {
        //
    }
}