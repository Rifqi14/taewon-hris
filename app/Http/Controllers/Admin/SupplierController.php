<?php

namespace App\Http\Controllers\Admin;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'supplier'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.supplier.index');
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
        $query = DB::table('suppliers');
        $query->select('suppliers.*');
        $query->whereRaw("upper(suppliers.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('suppliers');
        $query->select('suppliers.*');
        $query->whereRaw("upper(suppliers.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $suppliers = $query->get();

        $data = [];
        foreach($suppliers as $supplier){
            $supplier->no = ++$start;
            // $partner->category = $category[$partner->category];
			$data[] = $supplier;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.supplier.create');
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
            'name'      => 'required',
            'email'     => 'required|email',
            'phone'     => 'required',
            'address'   => 'required',
            'picture'   => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        
        
        $supplier = Supplier::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'address'   => $request->address,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'picture'   =>''
            ]);
        $picture = $request->file('picture');
        if($picture){    
            $filename = 'foto.'. $request->picture->getClientOriginalExtension();
            $src = 'assets/supplier/'.$supplier->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $picture->move($src,$filename);
            $supplier->picture = $src.'/'.$filename;
            $supplier->save();
        }
        
        if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' 	=> $supplier
            ], 400);
        }
        if ($supplier) {
            return response()->json([
                'status' => true,
                'results' 	=> route('supplier.index')
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $supplier = Supplier::find($id);
        if($supplier){
            return view('admin.supplier.edit',compact('supplier'));
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
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email',
            'phone'     => 'required',
            'address'   => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        
        $supplier = Supplier::find($id);
        $supplier->name = $request->name;
        $supplier->email = $request->email;
        $supplier->phone = $request->phone;
        $supplier->address = $request->address;
        $supplier->latitude = $request->latitude;
        $supplier->longitude = $request->longitude;
        $supplier->save();

        $picture = $request->file('picture');
        if($picture){  
            $filename = 'foto.'. $request->picture->getClientOriginalExtension(); 
            if(file_exists($supplier->picture)){
                unlink($supplier->picture);
            } 
            
            $src = 'assets/supplier/'.$supplier->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $picture->move($src,$filename);
            $supplier->picture = $src.'/'.$filename;
            $supplier->save();
        }

         if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' 	=> $supplier
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('supplier.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $supplier = Supplier::find($id);
            if(file_exists($supplier->picture)){
                unlink($supplier->picture);
            } 
            $supplier->delete();
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
