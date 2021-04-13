<?php

namespace App\Http\Controllers\Admin;

use App\Models\AttendanceCutOff;
use App\Models\AttendanceCutOffDepartment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;



class AttendanceCutOffController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'attendancecutoff'));
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
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('attendance_cut_offs');
        $query->select('attendance_cut_offs.*');
        if ($name) {
            $query->whereRaw("upper(name) like '$name'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('attendance_cut_offs');
        $query->select('attendance_cut_offs.*');
        if ($name) {
            $query->whereRaw("upper(name) like '$name'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $attendance_cut_offs = $query->get();

        $data = [];
        foreach ($attendance_cut_offs as $result) {
            $result->no = ++$start;
            $data[] = $result;
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
        return view('admin.attendancecutoff.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.attendancecutoff.create');
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
            'name'        => 'required',
            'option'      => 'required',
            'status'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $attendancecutoff = AttendanceCutOff::create([
            'name'          => $request->name,
            'option'        => $request->option,
            'duration'      => $request->option == 'Flexible' ?$request->duration:null,
            'hour'          => $request->option == 'Static' ? $request->hour : null,
            'status'        => $request->status,
            'description'   => $request->description
        ]);
        if ($attendancecutoff) {
            foreach ($request->department_id as $key => $department) {
                $createDepartment = AttendanceCutOffDepartment::create([
                    'attendance_cut_off_id' => $attendancecutoff->id,
                    'department_id'         => $department
                ]);
                if (!$createDepartment) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $createDepartment
                    ], 400);
                }
            }
        }

        if (!$attendancecutoff) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $attendancecutoff
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('attendancecutoff.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AttendanceCutOff  $attendanceCutOff
     * @return \Illuminate\Http\Response
     */
    public function show(AttendanceCutOff $attendanceCutOff)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AttendanceCutOff  $attendanceCutOff
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attendancecutoff = AttendanceCutOff::with(["attendancecutoffdepartment", "attendancecutoffdepartment.department"])->find($id);
        if ($attendancecutoff) {
            return view('admin.attendancecutoff.edit', compact('attendancecutoff'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AttendanceCutOff  $attendanceCutOff
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required',
            'option'       => 'required',
            'status'       => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $attendancecutoff = AttendanceCutOff::find($id);
        $attendancecutoff->name        = $request->name;
        $attendancecutoff->option      = $request->option;
        $attendancecutoff->hour        = $request->option == 'Static'?$request->hour:null;
        $attendancecutoff->duration    = $request->option == 'Flexible'?$request->duration:null;
        $attendancecutoff->status      = $request->status;
        $attendancecutoff->description = $request->description;
        $attendancecutoff->save();

        if (!$attendancecutoff) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $attendancecutoff
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('attendancecutoff.index'),
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AttendanceCutOff  $attendanceCutOff
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $attendancecutoff = AttendanceCutOff::find($id);
            $attendancecutoff->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
