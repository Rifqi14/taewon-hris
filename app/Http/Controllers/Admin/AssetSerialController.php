<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetSerial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class AssetSerialController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'assetserial'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.assetserial.index');
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
        $query = DB::table('asset_serials');
        $query->select('asset_serials.*',
                        'assets.name as asset_name',
                        'asset_categories.name as assetcategory_name');
        $query->leftJoin('assets', 'assets.id', '=', 'asset_serials.asset_id');
        $query->leftJoin('asset_categories', 'asset_categories.id', '=', 'assets.assetcategory_id');
        // $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('asset_serials');
        $query->select('asset_serials.*',
                        'assets.name as asset_name',
                        'asset_categories.name as assetcategory_name');
        $query->leftJoin('assets', 'assets.id', '=', 'asset_serials.asset_id');
        $query->leftJoin('asset_categories', 'asset_categories.id', '=', 'assets.assetcategory_id');
        // $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $assets = $query->get();

        $data = [];
        foreach($assets as $asset){
            $asset->no = ++$start;
			$data[] = $asset;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function selectemployee(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $employees = $query->get();

        $data = [];
        foreach($employees as $employee){
            $employee->no = ++$start;
			$data[] = $employee;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $asset = Asset::findOrFail($id);

        return view('admin.assetserial.detail', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);

        return view('admin.assetserial.edit', compact('asset'));
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
        DB::beginTransaction();
        if(count($request->serial_item) > 0)
        {
            foreach($request->serial_item as $item=>$v){
                $asset_id = $request->serial_id[$item];
                $data=array(
                    'asset_id'=>$id,
                    'serial_no'=>$request->serial_no[$item],
                    // 'employee_id'=>$request->employee_id[$item],
                );
                if($asset_id == 0){
                    // echo "create <br>";
                    $assetserial = AssetSerial::create($data);
                }else{
                    $assetserial = AssetSerial::where('id', '=', $asset_id)->update($data);
                    // echo "update <br>";
                }
                // dd($id);

                if (!$assetserial) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' 	=> $assetserial
                    ], 400);
                }
            }
        }


        DB::commit();
        return response()->json([
            'status' 	=> true,
            'results' 	=> route('assetserial.index'),
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

    public function deleteserial($id)
    {
        try {
            $assetserial = AssetSerial::find($id);
            $assetserial->delete();
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
