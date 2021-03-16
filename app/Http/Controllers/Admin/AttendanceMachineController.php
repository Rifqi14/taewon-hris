<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AttendanceMachine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AttendanceMachineController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/attendancemachine'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.attendancemachine.index');
    }

    /**
     * Method to get all data with specific filter from given parameter and get data from attendance_machines table
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        $start          = $request->start;
        $length         = $request->length;
        $query          = $request->search['value'];
        $sort           = $request->columns[$request->order[0]['column']]['data'];
        $dir            = $request->order[0]['dir'];
        $deviceSN       = strtoupper($request->deviceSN);
        $pointName      = strtoupper($request->pointName);

        // Count Data
        $machine        = AttendanceMachine::ByDeviceSN($deviceSN);
        if ($pointName) {
            $machine->ByPointName($pointName);
        }
        $recordsTotal   = $machine->get()->count();

        // Select Pagination
        $machine        = AttendanceMachine::ByDeviceSN($deviceSN);
        if ($pointName) {
            $machine->ByPointName($pointName);
        }
        $machine->paginate($length);
        $machine->orderBy($sort, $dir);
        $machines       = $machine->get();

        $data           = [];
        foreach ($machines as $key => $machine) {
            $machine->no        = ++$start;
            $data[]             = $machine;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'sort'              => $sort,
            'data'              => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.attendancemachine.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator      = Validator::make($request->all(), [
            'deviceSN'          => 'required',
            'pointName'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'        => false,
                'message'       => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $attendanceMachine      = AttendanceMachine::create([
            'device_sn'     => strtoupper($request->deviceSN),
            'point_name'    => $request->pointName,
        ]);

        if (!$attendanceMachine) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => 'Error create data: ' . $attendanceMachine
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('attendancemachine.index'),
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
        $attendanceMachine = AttendanceMachine::find($id);
        if ($attendanceMachine) {
            return view('admin.attendancemachine.edit', compact('attendanceMachine'));
        } else {
            abort(404);
        }
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
        $validator              = Validator::make($request->all(), [
            'deviceSN'      => 'required',
            'pointName'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'        => false,
                'message'       => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $attendanceMachine  = AttendanceMachine::find($id);
        $attendanceMachine->device_sn   = strtoupper($request->deviceSN);
        $attendanceMachine->point_name  = $request->pointName;
        $attendanceMachine->save();

        if (!$attendanceMachine) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $attendanceMachine
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'        => true,
            'results'       => route('attendancemachine.index'),
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
            $attendanceMachine  = AttendanceMachine::find($id);
            $attendanceMachine->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error delete attendance machine'
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete attendance machine'
        ], 200);
    }
}