<?php

namespace App\Http\Controllers\Admin;

use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class DepartmentController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'department'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.department.index');
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
        $query = DB::table('departments');
        $query->select('departments.*', 'parent.name as parent_name');
        $query->leftJoin('departments as parent', 'parent.id', '=', 'departments.parent_id');
        $query->whereRaw("upper(departments.path) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('departments');
        $query->select('departments.*', 'parent.name as parent_name');
        $query->leftJoin('departments as parent', 'parent.id', '=', 'departments.parent_id');
        $query->whereRaw("upper(departments.path) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('path', $dir);
        $departments = $query->get();

        $data = [];
        foreach ($departments as $department) {
            $department->no = ++$start;
            $department->path = str_replace('->', ' <i class="fas fa-angle-right"></i> ', $department->path);
            $data[] = $department;
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
        $department_id = $request->department_id;
        $parent_id = $request->parent_id;
        $name = strtoupper($request->name);
        $title_id = $request->title_id;

        //Count Data
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->whereRaw("upper(path) like '%$name%'");
        if ($parent_id!='') {
            $query->where('parent_id', $parent_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->whereRaw("upper(path) like '%$name%'");
        if ($department_id) {
            $query->where('id', $department_id);
        }
        if ($parent_id!='') {
            $query->where('parent_id', $parent_id);
        }
        $query->offset($start*$length);
        $query->limit($length);
        $query->orderBy('path','asc');
        $departments = $query->get();

        $data = [];
        foreach ($departments as $department) {
            $department->no = ++$start;
            $data[] = $department;
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
        return view('admin.department.create');
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

        $department = Department::create([
            'code' => '',
            'site_id' => Session::get('site_id'),
            'name' => $request->name,
            'parent_id' => $request->parent_id ? $request->parent_id : 0,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);
        if ($request->code) {
            $department->code = $request->code;
            $department->save();
        } else {
            $department->code = $department->code_system;
            $department->save();
        }
        $department->path = implode(' -> ', $this->createPath($department->id, []));
        $department->level = count($this->createLevel($department->id, []));
        $department->save();
        if (!$department) {
            return response()->json([
                'status' => false,
                'message'     => $department
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('department.index'),
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
        $department = Department::with('parent')->find($id);
        if ($department) {
            return view('admin.department.edit', compact('department'));
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

        $department = Department::find($id);
        $department->name = $request->name;
        $department->parent_id = $request->parent_id ? $request->parent_id : 0;
        $department->save();
        $department->path = implode(' -> ', $this->createPath($id, []));
        $department->level = count($this->createLevel($id, []));
        $department->save();
        $this->updatePath($id);
        $this->updateLevel($id);

        if (!$department) {
            return response()->json([
                'status' => false,
                'message'     => $department
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('department.index'),
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
            $department = Department::find($id);
            $department->delete();
            $this->destroychild($department->id);
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

    function destroychild($parent_id)
    {
        $departments = Department::where('parent_id', '=', $parent_id)->get();
        foreach ($departments as $department) {
            try {
                Department::find($department->id)->delete();
                $this->destroychild($department->id);
            } catch (\Illuminate\Database\QueryException $e) {
            }
        }
    }
    public function createPath($id, $path)
    {
        $department = Department::find($id);
        array_unshift($path, $department->name);
        if ($department->parent_id) {
            return $this->createPath($department->parent_id, $path);
        }
        return $path;
    }

    public function updatePath($id)
    {
        $departments = Department::where('parent_id', $id)->get();
        foreach ($departments as $department) {
            $department->path = implode(' -> ', $this->createPath($department->id, []));
            $department->save();
            $this->updatePath($department->id);
        }
    }

    public function createLevel($id, $level)
    {
        $department = Department::find($id);
        array_unshift($level, $department->name);
        if ($department->parent_id) {
            return $this->createLevel($department->parent_id, $level);
        }
        return $level;
    }

    public function updateLevel($id)
    {
        $departments = Department::where('parent_id', $id)->get();
        foreach ($departments as $department) {
            $department->level = count($this->createLevel($department->id, []));
            $department->save();
            $this->updateLevel($department->id);
        }
    }
}