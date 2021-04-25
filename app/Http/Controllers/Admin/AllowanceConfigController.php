<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllowanceConfig;
use App\Models\AllowanceConfigDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;


class AllowanceConfigController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'allowaceconfig'));
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
        $query = DB::table('allowance_configs');
        $query->select('allowance_configs.*', 'work_groups.name as workgroup_name', 'allowances.allowance as allowance_name' );
        $query->leftJoin('workgroup_allowances as workgroup', 'workgroup.id', '=', 'allowance_configs.workgroup_id');
        $query->leftJoin('workgroup_allowances as allowance', 'allowance.id', '=', 'allowance_configs.allowance_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'workgroup.workgroup_id');
        $query->leftJoin('allowances', 'allowances.id', '=', 'allowance.allowance_id');
        if ($name) {
            $query->whereRaw("upper(work_groups.name) like '$name'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('allowance_configs');
        $query->select('allowance_configs.*', 'work_groups.name as workgroup_name', 'allowances.allowance as allowance_name');
        $query->leftJoin('workgroup_allowances as workgroup', 'workgroup.id', '=', 'allowance_configs.workgroup_id');
        $query->leftJoin('workgroup_allowances as allowance', 'allowance.id', '=', 'allowance_configs.allowance_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'workgroup.workgroup_id');
        $query->leftJoin('allowances', 'allowances.id', '=', 'allowance.allowance_id');
        if ($name) {
            $query->whereRaw("upper(work_groups.name) like '$name'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $allowance_configs = $query->get();

        $data = [];
        foreach ($allowance_configs as $result) {
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
    public function selectworkgroup(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $workgroup = strtoupper($request->name);

        //Count Data
        $query = DB::table('workgroup_allowances');
        $query->select('workgroup_allowances.id', 'workgroup_allowances.workgroup_id', 'work_groups.name as workgroup_name');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'workgroup_allowances.workgroup_id');
        $query->where('workgroup_allowances.type', '=', 'percentage');
        $query->whereRaw("upper(work_groups.name) like '%$workgroup%'");
        $query->groupBy('work_groups.name',
            'workgroup_allowances.id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('workgroup_allowances');
        $query->select(
            'workgroup_allowances.id',
            'workgroup_allowances.workgroup_id',
            'work_groups.name as workgroup_name'
        );
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'workgroup_allowances.workgroup_id');
        $query->where('workgroup_allowances.type', '=', 'percentage');
        $query->whereRaw("upper(work_groups.name) like '%$workgroup%'");
        $query->offset($start);
        $query->limit($length);
        $query->groupBy('work_groups.name',
            'workgroup_allowances.id'
            );
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
    public function selectallowance(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $workgroup = $request->workgroup_id;
        $allowance = strtoupper($request->name);
        // dd($workgroup);
        //Count Data
        $query = DB::table('workgroup_allowances');
        $query->select('workgroup_allowances.*', 'allowances.allowance');
        $query->leftJoin('allowances', 'allowances.id', '=', 'workgroup_allowances.allowance_id');
        // if($workgroup){

        // }
        $query->where('workgroup_allowances.workgroup_id', '=', $workgroup);
        $query->where('workgroup_allowances.type', '=', 'percentage');
        $query->whereRaw("upper(allowances.allowance) like '%$allowance%'");
        // $query->groupBy('allowances.allowance');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('workgroup_allowances');
        $query->select(
            'workgroup_allowances.*',
            'allowances.allowance'
        );
        $query->leftJoin('allowances', 'allowances.id', '=', 'workgroup_allowances.allowance_id');
        $query->where('workgroup_allowances.workgroup_id', '=', $workgroup);
        $query->where('workgroup_allowances.type', '=', 'percentage');
        $query->whereRaw("upper(allowances.allowance) like '%$allowance%'");
        $query->offset($start);
        $query->limit($length);
        // $query->groupBy('allowances.allowance');
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
    public function index()
    {
        return view('admin.allowanceconfig.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.allowanceconfig.create');
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
            'workgroup_id'   => 'required',
            'allowance_id' => 'required',
            'type'          => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $allowanceconfig = AllowanceConfig::create([
            'workgroup_id'      => $request->workgroup_id,
            'allowance_id'      => $request->allowance_id,
            'note'              => $request->notes,
            'type'              => $request->type,
            'status'            => $request->status,
        ]);
        if ($allowanceconfig) {
            foreach ($request->allowanceID as $key => $allowance) {
                $createDepartment = AllowanceConfigDetail::create([
                    'allowance_config_id' => $allowanceconfig->id,
                    'allowance_id'        => $allowance
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

        if (!$allowanceconfig) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $allowanceconfig
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('allowanceconfig.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AllowanceConfig  $allowanceConfig
     * @return \Illuminate\Http\Response
     */
    public function show(AllowanceConfig $allowanceConfig)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AllowanceConfig  $allowanceConfig
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $allowanceconfig = AllowanceConfig::with(["allowance", "workgroup", "allowanceconfigdetail.allowance"])->find($id);
        if ($allowanceconfig) {
            return view('admin.allowanceconfig.edit', compact('allowanceconfig'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AllowanceConfig  $allowanceConfig
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'workgroup_id'   => 'required',
            'allowance_id' => 'required',
            'type'          => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $allowanceconfig = AllowanceConfig::find($id);
        $allowanceconfig->workgroup_id = $request->workgroup_id;
        $allowanceconfig->allowance_id = $request->allowance_id;
        $allowanceconfig->type         = $request->type;
        $allowanceconfig->notes        = $request->notes;
        $allowanceconfig->status       = $request->status;
        $allowanceconfig->save();

        if (!$allowanceconfig) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $allowanceconfig
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('allowanceconfig.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AllowanceConfig  $allowanceConfig
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $allowanceconfig = AllowanceConfig::find($id);
            $allowanceconfig->delete();
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
