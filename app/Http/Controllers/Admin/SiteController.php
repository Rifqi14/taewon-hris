<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SiteController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'site'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $code = strtoupper($request->code);
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('sites');
        $query->select('sites.*');
        $query->whereRaw("upper(code) like '%$code%'");
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('sites');
        $query->select('sites.*');
        $query->whereRaw("upper(code) like '%$code%'");
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $sites = $query->get();

        $data = [];
        foreach($sites as $site){
            $site->no = ++$start;
			$data[] = $site;
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
        return view('admin.site.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.site.create');
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
            'code' 	    => 'required|unique:sites|max:3|min:3',
            'name' 	=> 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'province_id' 	=> 'required',
            'region_id' 	=> 'required',
            'district_id' 	=> 'required',
            'address' 	=> 'required',
            'postal_code' 	=> 'required',
            'receipt_header' => 'required',
            'receipt_footer' => 'required',
            'logo' 		     => 'required|mimes:png',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $site = Site::create([
            'code' 	=> $request->code,
			'name' 	=> $request->name,
			'phone' 	=> $request->phone,
			'email' 	=> $request->email,
			'province_id' 	=> $request->province_id,
			'region_id' 	=> $request->region_id,
			'district_id' 	=> $request->district_id,
			'address' 	=> $request->address,
			'postal_code' 	=> $request->postal_code,
			'receipt_header' 	=> $request->receipt_header,
			'receipt_footer' 	=> $request->receipt_footer,
        ]);
        if (!$site) {
            return response()->json([
                'status' => false,
                'message' 	=> $site
            ], 400);
        }
        $logo = $request->file('logo');
        if($logo){
            $path = 'assets/site/';
            $logo->move($path, $site->code.'.'.$logo->getClientOriginalExtension());
            $filename = $path.$site->code.'.'.$logo->getClientOriginalExtension();
            $site->logo = $filename?$filename:'';
            $site->save();
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('site.index'),
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
        $site = Site::with('province','region','district')->find($id);
        if($site){
            return view('admin.site.edit',compact('site'));
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
            'code' 	    => 'required|unique:sites,code,'.$id.'|max:3|min:3',
            'name' 	=> 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'province_id' 	=> 'required',
            'region_id' 	=> 'required',
            'district_id' 	=> 'required',
            'address' 	=> 'required',
            'postal_code' 	=> 'required',
            'receipt_header' => 'required',
            'receipt_footer' => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $site = Site::find($id);
        $site->code = $request->code;
        $site->name = $request->name;
        $site->email = $request->email;
        $site->province_id = $request->province_id;
        $site->region_id = $request->region_id;
        $site->district_id = $request->district_id;
        $site->address = $request->address;
        $site->postal_code = $request->postal_code;
        $site->receipt_header = $request->receipt_header;
        $site->receipt_footer = $request->receipt_footer;
        $site->save();
        if (!$site) {
            return response()->json([
                'status' => false,
                'message' 	=> $site
            ], 400);
        }
        $logo = $request->file('logo');
        if($logo){
            if(file_exists($site->logo)){
                unlink($site->logo);
            }
            $path = 'assets/site/';
            $logo->move($path, $site->code.'.'.$logo->getClientOriginalExtension());
            $filename = $path.$site->code.'.'.$logo->getClientOriginalExtension();
            $site->logo = $filename?$filename:'';
            $site->save();
        }
        
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('site.index'),
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
            $site = Site::find($id);
            $site->delete();
            if(file_exists($site->logo)){
                unlink($site->logo);
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

    public function set(Request $request){
        $request->session()->put('site_id', $request->id);
        return redirect()->back();
     }
}
