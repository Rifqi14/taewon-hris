<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AssetMovement;
use App\Models\AssetSerial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AssetMovementController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'assetmovement'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.assetmovement.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $serial = strtoupper($request->serial);

        //Count Data
        $query = DB::table('asset_movements');
        $query->select('asset_movements.*',
                        'asset_serials.serial_no as asset_serial');
        $query->leftJoin('asset_serials', 'asset_serials.id', '=', 'asset_movements.asset_serial_id');
        $query->whereRaw("upper(asset_serials.serial_no) like '%$serial%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('asset_movements');
        $query->select('asset_movements.*',
                        'asset_serials.serial_no as asset_serial');
        $query->leftJoin('asset_serials', 'asset_serials.id', '=', 'asset_movements.asset_serial_id');
        $query->whereRaw("upper(asset_serials.serial_no) like '%$serial%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $assetmovements = $query->get();
        // dd($assetmovements);

        $data = [];
        foreach($assetmovements as $assetmovement){
            $assetmovement->no = ++$start;
			$data[] = $assetmovement;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function assetserialselect(Request $request)
    {
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $serial_no = strtoupper($request->serial_no);

        //Count Data
        $query = DB::table('asset_serials');
        $query->select('asset_serials.*');
        $query->whereRaw("upper(serial_no) like '%$serial_no%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('asset_serials');
        $query->select('asset_serials.*');
        $query->whereRaw("upper(serial_no) like '%$serial_no%'");
        $query->offset($start);
        $query->limit($length);
        $assetserials = $query->get();

        $data = [];
        foreach($assetserials as $assetserial){
            $assetserial->no = ++$start;
			$data[] = $assetserial;
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
        return view('admin.assetmovement.create');
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
            'asset_serial_id'      => 'required',
            'transaction_date'      => 'required',
            'type'      => 'required',
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }


        // dd($request->sku);
        DB::beginTransaction();
        $assetmovement = AssetMovement::create([
            'asset_serial_id'  => $request->asset_serial_id,
            'transaction_date' 	    => $request->transaction_date,
            'type' 	    => $request->type,
            'qty' 	    => 1,
        ]);


        if (!$assetmovement) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' 	=> $assetmovement
            ], 400);
        }
        // dd($assetmovement->id);
        dispatch(new \App\Jobs\QueueAssetMovement($assetmovement->id));


        DB::commit();
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('assetmovement.index'),
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
