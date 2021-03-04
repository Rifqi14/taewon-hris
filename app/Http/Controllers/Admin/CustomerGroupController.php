<?php

namespace App\Http\Controllers\Admin;

use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class CustomerGroupController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'customergroup'));
    }
    public function index(){
        return view('admin.customergroup.index');
    }
    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $customergroup_code = strtoupper($request->customergroup_code);
        $customergroup_name = strtoupper($request->customergroup_name);

        //Count Data
        $query = DB::table('customer_groups');
        $query->select('customer_groups.*');
        $query->whereRaw("upper(customergroup_code) like '%$customergroup_code%'");
        $query->whereRaw("upper(customergroup_name) like '%$customergroup_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('customer_groups');
        $query->select('customer_groups.*');
        $query->whereRaw("upper(customergroup_code) like '%$customergroup_code%'");
        $query->whereRaw("upper(customergroup_name) like '%$customergroup_name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $uomcategories = $query->get();

        $data = [];
        foreach($uomcategories as $uomcategory){
            $uomcategory->no = ++$start;
			$data[] = $uomcategory;
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
        $customergroup_name = strtoupper($request->customergroup_name);

        //Count Data
        $query = DB::table('customer_groups');
        $query->select('customer_groups.*');
        $query->whereRaw("upper(customergroup_name) like '%$customergroup_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('customer_groups');
        $query->select('customer_groups.*');
        $query->whereRaw("upper(customergroup_name) like '%$customergroup_name%'");
        $query->offset($start);
        $query->limit($length);
        $customergroups = $query->get();

        $data = [];
        foreach($customergroups as $customergroup){
            $customergroup->no = ++$start;
			$data[] = $customergroup;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }
    public function create(){
        return view('admin.customergroup.create');
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'customergroup_code' 	=> 'required|unique:customer_groups',
            'customergroup_name' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $customergroup = CustomerGroup::create([
            'customergroup_code' 	=> $request->customergroup_code,
			'customergroup_name' 	=> $request->customergroup_name
        ]);
        if (!$customergroup) {
            return response()->json([
                'status' => false,
                'message' 	=> $customergroup
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('customergroup.index'),
        ], 200);
    }
    public function edit($id){
        $customergroup = CustomerGroup::find($id);
        if($customergroup){
            return view('admin.customergroup.edit',compact('customergroup'));
        }
        else{
            abort(404);
        }
    }
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'customergroup_code' 	=> 'required|unique:customer_groups,customergroup_code,'.$request->id,
            'customergroup_name' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $customergroup = CustomerGroup::find($id);
        $customergroup->customergroup_code = $request->customergroup_code;
        $customergroup->customergroup_name = $request->customergroup_name;
        $customergroup->save();
        if (!$customergroup) {
            return response()->json([
                'status' => false,
                'message' 	=> $customergroup
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('customergroup.index'),
        ], 200);
    }

    public function destroy(Request $request)
    {
        try {
            $customergroup = CustomerGroup::find($request->id);
            $customergroup->delete();
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
