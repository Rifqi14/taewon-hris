<?php

namespace App\Http\Controllers\Admin;

use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CalendarException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class LeaveBalanceController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'leavebalance'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $type = strtoupper($request->leave_type);
        $balance = $request->balance;
        $description = $request->description;

        //Count Data
        $query = DB::table('leave_balances');
        $query->select('leave_balances.*');
        $query->whereRaw("upper(leave_type) like '%$type%'");
        if ($balance != '') {
            $query->where('leave_balances.balance', '=', $balance);
        }
        if ($description != '') {
            $query->where('leave_balances.description', '=', $description);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('leave_balances');
        $query->select('leave_balances.*');
        $query->whereRaw("upper(leave_type) like '%$type%'");
        if ($balance != '') {
            $query->where('leave_balances.balance', '=', $balance);
        }
        if ($description != '') {
            $query->where('leave_balances.description', '=', $description);
        }
        $query->offset($start);
        $query->limit($length);
        $balances = $query->get();

        $data = [];
        foreach ($balances as $balance) {
            $balance->no = ++$start;
            $data[] = $balance;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function getLeaveName(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;

        //Count Data
        $query = DB::table('leave_balances');
        $query->select('leave_balances.*');
        $query->where('leave_balances.id', '=', $request->id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('leave_balances');
        $query->select('leave_balances.*');
        $query->where('leave_balances.id', '=', $request->id);
        $query->offset($start);
        $query->limit($length);
        $names = $query->first();

        $data    = explode(',', $names->leave_tag);
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data,
        ], 200);
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $type = strtoupper($request->leave_type);
        $balance = $request->balance;
        $description = $request->description;

        // Count Data
        $query = DB::table('leave_balances');
        $query->select('leave_balances.*');
        $query->whereRaw("upper(leave_type) like '%$type%'");
        if ($balance != '') {
            $query->where('leave_balances.balance', '=', $balance);
        }
        if ($description != '') {
            $query->where('leave_balances.description', '=', $description);
        }
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('leave_balances');
        $query->select('leave_balances.*');
        $query->whereRaw("upper(leave_type) like '%$type%'");
        if ($balance != '') {
            $query->where('leave_balances.balance', '=', $balance);
        }
        if ($description != '') {
            $query->where('leave_balances.description', '=', $description);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $balances = $query->get();

        $data = [];
        foreach ($balances as $balance) {
            $balance->no    = ++$start;
            $data[]         = $balance;
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
        return view('admin.leavebalance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.leavebalance.create');
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
            'leave_type'    => 'required|unique:leave_balances',
            'balance'       => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $query = DB::table('leave_balances')->get();
        foreach ($query as $tagging) {
            $tagger[] = explode(',', $tagging->leave_tag);
        }

        $tags = explode(',', $request->leave_tag);
        if (!empty(array_intersect(Arr::flatten($tagger), $tags))) {
            return response()->json([
                'status'    => false,
                'message'   => 'One of leave name already use in another leave type'
            ], 400);
        }

        $leavebalance = LeaveBalance::create([
            'leave_type'    => $request->leave_type,
            'balance'       => $request->balance,
            'leave_tag'     => $request->leave_tag,
            'description'   => $request->description
        ]);
        if (!$leavebalance) {
            return response()->json([
                'status'    => false,
                'message'   => $leavebalance
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('leavebalance.index')
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Http\Response
     */
    public function show(LeaveBalance $leaveBalance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $leavebalance = LeaveBalance::find($id);
        if ($leavebalance) {
            return view('admin.leavebalance.edit', compact('leavebalance'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'leave_type'    => 'required',
            'balance'       => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $balance = LeaveBalance::find($id);
        $balance->leave_type = $request->leave_type;
        $balance->balance = $request->balance;
        $balance->leave_tag = $request->leave_tag;
        $balance->description = $request->description;
        $balance->save();
        if (!$balance) {
            return response()->json([
                'status'    => false,
                'message'   => $balance
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('leavebalance.index')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LeaveBalance  $leaveBalance
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $leavebalance = LeaveBalance::find($id);
            $leavebalance->delete();
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
