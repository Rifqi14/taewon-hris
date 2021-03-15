<?php

namespace App\Http\Controllers\Admin;

use App\Models\BreakTime;
use App\Models\BreakTimeLine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BreaktimeDepartment;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BreakTimeController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'breaktime'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $working_time_type = strtoupper($request->working_time_type);
        $description = strtoupper($request->name);

        //Count Data
        $query = DB::table('workingtimes');
        $query->select('workingtimes.*');
        if ($description) {
            $query->whereRaw("upper(description) like '%$description%'");
        }
        if ($working_time_type) {
            $query->whereRaw("upper(working_time_type) like '$working_time_type'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('workingtimes');
        $query->select('workingtimes.*');
        if ($description) {
            $query->whereRaw("upper(description) like '%$description%'");
        }
        if ($working_time_type) {
            $query->whereRaw("upper(working_time_type) like '$working_time_type'");
        }
        $query->offset($start);
        $query->limit($length);
        $workingtimes = $query->get();

        $data = [];
        foreach ($workingtimes as $workingtime) {
            $workingtime->no = ++$start;
            $data[] = $workingtime;
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
        $break_time = strtoupper($request->break_time);

        //Count Data
        $query = DB::table('break_times');
        $query->select('break_times.*');
        if ($break_time) {
            $query->whereRaw("upper(break_time) like '$break_time'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('break_times');
        $query->select('break_times.*');
        if ($break_time) {
            $query->whereRaw("upper(break_time) like '$break_time'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $break_times = $query->get();

        $data = [];
        foreach ($break_times as $break_time) {
            $break_time->no = ++$start;
            $data[] = $break_time;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    /**
     * Show the index page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.breaktime.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.breaktime.create');
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
            'break_time'    => 'required',
            'status'        => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $breaktime = Breaktime::create([
            'break_time' => $request->break_time,
            'start_time' => $request->start_time,
            'finish_time' => $request->finish_time,
            'notes' => $request->notes,
            'status' => $request->status,
            'breaktime' => Carbon::parse($request->start_time)->diff(Carbon::parse($request->finish_time))->format('%h')
        ]);
        if ($breaktime) {
            foreach ($request->department_id as $key => $department) {
                $createDepartment = BreaktimeDepartment::create([
                    'breaktime_id'      => $breaktime->id,
                    'department_id'     => $department
                ]);
                if (!$createDepartment) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $createDepartment
                    ], 400);
                }
            }
            $workgroup = explode(',', $request->workgroup);
            foreach ($workgroup as $key => $value) {
                $breaktimeline[] = array(
                    'breaktime_id'  => $breaktime->id,
                    'workgroup_id'  => $value,
                    'created_at'    => Carbon::now()->toDateTimeString(),
                    'updated_at'    => Carbon::now()->toDateTimeString(),
                );
            }
            $timeline = BreakTimeLine::insert($breaktimeline);
            if (!$timeline) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $breaktime
                ], 400);
            }
        }

        if (!$breaktime) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $breaktime
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('breaktime.index'),
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
        $breaktime = BreakTime::with(["breaktimeline", "breaktimedepartment", "breaktimedepartment.department"])->find($id);
        if ($breaktime) {
            $workgroup = [];
            foreach ($breaktime->breaktimeline as $brek) {
                $workgroup[] = $brek->workgroup->name;
            }
            return view('admin.breaktime.edit', compact('breaktime','workgroup'));
        } else {
            abort(404);
        }
    }

    public function multi(Request $request){
        $data = $request->data;
        $breaktime = BreakTime::with("breaktimeline")->where('id', 7)->first();
        // dd($breaktime);
        
            $workgroup = [];
            foreach ($breaktime->breaktimeline as $brek) {
            //  $brek->workgroup_id = $brek->workgroup_id;
             $brek->workgroup_name = $brek->workgroup->name;
             $workgroup[] = $brek;
            }
        // dd($workgroup);
        
        return response()->json([
            'status'     => true,
            'results'     => $workgroup,
        ], 200);
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
            'break_time'         => 'required',
            'status'         => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $breaktime = BreakTime::find($id);
        $breaktime->break_time = $request->break_time;
        $breaktime->start_time = $request->start_time;
        $breaktime->finish_time = $request->finish_time;
        $breaktime->notes = $request->notes;
        $breaktime->status = $request->status;
        $breaktime->save();

        if ($breaktime) {
            $departmentDelete = BreaktimeDepartment::where('breaktime_id', $id);
            $departmentDelete->delete();
            $departmentPath = explode(",", $request->department_id);
            foreach ($departmentPath as $key => $path) {
                $departments = Department::where('path', 'like', "%$path%")->get();
                foreach ($departments as $key => $department) {
                    $createBreaktimeDepartment = BreaktimeDepartment::create([
                        'department_id'     => $department->id,
                        'breaktime_id'      => $breaktime->id,
                    ]);
                    if (!$createBreaktimeDepartment) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $createBreaktimeDepartment
                        ], 400);
                    }
                }
            }
        }

        if (!$breaktime) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $breaktime
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('breaktime.index'),
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
            $breaktime = BreakTime::find($id);
            $breaktime->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}