<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomerContact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class CustomerContactController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'customercontact'));
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
        $contact_name = strtoupper($request->contact_name);
        $customer_id = $request->customer_id;

        //Count Data
        $query = DB::table('customer_contacts');
        $query->select('customers_contacts.*');
        $query->whereRaw("upper(customer_contacts.contact_name) like '%$contact_name%'");
        $query->where('customer_id', $customer_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('customer_contacts');
        $query->select('customer_contacts.*');
        $query->whereRaw("upper(customer_contacts.contact_name) like '%$contact_name%'");
        $query->where('customer_id', $customer_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $customercontacs = $query->get();

        $data = [];
        foreach($customercontacs as $customercontact){
            $customercontact->no = ++$start;
            // $partner->category = $category[$partner->category];
			$data[] = $customercontact;
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
            'contact_name'       => 'required',
            'contact_phone'      => 'required',
            'contact_email'      => 'required',
            'contact_address'    => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $customercontact = CustomerContact::create([
            'customer_id'   => $request->customer_id,
            'contact_name'  => $request->contact_name,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'contact_address'=> $request->contact_address,
            'default' 	     => 1
        ]);
        if (!$customercontact) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $customercontact
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
     * @param  \App\CustomerContact  $customerContact
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerContact $customerContact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerContact  $customerContact
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerContact $customerContact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerContact  $customerContact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerContact $customerContact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerContact  $customerContact
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $customercontact = CustomerContact::find($id);
            $customercontact->delete();
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
