<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class CustomerAddressController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'customeraddress'));
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
        $customer_id = $request->customer_id;

        //Count Data
        $query = DB::table('customer_addresses');
        $query->select('customers_addresses.*');
        $query->whereRaw("upper(customer_addresses.address) like '%$address%'");
        $query->where('customer_id', $customer_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('customer_addresses');
        $query->select('customer_addresses.*');
        $query->whereRaw("upper(customer_addresses.address) like '%$address%'");
        $query->where('customer_id', $customer_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $customeraddresses = $query->get();

        $data = [];
        foreach($customeraddresses as $customeraddress){
            $customeraddress->no = ++$start;
            // $partner->category = $category[$partner->category];
			$data[] = $customeraddress;
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
            'address'          => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $customeraddress = CustomerAddress::create([
            'customer_id' => $request->customer_id,
            'province_id' => $request->province_id,
            'region_id'   => $request->region_id,
            'district_id' => $request->district_id,
            'address'     => $request->address,
            'latitude' 	  => $request->latitude,
            'longitude'   => $request->longitude,
            'default'     => 1
        ]);
        
        if (!$customeraddress) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $customeraddress
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
     * @param  \App\CustomerAddress  $customerAddress
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerAddress $customerAddress)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerAddress  $customerAddress
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customeraddress = CustomerAddress::with('provinces', 'regions', 'districts')->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerAddress  $customerAddress
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerAddress $customerAddress)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerAddress  $customerAddress
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $customeraddress = CustomerAddress::find($id);
            $customeraddress->delete();
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
