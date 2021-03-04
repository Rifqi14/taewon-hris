<?php

namespace App\Http\Controllers\Admin;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin'.'jabatan'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $department_id = $request->department_id;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('positions');
        $query->select('positions.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($department_id){
            $query->where('department_id','=',$department_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('positions');
        $query->select('positions.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($department_id){
            $query->where('department_id','=',$department_id);
        }
        $query->offset($start);
        $query->limit($length);
        $positions = $query->get();

        $data = [];
        foreach($positions as $position){
            $position->no = ++$start;
            $data[] = $position;
        }
        return response()->json([
            'total'=>$recordsTotal,
            'rows'=>$data
        ], 200);
    }
    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $department_name = strtoupper($request->department_name);
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('positions');
        $query->select('positions.*');
        $query->leftJoin('departments','departments.id','=','positions.department_id');
        $query->whereRaw("upper(departments.name) like '%$department_name%'");
        $query->whereRaw("upper(positions.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('positions');
        $query->select('positions.*','departments.name as department_name');
        $query->leftJoin('departments','departments.id','=','positions.department_id');
        $query->whereRaw("upper(departments.name) like '%$department_name%'");
        $query->whereRaw("upper(positions.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $roles = $query->get();

        $data = [];
        foreach($roles as $role){
            $role->no = ++$start;
            $data[] = $role;
        }
        return response()->json([
            'draw'=>$request->draw,
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsTotal,
            'data'=>$data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.jabatan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.jabatan.create');
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
            'department_id'  => 'required',
            'name'      => 'required|min:3',
            'parent_id'      => 'required',
            'code'      => 'required|min:3',
            'max_person'      => 'required',
            'notes'      => 'required',
            'status'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $position = Position::create([
            'department_id'  => $request->department_id,
            'name'      => $request->name,
            'parent_id'      => $request->parent_id,
            'code'      => $request->code,
            'max_person'      => $request->max_person,
            'notes'      => $request->notes,
            'status'      => $request->status
        ]);
        if (!$position) {
            return response()->json([
                'status' => false,
                'message'   => $position
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('jabatan.index'),
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
        $jabatan = Position::find($id);
        if($jabatan){
            return view('admin.jabatan.edit', compact('jabatan'));
        }else{
            abort(404);
        }
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
            'department_id'  => 'required',
            'name'      => 'required',
            'parent_id'      => 'required',
            'code'      => 'required',
            'max_person'      => 'required',
            'notes'      => 'required',
            'status'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $jabatan = Position::find($id);
        $jabatan->department_id = $request->department_id;
        $jabatan->name = $request->name;
        $jabatan->parent_id = $request->parent_id;
        $jabatan->code = $request->code;
        $jabatan->max_person = $request->max_person;
        $jabatan->notes = $request->notes;
        $jabatan->status = $request->status;
        $jabatan->save();

        if (!$jabatan) {
            return response()->json([
                'status' => false,
                'message'   => $jabatan
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('jabatan.index'),
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
            $title = Position::find($id);
            $title->delete();
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
