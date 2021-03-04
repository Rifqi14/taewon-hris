<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\AssetCategory;
use App\Models\AssetSerial;
use App\Models\AssetHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use \stdClass;

class AssetController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'asset'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $total = 0;
        // $assets = Asset::all();
        $query = DB::table('assets');
        $query->select('assets.employee_id','assets.pic');
        $query->where('asset_type', 'other');
        $query->groupBy('assets.employee_id', 'assets.pic');
        $query->orderBy('pic', 'asc');
        $assets = $query->get();

        $query = DB::table('assets');
        $query->select('assets.name');
        $query->where('asset_type', 'other');
        $query->groupBy('assets.name');
        $query->orderBy('name', 'asc');
        $asset_names = $query->get();

        $query = DB::table('assets');
        $query->select('assets.location');
        $query->where('asset_type', 'other');
        $query->groupBy('assets.location');
        $query->orderBy('location', 'asc');
        $locations = $query->get();

        $query = DB::table('assets');
        $query->select('assets.vendor');
        $query->where('asset_type', 'other');
        $query->groupBy('assets.vendor');
        $query->orderBy('vendor', 'asc');
        $vendors = $query->get();

        $query = DB::table('assets');
        $query->select('assets.code');
        $query->where('asset_type', 'other');
        $query->groupBy('assets.code');
        $query->orderBy('code', 'asc');
        $codes = $query->get();
        
        $query = DB::table('asset_categories');
        $query->select('asset_categories.*',
        // DB::raw('(select sum(stock) from assets where assets.assetcategory_id = asset_categories.id) as stok'));
        DB::raw("(select sum(stock) from assets,asset_categories ac
            where ac.id = assets.assetcategory_id and 
            ac.path like '%'|| asset_categories.name ||'%' ) as stok"));
        $query->where('asset_categories.type', 'asset');
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

        return view('admin.asset.index', compact('assets', 'categories','locations', 'asset_names','vendors','codes'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $asset_name = strtoupper($request->asset_name);
        $categories = $request->category;
        $pic = $request->pic;
        $code = $request->code;
        $vendor = $request->vendor;
        $location = $request->location;
        $date_from = $request->date_from ? Carbon::parse(changeSlash($request->date_from))->endOfDay()->toDateTimeString() : '';
        $date_to = $request->date_to ? Carbon::parse(changeSlash($request->date_to))->endOfDay()->toDateTimeString() : '';


        //Count Data
        $query = Asset::with('assetcategory')->select('assets.*');
        $query->leftJoin('asset_categories', 'asset_categories.id', '=', 'assets.assetcategory_id');
        $query->where('asset_type', 'other');

        if ($date_from && $date_to) {
            $query->whereBetween('buy_date', [$date_from, $date_to]);
        }
        if ($asset_name) {
            $query->whereRaw("upper(assets.name) like '%$asset_name%'");
        }
        if ($code) {
            $query->whereRaw("upper(assets.code) like '%$code%'");
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
        if ($pic) {
            $query->whereIn("assets.pic", $pic);
        }
        if ($vendor) {
            $query->whereIn("assets.vendor", $vendor);
        }
        if ($location) {
            $query->whereIn("assets.location", $location);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Asset::with('assetcategory')->select('assets.*');
        $query->leftJoin('asset_categories', 'asset_categories.id', '=', 'assets.assetcategory_id');
        $query->where('asset_type', 'other');

        if ($date_from && $date_to) {
            $query->whereBetween('buy_date', [$date_from, $date_to]);
        }
        if ($asset_name) {
            $query->whereRaw("upper(assets.name) like '%$asset_name%'");
        }
        if ($code) {
            $query->whereRaw("upper(assets.code) like '%$code%'");
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
        if ($pic) {
            $query->whereIn("assets.pic", $pic);
        }
        if ($vendor) {
            $query->whereIn("assets.vendor", $vendor);
        }
        if ($location) {
            $query->whereIn("assets.location", $location);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $assets = $query->get();

        $data = [];
        foreach ($assets as $asset) {
            $asset->no = ++$start;
            $asset->image = asset($asset->image);
            $asset->category = $asset->assetcategory->name;
            $asset->buy_price = number_format($asset->buy_price, 0, ',', '.');
            $asset->stock = number_format($asset->stock, 0, ',', '.');
            $data[] = $asset;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        //Count Data
        $query = DB::table('assets');
        $query->select('assets.*');
        $query->where('asset_type', 'other');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('assets');
        $query->select('assets.*');
        $query->where('asset_type', 'other');
        $query->offset($start);
        $query->limit($length);
        $assetss = $query->get();

        $data = [];
        foreach ($assetss as $assets) {
            $assets->no = ++$start;
            $data[] = $assets;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
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
            DB::raw("(select sum(stock) from assets,asset_categories ac
            where ac.id = assets.assetcategory_id and 
            ac.path like '%'|| asset_categories.name ||'%' ) as asset_stock")
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
            DB::raw("(select sum(stock) from assets,asset_categories ac
            where ac.id = assets.assetcategory_id and 
            ac.path like '%'|| asset_categories.name ||'%' ) as asset_stock")
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
            $asset_category->stock = ["<span>$asset_category->path</span><span style='float:right'><i> " . ($asset_category->asset_stock * 1) . "</i></span>"];
            $data[] = $asset_category;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function draft()
    {
        $query = DB::table('asset_categories');
        $query->select('asset_categories.*');
        $query->where('parent_id', 0);
        $query->where('type', 'asset');
        $categories = $query->get();

        $drafts = DB::table('asset_categories as c1')
            ->select('c1.id')
            ->leftJoin('asset_categories as c2', 'c1.id', '=', 'c2.parent_id')
            ->where('c2.parent_id', '>', 0)
            // ->where('c2.status', 1)
            ->groupBy('c1.id')
            ->get();

        return view('admin.asset.draft', compact('categories', 'drafts'));
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
            ->leftJoin('asset_categories as c2', 'c1.id', '=', 'c2.parent_id')
            ->where('c2.parent_id', '>', 0)
            ->where('c2.status', 1)
            ->groupBy('c1.id')
            ->get();

        $name = $request->input('name');
        $category_name = $request->input('category_name');
        $asset_category_id = $request->input('asset_category_id');
        $vendors = Asset::select('vendor')->distinct()->get();
        $pics = Asset::select('pic')->distinct()->get();
        $locations = Asset::select('location')->distinct()->get();
        return view('admin.asset.create', compact('name', 'category_name', 'asset_category_id', 'categories', 'drafts', 'vendors', 'pics', 'locations'));
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
            'assetcategory_id'     => 'required',
            'code'                 => 'required',
            'name'                 => 'required',
            'buy_date'             => 'required',
            'buy_price'         => 'required',
            'stock'             => 'required',
            'vendor'             => 'required',
            'pic'                 => 'required',
            'location'             => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $asset = Asset::create([
            'asset_type'        => 'other',
            'assetcategory_id'     => $request->assetcategory_id,
            'code'                 => $request->code,
            'name'           => $request->name,
            'pic'           => $request->pic,
            'location'       => $request->location,
            'buy_price'   => $request->buy_price,
            'vendor'       => $request->vendor,
            'buy_date'       => $request->buy_date,
            'note'           => $request->note,
            'image'       => 'img/no-image.png',
            'document'       => 'img/no-image.png',
            'stock'       => $request->stock,
            // 'employee_id' => $request->employee_id
        ]);

        $assethistory = AssetHistory::create([
            'asset_id' => $asset->id,
            'pic'             => $asset->pic,
            'location'             => $asset->location,
            'stock'          => $asset->stock
        ]);
        $image = $request->file('image');

        if ($image) {
            $filename = 'foto.' . $request->image->getClientOriginalExtension();
            $src = 'assets/asset/' . $asset->id;
            if (!file_exists($src)) {
                mkdir($src, 0777, true);
            }
            $image->move($src, $filename);
            $asset->image = $src . '/' . $filename;
            $asset->save();
        }

        $document = $request->file('document');

        if ($document) {
            $filename = 'document.' . $request->document->getClientOriginalExtension();
            $src = 'assets/asset/' . $asset->id;
            if (!file_exists($src)) {
                mkdir($src, 0777, true);
            }
            $document->move($src, $filename);
            $asset->document = $src . '/' . $filename;
            $asset->save();
        }


        if (!$asset) {
            return response()->json([
                'status' => false,
                'message'     => $asset
            ], 400);
        }

        return response()->json([
            'status'     => true,
            'results'     => route('asset.index'),
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

        return view('admin.asset.detail', compact('asset'));
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
            ->leftJoin('asset_categories as c2', 'c1.id', '=', 'c2.parent_id')
            ->where('c2.parent_id', '>', 0)
            ->where('c2.status', 1)
            ->groupBy('c1.id')
            ->get();

        $asset = Asset::with('assetcategory')->findOrFail($id);
        $vendors = Asset::select('vendor')->distinct()->get();
        $pics = Asset::select('pic')->distinct()->get();
        $locations = Asset::select('location')->distinct()->get();
        return view('admin.asset.edit', compact('asset', 'categories', 'drafts', 'vendors', 'pics', 'locations'));
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
            'assetcategory_id'         => 'required',
            'code'                     => 'required',
            'name'                     => 'required',
            'buy_date'             => 'required',
            'buy_price'             => 'required',
            'stock'             => 'required',
            'vendor'             => 'required',
            'pic'             => 'required',
            'location'             => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $asset = Asset::find($id);
        $pic = $asset->pic;
        $location = $asset->location;
        $stock = $asset->stock;
        $asset->assetcategory_id = $request->assetcategory_id;
        $asset->code             = $request->code;
        $asset->name             = $request->name;
        $asset->buy_date         = $request->buy_date;
        $asset->buy_price        = $request->buy_price;
        $asset->stock            = $request->stock;
        $asset->vendor           = $request->vendor;
        $asset->pic              = $request->pic;
        $asset->location         = $request->location;
        $asset->note             = $request->note;
        // $asset->employee_id      = $request->employee_id;
        $asset->save();
        if ($location != $asset->location || $pic != $asset->pic || $stock != $asset->stock) {
            $assethistory = AssetHistory::create([
                'asset_id' => $asset->id,
                'pic'             => $asset->pic,
                'location'             => $asset->location,
                'stock'          => $asset->stock
            ]);
        }
        $image = $request->file('image');
        if ($image) {
            $filename = 'foto.' . $request->image->getClientOriginalExtension();
            if (file_exists($asset->image) && $asset->image != 'img/no-image.png') {
                unlink($asset->image);
            }

            $src = 'assets/asset/' . $asset->id;
            if (!file_exists($src)) {
                mkdir($src, 0777, true);
            }
            $image->move($src, $filename);
            $asset->image = $src . '/' . $filename;
            $asset->save();
        }
        $document = $request->file('document');
        if ($document) {
            $filename = 'document.' . $request->document->getClientOriginalExtension();
            if (file_exists($asset->document) && $asset->document != 'img/no-image.png') {
                unlink($asset->document);
            }

            $src = 'assets/asset/' . $asset->id;
            if (!file_exists($src)) {
                mkdir($src, 0777, true);
            }
            $document->move($src, $filename);
            $asset->document = $src . '/' . $filename;
            $asset->save();
        }
        if (!$asset) {
            return response()->json([
                'status' => false,
                'message'     => $asset
            ], 400);
        }

        return response()->json([
            'status'     => true,
            'results'     => route('asset.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function stockupdate(Request $request)
    {
        if (isset($request->stock)) {
            $asset = Asset::find($request->asset_id);
            $asset->stock = $request->stock;
            $asset->save();
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Success edit data',
        ], 200);
    }
    public function destroy($id)
    {
        try {
            $asset = Asset::find($id);
            $asset->delete();
            if (file_exists($asset->image)) {
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
                'category' => $category ? $category->name : null,
                'category_id' => $category ? $category->id : null,
                'code' => $code,
                'name' => $name,
                'pic' => $pic,
                'location' => $location,
                'buy_price' => $buy_price,
                'vendor' => $vendor,
                'buy_date' => $buy_date ? $buy_date : null,
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
                    'buy_date' => $asset->buy_date ? $asset->buy_date : null,
                    'note' => $asset->note,
                    'stock' => $asset->stock,
                    'image' => 'img/no-image.png',
                    'document' => 'img/no-image.png',
                ]);
                $assethistory = AssetHistory::create([
                    'asset_id' => $asset->id,
                    'pic'             => $asset->pic ? $asset->pic : 'Not Set',
                    'location'             => $asset->location ? $asset->location : 'Not Set',
                    'stock'          => $asset->stock ? $asset->stock : 0
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

    public function export(Request $request)
    {
        $from = $request->date_from ? dbDate($request->date_from) : null;
        $to = $request->date_to ? dbDate($request->date_to) : null;
        $pic = $request->pic;
        $location = $request->location;
        $vendor = $request->vendor;
        $category = $request->category;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $query = Asset::select('assets.*', 'ac.name as categories', 'employees.name as employee');
        $query->leftJoin('asset_categories as ac', 'assets.assetcategory_id', '=', 'ac.id');
        $query->leftJoin('employees', 'employees.id', '=', 'assets.employee_id');
        $query->where('assets.asset_type', 'other');
        if ($from && $to) {
            $query->whereBetween('assets.buy_date', [$from, $to]);
        }
        if ($pic) {
            $query->where('assets.pic', 'like', "%$pic%");
        }
        if ($location) {
            $query->where('assets.location', 'like', "%$location%");
        }
        if ($vendor) {
            $query->where('assets.vendor', 'like', "%$vendor%");
        }
        if ($category) {
            $query->whereRaw("ac.path like '%$category%'");
        }
        $assets = $query->get();

        $columns = [
            'No',
            'Asset Name',
            'Asset Category',
            'PIC',
            'Location',
            'Buy Date',
            'Note',
            'Vendor',
            'Buy Price',
            'Stock',
            'Code',
            'Asset Type',
            'License No',
            'Engine No',
            'Merk',
            'Type',
            'Model',
            'Production Year',
            'Manufacture',
            'Engine Capacity',
            'Driver',
            'Employee',
            'Image Link',
            'Document Link'
        ];

        $header_column = 0;
        foreach ($columns as $key => $column) {
            $sheet->setCellValueByColumnAndRow($header_column, 1, $column);
            $header_column++;
        }

        $row_number = 2;
        foreach ($assets as $key => $asset) {
            $sheet->setCellValue('A' . $row_number, ++$key);
            $sheet->setCellValue('B' . $row_number, $asset->name);
            $sheet->setCellValue('C' . $row_number, $asset->categories);
            $sheet->setCellValue('D' . $row_number, $asset->pic);
            $sheet->setCellValue('E' . $row_number, $asset->location);
            $sheet->setCellValue('F' . $row_number, $asset->buy_date);
            $sheet->setCellValue('G' . $row_number, $asset->note);
            $sheet->setCellValue('H' . $row_number, $asset->vendor);
            $sheet->setCellValue('I' . $row_number, $asset->buy_price);
            $sheet->setCellValue('J' . $row_number, $asset->stock);
            $sheet->setCellValue('K' . $row_number, $asset->code);
            $sheet->setCellValue('L' . $row_number, $asset->asset_type);
            $sheet->setCellValue('M' . $row_number, $asset->license_no);
            $sheet->setCellValue('N' . $row_number, $asset->engine_no);
            $sheet->setCellValue('O' . $row_number, $asset->merk);
            $sheet->setCellValue('P' . $row_number, $asset->type);
            $sheet->setCellValue('Q' . $row_number, $asset->model);
            $sheet->setCellValue('R' . $row_number, $asset->production_year);
            $sheet->setCellValue('S' . $row_number, $asset->manufacture);
            $sheet->setCellValue('T' . $row_number, $asset->engine_capacity);
            $sheet->setCellValue('U' . $row_number, $asset->driver);
            $sheet->setCellValue('V' . $row_number, $asset->employee);
            $sheet->setCellValue('W' . $row_number, URL::to('/' . $asset->image));
            $sheet->setCellValue('X' . $row_number, URL::to('/' . $asset->document));
            $sheet->getCell('W' . $row_number)->getHyperlink()->setUrl(URL::to('/' . $asset->image));
            $sheet->getCell('X' . $row_number)->getHyperlink()->setUrl(URL::to('/' . $asset->document));
            $row_number++;
        }

        foreach (range(0, $header_column) as $key => $value) {
            $sheet->getColumnDimensionByColumn($value)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($assets->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-asset-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Assets Data",
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