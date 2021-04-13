<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AttendanceCutOffDepartment;
use App\Models\Department;
use Illuminate\Support\Facades\DB;


class AttendanceCutOffDepartmentController extends Controller
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
    public function read(Request $request)
    {
        $start      = $request->start;
        $sort       = $request->columns[$request->order[0]['column']]['data'];
        $dir        = $request->order[0]['dir'];
        $attendancecutoff  = $request->attendance_cut_off_id;

        // Count Data
        $department = Department::with(['attendancecutoffdepartment' => function ($q) use ($attendancecutoff) {
            if ($attendancecutoff) {
                $q->ByAttendanceCutOff($attendancecutoff);
            }
        }])->Active();
        $recordsTotal = $department->get()->count();

        // Select Pagination
        $department = Department::with(['attendancecutoffdepartment' => function ($q) use ($attendancecutoff) {
            if ($attendancecutoff) {
                $q->ByAttendanceCutOff($attendancecutoff);
            }
        }]);
        $department->orderBy('path', $dir);
        $departments = $department->get();

        $data = [];
        foreach ($departments as $key => $department) {
            $department->no = ++$start;
            $department->path = str_replace("->", " <i class='fas fa-angle-right'></i>", $department->path);
            $data[] = $department;
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
        $status     = $request->status;

        DB::beginTransaction();
        if ($status == 1) {
            $attendancecutoffdepartment = AttendanceCutOffDepartment::create([
                'attendance_cut_off_id'     => $request->attendance_cut_off_id,
                'department_id'              => $request->department_id,
            ]);
            if (!$attendancecutoffdepartment) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $attendancecutoffdepartment
                ], 400);
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success add department',
            ], 200);
        } else {
            $attendancecutoffdepartment = AttendanceCutOffDepartment::ByDepartment($request->department_id)->ByAttendanceCutOff($request->attendance_cut_off_id)->first();
            if (!$attendancecutoffdepartment) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Data not found',
                ], 400);
            }
            $attendancecutoffdepartment->delete();
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success remove department',
            ], 200);
        }
    }

    public function updateAll(Request $request)
    {
        $status = $request->status;
        DB::beginTransaction();
        if ($status == 1) {
            $departments = Department::getActive();
            $deleteAll = AttendanceCutOffDepartment::ByAttendanceCutOff($request->attendance_cut_off_id)->delete();
           
            foreach ($departments as $key => $value) {
                $create = AttendanceCutOffDepartment::create([
                    'department_id'     => $value->id,
                    'attendance_cut_off_id'      => $request->attendance_cut_off_id,
                ]);
                if (!$create) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Error add data all departments'
                    ], 400);
                }
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success add data all department'
            ], 200);
        } else {
            $deleteAll = AttendanceCutOffDepartment::ByAttendanceCutOff($request->attendance_cut_off_id)->delete();
            if (!$deleteAll) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Error remove all departments',
                ], 400);
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success remove all departments'
            ], 200);
        }
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
