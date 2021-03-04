<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class TitleController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'title'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.title.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('titles');
        $query->select('titles.*');
        $query->whereRaw("upper(titles.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('titles');
        $query->select('titles.*');
        $query->whereRaw("upper(titles.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $titles = $query->get();

        $data = [];
        foreach ($titles as $title) {
            $title->no = ++$start;
            $data[] = $title;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
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
        $query = DB::table('titles');
        $query->select('titles.*');
        $query->whereRaw("upper(titles.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('titles');
        $query->select('titles.*');
        // $query->leftJoin('titles as parent', 'parent.id', '=', 'titles.parent_id');
        $query->whereRaw("upper(titles.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $titles = $query->get();

        $data = [];
        foreach ($titles as $title) {
            $title->no = ++$start;
            $title->path = str_replace('->', ' <i class="fas fa-angle-right"></i> ', $title->path);
            $data[] = $title;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function create()
    {
        return view('admin.title.create');
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
            'name'      => 'required|min:3',
            // 'code'      => 'required|min:3|alpha_dash',
            // 'max_person'      => 'required',
            'status'      => 'required',
            // 'department_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $title = Title::create([
            'site_id' => Session::get('site_id'),
            // 'department_id'  => $request->department_id,
            'name'      => $request->name,
            // 'parent_id' => $request->parent_id ? $request->parent_id : 0,
            'code'      => '',
            // 'max_person'   => $request->max_person,
            'notes'      => $request->notes,
            'status'      => $request->status
        ]);
        if ($request->code) {
            $title->code = $request->code;
            $title->save();
        } else {
            $title->code = $title->code_system;
            $title->save();
        }
        if (!$title) {
            return response()->json([
                'status' => false,
                'message'   => $title
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('title.index'),
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

    public function edit($id)
    {
        $title = Title::find($id);
        if ($title) {
            return view('admin.title.edit', compact('title'));
        } else {
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
            'name'      => 'required',
            // 'code'      => 'required|alpha_dash',
            // 'max_person'      => 'required',
            'status'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $title = Title::find($id);
        $title->code = $request->code;
        $title->name = $request->name;
        $title->notes = $request->notes;
        $title->status = $request->status;
        $title->save();
        if (!$title) {
            return response()->json([
                'status' => false,
                'message'   => $title
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('title.index'),
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
            $title = Title::find($id);
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