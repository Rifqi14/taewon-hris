<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'productcategory'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.productcategory.index');
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $path = strtoupper($request->path);
        $status = $request->status?true:false;

        //Count Data
        $query = DB::table('product_categories');
        $query->select('product_categories.*');
        $query->whereRaw("upper(product_categories.path) like '%$path%'");
        $query->where('product_categories.status',$status);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('product_categories');
        $query->select('product_categories.*');
        $query->whereRaw("upper(product_categories.path) like '%$path%'");
        $query->where('product_categories.status',$status);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $productcategories = $query->get();

        $data = [];
        foreach($productcategories as $productcategory){
            $productcategory->no = ++$start;
            $productcategory->path = str_replace('->',' <i class="fas fa-angle-right"></i> ',$productcategory->path);
			$data[] = $productcategory;
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
        $query = DB::table('product_categories');
        $query->select('product_categories.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('product_categories');
        $query->select('product_categories.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $product_categories = $query->get();

        $data = [];
        foreach($product_categories as $product_category){
            $product_category->no = ++$start;
			$data[] = $product_category;
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
        return view('admin.productcategory.create');
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
            'name'   => 'required',
            'description'   => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

    
        $productcategory = ProductCategory::create([
            'parent'      => $request->parent?$request->parent:0,
            'name'        => $request->name,
            'description' => $request->description,
            'display'     => $request->display?1:0,
            'picture'     =>'',
            'status'      => 1
        ]);
        $picture = $request->file('picture');
        if($picture){    
            $filename = 'foto.'. $request->picture->getClientOriginalExtension();
            $src = 'assets/productcategory/'.$productcategory->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $picture->move($src,$filename);
            $productcategory->picture = $src.'/'.$filename;
            $productcategory->save();
        }
        
        $productcategory->path = implode(' -> ',$this->createPath($productcategory->id,[]));
        $productcategory->save();
        if (!$productcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $productcategory
            ], 400);
        }
        return response()->json([
            'status' => true,
            'results' 	=> route('productcategory.index')
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function show(ProductCategory $productCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $productcategory = ProductCategory::with('parents')->find($id);
        if($productcategory){
            return view('admin.productcategory.edit',compact('productcategory'));
        }
        else{
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' 	    => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $productcategory = ProductCategory::find($id);
        $productcategory->name = $request->name;
        $productcategory->description = $request->description;
        $productcategory->display = $request->display?1:0;
        $productcategory->save();
        $productcategory->path = implode(' -> ',$this->createPath($id,[]));
        $productcategory->save();
        $this->updatePath($id);
        $picture = $request->file('picture');
        if($picture){  
            $filename = 'foto.'. $request->picture->getClientOriginalExtension(); 
            if(file_exists($productcategory->picture)){
                unlink($productcategory->picture);
            } 
            
            $src = 'assets/productcategory/'.$productcategory->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $picture->move($src,$filename);
            $productcategory->picture = $src.'/'.$filename;
            $productcategory->save();
        }

        if (!$productcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $productcategory
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('productcategory.index'),
        ], 200);
    }
    public function archive($id){
        // echo"arsip";
        $productcategory = ProductCategory::find($id);
        $productcategory->update(['status' =>'0']);
        $productcategory->status = '0';
        $productcategory->save();

        if (!$productcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $productcategory
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('productcategory.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ProductCategory  $productCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         try {
            $productcategory = ProductCategory::find($id);
            if(file_exists($productcategory->picture)){
                unlink($productcategory->picture);
            } 
            $productcategory->delete();
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

    function createPath($id,$path){
        $productcategory = ProductCategory::find($id);
        array_unshift($path,$productcategory->name);
        if($productcategory->parent){
            return $this->createPath($productcategory->parent,$path);
        }
        return $path;
    }

    function updatePath($id){
        $productcategoies = ProductCategory::where('parent',$id)->get();
        foreach($productcategoies as $productcategory){
            $productcategory->path = implode(' -> ',$this->createPath($productcategory->id,[]));
            $productcategory->save();
            $this->updatePath($productcategory->id);
        }
    }
}
