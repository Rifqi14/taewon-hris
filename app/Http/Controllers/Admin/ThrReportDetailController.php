<?php

namespace App\Http\Controllers\Admin;

use App\Models\ThrReportDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ThrReportDetailController extends Controller
{
    public function read(Request $request)
    {
        $start = 0;
        $length = $request->length;
        $report = $request->report_id;

        // Count Data
        $query = DB::table('thr_report_details');
        $query->select('thr_report_details.*');
        $query->where('thr_report_details.thr_report_id', '=', $report);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('thr_report_details');
        $query->select('thr_report_details.*');
        $query->where('thr_report_details.thr_report_id', '=', $report);
        $query->orderBy('created_at', 'asc');
        $details = $query->get();

        $data = [];
        foreach ($details as $detail) {
            $detail->no     = ++$start;
            $detail->total  = $detail->total;
            $data[]         = $detail;
        }
        return response()->json([
            'draw'              => $request->draw,
            'data'              => $data
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ThrReportDetail  $thrReportDetail
     * @return \Illuminate\Http\Response
     */
    public function show(ThrReportDetail $thrReportDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ThrReportDetail  $thrReportDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(ThrReportDetail $thrReportDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ThrReportDetail  $thrReportDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ThrReportDetail $thrReportDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ThrReportDetail  $thrReportDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(ThrReportDetail $thrReportDetail)
    {
        //
    }
}
