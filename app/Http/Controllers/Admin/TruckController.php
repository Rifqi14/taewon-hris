<?php

namespace App\Http\Controllers\Admin;

use App\Models\Truck;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class TruckController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'truck'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.truck.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->parent_name);

        //Count Data
        $query = Truck::select('trucks.*');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Truck::select('trucks.*');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $trucks = $query->get();

        $data = [];
        foreach ($trucks as $truck) {
            $truck->no = ++$start;
            $data[] = $truck;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'sort' => $sort,
            'data' => $data
        ], 200);
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = Truck::select('trucks.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = Truck::select('trucks.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start*$length);
        $query->limit($length);
        $query->orderBy('name','asc');
        $trucks = $query->get();

        $data = [];
        foreach ($trucks as $truck) {
            $truck->no = ++$start;
            $data[] = $truck;
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
        return view('admin.truck.create');
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
            'name'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $truck = Truck::create([
            'code' => '',
            'site_id' => Session::get('site_id'),
            'name' => $request->name,
            'notes' => $request->notes,
            'status' => $request->status
        ]);
        if ($request->code) {
            $truck->code = $request->code;
            $truck->save();
        } else {
            $truck->code = $truck->code_system;
            $truck->save();
        }
        $truck->save();
        if (!$truck) {
            return response()->json([
                'status' => false,
                'message'     => $truck
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('truck.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $truck = Truck::find($id);
        if ($truck) {
            return view('admin.truck.edit', compact('truck'));
        } else {
            abort(404);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required',
            // 'code' 	    => 'required|alpha_dash'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $truck = Truck::find($id);
        $truck->name = $request->name;
        $truck->notes = $request->notes;
        $truck->status = $request->status;
        $truck->save();

        if (!$truck) {
            return response()->json([
                'status' => false,
                'message'     => $truck
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('truck.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $truck = Truck::find($id);
            $truck->delete();
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