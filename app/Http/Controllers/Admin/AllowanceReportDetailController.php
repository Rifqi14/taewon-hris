<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AllowanceReportDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $report_id = $request->report_id;

        // Count Data
        $query = DB::table('allowance_report_details');
        $query->select('allowance_report_details.*');
        $query->where('allowance_report_details.allowance_report_id', '=', $report_id);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('allowance_report_details');
        $query->select('allowance_report_details.*');
        $query->where('allowance_report_details.allowance_report_id', '=', $report_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('id', 'asc');
        $details = $query->get();
        // dd($details);    
        $data = [];
        foreach ($details as $detail) {
            $detail->no     = ++$start;
            // $detail->total  = $detail->total;
            $data[]         = $detail;
        }
        return response()->json([
            'draw'              => $request->draw,
            'data'              => $data
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
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
