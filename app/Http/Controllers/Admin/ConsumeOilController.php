<?php

namespace App\Http\Controllers\Admin;

use App\Models\ConsumeOil;
use App\Models\Asset;
use App\Models\AssetMovement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ConsumeOilController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'consumeoil'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.consumeoil.index');
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
        $oil = strtoupper($request->oil);
        $vehicle = strtoupper($request->vehicle);
        $license_no = strtoupper($request->license_no);
        $driver = strtoupper($request->driver);

        //Count Data
        $query = DB::table('consume_oils');
        $query->select('consume_oils.*', 'vehicles.name as vehicle','oils.name as oil', 'vehicles.license_no', 'vehicles.type as vehicle_type');
        $query->leftJoin('assets as vehicles', 'vehicles.id', '=', 'consume_oils.vehicle_id');
        $query->leftJoin('assets as oils', 'oils.id', '=', 'consume_oils.oil_id');
        if ($date_from && $date_to) {
            $query->whereBetween('date', [$date_from, $date_to]);
        }
        if($oil)
        {
            $query->whereRaw("upper(oils.name) like '%$oil%'");
        }
        if ($vehicle) {
            $query->whereRaw("upper(vehicles.name) like '%$vehicle%'");
        }
        if ($license_no) {
            $query->whereRaw("upper(vehicles.license_no) like '%$license_no%'");
        }
        if ($driver) {
            $query->whereRaw("upper(consume_oils.driver) like '%$driver%'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('consume_oils');
        $query->select('consume_oils.*', 'vehicles.name as vehicle' , 'oils.name as oil', 'vehicles.license_no', 'vehicles.type as vehicle_type');
        $query->leftJoin('assets as vehicles', 'vehicles.id', '=', 'consume_oils.vehicle_id');
        $query->leftJoin('assets as oils', 'oils.id', '=', 'consume_oils.oil_id');
        if ($date_from && $date_to) {
            $query->whereBetween('date', [$date_from, $date_to]);
        }
        if ($oil) {
            $query->whereRaw("upper(oils.name) like '%$oil%'");
        }
        if ($vehicle) {
            $query->whereRaw("upper(vehicles.name) like '%$vehicle%'");
        }
        if ($license_no) {
            $query->whereRaw("upper(vehicles.license_no) like '%$license_no%'");
        }
        if ($driver) {
            $query->whereRaw("upper(consume_oils.driver) like '%$driver%'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $consume_oils = $query->get();

        $data = [];
        foreach ($consume_oils as $consume_oil) {
            $consume_oil->no = ++$start;
            $data[] = $consume_oil;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'sort' => $sort,
            'data' => $data
        ], 200);
    }
    
    public function readoil(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('assets');
        $query->select('assets.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('assets');
        $query->select('assets.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->where('asset_type', 'oil');
        
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
    public function readvehicle(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('assets');
        $query->select('assets.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('assets');
        $query->select('assets.*');
        $query->whereRaw("upper(name) like '%$name%'");
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.consumeoil.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->status);
        $validator = Validator::make($request->all(), [
            'date'       => 'required',
            'vehicle_id' => 'required',
            'oil_id'     => 'required',
            'engine_oil' => 'required',
            'km'         => 'required',
            'driver'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        // dd($request->status);
        $consumeoil = ConsumeOil::create([
            'date'       => $request->date,
            'vehicle_id' => $request->vehicle_id,
            'oil_id'     => $request->oil_id,
            'engine_oil' => $request->engine_oil, 
            'km'         => $request->km,
            'driver'     => $request->driver,
            'note'       => $request->note,
            'status'     => $request->status,
            'type' 	     => $request->type,
            'stock' 	 => $request->stock?$request->stock:0,

        ]);
        if (!$consumeoil) {
            return response()->json([
                'status'     => false,
                'message'    => $consumeoil
            ], 400);
        }

        if($request->status == 1){
            $assetmovement = AssetMovement::create([
                'asset_id'  => $request->oil_id,
                'type'      => 'out',
                'qty'       => $request->engine_oil,
                'reference' => $consumeoil->id,
                'from'      => 'consumeoil',
                'note'      => 'Consume oil #' . $consumeoil->id,
            ]);

            if (!$assetmovement) {
                return response()->json([
                    'status'     => false,
                    'message'    => $assetmovement
                ], 400);
            }

            $asset = Asset::find($request->oil_id);
            $asset->stock = $asset->stock - $request->engine_oil;
            $asset->save();
        }
        return response()->json([
            'status'     => true,
            'results'     => route('consumeoil.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ConsumeOil  $consumeOil
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $consumeoils = ConsumeOil::find($id);

        return view('admin.consumeoil.detail', compact('consumeoils'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConsumeOil  $consumeOil
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $consumeoils = ConsumeOil::find($id);
        return view('admin.consumeoil.edit', compact('consumeoils'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConsumeOil  $consumeOil
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date'       => 'required',
            'vehicle_id' => 'required',
            'oil_id'     => 'required',
            'engine_oil' => 'required',
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
        $consumeoil = ConsumeOil::find($id);
        $consumeoil->date       = $request->date;
        $consumeoil->vehicle_id = $request->vehicle_id;
        $consumeoil->oil_id     = $request->oil_id;
        $consumeoil->engine_oil = $request->engine_oil;
        $consumeoil->km         = $request->km;
        $consumeoil->driver     = $request->driver;
        $consumeoil->status     = $request->status;
        $consumeoil->note       = $request->note;
        $consumeoil->type       = $request->type;
        $consumeoil->stock      = $request->stock ? $request->stock:0;
        $consumeoil->save();

        if (!$consumeoil) {
            DB::rollBack();
            return response()->json([
                'status'     => false,
                'message'    => $consumeoil
            ], 400);
        }

        if ($request->status == 1) {
            $assetmovement = AssetMovement::create([
                'asset_id'  => $request->oil_id,
                'type'      => 'out',
                'qty'       => $request->engine_oil,
                'reference' => $consumeoil->id,
                'from'      => 'consumeoil',
                'note'      => 'Consume oil #'. $consumeoil->id,
            ]);

            if (!$assetmovement) {
                DB::rollBack();
                return response()->json([
                    'status'     => false,
                    'message'    => $assetmovement
                ], 400);
            }
            $asset = Asset::find($request->oil_id);
            $asset->stock = $asset->stock - $request->engine_oil;
            $asset->save();
        }
       
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('consumeoil.index'),
        ], 200);

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConsumeOil  $consumeOil
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $consumeoil = ConsumeOil::find($id);
            $consumeoil->delete();
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
}
