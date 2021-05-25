<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class LogHistoryController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'loghistory'));
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
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee = strtoupper(str_replace("'","''",$request->employee));
        $nik = $request->nik;
        $working_group = $request->working_group;
        $status = $request->status;
        $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;

        //Count Data
        $query = DB::table('log_histories');
        $query->select('log_histories.*', 'employees.name as name','departments.name as department_name','users.name as user_name');
        $query->leftJoin('employees', 'employees.id', '=', 'log_histories.employee_id');
        $query->leftJoin('departments', 'departments.id', '=', 'log_histories.department_id');
        $query->leftJoin('users', 'users.id', '=', 'log_histories.user_id');
        if ($employee) {
            $query->whereRaw("upper(employees.name) like '%$employee%'");
        }
        if ($from && $to) {
            $query->whereBetween('log_histories.date', [$from, $to]);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('log_histories');
        $query->select('log_histories.*', 'employees.name as name','departments.name as department_name','users.name as user_name');
        $query->leftJoin('employees', 'employees.id', '=', 'log_histories.employee_id');
        $query->leftJoin('departments', 'departments.id', '=', 'log_histories.department_id');
        $query->leftJoin('users', 'users.id', '=', 'log_histories.user_id');
        if ($employee) {
            $query->whereRaw("upper(employees.name) like '%$employee%'");
        }
        if ($from && $to) {
            $query->whereBetween('log_histories.date', [$from, $to]);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $attendances = $query->get();

        $data = [];
        foreach ($attendances as $attendance) {
            $attendance->no = ++$start;
            $data[] = $attendance;
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
        $query = DB::table('employees');
        $query->select('employees.name','employees.nid', 'employees.status');
        $query->where('employees.status', 1);
        $employees = $query->get();
        return view('admin.loghistory.index', compact('employees'));
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
