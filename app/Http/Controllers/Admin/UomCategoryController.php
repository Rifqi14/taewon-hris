<?php

namespace App\Http\Controllers\Admin;

use App\Models\UomCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class UomCategoryController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'uomcategory'));
    }
    public function index(){
        return view('admin.uomcategory.index');
    }
    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $code = strtoupper($request->code);
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('uom_categories');
        $query->select('uom_categories.*');
        $query->whereRaw("upper(code) like '%$code%'");
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('uom_categories');
        $query->select('uom_categories.*');
        $query->whereRaw("upper(code) like '%$code%'");
        $query->whereRaw("upper(name) like '%$name%'");
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
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('uom_categories');
        $query->select('uom_categories.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('uom_categories');
        $query->select('uom_categories.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $uomcategories = $query->get();

        $data = [];
        foreach($uomcategories as $uomcategory){
            $uomcategory->no = ++$start;
			$data[] = $uomcategory;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }
    public function create(){
        return view('admin.uomcategory.create');
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'code' 	=> 'required|unique:uom_categories',
            'name' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $uomcategory = UomCategory::create([
            'code' 	=> $request->code,
			'name' 	=> $request->name
        ]);
        if (!$uomcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $uomcategory
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('uomcategory.index'),
        ], 200);
    }
    public function edit($id){
        $uomcategory = UomCategory::find($id);
        if($uomcategory){
            return view('admin.uomcategory.edit',compact('uomcategory'));
        }
        else{
            abort(404);
        }
    }
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'code' 	=> 'required|unique:uom_categories,code,'.$request->id,
            'name' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $uomcategory = UomCategory::find($id);
        $uomcategory->code = $request->code;
        $uomcategory->name = $request->name;
        $uomcategory->save();
        if (!$uomcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $uomcategory
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('uomcategory.index'),
        ], 200);
    }

    public function destroy(Request $request)
    {
        try {
            $uomcategory = UomCategory::find($request->id);
            $uomcategory->delete();
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
