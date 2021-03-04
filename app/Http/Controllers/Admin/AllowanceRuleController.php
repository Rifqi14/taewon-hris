<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AllowanceRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AllowanceRuleController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'allowancerules'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $allowance_id = $request->allowance_id;

        //Count Data
        $query = DB::table('allowance_rules');
        $query->select('allowance_rules.*');
        $query->where('allowance_id', $allowance_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('allowance_rules');
        $query->select(
            'allowance_rules.*',
            'allowances.allowance as allowance_name'
        );
        $query->leftJoin('allowances', 'allowances.id', '=', 'allowance_rules.allowance_id');
        $query->where('allowance_id', $allowance_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $allowancerules = $query->get();

        $data = [];
        foreach ($allowancerules as $allowancerule) {
            $allowancerule->no = ++$start;
            $data[] = $allowancerule;
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
        $validator = Validator::make($request->all(), [
            'qty_absent'    => 'required|integer',
            'qty_allowance' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $allowancerule = AllowanceRule::create([
            'allowance_id'  => $request->allowance_id,
            'qty_absent'    => $request->qty_absent,
            'qty_allowance'  => $request->qty_allowance
        ]);

        if (!$allowancerule) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $allowancerule
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success add data'
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
        $allowancerule = AllowanceRule::with('allowance')->find($id);
        return response()->json([
            'status'     => true,
            'data' => $allowancerule
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
            'qty_absent'    => 'required|integer',
            'qty_allowance' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $allowancerule = AllowanceRule::find($id);
        $allowancerule->allowance_id    = $request->allowance_id;
        $allowancerule->qty_absent      = $request->qty_absent;
        $allowancerule->qty_allowance   = $request->qty_allowance;
        $allowancerule->save();

        if (!$allowancerule) {
            return response()->json([
                'status' => false,
                'message'     => $allowancerule
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => $allowancerule
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
            $allowancerule = AllowanceRule::find($id);
            $allowancerule->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     =>  'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}