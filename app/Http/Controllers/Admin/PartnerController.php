<?php

namespace App\Http\Controllers\admin;

use App\Models\Partner;
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
        return view('admin.partner.create');
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
            'code'     => 'required',
            'rit'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $partner = Partner::create([
            'code'     => $request->code,
            'name'     => $request->name,
            'address'  => $request->address,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'rit'      => $request->rit
        ]);
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
        return view('admin.partner.edit', compact('partner'));
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
            'code'     => 'required',
            'rit'      => 'required'
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
