<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WorkgroupMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class WorkgroupMasterController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'workgroupmaster'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $workgroup = strtoupper($request->name);

        //Count Data
        $query = DB::table('workgroup_masters');
        $query->select('workgroup_masters.*');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('workgroup_masters');
        $query->select('workgroup_masters.*');
        $query->offset($start);
        $query->limit($length);
        $workgroups = $query->get();

        $data = [];
        foreach ($workgroups as $workg) {
            $workg->no = ++$start;
            $data[] = $workg;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $code = strtoupper($request->code);
        $workgroup = strtoupper($request->name);

        //Count Data
        $query = DB::table('workgroup_masters');
        $query->select('workgroup_masters.*');
        $query->whereRaw("upper(workgroup_masters.name) like '%$workgroup%'");
        $query->whereRaw("upper(workgroup_masters.code) like '%$code%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('workgroup_masters');
        $query->select('workgroup_masters.*');
        $query->whereRaw("upper(workgroup_masters.name) like '%$workgroup%'");
        $query->whereRaw("upper(workgroup_masters.code) like '%$code%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $workgroups = $query->get();

        $data = [];
        foreach ($workgroups as $workg) {
            $workg->no = ++$start;
            $data[] = $workg;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.workgroupmaster.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.workgroupmaster.create');
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
            'code'     => 'required|alpha_dash|unique:workgroup_masters',
            'name'      => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $workgroup = WorkgroupMaster::create([
            'code'       => $request->code,
            'name'       => $request->name,
            'can_edit'   => 1,
            'can_delete' => 1
        ]);
        if (!$workgroup) {
            return response()->json([
                'status'    => false,
                'message'   => $workgroup
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('workgroupmaster.index')
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $workgroupmaster = WorkgroupMaster::find($id);
        if ($workgroupmaster) {
            if ($workgroupmaster->can_edit == 0) {
                abort(404);
            }
            return view('admin.workgroupmaster.edit', compact('workgroupmaster'));
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
        $validator = Validator::make($request->all(), [
            'code'     => 'required|alpha_dash|unique:workgroup_masters,code,'.$id,
            'name'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $workgroupmaster = WorkgroupMaster::find($id);
        $workgroupmaster->code = $request->code;
        $workgroupmaster->name = $request->name;
        $workgroupmaster->save();
        if (!$workgroupmaster) {
            return response()->json([
                'status' => false,
                'message'     => $workgroupmaster
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('workgroupmaster.index'),
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
            $workgroup = WorkgroupMaster::find($id);
            $workgroup->delete();
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