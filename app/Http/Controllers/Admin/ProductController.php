<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'product'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.product.index');
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
        $query = DB::table('products');
        $query->select('products.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('products');
        $query->select('products.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $products = $query->get();

        $data = [];
        foreach($products as $product){
            $product->no = ++$start;
			$data[] = $product;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function selectCategory(Request $request){
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
        $categories = $query->get();

        $data = [];
        foreach($categories as $category){
            $category->no = ++$start;
			$data[] = $category;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }

    public function selectUom(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('uoms');
        $query->select('uoms.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('uoms');
        $query->select('uoms.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $uoms = $query->get();

        $data = [];
        foreach($uoms as $uom){
            $uom->no = ++$start;
			$data[] = $uom;
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
    public function draft()
    {
        $query = DB::table('product_categories');
        $query->select('product_categories.*');
        $query->where('parent', 0);
        // $query->where('status', 1);
        $categories = $query->get();

        // SELECT x.name
        // FROM product_categories x
        // JOIN product_categories y
        //   ON x.id = y.parent

        $drafts = DB::table('product_categories as c1')
                    ->select('c1.id')
                    ->leftJoin('product_categories as c2','c1.id', '=', 'c2.parent')
                    ->where('c2.parent','>',0)
                    // ->where('c2.status',2)
                    ->groupBy('c1.id')
                    ->get();
        // $drafts2 = DB::table('product_categories as c1')
        //             ->select('c1.id')
        //             ->leftJoin('product_categories as c2','c1.id', '=', 'c2.parent')
        //             // ->where('c2.parent','>',0)
        //             ->where('c2.status',3)
        //             ->groupBy('c1.id')
        //             ->get();

        // dd($drafts2);

        // $query2 = DB::table('product_categories');
        // $query2->select('product_categories.*');
        // $query2->where('status', 2);
        // $categories2 = $query2->get();


        return view('admin.product.draft', compact('categories', 'drafts'));
    }

    // public function draft2()
    // {
    //     $drafts2 = DB::table('product_categories as c1')
    //                 ->select('c1.id')
    //                 ->leftJoin('product_categories as c2','c1.id', '=', 'c2.parent')
    //                 // ->where('c2.parent','>',0)
    //                 ->where('c1.status',3)
    //                 ->groupBy('c1.id')
    //                 ->get();

    //     return response()->json([
    //         $drafts2
    //     ]);
    // }

    public function subcat(Request $request)
    {
        $parent = $request->cat_id;

        $subcategories = ProductCategory::where('id', $parent)
                            ->with('subcategories')
                            ->get();

        return response()->json([
            'subcategories' => $subcategories
        ]);
    }

    public function create(Request $request)
    {
        $name = $request->input('name');
        $category_name = $request->input('category_name');
        $product_category_id = $request->input('product_category_id');

        return view('admin.product.create', compact('name', 'category_name', 'product_category_id'));
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
            'productcategory_id' 	=> 'required',
            'uom_id' 	            => 'required',
            'type' 	                => 'required',
            'name' 	                => 'required',
            'description' 	        => 'required',
            'merk' 	                => 'required',
            'price' 	            => 'required',
            'weight' 	            => 'required',
            'volume_l' 	            => 'required',
            'volume_p' 	            => 'required',
            'volume_t' 	            => 'required',
            'condition' 	        => 'required',
            'sku' 	                => 'required',
            'barcode' 	            => 'required',
            'minimum_qty' 	        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' 	=> false,
                'message' 	=> $validator->errors()->first()
            ], 400);
        }

        $product = Product::create([
            'productcategory_id' 	=> $request->productcategory_id,
			'uom_id' 	            => $request->uom_id,
			'type' 	                => $request->type,
			'name' 	                => $request->name,
			'description' 	        => $request->description,
            'best_product' 	        => $request->best_product?1:0,
			'merk' 	                => $request->merk,
			'price' 	            => $request->price,
			'weight' 	            => $request->weight,
			'volume_l' 	            => $request->volume_l,
			'volume_p' 	            => $request->volume_p,
			'volume_t' 	            => $request->volume_t,
			'condition' 	        => $request->condition,
			'sku' 	                => $request->sku,
			'barcode' 	            => $request->barcode,
			'minimum_qty' 	        => $request->minimum_qty,
        ]);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' 	=> $product
            ], 400);
        }

        // $image = $request->file('image');
        if($request->hasfile('image')){
            if(file_exists($product->image)){
                unlink($product->image);
            }
            foreach($request->file('image') as $image)
            {
            $rd = Str::random(5);
            $path = 'assets/product/';
                $image->move($path, $rd.'.'.$image->getClientOriginalExtension());
                $filename = $path.$rd.'.'.$image->getClientOriginalExtension();
                $data[] = $filename;
            }
            $product->image = $data?json_encode($data):'';
            $product->save();
        }

        return response()->json([
            'status' 	=> true,
            'results' 	=> route('product.index'),
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
        $product = Product::with('uom', 'productcategory')->findOrFail($id);
        $img = json_decode($product->image);

        return view('admin.product.detail', compact('product', 'img'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::with('uom','productcategory')->find($id);
        $img = json_decode($product->image);
        // dd($img);
        if($product){
            return view('admin.product.edit',compact('product', 'img'));
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
            'productcategory_id' 	=> 'required',
            'uom_id' 	            => 'required',
            'type' 	                => 'required',
            'name' 	                => 'required',
            'description' 	        => 'required',
            'merk' 	                => 'required',
            'price' 	            => 'required',
            'weight' 	            => 'required',
            'volume_l' 	            => 'required',
            'volume_p' 	            => 'required',
            'volume_t' 	            => 'required',
            'condition' 	        => 'required',
            'sku' 	                => 'required',
            'barcode' 	            => 'required',
            'minimum_qty' 	        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' 	=> false,
                'message' 	=> $validator->errors()->first()
            ], 400);
        }

        $product = Product::find($id);
        $product->productcategory_id = $request->productcategory_id;
        $product->uom_id             = $request->uom_id;
        $product->type               = $request->type;
        $product->name               = $request->name;
        $product->description        = $request->description;
        $product->best_product       = $request->best_product;
        $product->merk               = $request->merk;
        $product->price              = $request->price;
        $product->weight             = $request->weight;
        $product->volume_l           = $request->volume_l;
        $product->volume_p           = $request->volume_p;
        $product->volume_t           = $request->volume_t;
        $product->condition          = $request->condition;
        $product->sku                = $request->sku;
        $product->barcode            = $request->barcode;
        $product->minimum_qty        = $request->minimum_qty;

        // dd($product);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' 	=> $product
            ], 400);
        }

        // $image = $request->file('image');
        if($request->hasfile('image')){
            // $dt = Carbon::now();
            $rd = Str::random(5);
            $path = 'assets/product/';
            // $image->move($path, $rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension());
            // $filename = $path.$rd.'.'.$dt->format('Y-m-d').'.'.$image->getClientOriginalExtension();
            foreach($request->file('image') as $image)
            {
                $image->move($path, $rd.'.'.$image->getClientOriginalExtension());
                $filename = $path.$rd.'.'.$image->getClientOriginalExtension();
                $data[] = $filename;
            }
            $product->image = $data?json_encode($data):'';
            $product->save();
        }

        return response()->json([
            'status' 	=> true,
            'results' 	=> route('product.index'),
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
            $product = Product::find($id);
            $product->delete();
            if(file_exists($product->image)){
                unlink($product->image);
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
