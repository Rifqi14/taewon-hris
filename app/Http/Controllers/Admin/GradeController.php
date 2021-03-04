<?php

namespace App\Http\Controllers\Admin;

use App\Models\Grade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GradeController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'grade'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $bestsallary_id = $request->bestsallary_id;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('grades');
        $query->select('grades.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($bestsallary_id){
            $query->where('bestsallary_id','=',$bestsallary_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('grades');
        $query->select('grades.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($bestsallary_id){
            $query->where('bestsallary_id','=',$bestsallary_id);
        }
        $query->offset($start);
        $query->limit($length);
        $grades = $query->get();

        $data = [];
        foreach($grades as $grade){
            $grade->no = ++$start;
            $data[] = $grade;
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
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('grades');
        $query->select('grades.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('grades');
        $query->select('grades.*','name as name');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $grades = $query->get();

        $data = [];
        foreach($grades as $grade){
            $grade->no = ++$start;
            $data[] = $grade;
        }
        return response()->json([
            'draw'=>$request->draw,
            'recordsTotal'=>$recordsTotal,
            'recordsFiltered'=>$recordsTotal,
            'data'=>$data
        ], 200);
    }
    public function index()
    {
        return view('admin.grade.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.grade.create');
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
            // 'code' => 'required',
            'name'      => 'required',
            'order'      => 'required',
            'min_duration'      => 'required',
            'bestsallary_id'      => 'required',
            'basic_umk_value'      => 'required',
            'additional_type'      => 'required',
            'additional_value'      => 'required',
            'basic_sallary'      => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $grade = Grade::create([
            'code' => '',
            'site_id' => Session::get('site_id'),
            'name' => $request->name,
            'order' => $request->order,
            'min_duration' => $request->min_duration,
            'month' => $request->month,
            'bestsallary_id' => $request->bestsallary_id,
            'basic_umk_value' => $request->basic_umk_value,
            'additional_type' => $request->additional_type,
            'additional_value' => $request->additional_value,
            'basic_sallary' => $request->basic_sallary,
            'notes' => $request->notes,
            'status' => $request->status
        ]);
        if($request->code){ 
            $grade->code = $request->code;
            $grade->save();
        }else{
            $grade->code = $grade->code_system;
            $grade->save();
        }
        if (!$grade) {
            return response()->json([
                'status' => false,
                'message'   => $grade
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('grade.index'),
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
        $grade = Grade::find($id);
        if ($grade) {
            return view('admin.grade.edit', compact('grade'));
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
            'name'      => 'required',
            'order'      => 'required',
            'min_duration'      => 'required',
            'bestsallary_id'      => 'required',
            'basic_umk_value'      => 'required',
            'additional_type'      => 'required',
            'additional_value'      => 'required',
            'basic_sallary'      => 'required',
            'status'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $grade = Grade::find($id);
        $grade->code = $request->code;
        $grade->name = $request->name;
        $grade->order = $request->order;
        $grade->month = $request->month;
        $grade->bestsallary_id = $request->bestsallary_id;
        $grade->basic_umk_value = $request->basic_umk_value;
        $grade->additional_type = $request->additional_type;
        $grade->additional_value = $request->additional_value;
        $grade->basic_sallary = $request->basic_sallary;
        $grade->min_duration = $request->min_duration;
        $grade->notes = $request->notes;
        $grade->status = $request->status?1:0;
        $grade->save();
        if($request->code){ 
            $grade->code = $request->code;
            $grade->save();
        }else{
            $grade->code = $grade->code_system;
            $grade->save();
        }
        if (!$grade) {
            return response()->json([
                'status' => false,
                'message'   => $grade
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('grade.index'),
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
        $user = Grade::find($id);
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
}
