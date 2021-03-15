<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentShift;
use Illuminate\Support\Facades\DB;

class DepartmentShiftController extends Controller
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

    /**
     * Method to get data from department table and populate to blade datatable
     *
     * @param Request $request
     * @return void
     */
    public function read(Request $request)
    {
        $start      = $request->start;
        $length     = $request->length;
        $query      = $request->search['value'];
        $sort       = $request->columns[$request->order[0]['column']]['data'];
        $dir        = $request->order[0]['dir'];
        $shift      = $request->workingtime_id;

        // Count Data
        $department = Department::with(['departmentshift' => function ($q) use ($shift) {
            if ($shift) {
                $q->ByShift($shift);
            }
        }])->Active();
        $recordsTotal = $department->get()->count();

        // SelectPagination
        $department = Department::with(['departmentshift' => function ($q) use ($shift) {
            if ($shift) {
                $q->ByShift($shift);
            }
        }])->Active();
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
        $status = $request->status;

        DB::beginTransaction();
        if ($status == 1) {
            $departmentShift = DepartmentShift::create([
                'department_id'     => $request->department_id,
                'workingtime_id'    => $request->workingtime_id,
            ]);
            if (!$departmentShift) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $departmentShift
                ], 400);
            } else {
                DB::commit();
                return response()->json([
                    'status'    => true,
                    'message'   => 'Success add department',
                ], 200);
            }
        } else {
            $departmentShift = DepartmentShift::where('department_id', $request->department_id)->where('workingtime_id', $request->workingtime_id)->first();
            if ($departmentShift) {
                $departmentShift->delete();
                DB::commit();
                return response()->json([
                    'status'    => true,
                    'message'   => 'Success remove department',
                ], 200);
            } else {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Data not found',
                ], 400);
            }
        }
    }

    /**
     * Method to add or assign all data to department shift
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
        $status = $request->status;
        DB::beginTransaction();
        if ($status == 1) {
            $departments = Department::getActive();
            $deleteAll = DepartmentShift::ByShift($request->workingtime_id)->delete();
            foreach ($departments as $key => $value) {
                $create = DepartmentShift::create([
                    'department_id'     => $value->id,
                    'workingtime_id'    => $request->workingtime_id,
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
                'message'   => 'Success add data all departments'
            ], 200);
        } else {
            $deleteAll = DepartmentShift::ByShift($request->workingtime_id)->delete();
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
                'message'   => 'Success remove all departemnts'
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