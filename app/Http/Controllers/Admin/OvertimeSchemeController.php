<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OvertimeSchemeList;
use App\Models\OvertimeScheme;
use App\Models\OvertimeschemeDepartment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class OvertimeSchemeController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'overtimescheme'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $category = strtoupper($request->category);
        $workingtime = $request->working_time;

        //Count Data
        $query = DB::table('overtime_schemes');
        $query->select('overtime_schemes.*');
        $query->whereRaw("upper(overtime_schemes.scheme_name) like '%$name%'");
        if ($workingtime != "") {
            $query->where('overtime_schemes.working_time', '=', $workingtime);
        }
        if ($category != "") {
            $query->whereRaw("upper(overtime_schemes.category) like '%$category%'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('overtime_schemes');
        $query->select('overtime_schemes.*');
        $query->whereRaw("upper(overtime_schemes.scheme_name) like '%$name%'");
        if ($workingtime != "") {
            $query->where('overtime_schemes.working_time', '=', $workingtime);
        }
        if ($category != "") {
            $query->whereRaw("upper(overtime_schemes.category) like '%$category%'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $overtimes = $query->get();

        $data = [];
        foreach ($overtimes as $overtime) {
            $overtime->no = ++$start;
            $overtime->category = @config('enums.allowance_category')[$overtime->category];
            $data[] = $overtime;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.overtimescheme.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.overtimescheme.create');
    }

    public function getLatestId()
    {
        $read = OvertimeScheme::max('id');
        return $read + 1;
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
            'scheme_name'       => 'required',
            'category'          => 'required',
            'working_time'      => 'required|numeric'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $id = $this->getLatestId();
        DB::beginTransaction();
        $overtime = OvertimeScheme::create([
            'id'            => $id,
            'scheme_name'   => $request->scheme_name,
            'category'      => $request->category,
            'working_time'  => $request->working_time
        ]);
        if ($overtime) {
            foreach ($request->department_id as $key => $department) {
                $Department = OvertimeschemeDepartment::create([
                    'overtime_scheme_id'    => $overtime->id,
                    'department_id'         => $department
                ]);
                if (!$Department) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $Department
                    ], 400);
                }
            }
            foreach ($request->workday as $key => $value) {
                foreach ($request->overtime_rules as $key1 => $value1) {
                    $list = OvertimeSchemeList::create([
                        'overtime_scheme_id'    => $id,
                        'recurrence_day'        => $value,
                        'hour'                  => $request->hour[$key1],
                        'amount'                => $request->amount[$key1]
                    ]);
                    if (!$list) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $list
                        ], 400);
                    }
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $overtime
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('overtimescheme.index')
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\OvertimeScheme  $overtimeScheme
     * @return \Illuminate\Http\Response
     */
    public function show(OvertimeScheme $overtimeScheme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OvertimeScheme  $overtimeScheme
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $overtime = OvertimeScheme::with('overtimelist')->find($id);
        $query = DB::table('overtime_scheme_lists');
        $query->select('overtime_scheme_lists.hour', 'overtime_scheme_lists.amount');
        $query->leftJoin('overtime_schemes', 'overtime_schemes.id', '=', 'overtime_scheme_lists.overtime_scheme_id');
        $query->where('overtime_scheme_lists.overtime_scheme_id', '=', $id);
        $query->whereNotNull('overtime_scheme_lists.recurrence_day');
        $query->orderBy('hour', 'asc');
        $list = $query->distinct()->get();

        $query1 = DB::table('overtime_scheme_lists');
        $query1->select('overtime_scheme_lists.recurrence_day as id');
        $query1->leftJoin('overtime_schemes', 'overtime_schemes.id', '=', 'overtime_scheme_lists.overtime_scheme_id');
        $query1->where('overtime_scheme_lists.overtime_scheme_id', '=', $id);
        $recurrence = $query1->distinct()->get();
        $day = [];
        foreach ($recurrence as $key => $value) {
            switch ($value->id) {
                case 'Mon':
                    $value->text = 'Monday';
                    break;
                case 'Tue':
                    $value->text = 'Tuesday';
                    break;
                case 'Wed':
                    $value->text = 'Wednesday';
                    break;
                case 'Thu':
                    $value->text = 'Thursday';
                    break;
                case 'Fri':
                    $value->text = 'Friday';
                    break;
                case 'Sat':
                    $value->text = 'Saturday';
                    break;

                default:
                    $value->text = 'Sunday';
                    break;
            }
            $day[] = $value;
        }
        if ($overtime) {
            return view('admin.overtimescheme.edit', compact('overtime', 'list', 'day'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OvertimeScheme  $overtimeScheme
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'scheme_name'       => 'required',
            'category'          => 'required',
            'working_time'      => 'required|numeric',
            'workday'           => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $overtime = OvertimeScheme::find($id);
        $overtime->scheme_name  = $request->scheme_name;
        $overtime->category     = $request->category;
        $overtime->working_time = $request->working_time;
        $overtime->save();
        if ($overtime) {
            $list = OvertimeSchemeList::where('overtime_scheme_id', '=', $id);
            $list->delete();
            if (isset($request->overtime_rules)) {
                foreach ($request->workday as $key => $value) {
                    foreach ($request->overtime_rules as $key1 => $value1) {
                        if (isset($request->hour[$key1]) && isset($request->amount[$key1])) {
                            $list = OvertimeSchemeList::create([
                                'overtime_scheme_id'    => $id,
                                'recurrence_day'        => $value,
                                'hour'                  => $request->hour[$key1],
                                'amount'                => $request->amount[$key1]
                            ]);
                            if (!$list) {
                                DB::rollBack();
                                return response()->json([
                                    'status'    => false,
                                    'message'   => $list
                                ], 400);
                            }
                        } else {
                            continue;
                        }
                    }
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'status'    => 'refresh',
                    'message'   => 'You must add at least one list please refresh your web browser'
                ], 400);
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $overtime
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('overtimescheme.index')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OvertimeScheme  $overtimeScheme
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $list = OvertimeSchemeList::where('overtime_scheme_id', '=', $id);
            $list->delete();
            $overtime = OvertimeScheme::find($id);
            $overtime->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'    => false,
                'message'   => 'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }
}