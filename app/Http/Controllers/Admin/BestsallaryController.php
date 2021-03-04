<?php

namespace App\Http\Controllers\Admin;

use App\Models\BestSallary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BestsallaryController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'basesallary'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('bestsallarys');
        $query->select('bestsallarys.*','regions.name');
        $query->leftJoin('regions','regions.id','=','bestsallarys.region_id');
        $query->whereRaw("upper(regions.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('bestsallarys');
        $query->select('bestsallarys.*','regions.name');
        $query->leftJoin('regions','regions.id','=','bestsallarys.region_id');
        $query->whereRaw("upper(regions.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $bestsallarys = $query->get();

        $data = [];
        foreach($bestsallarys as $bestsallary){
            $bestsallary->no = ++$start;
            $data[] = $bestsallary;
        }
        return response()->json([
            'total'=>$recordsTotal,
            'rows'=>$data
        ], 200);
    }

    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $region_name = strtoupper($request->region_name);

        //Count Data
        $query = DB::table('bestsallarys');
        $query->select('bestsallarys.*');
        $query->leftJoin('regions','regions.id','=','bestsallarys.region_id');
        $query->whereRaw("upper(regions.name) like '%$region_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('bestsallarys');
        $query->select('bestsallarys.*','regions.name as region_name');
        $query->leftJoin('regions','regions.id','=','bestsallarys.region_id');
        $query->whereRaw("upper(regions.name) like '%$region_name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $bestsallarys = $query->get();

        $data = [];
        foreach($bestsallarys as $bestsallary){
            $bestsallary->no = ++$start;
            $data[] = $bestsallary;
        }
        return response()->json([
            'draw'=>$request->draw,
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsTotal,
            'data'=>$data
        ], 200);
    }

    public function index()
    {
        return view('admin.basesallary.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.basesallary.create');
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
            'region_id' => 'required',
            'sallary'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $bestsallary = BestSallary::create([
            'region_id' => $request->region_id,
            'sallary' => $request->sallary
        ]);
        if (!$bestsallary) {
            return response()->json([
                'status' => false,
                'message'   => $bestsallary
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('basesallary.index'),
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
        $basesallary = BestSallary::with('region')->find($id);
        if ($basesallary) {
            return view('admin.basesallary.edit', compact('basesallary'));
        }else{
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
            'sallary'  => 'required',
            'region_id'   => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $bestsallary = BestSallary::find($id);
        $bestsallary->sallary = $request->sallary;
        $bestsallary->region_id = $request->region_id;
        $bestsallary->save();
        if (!$bestsallary) {
            return response()->json([
                'status' => false,
                'message'   => $bestsallary
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => url('admin/basesallary'),
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
            $bestsallary = BestSallary::find($id);
            $bestsallary->delete();
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
