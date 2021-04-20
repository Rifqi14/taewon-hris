<?php

namespace App\Http\Controllers\Admin;

use App\SalaryReportDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SalaryReport;
use App\Models\SalaryReportDetail as ModelsSalaryReportDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class SalaryReportDetailController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'leave'));
    }

    public function read_gross(Request $request)
    {
        $start = 0;
        $length = $request->length;
        $report = $request->report_id;

        // Count Data
        $query = DB::table('salary_report_details');
        $query->select('salary_report_details.*');
        $query->where('salary_report_details.salary_report_id', '=', $report);
        $query->where('salary_report_details.type', '=', 1);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('salary_report_details');
        $query->select('salary_report_details.*');
        $query->where('salary_report_details.salary_report_id', '=', $report);
        $query->where('salary_report_details.type', '=', 1);
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
    public function employeegross(Request $request)
    {
        $start = 0;
        $length = $request->length;
        $employee = $request->employee_id;
        $month = $request->month;
        $year = $request->year;

        // dd($month, $year);
        // Count Data
        $report_id = SalaryReport::where('employee_id', $employee)->first();
        // dd($report_id);
        $query = DB::table('salary_report_details');
        $query->select('salary_report_details.*', 'salary_reports.period');
        $query->leftJoin('salary_reports', 'salary_reports.id', '=', 'salary_report_details.salary_report_id');
        $query->where('salary_reports.employee_id', '=', $employee);
        $query->where('salary_report_details.salary_report_id', '=', $report_id->id);
        $query->where('salary_report_details.type', '=', 1);
        $query->whereMonth('salary_reports.period', '=', $month);
        $query->whereYear('salary_reports.period', '=', $year);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('salary_report_details');
        $query->select('salary_report_details.*', 'salary_reports.period');
        $query->leftJoin('salary_reports', 'salary_reports.id', '=', 'salary_report_details.salary_report_id');
        $query->where('salary_reports.employee_id', '=', $employee);
        $query->where('salary_report_details.salary_report_id', '=', $report_id->id);
        $query->where('salary_report_details.type', '=', 1);
        $query->whereMonth('salary_reports.period', '=', $month);
        $query->whereYear('salary_reports.period', '=', $year);
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

    public function read_deduction(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $report = $request->report_id;

        // Count Data
        $query = DB::table('salary_report_details');
        $query->select('salary_report_details.*');
        $query->where('salary_report_details.salary_report_id', '=', $report);
        $query->where('salary_report_details.type', '=', 0);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('salary_report_details');
        $query->select('salary_report_details.*');
        $query->where('salary_report_details.salary_report_id', '=', $report);
        $query->where('salary_report_details.type', '=', 0);
        $query->offset($start);
        $query->limit($length);
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
        $query = DB::table('salary_reports');
        $query->select('salary_reports.*');
        $query->leftJoin('salary_report_details', 'salary_report_details.salary_report_id', '=', 'salary_reports.id');
        $query->where('salary_report_details.salary_report_id', '=', $id);
        $query->where('salary_report_details.type', '=', 1);
        $gross = $query->sum('salary_report_details.total');

        return $gross;
    }

    public function deduction_salary($id)
    {
        $query = DB::table('salary_reports');
        $query->select('salary_reports.*');
        $query->leftJoin('salary_report_details', 'salary_report_details.salary_report_id', '=', 'salary_reports.id');
        $query->where('salary_report_details.salary_report_id', '=', $id);
        $query->where('salary_report_details.type', '=', 0);
        $deduction = $query->sum('salary_report_details.total');

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
        $detail = ModelsSalaryReportDetail::create([
            'salary_report_id'  => $request->id,
            'employee_id'       => $request->employee,
            'description'       => $request->description,
            'total'             => str_replace('.', '', $request->total),
            'type'              => $request->type,
            'status'            => $request->add_status,
            'is_added'          => 'YES'
        ]);
        if ($detail) {
            $report = SalaryReport::find($request->id);
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
            'message'   => 'salary report generated successfully',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SalaryReportDetail  $salaryReportDetail
     * @return \Illuminate\Http\Response
     */
    public function show(SalaryReportDetail $salaryReportDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SalaryReportDetail  $salaryReportDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(SalaryReportDetail $salaryReportDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SalaryReportDetail  $salaryReportDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalaryReportDetail $salaryReportDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SalaryReportDetail  $salaryReportDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalaryReportDetail $salaryReportDetail)
    {
        //
    }
}