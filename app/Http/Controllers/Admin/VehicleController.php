<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\AssetCategory;
use App\Models\AssetSerial;
use App\Models\Maintanance;
use App\Models\AssetHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Carbon\Carbon;
use \stdClass;

class VehicleController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'vehicle'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $total = 0;
        // $employees = Employee::all();
        $query = DB::table('assets');
        $query->select('assets.employee_id','assets.pic');
        $query->where('asset_type', 'vehicle');
        $query->groupBy('assets.employee_id', 'assets.pic');
        $query->orderBy('pic', 'asc');
        $assets = $query->get();

        $query = DB::table('assets');
        $query->select('assets.driver','assets.driver_id');
        $query->where('asset_type', 'vehicle');
        $query->groupBy('assets.driver','assets.driver_id');
        $query->orderBy('driver', 'asc');
        $drivers = $query->get();

        $query = DB::table('assets');
        $query->select('assets.merk');
        $query->where('asset_type', 'vehicle');
        $query->groupBy('assets.merk');
        $merks = $query->get();

        $query = DB::table('assets');
        $query->select('assets.name');
        $query->where('asset_type', 'vehicle');
        $query->groupBy('assets.name');
        $plats = $query->get();

        $query = DB::table('asset_categories');
        $query->select('asset_categories.*',
        // DB::raw('(select sum(stock) from assets where assets.assetcategory_id = asset_categories.id) as stok'));
        DB::raw("(select sum(stock) from assets,asset_categories ac
            where ac.id = assets.assetcategory_id and 
            ac.path like '%'|| asset_categories.name ||'%' ) as stok"));
        $query->where('asset_categories.type', 'vehicle');
        $query->orderBy('path', 'asc');
        $categories = $query->get();

        $total = 0;
        foreach ($categories as $key => $value) {
            $total += (int) $value->stok;
        }

        $_arr = [];
        foreach ($categories as $key => $items) {
            $items->stok = (!$items->stok) ? $total : (int) $items->stok;
            $_arr[] = $items; 
        }
       
        return view('admin.vehicle.index', compact('assets', 'drivers', 'categories', 'merks', 'plats'));
    }
    public function readhistories(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $id = $request->id;
        $month = $request->month;
        $year = $request->year;

        // dd($id);

        //Count Data
        $query = AssetHistory::where('asset_id', $id);
        $query->whereMonth('created_at', $month);
        $query->whereYear('created_at', $year);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AssetHistory::where('asset_id', $id);
        $query->whereMonth('created_at', $month);
        $query->whereYear('created_at', $year);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('id', 'asc');
        $assets = $query->get();

        $data = [];
        foreach ($assets as $asset) {
            $asset->no = ++$start;
            $data[] = $asset;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    public function select_employee(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = Str::upper($request->name);

        // Count Data
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->whereRaw("upper(departments.name) like '%DRIVER TEAM%'");
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->whereRaw("upper(departments.name) like '%DRIVER TEAM%'");
        $query->offset($start);
        $query->limit($length);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no   = ++$start;
            $data[]         = $employee;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows'  => $data
        ], 200);
    }
    public function selectcategory(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $assetcategory_id = $request->assetcategory_id;
        $parent_id = $request->parent_id;
        $name = strtoupper($request->name);
        $title_id = $request->title_id;

        //Count Data
        $query = DB::table('asset_categories');
        $query->select(
            'asset_categories.*',
            DB::raw('(select sum(stock) from assets where assets.assetcategory_id = asset_categories.id) as asset_stock')
        );
        $query->whereRaw("upper(path) like '%$name%'");
        if ($parent_id != '') {
            $query->where('parent_id', $parent_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('asset_categories');
        $query->select(
            'asset_categories.*',
            DB::raw('(select sum(stock) from assets where assets.assetcategory_id = asset_categories.id) as asset_stock')
        );
        $query->whereRaw("upper(path) like '%$name%'");
        if ($assetcategory_id) {
            $query->where('id', $assetcategory_id);
        }
        if ($parent_id != '') {
            $query->where('parent_id', $parent_id);
        }
        $query->offset($start * $length);
        $query->limit($length);
        $query->orderBy('path', 'asc');
        $asset_categories = $query->get();

        $data = [];
        foreach ($asset_categories as $asset_category) {
            $asset_category->no = ++$start;
            $asset_category->stock = ["<span>$asset_category->path</span><span style='float:right'><i> $asset_category->asset_stock</i></span>"];
            $data[] = $asset_category;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }
    public function readmaintenance(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $vehicle_id = $request->vehicle_id;
        $month = $request->month;
        $year = $request->year;

        // dd($id);

        //Count Data
        $query = DB::table('maintanances');
        $query->select('maintanances.*', 'vehicles.name as vehicle');
        $query->leftJoin('assets as vehicles', 'vehicles.id', '=', 'maintanances.vehicle_id');
        if ($month) {
            $query->whereMonth('date', $month);
        }
        if ($year) {
            $query->whereYear('date', $year);
        }
        if ($vehicle_id) {
            $query->where('maintanances.vehicle_id', $vehicle_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('maintanances');
        $query->select('maintanances.*', 'vehicles.name as vehicle');
        $query->leftJoin('assets as vehicles', 'vehicles.id', '=', 'maintanances.vehicle_id');
        if ($month) {
            $query->whereMonth('date', $month);
        }
        if ($year) {
            $query->whereYear('date', $year);
        }
        if ($vehicle_id) {
            $query->where('maintanances.vehicle_id', $vehicle_id);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('id', 'asc');
        $assets = $query->get();

        $data = [];
        foreach ($assets as $asset) {
            $asset->no = ++$start;
            $asset->link = url($asset->image);
            $data[] = $asset;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $pic  = $request->pic;
        $vendor = strtoupper($request->vendor);
        $driver = $request->driver;
        $merk = strtoupper($request->merk);
        $categories = $request->category;
        $date_from = $request->date_from ? Carbon::parse(changeSlash($request->date_from))->endOfDay()->toDateTimeString() : '';
        $date_to = $request->date_to ? Carbon::parse(changeSlash($request->date_to))->endOfDay()->toDateTimeString() : '';

        //Count Data
        $query = Asset::with('assetcategory')->select('assets.*');
        $query->leftJoin('asset_categories', 'asset_categories.id', '=', 'assets.assetcategory_id');
        $query->where('asset_type','vehicle');
        if ($date_from && $date_to) {
            $query->whereBetween('buy_date', [$date_from, $date_to]);
        }
        if($name)
        {
            $query->whereRaw("upper(assets.name) like '%$name%'");
        }
        if($pic)
        {
            $query->whereIn('assets.employee_id', $pic);
        }
        if ($driver) {
            $query->whereIn('assets.driver_id', $driver);
        }
        if ($merk) {
            $query->whereRaw("upper(assets.merk) like '%$merk%'");
        }
        if ($categories) {
            $string = '';
            foreach ($categories as $category) {
                $string .= "asset_categories.path like '%$category%'";
                if (end($categories) != $category) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Asset::with('assetcategory')->select('assets.*');
        $query->leftJoin('asset_categories', 'asset_categories.id', '=', 'assets.assetcategory_id');
        $query->where('asset_type','vehicle');
        if ($date_from && $date_to) {
            $query->whereBetween('buy_date', [$date_from, $date_to]);
        }
        if ($name) {
            $query->whereRaw("upper(assets.name) like '%$name%'");
        }
        if ($pic) {
            $query->whereIn('assets.employee_id', $pic);
        }
        if ($driver) {
            $query->whereIn('assets.driver_id', $driver);
        }
        if ($merk) {
            $query->whereRaw("upper(assets.merk) like '%$merk%'");
        }
        if ($categories) {
            $string = '';
            foreach ($categories as $category) {
                $string .= "asset_categories.path like '%$category%'";
                if (end($categories) != $category) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $assets = $query->get();

        $data = [];
        foreach($assets as $asset){
            $asset->no = ++$start;
            $asset->image = asset($asset->image);
            $asset->category = $asset->assetcategory->name;
            $asset->buy_price = number_format($asset->buy_price,0,',','.');
			$data[] = $asset;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function draft()
    {
        $query = DB::table('asset_categories');
        $query->select('asset_categories.*');
        $query->where('parent_id', 0);
        $query->where('type', 'vehicle');
        $categories = $query->get();

        $drafts = DB::table('asset_categories as c1')
                    ->select('c1.id')
                    ->leftJoin('asset_categories as c2','c1.id', '=', 'c2.parent_id')
                    ->where('c2.parent_id','>',0)
                    ->where('c2.status', 1)
                    ->groupBy('c1.id')
                    ->get();

        return view('admin.vehicle.draft', compact('categories', 'drafts'));
    }

    public function subcat(Request $request)
    {
        $parent = $request->cat_id;

        $subcategories = AssetCategory::where('id', $parent)
                            ->with('subcategories')
                            ->get();
        // dd($subcategories);
        return response()->json([
            'subcategories' => $subcategories
        ]);
    }


    public function create(Request $request)
    {
        $query = DB::table('asset_categories');
        $query->select('asset_categories.*');
        $query->where('parent_id', 0);
        $query->where('status', 1);
        $categories = $query->get();

        $drafts = DB::table('asset_categories as c1')
                    ->select('c1.id')
                    ->leftJoin('asset_categories as c2','c1.id', '=', 'c2.parent_id')
                    ->where('c2.parent_id','>',0)
                    ->where('c2.status', 1)
                    ->groupBy('c1.id')
                    ->get();

        $license_no = $request->input('license_no');
        $category_name = $request->input('category_name');
        $asset_category_id = $request->input('asset_category_id');
        $vendors = Asset::select('vendor')->distinct()->get();
        $pics = Asset::select('pic')->distinct()->get();
        $locations = Asset::select('location')->distinct()->get();
        return view('admin.vehicle.create', compact('license_no', 'category_name', 'asset_category_id', 'categories', 'drafts','vendors','pics','locations'));
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
            'assetcategory_id'  => 'required',
            'license_no' 	    => 'required',
            'engine_no'         => 'required',
            'merk' 	            => 'required',
            'type' 	            => 'required',
            'model' 	        => 'required',
            'production_year'   => 'required',
            'manufacture'       => 'required',
            'engine_capacity'   => 'required',
            'vendor' 	        => 'required',
            'pic' 	            => 'required',
            'location' 	        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' 	=> false,
                'message' 	=> $validator->errors()->first()
            ], 400);
        }

        $asset = Asset::create([
            'asset_type'    => 'vehicle',
            'assetcategory_id'  => $request->assetcategory_id,
			'code' 	            => $request->engine_no,
			'engine_no'         => $request->engine_no,
			'name' 	            => $request->license_no,
			'license_no'        => $request->license_no,
			'merk' 	            => $request->merk,
			'type' 	            => $request->type,
			'model' 	        => $request->model,
			'production_year'   => $request->production_year,
			'manufacture'       => $request->manufacture,
			'engine_capacity'   => $request->engine_capacity,
            'pic' 	            => $request->pic,
            'driver'            => $request->driver,
            'driver_id'         => $request->driver_id,
            'employee_id'       => $request->employee_id,
			'location' 	        => $request->location,
			'buy_price' 	    => $request->buy_price,
			'vendor' 	        => $request->vendor,
			'buy_date' 	        => $request->buy_date,
			'note' 	            => $request->note,
			'image' 	        => 'img/no-image.png',
            'document' 	        => 'img/no-image.png',
            'stock'             => 1
        ]);

        $assethistory = AssetHistory::create([
            'asset_id'  =>$asset->id,
            'pic' 	    => $asset->pic,
            'location'  => $asset->location,
            'stock'     => $asset->stock,
            'driver'     => $asset->driver
        ]);
        $image = $request->file('image');

        if($image){
            $filename = 'foto.'. $request->image->getClientOriginalExtension();
            $src = 'assets/asset/'.$asset->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $image->move($src,$filename);
            $asset->image = $src.'/'.$filename;
            $asset->save();
        }

        $document = $request->file('document');

        if($document){
            $filename = 'document.'. $request->document->getClientOriginalExtension();
            $src = 'assets/asset/'.$asset->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $document->move($src,$filename);
            $asset->document = $src.'/'.$filename;
            $asset->save();
        }


        if (!$asset) {
            return response()->json([
                'status' => false,
                'message' 	=> $asset
            ], 400);
        }

        return response()->json([
            'status' 	=> true,
            'results' 	=> route('vehicle.index'),
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
        $asset = Asset::with('assetcategory')->findOrFail($id);

        return view('admin.vehicle.detail', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $query = DB::table('asset_categories');
        $query->select('asset_categories.*');
        $query->where('parent_id', 0);
        $query->where('status', 1);
        $categories = $query->get();

        $drafts = DB::table('asset_categories as c1')
                    ->select('c1.id')
                    ->leftJoin('asset_categories as c2','c1.id', '=', 'c2.parent_id')
                    ->where('c2.parent_id','>',0)
                    ->where('c2.status', 1)
                    ->groupBy('c1.id')
                    ->get();

        $asset = Asset::with('assetcategory','employee','drivers')->findOrFail($id);
        //dd($asset);
        $vendors = Asset::select('vendor')->distinct()->get();
        $pics = Asset::select('pic')->distinct()->get();
        $locations = Asset::select('location')->distinct()->get();
        return view('admin.vehicle.edit',compact('asset', 'categories', 'drafts','vendors','pics','locations'));
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
            'assetcategory_id'  => 'required',
            'license_no' 	    => 'required',
            'engine_no'         => 'required',
            'merk' 	            => 'required',
            'type' 	            => 'required',
            'model' 	        => 'required',
            'production_year'   => 'required',
            'manufacture'       => 'required',
            'engine_capacity'   => 'required',
            'vendor' 	        => 'required',
            'pic' 	            => 'required',
            'location' 	        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' 	=> false,
                'message' 	=> $validator->errors()->first()
            ], 400);
        }

        $asset = Asset::find($id);
        $pic      = $asset->pic;
        $location = $asset->location;
        $stock    = $asset->stock;
        $asset->assetcategory_id    = $request->assetcategory_id;
        $asset->license_no          = $request->license_no;
        $asset->code                = $request->engine_no;
        $asset->engine_no           = $request->engine_no;
        $asset->name                = $request->license_no;
        $asset->merk                = $request->merk;
        $asset->type                = $request->type;
        $asset->model               = $request->model;
        $asset->production_year     = $request->production_year;
        $asset->manufacture         = $request->manufacture;
        $asset->engine_capacity     = $request->engine_capacity;
        $asset->buy_date            = $request->buy_date;
        $asset->buy_price           = $request->buy_price;
        $asset->vendor              = $request->vendor;
        $asset->pic                 = $request->pic;
        $asset->driver              = $request->driver;
        $asset->note                = $request->note;
        $asset->location            = $request->location;
        $asset->driver_id           = $request->driver_id;
        $asset->employee_id         = $request->employee_id;
        $asset->save();
        if($location != $asset->location || $pic != $asset->pic || $stock != $asset->stock){
            $assethistory = AssetHistory::create([
                'asset_id'  =>$asset->id,
                'pic' 	    => $asset->pic,
                'location'  => $asset->location,
                'stock'     => $asset->stock,
                'driver'     => $asset->driver
            ]);
        }
        $image = $request->file('image');
        if($image){
            $filename = 'foto.'. $request->image->getClientOriginalExtension();
            if(file_exists($asset->image) && $asset->image != 'img/no-image.png'){
                unlink($asset->image);
            }

            $src = 'assets/asset/'.$asset->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $image->move($src,$filename);
            $asset->image = $src.'/'.$filename;
            $asset->save();
        }
        $document = $request->file('document');
        if($document){
            $filename = 'document.'. $request->document->getClientOriginalExtension();
            if(file_exists($asset->document) && $asset->document != 'img/no-image.png'){
                unlink($asset->document);
            }

            $src = 'assets/asset/'.$asset->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $document->move($src,$filename);
            $asset->document = $src.'/'.$filename;
            $asset->save();
        }
        if (!$asset) {
            return response()->json([
                'status' => false,
                'message' 	=> $asset
            ], 400);
        }

        return response()->json([
            'status' 	=> true,
            'results' 	=> route('vehicle.index'),
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
            $asset = Asset::find($id);
            $asset->delete();
            if(file_exists($asset->image)){
                unlink($asset->image);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }

    public function import()
    {
        return view('admin.asset.import');
    }

    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'         => 'required|mimes:xlsx'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $file = $request->file('file');
        try {
            $filetype     = \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch (\Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
        $data     = [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $category = strtoupper($sheet->getCellByColumnAndRow(0, $row)->getValue());
            $code = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $name = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $pic = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $location = $sheet->getCellByColumnAndRow(4, $row)->getValue();
            $buy_price = $sheet->getCellByColumnAndRow(5, $row)->getValue();
            $vendor = $sheet->getCellByColumnAndRow(6, $row)->getValue();
            $buy_date = $sheet->getCellByColumnAndRow(7, $row)->getValue();
            $note = $sheet->getCellByColumnAndRow(8, $row)->getValue();
            $stock = $sheet->getCellByColumnAndRow(9, $row)->getValue();
            $category = AssetCategory::whereRaw("upper(name) like '%$category%'")->get()->first();
            $data[] = array(
                'index' => $no,
                'category' => $category ? $category->name :null,
                'category_id' => $category ? $category->id : null,
                'code' => $code,
                'name' => $name,
                'pic' => $pic,
                'location' => $location,
                'buy_price' => $buy_price,
                'vendor' => $vendor,
                'buy_date' => $buy_date,
                'note' => $note,
                'stock' => $stock,
            );
            $no++;

            // dd($assetcategory);
        }
        return response()->json([
            'status'     => true,
            'data'     => $data
        ], 200);
    }

    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asset'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $assets = json_decode($request->asset);
        foreach ($assets as $asset) {
            if ($asset->category_id != null) {
                    $asset = Asset::create([
                        'asset_type'    => 'other',
                        'assetcategory_id' => $asset->category_id,
                        'code' => $asset->code,
                        'name' => $asset->name,
                        'pic' => $asset->pic,
                        'location' => $asset->location,
                        'buy_price' => $asset->buy_price,
                        'vendor' => $asset->vendor,
                        'buy_date' => $asset->buy_date,
                        'note' => $asset->note,
                        'stock' => $asset->stock,
                        'image' => 'img/no-image.png',
                       'document' => 'img/no-image.png',
                    ]);
                    $assethistory = AssetHistory::create([
                        'asset_id'=>$asset->id,
                        'pic' 	        => $asset->pic?$asset->pic:'Not Set',
                        'location' 	        => $asset->location?$asset->location:'Not Set',
                        'stock'          => $asset->stock?$asset->stock:0
                    ]);
                    // $prod->image = json_encode(['assets/asset/no-image.jpg']);
                    // $prod->save();

            }
        }

        return response()->json([
            'status'     => true,
            'results'     => route('asset.index'),
        ], 200);
    }

    public function exportmaintenance(Request $request)
    {
        $id = $request->vehicle_id;
        $month = $request->month_histories;
        $year = $request->year_histories;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $maintenances = Maintanance::where('vehicle_id', $id)->whereMonth('date', $month)->whereYear('date', $year)->get();

        $sheet->setCellValue('A1', 'No')->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('B1', 'Vehicle')->getStyle('B1')->getFont()->setBold(true);
        $sheet->setCellValue('C1', 'Date')->getStyle('C1')->getFont()->setBold(true);
        $sheet->setCellValue('D1', 'KM')->getStyle('D1')->getFont()->setBold(true);
        $sheet->setCellValue('E1', 'Driver')->getStyle('E1')->getFont()->setBold(true);
        $sheet->setCellValue('F1', 'Total')->getStyle('F1')->getFont()->setBold(true);

        $row_number = 2;
        foreach ($maintenances as $key => $value) {
            $sheet->setCellValue('A' . $row_number, ++$key);
            $sheet->setCellValue('B' . $row_number, $value->vehicle_name);
            $sheet->setCellValue('C' . $row_number, $value->date);
            $sheet->setCellValue('D' . $row_number, $value->km);
            $sheet->setCellValue('E' . $row_number, $value->driver);
            $sheet->setCellValue('F' . $row_number, $value->total);
            $row_number++;
        }

        foreach (range('A', 'F') as $key => $value) {
            $sheet->getColumnDimension($value)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($maintenances->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-maintenance-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Maintenance Data",
                'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
            ], 200);
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
        }
    }

    public function exporthistory(Request $request)
    {
        $id = $request->history_id;
        $month = $request->month;
        $year = $request->year;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $historys = AssetHistory::where('asset_id', $id)->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();

        $sheet->setCellValue('A1', 'No')->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('B1', 'Vehicle')->getStyle('B1')->getFont()->setBold(true);
        $sheet->setCellValue('C1', 'PIC')->getStyle('C1')->getFont()->setBold(true);
        $sheet->setCellValue('D1', 'Driver')->getStyle('D1')->getFont()->setBold(true);
        $sheet->setCellValue('E1', 'Location')->getStyle('E1')->getFont()->setBold(true);
        $sheet->setCellValue('F1', 'Stock')->getStyle('F1')->getFont()->setBold(true);
        $sheet->setCellValue('G1', 'Date')->getStyle('G1')->getFont()->setBold(true);

        $row_number = 2;
        foreach ($historys as $key => $value) {
            $sheet->setCellValue('A' . $row_number, ++$key);
            $sheet->setCellValue('B' . $row_number, $value->asset->name);
            $sheet->setCellValue('C' . $row_number, $value->pic);
            $sheet->setCellValue('D' . $row_number, $value->driver);
            $sheet->setCellValue('E' . $row_number, $value->location);
            $sheet->setCellValue('F' . $row_number, $value->stock);
            $sheet->setCellValue('G' . $row_number, $value->created_at);
            $row_number++;
        }

        foreach (range('A', 'F') as $key => $value) {
            $sheet->getColumnDimension($value)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($historys->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-history-vehicle-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download History Vehicle Data",
                'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
            ], 200);
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
        }
    }
}
