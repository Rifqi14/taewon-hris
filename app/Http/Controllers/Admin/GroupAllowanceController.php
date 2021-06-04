<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\GroupAllowance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class GroupAllowanceController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'groupallowance'));
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
        $groupallowance = strtoupper($request->group_allowance);

        //Count Data
        $query = DB::table('group_allowances');
        $query->select('group_allowances.*');
        $query->whereRaw("upper(group_allowances.name) like '%$groupallowance%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('group_allowances');
        $query->select('group_allowances.*');
        $query->whereRaw("upper(group_allowances.name) like '%$groupallowance%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $group_allowances = $query->get();

        $data = [];
        foreach ($group_allowances as $group_allowance) {
            $group_allowance->no = ++$start;
            $data[] = $group_allowance;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'sort' => $sort,
            'data' => $data
        ], 200);
    }
    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $groupAllowance = strtoupper($request->groupallowance);

        //Count Data
        $query = DB::table('group_allowances');
        $query->select('group_allowances.*');
        $query->whereRaw("upper(name) like '%$groupAllowance%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('group_allowances');
        $query->select('group_allowances.*');
        $query->whereRaw("upper(name) like '%$groupAllowance%'");
        $query->offset($start);
        $query->limit($length);
        $group_allowances = $query->get();

        $data = [];
        foreach ($group_allowances as $groupallowance) {
            $groupallowance->no = ++$start;
            $data[] = $groupallowance;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }
    public function index()
    {
        return view('admin.groupallowance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.groupallowance.create');
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
            'group_allowance'     => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        if ($request->coordinate > 0) {
            $cek_coordinate = GroupAllowance::where('coordinate', $request->coordinate)->first();

            if ($cek_coordinate) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Coordinate has been used' . $cek_coordinate->name
                ], 400);
            }
        }
        $groupAllowance = GroupAllowance::create([
            'code'          => '',
            'site_id'       => Session::get('site_id'),
            'name'          => $request->group_allowance,
            'notes'         => $request->notes,
            'status'        => $request->status,
            'group_type'    => $request->type,
            'coordinate'    => $request->coordinate
        ]);
        if ($request->code) {
            $groupAllowance->code = $request->code;
            $groupAllowance->save();
        } else {
            $groupAllowance->code = $groupAllowance->code_system;
            $groupAllowance->save();
        }
        if (!$groupAllowance) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => 'Gagal Menyimpan Data Harap Diperiksa Kembali'
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('groupallowance.index'),
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
        $groupAllowance = GroupAllowance::find($id);
        if ($groupAllowance) {
            return view('admin.groupallowance.edit', compact('groupAllowance'));
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
            'group_allowance'         => 'required',
            // 'code' 	    => 'required|alpha_dash'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        if ($request->coordinate > 0) {
            $cek_coordinate = GroupAllowance::where('coordinate', $request->coordinate)->where('id', '!=', $id)->first();

            if ($cek_coordinate) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Coordinate has been used' . $cek_coordinate->name
                ], 400);
            }
        }
        $groupAllowance = GroupAllowance::find($id);
        $groupAllowance->code       = $request->code;
        $groupAllowance->name       = $request->group_allowance;
        $groupAllowance->notes      = $request->notes;
        $groupAllowance->status     = $request->status;
        $groupAllowance->group_type = $request->type;
        $groupAllowance->coordinate = $request->coordinate;
        $groupAllowance->save();

        if (!$groupAllowance) {
            return response()->json([
                'status' => false,
                'message'     => 'Gagal Menyimpan Data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('groupallowance.index'),
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
            $groupAllowance = GroupAllowance::find($id);
            $groupAllowance->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}