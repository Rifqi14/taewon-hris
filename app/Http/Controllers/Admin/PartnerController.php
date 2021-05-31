<?php

namespace App\Http\Controllers\admin;

use App\Models\Partner;
use App\Models\Truck;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class PartnerController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'partner'));
    }
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $truck_id = $request->truck_id;

        //Count Data
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($truck_id){
            $query->where('truck_id',$truck_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($truck_id){
            $query->where('truck_id',$truck_id);
        }
        $query->offset($start);
        $query->limit($length);
        $partners = $query->get();

        $data = [];
        foreach($partners as $partner){
            $partner->no = ++$start;
            $partner->rit = number_format($partner->rit,0,'.',',');
            $data[] = $partner;
        }
        return response()->json([
            'total'=>$recordsTotal,
            'rows'=>$data
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

        //Count Data
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $partners = $query->get();

        $data = [];
        foreach ($partners as $partner) {
            $partner->no = ++$start;
            $partner->rit = number_format($partner->rit,0,',','.');
            $data[] = $partner;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.partner.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $trucks = Truck::where('status',1)->get();
        return view('admin.partner.create',compact('trucks'));
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
            'name'     => 'required',
            'rit'      => 'required',
            'truck_id'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $partner = Partner::create([
            'code'     => '',
            'site_id'  => Session::get('site_id'),
            'name'     => $request->name,
            'address'  => $request->address,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'rit'      => $request->rit,
            'status'   => $request->status,
            'truck_id'   => $request->truck_id,
        ]);
        if ($request->code) {
            $partner->code = $request->code;
            $partner->save();
        } else {
            $partner->code = $partner->code_system;
            $partner->save();
        }
        if (!$partner) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $partner
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('partner.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function show(Partner $partner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $partner = Partner::find($id);
        $trucks = Truck::where('status',1)->get();
        return view('admin.partner.edit', compact('partner','trucks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'rit'      => 'required',
            'truck_id'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $partner = Partner::find($id);
        $partner->code    = $request->code;
        $partner->name    = $request->name;
        $partner->address = $request->address;
        $partner->email   = $request->email;
        $partner->phone   = $request->phone;
        $partner->rit     = $request->rit;
        $partner->status  = $request->status;
        $partner->truck_id  = $request->truck_id;
        $partner->save();

        if (!$partner) {
            return response()->json([
                'status' => false,
                'message'     => $partner
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('partner.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $partner = Partner::find($id);
            $partner->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
