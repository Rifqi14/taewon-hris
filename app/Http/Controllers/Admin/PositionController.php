<?php

namespace App\Http\Controllers\Admin;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'position'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('positions');
        $query->select('positions.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('positions');
        $query->select('positions.*');
        $query->whereRaw("upper(name) like '%$name%'");
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
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.position.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('positions');
        $query->select('positions.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('positions');
        $query->select('positions.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $positions = $query->get();

        $data = [];
        foreach($positions as $view){
            $view->no = ++$start;
            $position->path = str_replace('->', ' <i class="fas fa-angle-right"></i> ', $position->path);
            $data[] = $view;
        }
        return response()->json([
            'draw'=>$request->draw,
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsTotal,
            'data'=>$data
        ], 200);
    }
    public function create()
    {
        return view('admin.position.create');
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
            'code' => 'required|alpha_dash',
            'name'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $position = Position::create([
            'code' => $request->code,
            'name' => $request->name,
            'parent_id' => $request->parent_id?$request->parent_id:0,
            'department_id' => $request->department_id,
            'max_person' => $request->max_person,
            'notes' => $request->notes, 
            'status' => $request->status
        ]);
        $position->path = implode(' -> ', $this->createPath($position->id, []));
        $position->level = count($this->createLevel($position->id, []));
        $position->save();

        if (!$position) {
            return response()->json([
                'status' => false,
                'message'   => $position
            ], 400);
        }

        return response()->json([
            'status' => true,
            'results' => route('position.index'),
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
        $position = Position::find($id);
        if($position){
            return view('admin.position.edit', compact('position'));
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
            'name'  => 'required',
            'code'    => 'required|alpha_dash',
            'parent'  => 'required',
            'department'  => 'required',
            'max_person'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $position = Position::find($request->id);
        $position->code = $request->code;
        $position->name = $request->name;
        $position->parent = $request->parent;
        $position->department = $request->department;
        $position->max_person = $request->max_person;
        $position->status = $request->status?1:0;
        $position->notes = $request->notes;
        $position->save();
        $position->path = implode(' -> ', $this->createPath($id, []));
        $position->level = count($this->createLevel($id, []));
        $position->save();
        if (!$position) {
            return response()->json([
                'status' => false,
                'message'   => $uom
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => url('admin/position'),
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
            $user = Position::find($id);
            $user->delete();
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
    public function createPath($id, $path)
    {
        $position = Position::find($id);
        array_unshift($path, $position->name);
        if ($position->parent_id) {
            return $this->createPath($position->parent_id, $path);
        }
        return $path;
    }

    public function updatePath($id)
    {
        $positions = Position::where('parent_id', $id)->get();
        foreach ($positions as $position) {
            $position->path = implode(' -> ', $this->createPath($position->id, []));
            $position->save();
            $this->updatePath($position->id);
        }
    }

    public function createLevel($id, $level)
    {
        $position = Position::find($id);
        array_unshift($level, $position->name);
        if ($position->parent_id) {
            return $this->createLevel($position->parent_id, $level);
        }
        return $level;
    }

    public function updateLevel($id)
    {
        $positions = Position::where('parent_id', $id)->get();
        foreach ($positions as $position) {
            $position->level = count($this->createLevel($position->id, []));
            $position->save();
            $this->updateLevel($position->id);
        }
    }
}
