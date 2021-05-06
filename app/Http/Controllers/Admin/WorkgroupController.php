<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WorkGroup;
use App\Models\WorkgroupAllowance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class WorkgroupController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'workgroup'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $workgroup = strtoupper($request->name);

        //Count Data
        $query = DB::table('work_groups');
        $query->select('work_groups.*');
        $query->whereRaw("upper(work_groups.name) like '%$workgroup%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('work_groups');
        $query->select(
            'work_groups.*',
            'workgroup_masters.code'
        );
        $query->leftJoin('workgroup_masters', 'workgroup_masters.id', '=', 'work_groups.workgroupmaster_id');
        $query->whereRaw("upper(work_groups.name) like '%$workgroup%'");
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
        $workgroup = strtoupper($request->name);

        //Count Data
        $query = DB::table('work_groups');
        $query->select('work_groups.*', 'workgroup_masters.code as maser_code', 'workgroup_masters.name as master_name');
        $query->leftJoin('workgroup_masters', 'workgroup_masters.id', '=', 'work_groups.workgroupmaster_id');
        $query->whereRaw("upper(work_groups.name) like '%$workgroup%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('work_groups');
        $query->select('work_groups.*', 'workgroup_masters.code as maser_code', 'workgroup_masters.name as master_name');
        $query->leftJoin('workgroup_masters', 'workgroup_masters.id', '=', 'work_groups.workgroupmaster_id');
        $query->whereRaw("upper(work_groups.name) like '%$workgroup%'");
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
        return view('admin.workgroup.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.workgroup.create');
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
            'workgroup_id'     => 'required',
            'name'          => 'required',
            'code'          => 'required|alpha_dash|unique:work_groups'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $workgroup = WorkGroup::create([
            'workgroupmaster_id'     => $request->workgroup_id,
            'code'          => $request->code,
            'name'          => $request->name,
            'description'   => $request->description,
            'status'        => $request->status,
            'penalty'       => 'basic_salary'
        ]);
        if (!$workgroup) {
            return response()->json([
                'status'    => false,
                'message'   => $workgroup
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('workgroup.index'),
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
        $workgroup = WorkGroup::find($id);
        if ($workgroup) {
            $query = DB::table('allowances');
            $query->select('allowances.*');
            $query->orderBy('allowances.id', 'asc');
            $allowances = $query->get();
            foreach ($allowances as $allowance) {
                $workgroupAllowance = WorkgroupAllowance::where('allowance_id', $allowance->id)->where('workgroup_id', $id)->get()->first();

                if (!$workgroupAllowance) {
                    WorkgroupAllowance::create([
                        'workgroup_id'  => $id,
                        'allowance_id'  => $allowance->id,
                        'is_default'    => 0
                    ]);
                }
            }
            $workgroupAllowances = WorkgroupAllowance::select('work_group_allowances.*')->where('workgroup_id', $id)->leftJoin('work_groups', 'work_groups.id', '=', 'work_group_allowances.workgroup_id')->leftJoin('allowances', 'allowances.id', '=', 'work_group_allowances.allowance_id')->get();
            return view('admin.workgroup.edit', compact('workgroup', 'workgroupallowances'));
        } else {
            abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $workgroup = WorkGroup::with('workgroupmaster')->find($id);
        if ($workgroup) {
            $query = DB::table('allowances');
            $query->select('allowances.*');
            $query->orderBy('allowances.id', 'asc');
            $allowances = $query->get();
            foreach ($allowances as $allowance) {
                $workgroupAllowance = WorkgroupAllowance::where('allowance_id', $allowance->id)->where('workgroup_id', $id)->get()->first();

                if (!$workgroupAllowance) {
                    WorkgroupAllowance::create([
                        'workgroup_id'  => $id,
                        'allowance_id'  => $allowance->id,
                        'is_default'    => 0
                    ]);
                }
            }
            $workgroupAllowances = WorkgroupAllowance::select('work_groups.*')
                ->where('workgroup_id', $id)
                ->leftJoin('work_groups', 'work_groups.id', '=', 'workgroup_allowances.workgroup_id')
                ->leftJoin('allowances', 'allowances.id', '=', 'workgroup_allowances.allowance_id')->get();
            return view('admin.workgroup.edit', compact('workgroup'));
        } else {
            abort(404);
        }
    }

    public function update_allowances(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_default'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $workgroupAllowance = WorkgroupAllowance::find($request->id);
        $workgroupAllowance->is_default = $request->is_default;
        $workgroupAllowance->save();
        if (!$workgroupAllowance) {
            return response()->json([
                'success' => false,
                'message'     => $workgroupAllowance
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message'     => 'Default access has been updated',
        ], 200);
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
            'workgroup_id'     => 'required',
            'name'          => 'required',
            'code'          => 'required|alpha_dash'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $workgroup = WorkGroup::find($id);
        $workgroup->workgroupmaster_id = $request->workgroup_id;
        $workgroup->code               = $request->code;
        $workgroup->name               = $request->name;
        $workgroup->description        = $request->description;
        $workgroup->status             = $request->status;
        $workgroup->save();
        if (!$workgroup) {
            return response()->json([
                'status'    => false,
                'message'   => $workgroup
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('workgroup.index')
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
            $workgroup = WorkGroup::find($id);
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