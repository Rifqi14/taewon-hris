<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Principle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PrincipleController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'principle'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.principle.index');
    }

    /**
     * Show the form for creating a new resource.
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
        $query = DB::table('principles');
        $query->select('principles.*');
        $query->whereRaw("upper(principles.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('principles');
        $query->select('principles.*');
        $query->whereRaw("upper(principles.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $partners = $query->get();

        $data = [];
        foreach($partners as $partner){
            $partner->no = ++$start;
			$data[] = $partner;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function create()
    {
        return view('admin.principle.create');
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
            'name'    => 'required',
            'phone'   => 'required',
            'address' => 'required',
            'image' 	  => 'required|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $principle = Principle::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email'  => $request->email,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        $image = $request->file('image');
        if($image){
            $dt = Carbon::now();
            $rd = Str::random(5);
            $path = 'assets/principle/';
            $image->move($path, $rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension());
            $filename = $path.$rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension();
            $principle->image = $filename?$filename:'';
            $principle->save();
        }

        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('principle.index'),
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
        $principle = Principle::findOrFail($id);

        return view('admin.principle.detail',compact('principle'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $principle = Principle::findOrFail($id);

        return view('admin.principle.edit',compact('principle'));

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
            'name'   => 'required',
            'phone'   => 'required',
            'address'   => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $principle = Principle::find($id);
        $principle->name = $request->name;
        $principle->phone = $request->phone;
        $principle->email = $request->email;
        $principle->address = $request->address;
        $principle->latitude  = $request->latitude;
        $principle->longitude  = $request->longitude;
        $principle->save();

        if (!$principle) {
            return response()->json([
                'status' => false,
                'message' 	=> $principle
            ], 400);
        }

        $image = $request->file('image');
        if($image){
            if(file_exists($principle->image)){
                unlink($principle->image);
            }
            $dt = Carbon::now();
            $rd = Str::random(5);
            $path = 'assets/principle/';
            $image->move($path, $rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension());
            $filename = $path.$rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension();
            $principle->image = $filename?$filename:'';
            $principle->save();
        }

        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('principle.index'),
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
            $principle = Principle::find($id);
            $principle->delete();
            if(file_exists($principle->logo)){
                unlink($principle->logo);
            }
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
