<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerContact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'customer'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.customer.index');
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('customers');
        $query->select('customers.*');
        $query->whereRaw("upper(customers.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('customers');
        $query->select('customers.*');
        $query->whereRaw("upper(customers.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $customers = $query->get();

        $data = [];
        foreach($customers as $customer){
            $customer->no = ++$start;
            // $partner->category = $category[$partner->category];
			$data[] = $customer;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('customers');
        $query->select('customers.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('customers');
        $query->select('customers.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $customers = $query->get();

        $data = [];
        foreach($customers as $customer){
            $customer->no = ++$start;
			$data[] = $customer;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.customer.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // echo"tombol di klik";
        $validator = Validator::make($request->all(), [
            'name'             => 'required',
            'customergroup_id' => 'required',
            'email'            => 'required|email',
            'code'             => 'required',
            'picture'          => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        
        DB::beginTransaction();
        $customer = Customer::create([
            'name'  => $request->name,
            'customergroup_id'  => $request->customergroup_id,
            'email' => $request->email,
            'code' => $request->code,
            'picture'=>''
            ]);
        $picture = $request->file('picture');
        if($picture){    
            $filename = 'foto.'. $request->picture->getClientOriginalExtension();
            $src = 'assets/customer/'.$customer->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $picture->move($src,$filename);
            $customer->picture = $src.'/'.$filename;
            $customer->save();
        }
        
        if (!$customer) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $customer
            ], 400);
        }
        $customeraddress = CustomerAddress::create([
            'customer_id'   => $customer->id,
            'province_id' 	=> $request->province_id,
            'region_id' 	=> $request->region_id,
            'district_id' 	=> $request->district_id,
            'address'       => $request->address,
            'latitude' 	    => $request->latitude,
            'longitude'     => $request->longitude,
            'default'       => 1
        ]);
        if (!$customeraddress) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $customeraddress
            ], 400);
        }
        
        $customercontact = CustomerContact::create([
            'customer_id'     => $customer->id,
            'contact_name'    => $request->contact_name,
            'contact_phone'   => $request->contact_phone,
            'contact_email'   => $request->contact_email,
            'contact_address' => $request->contact_address,
            'default'         => 1
        ]);
        if (!$customercontact) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $customercontact
            ], 400);
        }

        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('customer.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::with('customergroup')->find($id);
        if($customer){
            return view('admin.customer.edit',compact('customer'));
        }
        else{
            abort(404);
        }
        // return response()->json($customer);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'name'   => 'required',
            'customergroup_id' => 'required',
            'email'  => 'required|email',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        
        $customer = Customer::find($id);
        $customer->code = $request->code;
        $customer->name = $request->name;
        $customer->customergroup_id = $request->customergroup_id;
        $customer->email = $request->email;
        $customer->save();

         $picture = $request->file('picture');
        if($picture){  
            $filename = 'foto.'. $request->picture->getClientOriginalExtension(); 
            if(file_exists($customer->picture)){
                unlink($customer->picture);
            } 
            
            $src = 'assets/customer/'.$customer->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $picture->move($src,$filename);
            $customer->picture = $src.'/'.$filename;
            $customer->save();
        }

         if (!$customer) {
            return response()->json([
                'status' => false,
                'message' 	=> $customer
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('customer.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::find($id);
            if(file_exists($customer->picture)){
                unlink($customer->picture);
            } 
            $customer->delete();
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
