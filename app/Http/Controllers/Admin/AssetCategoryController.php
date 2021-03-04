<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AssetCategoryController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'assetcategory'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.assetcategory.index');
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
        $query = DB::table('asset_categories');
        $query->select(
            'asset_categories.*',
            DB::raw("(select sum(stock) from assets,asset_categories ac
            where ac.id = assets.assetcategory_id and 
            ac.path like '%'|| asset_categories.name ||'%' ) as asset_stock")
        );
        $query->whereRaw("upper(asset_categories.path) like '%$path%'");
        $query->where('asset_categories.status',$status);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('asset_categories');
        $query->select(
            'asset_categories.*',
            DB::raw("(select sum(stock) from assets,asset_categories ac
            where ac.id = assets.assetcategory_id and 
            ac.path like '%'|| asset_categories.name ||'%' ) as asset_stock")
        );
        $query->whereRaw("upper(asset_categories.path) like '%$path%'");
        $query->where('asset_categories.status',$status);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('path', $dir);
        $assetcategories = $query->get();

        $data = [];
        foreach($assetcategories as $assetcategory){
            $assetcategory->no = ++$start;
            $assetcategory->path = str_replace('->',' <i class="fas fa-angle-right"></i> ',$assetcategory->path);
            $assetcategory->asset_stock = number_format($assetcategory->asset_stock *1,0,',','.'); 
			$data[] = $assetcategory;
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
        $query = DB::table('asset_categories');
        $query->select('asset_categories.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('asset_categories');
        $query->select('asset_categories.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $asset_categories = $query->get();

        $data = [];
        foreach($asset_categories as $asset_category){
            $asset_category->no = ++$start;
			$data[] = $asset_category;
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
        return view('admin.assetcategory.create');
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
            'name'          => 'required',
            'description'   => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $assetcategory = AssetCategory::create([
            'parent_id'   => $request->parent_id?$request->parent_id:0,
            'name'        => $request->name,
            'description' => $request->description,
            'display'     => $request->display?1:0,
            'type'        => $request->type,
            'picture'     =>'',
            'status'      => 1
        ]);
        $picture = $request->file('picture');

        if($picture){
            $filename = 'foto.'. $request->picture->getClientOriginalExtension();
            $src = 'assets/assetcategory/'.$assetcategory->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $picture->move($src,$filename);
            $assetcategory->picture = $src.'/'.$filename;
            $assetcategory->save();
        }

        $assetcategory->path = implode(' -> ',$this->createPath($assetcategory->id,[]));
        $assetcategory->children = $this->createChildren($assetcategory->id,0);
        $assetcategory->save();
        $this->updateChildren($assetcategory->id);
        if (!$assetcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $assetcategory
            ], 400);
        }
        return response()->json([
            'status' => true,
            'results' 	=> route('assetcategory.index')
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
        $assetcategory = AssetCategory::with('parent')->findOrfail($id);

        return view('admin.assetcategory.edit', compact('assetcategory'));
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
            'name' 	      => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $assetcategory = AssetCategory::find($id);
        $assetcategory->name        = $request->name;
        $assetcategory->description = $request->description;
        $assetcategory->display     = $request->display?1:0;
        $assetcategory->type     = $request->type;
        $assetcategory->save();

        $assetcategory->path = implode(' -> ',$this->createPath($id,[]));
        $assetcategory->children = $this->createChildren($id,0);
        $assetcategory->save();

        $this->updatePath($id);
        $this->updateChildren($id);
        $picture = $request->file('picture');
        if($picture){
            $filename = 'foto.'. $request->picture->getClientOriginalExtension();
            if(file_exists($assetcategory->picture)){
                unlink($assetcategory->picture);
            }

            $src = 'assets/assetcategory/'.$assetcategory->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $picture->move($src,$filename);
            $assetcategory->picture = $src.'/'.$filename;
            $assetcategory->save();
        }

        if (!$assetcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $assetcategory
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('assetcategory.index'),
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
            $assetcategory = AssetCategory::find($id);
            if(file_exists($assetcategory->picture)){
                unlink($assetcategory->picture);
            }
            $assetcategory->delete();
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

    public function archive($id){
        // echo"arsip";
        $assetcategory = AssetCategory::find($id);
        $assetcategory->update(['status' =>'0']);
        $assetcategory->status = '0';
        $assetcategory->save();

        if (!$assetcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $assetcategory
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'message' 	=> 'Data Berhasil Diarsipkan',
        ], 200);
    }


    public function nonarchive($id){
        // echo"arsip";
        $assetcategory = AssetCategory::find($id);
        $assetcategory->update(['status' =>'1']);
        $assetcategory->status = '1';
        $assetcategory->save();

        if (!$assetcategory) {
            return response()->json([
                'status' => false,
                'message' 	=> $assetcategory
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'message' 	=> 'Data Kembali Ditampilkan',
        ], 200);
    }

    function createPath($id,$path){
        $assetcategory = AssetCategory::find($id);
        array_unshift($path,$assetcategory->name);
        if($assetcategory->parent_id){
            return $this->createPath($assetcategory->parent_id,$path);
        }
        return $path;
    }

    function updatePath($id){
        $assetcategories = AssetCategory::where('parent_id',$id)->get();
        foreach($assetcategories as $assetcategory){
            $assetcategory->path = implode(' -> ',$this->createPath($assetcategory->id,[]));
            $assetcategory->save();
            $this->updatePath($assetcategory->id);
        }
    }
    function createChildren($id,$children){
		$assetcategories = AssetCategory::where('parent_id',$id)->get();
        foreach($assetcategories as $assetcategory){
			$children = $children + $assetcategories->count();
            return $this->createChildren($assetcategory->id,$children);
        }
        return $children;
	}
	
	function updateChildren($id){
        $assetcategories = AssetCategory::where('id',$id)->get();
        foreach($assetcategories as $assetcategory){
            $assetcategory->children = $this->createChildren($assetcategory->id,0);
            $assetcategory->save();
            $this->updateChildren($assetcategory->parent_id);
        }
	}
}
