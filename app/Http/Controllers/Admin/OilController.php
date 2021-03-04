<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\ConsumeOil;
use App\Models\AssetCategory;
use App\Models\AssetSerial;
use App\Models\AssetHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class OilController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'oil'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.oil.index');
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
        $query = Asset::with('assetcategory')->select('assets.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->where('asset_type', 'oil');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Asset::with('assetcategory')->select('assets.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->where('asset_type', 'oil');
        $query->offset($start);
        $query->limit($length);
        // $query->orderBy('created_at', 'desc');
        $query->orderBy($sort, $dir);
        $assets = $query->get();

        $data = [];
        foreach ($assets as $asset) {
            $asset->no = ++$start;
            $asset->image = asset($asset->image);
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
    public function consumeoil(Request $request)
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
        $query = AssetMovement::select('asset_movements.*');
        $query->where('asset_id', $id);
        $query->whereMonth('created_at', $month);
        $query->whereYear('created_at', $year);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = AssetMovement::select('asset_movements.*');
        $query->where('asset_id', $id);
        $query->whereMonth('created_at', $month);
        $query->whereYear('created_at', $year);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
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
        // $query = AssetHistory::where('asset_id', $id);
        $query = AssetHistory::select('asset_histories.*', 
                                    'assets.buy_price as price',
                                    'assets.created_at as date');
        $query->leftJoin('assets', 'assets.id', '=', 'asset_histories.asset_id');
        $query->where('asset_histories.asset_id', $id);
        $query->whereMonth('assets.created_at', $month);
        $query->whereYear('assets.created_at', $year);
        $recordsTotal = $query->count();

        //Select Pagination
        // $query = AssetHistory::where('asset_id', $id);
        $query = AssetHistory::select('asset_histories.*', 
                                    'assets.buy_price as price',
                                    'assets.created_at as date');
        $query->leftJoin('assets', 'assets.id', '=', 'asset_histories.asset_id');
        $query->where('asset_histories.asset_id', $id);
        $query->whereMonth('assets.created_at', $month);
        $query->whereYear('assets.created_at', $year);
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

    public function readconsumeoil(Request $request)
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
        $query = ConsumeOil::select('consume_oils.*', 'vehicles.name as vehicle', 'oils.name as oil', 'vehicles.license_no', 'vehicles.type as vehicle_type');
        $query->leftJoin('assets as vehicles', 'vehicles.id', '=', 'consume_oils.vehicle_id');
        $query->leftJoin('assets as oils', 'oils.id', '=', 'consume_oils.oil_id');
        $query->where('consume_oils.oil_id', $id);
        $query->whereMonth('date', $month);
        $query->whereYear('date', $year);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = ConsumeOil::select('consume_oils.*', 'vehicles.name as vehicle', 'oils.name as oil', 'vehicles.license_no', 'vehicles.type as vehicle_type');
        $query->leftJoin('assets as vehicles', 'vehicles.id', '=', 'consume_oils.vehicle_id');
        $query->leftJoin('assets as oils', 'oils.id', '=', 'consume_oils.oil_id');
        $query->where('consume_oils.oil_id', $id);
        $query->whereMonth('date', $month);
        $query->whereYear('date', $year);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('consume_oils.id', 'asc');
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

    public function draft()
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

        return view('admin.oil.draft', compact('categories', 'drafts'));
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

        $name = $request->input('name');
        $category_name = $request->input('category_name');
        $asset_category_id = $request->input('asset_category_id');
        $vendors = Asset::select('vendor')->distinct()->get();
        $pics = Asset::select('pic')->distinct()->get();
        $locations = Asset::select('location')->distinct()->get();
        return view('admin.oil.create', compact('name', 'category_name', 'asset_category_id', 'vendors', 'pics', 'locations'));
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

        $asset = Asset::create([
            'asset_type'      => 'oil',
            'code'               => $request->code,
            'name'               => $request->name,
            'pic'               => $request->pic,
            'location'           => $request->location,
            'buy_price'       => $request->buy_price,
            'vendor'           => $request->vendor,
            'buy_date'           => $request->buy_date,
            'note'               => $request->note,
            'image'           => 'img/no-image.png',
            'document'           => 'img/no-image.png',
            'stock'           => $request->stock,
            'employee_id'     => $request->employee_id
        ]);

        $assethistory = AssetHistory::create([
            'asset_id'      => $asset->id,
            'pic'             => $asset->pic,
            'location'         => $asset->location,
            'stock'         => $asset->stock
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
        $assetmovement = AssetMovement::create([
            'asset_id'  => $asset->id,
            'type'      => 'in',
            'qty'       => $request->stock,
            'reference' => 0,
            'from'      => 'stockbalance',
            'note'      => 'Stock Balance'
        ]);

        if (!$assetmovement) {
            return response()->json([
                'status'     => false,
                'message'    => $assetmovement
            ], 400);
        }

        return response()->json([
            'status'     => true,
            'results'     => route('oil.index'),
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
        // dd($consumeoils);

        return view('admin.oil.detail', compact('asset'));
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
        return view('admin.oil.edit', compact('asset', 'categories', 'drafts', 'vendors', 'pics', 'locations'));
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
        $pic                     = $asset->pic;
        $location                = $asset->location;
        $stock                   = $asset->stock;
        $asset->code             = $request->code;
        $asset->name             = $request->name;
        $asset->buy_date         = $request->buy_date;
        $asset->buy_price        = $request->buy_price;
        $asset->stock            = $request->stock;
        $asset->vendor           = $request->vendor;
        $asset->pic              = $request->pic;
        $asset->location         = $request->location;
        $asset->note             = $request->note;
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
            'results'     => route('oil.index'),
        ], 200);
    }

    public function stockupdate(Request $request)
    {
        if (isset($request->stock)) {
            $asset = Asset::find($request->asset_id);
            $stockbefore = $asset->stock;
            $asset->stock = $request->stock;
            $asset->save();
            $assetmovement = AssetMovement::create([
                'asset_id'  => $request->asset_id,
                'type'      => $stockbefore > $request->stock ? 'out' : 'in',
                'qty'       => $stockbefore > $request->stock ? $stockbefore - $request->stock : $request->stock - $stockbefore,
                'reference' => 0,
                'from'      => 'updatestock',
                'note'      => 'Update Stock'
            ]);

            if (!$assetmovement) {
                return response()->json([
                    'status'     => false,
                    'message'    => $assetmovement
                ], 400);
            } else {
                $assethistory = AssetHistory::create([
                    'asset_id'      => $asset->id,
                    'pic'           => Auth::user() ? Auth::user()->name : 'Not Set',
                    'location'      => $asset->location ? $asset->location : 'Not Set',
                    'stock'         => $asset->stock ? $asset->stock : 0
                ]);
                if (!$assethistory) {
                    return response()->json([
                        'status'     => false,
                        'message'    => $assethistory
                    ], 400);
                }
            }
        }


        return response()->json([
            'status'    => true,
            'message'   => 'Success edit data',
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
        $id = $request->asset_id;
        $month = $request->month_movement;
        $year = $request->year_movement;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $oils = AssetMovement::where('asset_id', $id)->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();

        $sheet->setCellValue('A1', 'No')->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('B1', 'Name')->getStyle('B1')->getFont()->setBold(true);
        $sheet->setCellValue('C1', 'Note')->getStyle('C1')->getFont()->setBold(true);
        $sheet->setCellValue('D1', 'Type')->getStyle('D1')->getFont()->setBold(true);
        $sheet->setCellValue('E1', 'Qty')->getStyle('E1')->getFont()->setBold(true);
        $sheet->setCellValue('F1', 'Date')->getStyle('F1')->getFont()->setBold(true);

        $row_number = 2;
        foreach ($oils as $key => $value) {
            $sheet->setCellValue('A' . $row_number, ++$key);
            $sheet->setCellValue('B' . $row_number, $value->asset->name);
            $sheet->setCellValue('C' . $row_number, $value->note);
            $sheet->setCellValue('D' . $row_number, ucwords($value->type));
            $sheet->setCellValue('E' . $row_number, $value->qty);
            $sheet->setCellValue('F' . $row_number, $value->created_at);
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
        if ($oils->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-oil-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Oils Data",
                'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
            ], 200);
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
        }
    }

    public function exportconsume(Request $request)
    {
        $id = $request->oil_id;
        $month = $request->month_consume;
        $year = $request->year_consume;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $oils = ConsumeOil::where('oil_id', $id)->whereMonth('date', $month)->whereYear('date', $year)->get();

        $sheet->setCellValue('A1', 'No')->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('B1', 'Oil Name')->getStyle('B1')->getFont()->setBold(true);
        $sheet->setCellValue('C1', 'Vehicle')->getStyle('C1')->getFont()->setBold(true);
        $sheet->setCellValue('D1', 'Driver')->getStyle('D1')->getFont()->setBold(true);
        $sheet->setCellValue('E1', 'Used Type')->getStyle('E1')->getFont()->setBold(true);
        $sheet->setCellValue('F1', 'Used Oil')->getStyle('F1')->getFont()->setBold(true);
        $sheet->setCellValue('G1', 'KM')->getStyle('G1')->getFont()->setBold(true);
        $sheet->setCellValue('H1', 'Initial Stock')->getStyle('H1')->getFont()->setBold(true);
        $sheet->setCellValue('I1', 'Stock Left')->getStyle('I1')->getFont()->setBold(true);
        $sheet->setCellValue('J1', 'Date')->getStyle('J1')->getFont()->setBold(true);
        $sheet->setCellValue('K1', 'Note')->getStyle('K1')->getFont()->setBold(true);

        $row_number = 2;
        foreach ($oils as $key => $value) {
            $sheet->setCellValue('A' . $row_number, ++$key);
            $sheet->setCellValue('B' . $row_number, $value->oil->name);
            $sheet->setCellValue('C' . $row_number, $value->asset->name);
            $sheet->setCellValue('D' . $row_number, $value->driver);
            $sheet->setCellValue('E' . $row_number, $value->type);
            $sheet->setCellValue('F' . $row_number, $value->engine_oil);
            $sheet->setCellValue('G' . $row_number, $value->km);
            $sheet->setCellValue('H' . $row_number, $value->stock);
            $sheet->setCellValue('I' . $row_number, '=H' . $row_number . ' - F' . $row_number);
            $sheet->setCellValue('J' . $row_number, $value->date);
            $sheet->setCellValue('K' . $row_number, $value->note);
            $row_number++;
        }

        foreach (range('A', 'K') as $key => $value) {
            $sheet->getColumnDimension($value)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($oils->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-consume-oil-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Consume Oils Data",
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
        $month = $request->month_histories;
        $year = $request->year_histories;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $oils = AssetHistory::where('asset_id', $id)->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();

        $sheet->setCellValue('A1', 'No')->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('B1', 'Oil Name')->getStyle('B1')->getFont()->setBold(true);
        $sheet->setCellValue('C1', 'PIC')->getStyle('C1')->getFont()->setBold(true);
        $sheet->setCellValue('D1', 'Location')->getStyle('D1')->getFont()->setBold(true);
        $sheet->setCellValue('E1', 'Stock')->getStyle('E1')->getFont()->setBold(true);
        $sheet->setCellValue('F1', 'Buy Price')->getStyle('F1')->getFont()->setBold(true);
        $sheet->setCellValue('G1', 'Date')->getStyle('G1')->getFont()->setBold(true);

        $row_number = 2;
        foreach ($oils as $key => $value) {
            $sheet->setCellValue('A' . $row_number, ++$key);
            $sheet->setCellValue('B' . $row_number, $value->asset->name);
            $sheet->setCellValue('C' . $row_number, $value->pic);
            $sheet->setCellValue('D' . $row_number, $value->location);
            $sheet->setCellValue('E' . $row_number, $value->stock);
            $sheet->setCellValue('F' . $row_number, $value->asset->buy_price); 
            $sheet->setCellValue('G' . $row_number, $value->created_at);
            $row_number++;
        }

        foreach (range('A', 'G') as $key => $value) {
            $sheet->getColumnDimension($value)->setAutoSize(true);
        }

        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($oils->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-history-oil-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download History Oils Data",
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