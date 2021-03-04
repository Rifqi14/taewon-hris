<?php

namespace App\Http\Controllers\Admin;

use App\Models\Maintanance;
use App\Models\MaintananceItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;



class MaintananceController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'maintenance'));
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $date_from = $request->date_from ? Carbon::parse(changeSlash($request->date_from))->endOfDay()->toDateTimeString() : '';
        $date_to = $request->date_to ? Carbon::parse(changeSlash($request->date_to))->endOfDay()->toDateTimeString() : '';
        $vehicle_id = $request->vehicle_id;
        $vehicle_name = strtoupper($request->vehicle_name);
        $vehicle_no = strtoupper($request->vehicle_no);
        $vehicle_category = strtoupper($request->vehicle_category);
        $vendor = strtoupper($request->vendor);
        $driver = strtoupper($request->driver);

        //Count Data
        $query = DB::table('maintanances');
        $query->select('maintanances.*', 'vehicles.name as vehicle');
        $query->leftJoin('assets as vehicles', 'vehicles.id', '=', 'maintanances.vehicle_id');
        if ($date_from && $date_to) {
            $query->whereBetween('date', [$date_from, $date_to]);
        }
        if ($vehicle_name) {
            $query->whereRaw("upper(vehicle_name) like '%$vehicle_name%'");
        }
        if ($vehicle_no) {
            $query->whereRaw("upper(vehicle_no) like '%$vehicle_no%'");
        }
        if ($vehicle_category) {
            $query->whereRaw("upper(vehicle_category) like '%$vehicle_category%'");
        }
        if ($vendor) {
            $query->whereRaw("upper(maintanances.vendor) like '%$vendor%'");
        }
        if ($driver) {
            $query->whereRaw("upper(maintanances.driver) like '%$driver%'");
        }
        if($vehicle_id){
            $query->where('maintanances.vehicle_id',$vehicle_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('maintanances');
        $query->select('maintanances.*', 'vehicles.name as vehicle');
        $query->leftJoin('assets as vehicles', 'vehicles.id', '=', 'maintanances.vehicle_id');
        if ($date_from && $date_to) {
            $query->whereBetween('date', [$date_from, $date_to]);
        }
        if ($vehicle_name) {
            $query->whereRaw("upper(vehicle_name) like '%$vehicle_name%'");
        }
        if ($vehicle_no) {
            $query->whereRaw("upper(vehicle_no) like '%$vehicle_no%'");
        }
        if ($vehicle_category) {
            $query->whereRaw("upper(vehicle_category) like '%$vehicle_category%'");
        }
        if ($vendor) {
            $query->whereRaw("upper(maintanances.vendor) like '%$vendor%'");
        }
        if ($driver) {
            $query->whereRaw("upper(maintanances.driver) like '%$driver%'");
        }
        if($vehicle_id){
            $query->where('maintanances.vehicle_id',$vehicle_id);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $maintanances = $query->get();

        $data = [];
        foreach ($maintanances as $maintanance) {
            $maintanance->no = ++$start;
            $maintanance->link = url($maintanance->image);
            $data[] = $maintanance;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'sort' => $sort,
            'data' => $data
        ], 200);
    }
   
    public function readvehicle(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('assets');
        $query->select('assets.*', 'assets.name', 'asset_categories.name as category_name');
        $query->leftJoin('asset_categories', 'asset_categories.id', '=','assets.assetcategory_id');
        $query->whereRaw("upper(assets.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('assets');
        $query->select('assets.*', 'assets.name', 'asset_categories.name as category_name');
        $query->leftJoin('asset_categories', 'asset_categories.id', '=', 'assets.assetcategory_id');
        $query->whereRaw("upper(assets.name) like '%$name%'");
        $query->where('asset_type', 'vehicle');

        $query->offset($start);
        $query->limit($length);
        $assets = $query->get();

        $data = [];
        foreach ($assets as $asset) {
            $asset->no = ++$start;
            $data[] = $asset;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function index()
    {
        return view('admin.maintanance.index');
    }
    public function create()
    {
        return view('admin.maintanance.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date'       => 'required',
            'vehicle_id' => 'required',
            'km'         => 'required',
            'driver'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $maintanance = Maintanance::create([
            'date'            => $request->date,
            'vehicle_id'      => $request->vehicle_id,
            'km'              => $request->km,
            'driver'          => $request->driver,
            'total'           => $request->total,
            'status'          => $request->status,
            'vehicle_name'    => $request->vehicle_name,
            'vehicle_no'      => $request->vehicle_no,
            'vehicle_category'=> $request->vehicle_category,
            'vendor'          => $request->vendor,
            'technician'      => $request->technician,
            'image'           => ''

        ]); 

        if (!$maintanance) {
            DB::rollBack();
            return response()->json([
                'status'     => false,
                'message'    => $maintanance
            ], 400);
        }
        $image = $request->file('image');
        if ($image) {
            $dt = Carbon::now();
            $rd = Str::random(5);
            $path = 'assets/maintenance/';
            $image->move($path, $rd . '.' . $dt->format('Y-m-d') . '.' . $image->getClientOriginalExtension());
            $filename = $path . $rd . '.' . $dt->format('Y-m-d') . '.' . $image->getClientOriginalExtension();
            $maintanance->image = $filename;
            $maintanance->save();
        }

        if(count($request->item) > 0)
        {
            foreach ($request->item as $item => $v)
            {
                $maintananceitem = MaintananceItem::create([
                    'maintanance_id' => $maintanance->id,
                    'item'           => $request->item[$item],
                    'cost'           => $request->cost[$item],
                    'qty'            => $request->qty[$item],
                    'subtotal'       => $request->subtotal[$item]
                ]);

                if (!$maintananceitem) {
                    DB::rollBack();
                    return response()->json([
                        'status'     => false,
                        'message'    => $maintananceitem
                    ], 400);
                }
            }
        }

        DB::commit();
        return response()->json([
            'status'     => true,
            'results'    => route('maintenance.index'),
        ], 200);
    }

    public function show($id)
    {
        $maintanance = Maintanance::find($id);
        $maintananceitems = MaintananceItem::where('maintanance_id', $id)->get();

        return view('admin.maintanance.detail', compact('maintanance', 'maintananceitems'));
    }

    public function edit($id)
    {
        $maintanance = Maintanance::find($id);
        $maintananceitems = MaintananceItem::where('maintanance_id', $id)->get();

        return view('admin.maintanance.edit', compact('maintanance', 'maintananceitems'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date'       => 'required',
            'vehicle_id' => 'required',
            'km'         => 'required',
            'driver'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $maintanance = Maintanance::find($id);
        $maintanance->date             = $request->date;
        $maintanance->vehicle_id       = $request->vehicle_id;
        $maintanance->km               = $request->km;
        $maintanance->driver           = $request->driver;
        $maintanance->status           = $request->status;
        $maintanance->total            = $request->total;
        $maintanance->vehicle_name     = $request->vehicle_name;
        $maintanance->vehicle_no       = $request->vehicle_no;
        $maintanance->vehicle_category = $request->vehicle_category;
        $maintanance->vendor           = $request->vendor;
        $maintanance->technician       = $request->technician;
        $maintanance->save();

        if($maintanance){
            if(isset($request->item)){
                $list = MaintananceItem::where('maintanance_id', $id);
                $list->delete();

                foreach($request->item as $key => $value){
                    $maintananceitem = MaintananceItem::create([
                        'maintanance_id' => $id,
                        'item'           => $request->item[$key],
                        'cost'           => $request->cost[$key],
                        'qty'            => $request->qty[$key],
                        'subtotal'       => $request->subtotal[$key]
                    ]);
                    if (!$maintananceitem) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $maintananceitem
                        ], 400);
                    }
                }
            }
        }else{
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $maintanance
            ], 400);
        }
        
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'    => route('maintenance.index'),
        ], 200);

    }

    public function destroy($id)
    {
        try {
            $maintanance = Maintanance::find($id);
            $maintanance->delete();
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

    public function export(Request $request)
    {
        $from = $request->date_from ? dbDate($request->date_from) : null;
        $to = $request->date_to ? dbDate($request->date_to) : null;
        $vehicle_name = $request->vehicle_name;
        $vehicle_no = $request->vehicle_no;
        $vehicle_category = $request->vehicle_category;
        $vendor = $request->vendor;
        $driver = $request->driver;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $query = Maintanance::select('maintanances.*',
                                    'maintanance_items.*');
        // $query->leftJoin('asset_categories as ac', 'assets.assetcategory_id', '=', 'ac.id');
        $query->leftJoin('maintanance_items', 'maintanance_items.maintanance_id', '=', 'maintanances.id');
        // $query->where('assets.asset_type', 'other');
        if ($from && $to) {
            $query->whereBetween('maintanances.date', [$from, $to]);
        }
        if ($vehicle_name) {
            $query->where('maintanances.vehicle_name', 'like', "%$vehicle_name%");
        }
        if ($vehicle_no) {
            $query->where('maintanances.vehicle_no', 'like', "%$vehicle_no%");
        }
        if ($vehicle_category) {
            $query->where('maintanances.vehicle_category', 'like', "%$vehicle_category%");
        }
        if ($vendor) {
            $query->where('maintanances.vendor', 'like', "%$vendor%");
        }
        if ($driver) {
            $query->where('maintanances.driver', 'like', "%$driver%");
        }
        $maintenances = $query->get();

        $columns = [
            'No',
            'Vehicle',
            'Vehicle Category',
            'Vendor',
            'Technician',
            'Driver',
            'KM',
            'Date',
            'Item',
            'Cost',
            'Qty',
            'Subtotal',
            'Image Link'
        ];

        $header_column = 0;
        foreach ($columns as $key => $column) {
            $sheet->setCellValueByColumnAndRow($header_column, 1, $column);
            $header_column++;
        }
        $row_number = 2;
        foreach ($maintenances as $key => $value) {
            $sheet->setCellValue('A' . $row_number, ++$key);
            $sheet->setCellValue('B' . $row_number, $value->vehicle_name);
            $sheet->setCellValue('C' . $row_number, $value->vehicle_category);
            $sheet->setCellValue('D' . $row_number, $value->vendor);
            $sheet->setCellValue('E' . $row_number, $value->technician);
            $sheet->setCellValue('F' . $row_number, $value->driver);
            $sheet->setCellValue('G' . $row_number, $value->km);
            $sheet->setCellValue('H' . $row_number, $value->date);
            $sheet->setCellValue('I' . $row_number, $value->item);
            $sheet->setCellValue('J' . $row_number, $value->cost);
            $sheet->setCellValue('K' . $row_number, $value->qty);
            $sheet->setCellValue('L' . $row_number, $value->subtotal);
            $sheet->setCellValue('M' . $row_number, URL::to('/' . $value->image));
            $sheet->getCell('M' . $row_number)->getHyperlink()->setUrl(URL::to('/' . $value->image));
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
}
