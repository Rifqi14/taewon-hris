<?php

namespace App\Http\Controllers\Admin;

use  App\Models\OutsourcingAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class OutsourcingAddressController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'outsourcingaddress'));
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
        $address = strtoupper($request->address);
        $name = strtoupper($request->name);
        $outsourcing_id = $request->outsourcing_id;

        //Count Data
        $query = DB::table('outsourcing_addresses');
        $query->select('outsourcing_addresses.*');
        $query->whereRaw("upper(outsourcing_addresses.address) like '%$address%'");
        $query->where('outsourcing_id', $outsourcing_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('outsourcing_addresses');
        $query->select('outsourcing_addresses.*',
                        'provinces.name as province_name',
                        'regions.name as region_name',
                        'districts.name as district_name',
                        'outsourcings.name as outsourcing_name'
        );
        $query->leftJoin('provinces','provinces.id','=','outsourcing_addresses.province_id');
        $query->leftJoin('regions','regions.id','=','outsourcing_addresses.region_id');
        $query->leftJoin('districts','districts.id','=','outsourcing_addresses.district_id');
        // $query->leftJoin('villages','villages.id','=','outsourcing_addresses.village_id');
        $query->leftJoin('outsourcings','outsourcings.id','=','outsourcing_addresses.outsourcing_id');
        $query->whereRaw("upper(provinces.name) like '%$name%'");
        $query->whereRaw("upper(regions.name) like '%$name%'");
        $query->whereRaw("upper(districts.name) like '%$name%'");
        $query->whereRaw("upper(outsourcings.name) like '%$name%'");
        $query->whereRaw("upper(outsourcing_addresses.address) like '%$address%'");
        $query->where('outsourcing_id', $outsourcing_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $outsourcingaddresses = $query->get();

        $data = [];
        foreach($outsourcingaddresses as $outsourcingaddress){
            $outsourcingaddress->no = ++$start;
            // $partner->category = $category[$partner->category];
			$data[] = $outsourcingaddress;
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
            'province_id'      => 'required',
            'region_id'        => 'required',
            'district_id'      => 'required',
            'address'          => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $outsourcingaddress = OutsourcingAddress::create([
            'outsourcing_id'=> $request->outsourcing_id,
            'province_id' => $request->province_id,
            'region_id'   => $request->region_id,
            'district_id' => $request->district_id,
            'kode_pos' => $request->kode_pos,
            'address'     => $request->address,
            'default'     => $request->default?1:0,
        ]);
        if($request->default){
            $outsourcingaddresses = OutsourcingAddress::where('id','<>',$outsourcingaddress->id)->where('outsourcing_id',$request->outsourcing_id)->get();
            foreach($outsourcingaddresses as $outsourcingaddress){
                $outsourcingaddress->default = 0 ;
                $outsourcingaddress->save();
            }
        }
        if (!$outsourcingaddress) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcingaddress
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
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
        $outsourcingaddress = OutsourcingAddress::with('province', 'region', 'district')->find($id);
        return response()->json([
            'status' 	=> true,
            'data' => $outsourcingaddress
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
            'outsourcing_id' 	=> 'required',
            'province_id' 	=> 'required',
            'region_id' 	=> 'required',
            'district_id' 	=> 'required',
            'address' 	    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $outsourcingaddress = OutsourcingAddress::find($id);
        $outsourcingaddress->outsourcing_id = $request->outsourcing_id;
        $outsourcingaddress->province_id = $request->province_id;
        $outsourcingaddress->region_id   = $request->region_id;
        $outsourcingaddress->district_id = $request->district_id;
        $outsourcingaddress->address     = $request->address;
        $outsourcingaddress->kode_pos = $request->kode_pos;
        $outsourcingaddress->default     = $request->default?1:0;
        $outsourcingaddress->save();

        if($request->default){
            $outsourcingaddresses = OutsourcingAddress::where('id','<>',$id)->where('outsourcing_id',$request->outsourcing_id)->get();
            foreach($outsourcingaddresses as $outsourcingaddress){
                $outsourcingaddress->default = 0 ;
                $outsourcingaddress->save();
            }
        }
        if (!$outsourcingaddress) {
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcingaddress
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $outsourcingaddress
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
            $outsourcingaddress = OutsourcingAddress::find($id);
            $outsourcingaddress->delete();
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
