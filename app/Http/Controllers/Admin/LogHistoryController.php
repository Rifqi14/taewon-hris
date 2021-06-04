<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Employee;
use App\User;
use App\Models\LogHistory;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
        $start          = $request->start;
        $length         = $request->length;
        $query          = $request->search['value'];
        $sort           = $request->columns[$request->order[0]['column']]['data'];
        $dir            = $request->order[0]['dir'];
        $employee       = $request->employee_id;
        $user_id        = $request->user_id;
        $page_id        = $request->page_id;
        $activity_id    = $request->activity_id;
        $detail_id      = $request->detail_id;
        $department_ids = $request->department_id ? $request->department_id : null;
        $from           = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to             = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;

        //Count Data
        $query = DB::table('log_histories');
        $query->select('log_histories.*', 'employees.name as name','departments.name as department_name','users.name as user_name');
        $query->leftJoin('employees', 'employees.id', '=', 'log_histories.employee_id');
        $query->leftJoin('departments', 'departments.id', '=', 'log_histories.department_id');
        $query->leftJoin('users', 'users.id', '=', 'log_histories.user_id');
        if ($employee) {
            $query->whereIn('log_histories.employee_id', $employee);
        }
        if ($user_id) {
            $query->whereIn('log_histories.user_id', $user_id);
        }
        if ($page_id) {
            $query->whereIn('log_histories.page', $page_id);
        }
        if ($activity_id) {
            $query->whereIn('log_histories.activity', $activity_id);
        }
        if ($detail_id) {
            $query->whereIn('log_histories.detail', $detail_id);
        }
        if ($department_ids) {
            $string = '';
            $uniqdepartments = [];
            foreach($department_ids as $dept){
                if(!in_array($dept,$uniqdepartments)){
                    $uniqdepartments[] = $dept;
                }
            }
            $department_ids = $uniqdepartments;
            foreach ($department_ids as $dept) {
                $string .= "departments.path like '%$dept%'";
                if (end($department_ids) != $dept) {
                $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
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
            $query->whereIn('log_histories.employee_id', $employee);
        }
        if ($user_id) {
            $query->whereIn('log_histories.user_id', $user_id);
        }
        if ($page_id) {
            $query->whereIn('log_histories.page', $page_id);
        }
        if ($activity_id) {
            $query->whereIn('log_histories.activity', $activity_id);
        }
        if ($detail_id) {
            $query->whereIn('log_histories.detail', $detail_id);
        }
        if ($department_ids) {
            $string = '';
            $uniqdepartments = [];
            foreach($department_ids as $dept){
                if(!in_array($dept,$uniqdepartments)){
                    $uniqdepartments[] = $dept;
                }
            }
            $department_ids = $uniqdepartments;
            foreach ($department_ids as $dept) {
                $string .= "departments.path like '%$dept%'";
                if (end($department_ids) != $dept) {
                $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($from && $to) {
            $query->whereBetween('log_histories.date', [$from, $to]);
        }
        $query->offset($start);
        $query->limit($length);
        // $query->orderBy($sort, $dir);
        $query->orderBy('log_histories.id', 'desc');
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
        $emp = DB::table('employees');
        $emp->select('employees.*');
        $emp->where('status', 1);
        $employees = $emp->get();

        $query = DB::table('departments');
        $query->select('departments.*');
        $query->orderBy('path','asc');
        $departments = $query->get();

        $users = User::all();
        $pages = DB::table('log_histories')
                 ->select('page')
                 ->groupBy('page')
                 ->get();
        $activitys = DB::table('log_histories')
                 ->select('activity')
                 ->groupBy('activity')
                 ->get();
        $details = DB::table('log_histories')
                 ->select('detail')
                 ->groupBy('detail')
                 ->get();
        return view('admin.loghistory.index', compact('employees','users','pages','departments','activitys','details'));
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
