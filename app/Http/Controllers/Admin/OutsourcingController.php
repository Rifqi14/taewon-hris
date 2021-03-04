<?php

namespace App\Http\Controllers\Admin;

use App\Models\Outsourcing;
use App\Models\OutsourcingAddress;
use App\Models\OutsourcingPic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Share;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Validator;

class OutsourcingController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'outsourcing'));
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
        $query = Outsourcing::select('outsourcings.*');
        // $query->leftJoin(DB::raw('(select * from principle_addresses where principle_addresses.default = 1) as principle_addresses'),'principle_addresses.principle_id','=','principles.outsourcings');
        $query->whereRaw("upper(outsourcings.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Outsourcing::with(['addresses' => function ($q)
        {
            $q->where(['default' => 1]);
        }])->select('outsourcings.*');
        // $query->leftJoin(DB::raw('(select * from principle_addresses where principle_addresses.default = 1) as principle_addresses'),'principle_addresses.principle_id','=','principles.id');
        $query->whereRaw("upper(outsourcings.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $outsourcings = $query->get();

        $data = [];
        foreach($outsourcings as $outsource){
            $outsource->no = ++$start;
            $outsource->address = (count($outsource->addresses) > 0) ? $outsource->addresses[0]->fullAddress : 'Tidak Ada Alamat Default';
			$data[] = $outsource;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = Outsourcing::select('outsourcings.*');
        // $query->leftJoin(DB::raw('(select * from principle_addresses where principle_addresses.default = 1) as principle_addresses'),'principle_addresses.principle_id','=','principles.outsourcings');
        $query->whereRaw("upper(outsourcings.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Outsourcing::with(['addresses' => function ($q)
        {
            $q->where(['default' => 1]);
        }])->select('outsourcings.*');
        // $query->leftJoin(DB::raw('(select * from principle_addresses where principle_addresses.default = 1) as principle_addresses'),'principle_addresses.principle_id','=','principles.id');
        $query->whereRaw("upper(outsourcings.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $outsourcings = $query->get();

        $data = [];
        foreach($outsourcings as $outsource){
            $outsource->no = ++$start;
			$data[] = $outsource;
		}
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }
    public function index()
    {
        return view('admin/outsourcing/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin/outsourcing/create');
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
            'code'  => 'required',
            'name'  => 'required',
            'email' => 'required|email',
            'image' => 'required|mimes:jpeg,png,jpg',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $outsourcing = Outsourcing::create([
            'code'   => $request->code,
            'name'   => $request->name,
            'email'  => $request->email,
            'no_tlpn'  => $request->no_tlpn,
            'image' => '',
            'workgroup_id' => $request->workgroup_id,
            'status' => $request->status
        ]);

        $image = $request->file('image');
        if($image){
            $dt = Carbon::now();
            $rd = Str::random(5);
            $path = 'assets/outsourcing/';
            $image->move($path, $rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension());
            $filename = $path.$rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension();
            $outsourcing->image = $filename?$filename:'';
            $outsourcing->save();
        }

        if (!$outsourcing) {
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcing
            ], 400);
        }
         $outsourcingaddress = OutsourcingAddress::create([
            'outsourcing_id'  => $outsourcing->id,
            'province_id' 	=> $request->province_id,
            'region_id' 	=> $request->region_id,
            'district_id' 	=> $request->district_id,
            // 'village_id'    => $request->village_id,
            'kode_pos' 	=> $request->kode_pos,
            'address'       => $request->address,
            'default'       => 1
        ]);
        if (!$outsourcingaddress) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcingaddress
            ], 400);
        }
        
        $outsourcingpic = OutsourcingPic::create([
            'outsourcing_id'    => $outsourcing->id,
            'pic_name'    => $request->pic_name,
            'pic_phone'   => $request->pic_phone,
            'pic_email'   => $request->pic_email,
            'pic_address' => $request->pic_address,
            'pic_category'=> $request->pic_category,
            'default'       => 1
        ]);
        if (!$outsourcingpic) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcingpic
            ], 400);
        }

        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('outsourcing.index'),
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
        $outsourcing = Outsourcing::find($id);
        if ($outsourcing) {
            return view('admin/outsourcing/edit', compact('outsourcing'));
        }else{
            Abort(404);
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
            'code'  => 'required',
            'name'  => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $outsourcing = Outsourcing::find($id);
        $outsourcing->name   = $request->name;
        $outsourcing->code   = $request->code;
        $outsourcing->email  = $request->email;
        $outsourcing->no_tlpn  = $request->no_tlpn;
        $outsourcing->status = $request->status;
        $outsourcing->save();

        if (!$outsourcing) {
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcing
            ], 400);
        }

        $image = $request->file('image');
        if($image){
            if(file_exists($outsourcing->image)){
                unlink($outsourcing->image);
            }
            $dt = Carbon::now();
            $rd = Str::random(5);
            $path = 'assets/outsourcing/';
            $image->move($path, $rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension());
            $filename = $path.$rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension();
            $outsourcing->image = $filename?$filename:'';
            $outsourcing->save();
        }

        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('outsourcing.index'),
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
            $outsourcing = Outsourcing::find($id);
            $outsourcing->delete();
            if(file_exists($outsourcing->logo)){
                unlink($outsourcing->logo);
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
