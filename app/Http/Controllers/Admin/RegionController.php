<?php

namespace App\Http\Controllers\Admin;

use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegionController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'region'));
    }
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $province_id = $request->province_id;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('regions');
        $query->select('regions.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($province_id){
            $query->where('province_id','=',$province_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('regions');
        $query->select('regions.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($province_id){
            $query->where('province_id','=',$province_id);
        }
        $query->offset($start);
        $query->limit($length);
        $regions = $query->get();

        $data = [];
        foreach($regions as $region){
            $region->no = ++$start;
			$data[] = $region;
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
        $province_name = strtoupper($request->province_name);
        $region_name = strtoupper($request->region_name);

        //Count Data
        $query = DB::table('regions');
        $query->select('regions.*');
        $query->leftJoin('provinces','provinces.id','=','regions.province_id');
        $query->whereRaw("upper(provinces.name) like '%$province_name%'");
        $query->whereRaw("upper(regions.name) like '%$region_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('regions');
        $query->select('regions.*','provinces.name as province_name');
        $query->leftJoin('provinces','provinces.id','=','regions.province_id');
        $query->whereRaw("upper(provinces.name) like '%$province_name%'");
        $query->whereRaw("upper(regions.name) like '%$region_name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $regions = $query->get();

        $data = [];
        foreach($regions as $region){
            $region->no = ++$start;
			$data[] = $region;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.region.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('admin.region.create');
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
            'province_id' => 'required',
            'name' 	    => 'required',
            'type'      => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $region = Region::create([
            'province_id' => $request->province_id,
            'name' => $request->name,
            'type' => $request->type
        ]);
        if (!$region) {
            return response()->json([
                'status' => false,
                'message' 	=> $region
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('region.index'),
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
        $region = Region::with('province')->find($id);
        if($region){
            return view('admin.region.edit',compact('region'));
        }
        else{
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
            'name' 	=> 'required',
            'province_id' 	=> 'required',
            'type' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $region = Region::find($id);
        $region->name = $request->name;
        $region->province_id = $request->province_id;
        $region->type = $request->type;
        $region->save();
        if (!$region) {
            return response()->json([
                'status' => false,
                'message' 	=> $region
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> url('admin/region'),
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
            $region = Region::find($id);
            $region->delete();
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
