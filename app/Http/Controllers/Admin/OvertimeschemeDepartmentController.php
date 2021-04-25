<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\OvertimeschemeDepartment;
use Illuminate\Support\Facades\DB;

class OvertimeschemeDepartmentController extends Controller
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
        $length     = $request->length;
        $query      = $request->search['value'];
        $sort       = $request->columns[$request->order[0]['column']]['data'];
        $dir        = $request->order[0]['dir'];
        $overtimescheme   = $request->overtime_scheme_id;

        // Count Data
        $department = Department::with(['departmentovertimescheme' => function ($q) use ($overtimescheme) {
            if ($overtimescheme) {
                $q->ByOvertimeScheme($overtimescheme);
            }
        }])->Active();
        $recordsTotal = $department->get()->count();

        // SelectPagination
        $department = Department::with(['departmentovertimescheme' => function ($q) use ($overtimescheme) {
            if ($overtimescheme) {
                $q->ByOvertimeScheme($overtimescheme);
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
            $department = OvertimeschemeDepartment::create([
                'department_id'     => $request->department_id,
                'overtime_scheme_id'    => $request->overtime_scheme_id,
            ]);
            if (!$department) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $department
                ], 400);
            } else {
                DB::commit();
                return response()->json([
                    'status'    => true,
                    'message'   => 'Success add department',
                ], 200);
            }
        } else {
            $department = OvertimeschemeDepartment::where('department_id', $request->department_id)->where('overtime_scheme_id', $request->overtime_scheme_id)->first();
            if ($department) {
                $department->delete();
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

    public function updateAll(Request $request)
    {
        $status = $request->status;
        DB::beginTransaction();
        if ($status == 1) {
            $departments = Department::getActive();
            $deleteAll = OvertimeschemeDepartment::ByOvertimeScheme($request->overtime_scheme_id)->delete();
            foreach ($departments as $key => $value) {
                $create = OvertimeschemeDepartment::create([
                    'department_id'     => $value->id,
                    'overtime_scheme_id'    => $request->overtime_scheme_id,
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
            $deleteAll = OvertimeschemeDepartment::ByOvertimeScheme($request->overtime_scheme_id)->delete();
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
