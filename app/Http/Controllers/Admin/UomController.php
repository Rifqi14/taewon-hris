<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Uom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class UomController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'uom'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.uom.index');
    }

    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $uomcategory_name = strtoupper($request->uomcategory_name);
        $uom_name = strtoupper($request->uom_name);

        //Count Data
        $query = DB::table('uoms');
        $query->select('uoms.*');
        $query->leftJoin('uom_categories','uom_categories.id','=','uoms.uomcategory_id');
        $query->whereRaw("upper(uom_categories.name) like '%$uomcategory_name%'");
        $query->whereRaw("upper(uoms.name) like '%$uom_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('uoms');
        $query->select('uoms.*','uom_categories.name as uomcategory_name','uoms.name as uom_name');
        $query->leftJoin('uom_categories','uom_categories.id','=','uoms.uomcategory_id');
        $query->whereRaw("upper(uom_categories.name) like '%$uomcategory_name%'");
        $query->whereRaw("upper(uoms.name) like '%$uom_name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $uoms = $query->get();

        $data = [];
        foreach($uoms as $uom){
            $uom->no = ++$start;
			$data[] = $uom;
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
        $uomcategory_name = strtoupper($request->uomcategory_name);

        //Count Data
        $query = DB::table('uom_categories');
        $query->select('uom_categories.*');
        $query->whereRaw("upper(uomcategory_name) like '%$uomcategory_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('uom_categories');
        $query->select('uom_categories.*');
        $query->whereRaw("upper(uomcategory_name) like '%$uomcategory_name%'");
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.uom.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' 	=> 'required|unique:uoms',
            'uomcategory_id' 	=> 'required',
            'type' 	=> 'required',
            'ratio' 	=> 'required|numeric'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        // dd($validator);

        $uom = Uom::create([
            'name' 	=> $request->name,
			'uomcategory_id' 	=> $request->uomcategory_id,
			'type' 	=> $request->type,
			'ratio' 	=> $request->ratio
        ]);

        if (!$uom) {
            return response()->json([
                'status' => false,
                'message' 	=> $uom
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
            'results' 	=> route('uom.index'),
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
        $uom = Uom::with('uomcategory')->find($id);
        // dd($uom);
        if($uom){
            return view('admin.uom.edit',compact('uom'));
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
            'id' 	=> 'required',
            'name' 	=> 'required|unique:uoms,name,'.$request->id,
            'uomcategory_id' 	=> 'required',
            'type' 	=> 'required',
            'ratio' 	=> 'required|numeric'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $uom = Uom::find($request->id);
        $uom->name = $request->name;
        $uom->uomcategory_id = $request->uomcategory_id;
        $uom->type = $request->type;
        $uom->ratio = $request->ratio;
        $uom->save();
        if (!$uom) {
            return response()->json([
                'status' => false,
                'message' 	=> $uom
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> url('admin/uom'),
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
            $uom = Uom::find($id);
            $uom->delete();
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
