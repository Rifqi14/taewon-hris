<?php

namespace App\Http\Controllers\Admin;

use App\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'district'));
    }
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $region_id = $request->region_id;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('districts');
        $query->select('districts.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($region_id){
            $query->where('region_id','=',$region_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('districts');
        $query->select('districts.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($region_id){
            $query->where('region_id','=',$region_id);
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
        $region_name = strtoupper($request->region_name);
        $district_name = strtoupper($request->district_name);

        //Count Data
        $query = DB::table('districts');
        $query->select('districts.*');
        $query->leftJoin('regions','regions.id','=','districts.region_id');
        $query->whereRaw("upper(regions.name) like '%$region_name%'");
        $query->whereRaw("upper(districts.name) like '%$district_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('districts');
        $query->select('districts.*','regions.name as region_name','regions.type as region_type');
        $query->leftJoin('regions','regions.id','=','districts.region_id');
        $query->whereRaw("upper(regions.name) like '%$region_name%'");
        $query->whereRaw("upper(districts.name) like '%$district_name%'");
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
        return view('admin.district.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         return view('admin.district.create');
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
            'region_id' 	    => 'required',
            'name' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $district = District::create([
            'region_id' => $request->region_id,
            'name' => $request->name
        ]);
        if (!$district) {
            return response()->json([
                'status' => false,
                'message' 	=> $district
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('district.index'),
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
        $district = District::with('region')->find($id);
        if($district){
            return view('admin.district.edit',compact('district'));
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
            'region_id' => 'required',
            'name' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $district = District::find($id);
        $district->region_id = $request->region_id;
        $district->name = $request->name;
        $district->save();
        if (!$district) {
            return response()->json([
                'status' => false,
                'message' 	=> $district
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> url('admin/district'),
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
            $district = District::find($id);
            $district->delete();
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
