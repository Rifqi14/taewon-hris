<?php

namespace App\Http\Controllers\Admin;

use App\Models\WorkgroupAllowance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Validator;

class WorkgroupAllowanceController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'workgroup'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $workgroup_id = $request->workgroup_id;

        //Count Data
        $query = DB::table('workgroup_allowances');
        $query->select('workgroup_allowances.*');
        $query->leftJoin('allowances', 'allowances.id', '=', 'workgroup_allowances.allowance_id');
        $query->where('allowances.status', '=', 1);
        $query->where('workgroup_allowances.workgroup_id', '=', $workgroup_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('workgroup_allowances');
        $query->select('workgroup_allowances.*', 'workgroup_allowances.workgroup_id as workgroups_id', 'allowances.allowance', 'allowances.category');
        $query->leftJoin('allowances', 'allowances.id', '=', 'workgroup_allowances.allowance_id');
        $query->where('allowances.status', '=', 1);
        $query->where('workgroup_allowances.workgroup_id', '=', $workgroup_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('id', 'asc');
        $roles = $query->get();

        $data = [];
        foreach ($roles as $role) {
            $role->no = ++$start;
            $role->category = @config('enums.allowance_category')[$role->category];
            $data[] = $role;
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
        //
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
        $workgroupallowance = WorkgroupAllowance::with('allowance')->find($id);
        return response()->json([
            'status'     => true,
            'data' => $workgroupallowance
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
            'type'    => 'required',
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $workgroupallowance = WorkgroupAllowance::find($id);
        // $workgroupallowance->allowance_id    = $request->allowance_id;
        $workgroupallowance->type      = $request->type;
        $workgroupallowance->value   = $request->value;
        $workgroupallowance->save();

        if (!$workgroupallowance) {
            return response()->json([
                'status' => false,
                'message'     => $workgroupallowance
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => $workgroupallowance,
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
        //
    }
}