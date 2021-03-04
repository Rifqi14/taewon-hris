<?php

namespace App\Http\Controllers\Admin;

use App\PphReportDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PphReport;
use App\Models\PphReportDetail as ModelsPphReportDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class PphDetailController extends Controller 
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'pphreportdetail'));
    }
    public function read_gross(Request $request)
    {
        $start = 0;
        $length = $request->length;
        $report = $request->report_id;

        // Count Data
        $query = DB::table('pph_report_details');
        $query->select('pph_report_details.*');
        $query->where('pph_report_details.pph_report_id', '=', $report);
        // $query->where('pph_report_details.type', '=', 1);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('pph_report_details');
        $query->select('pph_report_details.*');
        $query->where('pph_report_details.pph_report_id', '=', $report);
        // $query->where('pph_report_details.type', '=', 1);
        $query->orderBy('created_at', 'asc');
        $details = $query->get();

        $data = [];
        $pengurangan = true;
        $pphmonthly = true;
        $pphyearly = true;
        foreach ($details as $detail) {
            if($detail->type == 0 && $pengurangan && $detail->description == "Pengurangan"){
                $pengurangan = false;
                $as = new \stdClass;
                $as->no     = ++$start;
                $as->description = 'Pengurangan';
                $as->total = -1; 
                $as->type = 1;
                $data[]         = $as;   
            }
            if($detail->type == 0){
                $detail->no     = '';    
            }else{
                $detail->no     = ++$start;
            }
            if($detail->type == 0 && $pphmonthly && $detail->description == "PPh 21 (Monthly)"){
                $pphmonthly = false;
                $as = new \stdClass;
                $as->no     = ++$start;
                $as->description = 'PPh 21 (Monthly)';
                $as->total = -1; 
                $as->type = 1;
                $data[]         = $as;   
            }
            if($detail->type == 0){
                $detail->no     = '';    
            }else{
                $detail->no     = ++$start;
            }
            if($detail->type == 0 && $pphyearly && $detail->description == "PPh 21 (Yearly)"){
                $pphyearly = false;
                $as = new \stdClass;
                $as->no     = ++$start;
                $as->description = 'PPh 21 (Yearly)';
                $as->total = -1; 
                $as->type = 1;
                $data[]         = $as;   
            }
            if($detail->type == 0){
                $detail->no     = '';    
            }else{
                $detail->no     = ++$start;
            }
            
            $detail->total  = number_format($detail->total, 2, ',', '.');
            $data[]         = $detail;
        }
            // $ptkp = new \stdClass;
            // $ptkp->no     = ++$start;
            // $ptkp->description = 'PTKP';
            // $ptkp->total = -1;
            // $data[] = $ptkp;
            // $pkp = new \stdClass;
            // $pkp->no     = ++$start;
            // $pkp->description = 'PKP (Yearly)';
            // // $pkp->total = -1;
            // $data[] = $pkp;
            // $pph = new \stdClass;
            // $pph->no     = ++$start;
            // $pph->description = 'PPh 21 (Yearly)';
            // $pph->total = -1;
            // $data[] = $pph;
            // $pphmontly = new \stdClass;
            // $pphmontly->no     = ++$start;
            // $pphmontly->description = 'PPh 21 (Monthly)';
            // $pphmontly->total = -1;
            // $data[] = $pphmontly;
        return response()->json([
            'draw'              => $request->draw,
            'data'              => $data
        ], 200);
    }

    public function read_deduction(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $report = $request->report_id;

        // Count Data
        $query = DB::table('pph_report_details');
        $query->select('pph_report_details.*');
        $query->where('pph_report_details.pph_report_id', '=', $report);
        $query->where('pph_report_details.type', '=', 0);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('pph_report_details');
        $query->select('pph_report_details.*');
        $query->where('pph_report_details.pph_report_id', '=', $report);
        $query->where('pph_report_details.type', '=', 0);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('created_at', 'asc');
        $details = $query->get();

        $data = [];
        foreach ($details as $detail) {
            $detail->no     = ++$start;
            $detail->total  = number_format($detail->total, 2, ',', '.');
            $data[]         = $detail;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
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
    public function gross_salary($id)
    {
        $query = DB::table('pph_reports');
        $query->select('pph_reports.*');
        $query->leftJoin('pph_report_details', 'pph_report_details.pph_report_id', '=', 'pph_reports.id');
        $query->where('pph_report_details.pph_report_id', '=', $id);
        $query->where('pph_report_details.type', '=', 1);
        $gross = $query->sum('pph_report_details.total');

        return $gross;
    }

    public function deduction_salary($id)
    {
        $query = DB::table('pph_reports');
        $query->select('pph_reports.*');
        $query->leftJoin('pph_report_details', 'pph_report_details.pph_report_id', '=', 'pph_reports.id');
        $query->where('pph_report_details.pph_report_id', '=', $id);
        $query->where('pph_report_details.type', '=', 0);
        $deduction = $query->sum('pph_report_details.total');

        return $deduction;
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
            'description'   => 'required',
            'total'         => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $detail = ModelsPphReportDetail::create([
            'pph_report_id'  => $request->id,
            'employee_id'       => $request->employee,
            'description'       => $request->description,
            'total'             => str_replace('.', '', $request->total),
            'type'              => $request->type,
            'status'            => $request->add_status
        ]);
        if ($detail) {
            $report = PphReport::find($request->id);
            $report->gross_salary = $this->gross_salary($request->id) ? $this->gross_salary($request->id) : 0;
            $report->deduction = $this->deduction_salary($request->id) ? $this->deduction_salary($request->id) : 0;
            $report->net_salary = $report->gross_salary - $report->deduction;
            $report->save();
            if (!$report) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $report
                ], 400);
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $detail
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'message'   => 'PPh report generated successfully',
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
