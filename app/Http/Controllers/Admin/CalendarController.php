<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Models\CalendarException;
use App\Models\Workingtime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class CalendarController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'calendar'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $calendar = strtoupper($request->name);

        //Count Data
        $query = DB::table('calendars');
        $query->select('calendars.*');
        $query->whereRaw("upper(name) like '%$calendar%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('calendars');
        $query->select('calendars.*');
        $query->whereRaw("upper(name) like '%$calendar%'");
        $query->offset($start);
        $query->limit($length);
        $calendars = $query->get();

        $data = [];
        foreach ($calendars as $cal) {
            $cal->no = ++$start;
            $data[] = $cal;
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
        $calendar = strtoupper($request->name);
        $code = strtoupper($request->code);
        $description = strtoupper($request->description);

        //Count Data
        $query = DB::table('calendars');
        $query->select('calendars.*');
        $query->whereRaw("upper(name) like '%$calendar%'");
        $query->whereRaw("upper(code) like '%$code%'");
        if ($description) {
            $query->whereRaw("upper(description) like '%$description%'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('calendars');
        $query->select('calendars.*');
        $query->whereRaw("upper(name) like '%$calendar%'");
        $query->whereRaw("upper(code) like '%$code%'");
        if ($description) {
            $query->whereRaw("upper(description) like '%$description%'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $calendars = $query->get();

        $data = [];
        foreach ($calendars as $cal) {
            $cal->no = ++$start;
            $data[] = $cal;
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
        return view('admin.calendar.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.calendar.create');
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
            'code'              => 'required|alpha_dash|unique:calendars',
            'calendar_name'     => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $calendar = Calendar::create([
            'code'          => $request->code,
            'name'          => $request->calendar_name,
            'label_color'   => $request->label_color,
            'description'   => $request->calendar_desc,
            'is_default'    => $request->is_default ? 1 : 0
        ]);
        if (!$calendar) {
            return response()->json([
                'status'    => false,
                'message'   => $calendar
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('calendar.index')
        ], 200);
    }

    /**
     * Display the detail resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $calendar = Calendar::find($id);
        $query = DB::table('calendar_exceptions');
        $query->select('calendars.name as title', 'calendar_exceptions.date_exception as start', 'calendars.label_color as color');
        $query->leftJoin('calendars', 'calendars.id', '=', 'calendar_exceptions.calendar_id');
        $query->where('calendar_exceptions.calendar_id', '=', $id);
        $calendars = $query->get();

        $data = [];
        foreach ($calendars as $cal) {
            $cal->textColor = 'black';
            $data[] = $cal;
        }
        $exception = $data;
        if ($calendar) {
            return view('admin.calendar.detail', compact('calendar', 'exception'));
        } else {
            abort(404);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $calendar = Calendar::find($id);
        $query = DB::table('calendar_exceptions');
        $query->select('calendar_exceptions.description as title', 'calendar_exceptions.date_exception as start', 'calendar_exceptions.label_color as color', 'calendar_exceptions.text_color as textColor');
        $query->where('calendar_exceptions.calendar_id', '=', $id);
        $calendars = $query->get();

        $data = [];
        foreach ($calendars as $cal) {
            $data[] = $cal;
        }
        $exception = $data;

        $shift = Workingtime::all();
        if ($calendar) {
            return view('admin.calendar.edit', compact('calendar', 'exception', 'shift'));
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
            'code'           => 'required|alpha_dash',
            'calendar_name'  => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $calendar = Calendar::find($id);
        $calendar->code = $request->code;
        $calendar->name = $request->calendar_name;
        $calendar->label_color = $request->label_color;
        $calendar->description = $request->calendar_desc;
        $calendar->is_default = $request->is_default ? 1 : 0;
        $calendar->save();
        if (!$calendar) {
            return response()->json([
                'status'    => false,
                'message'   => $calendar
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('calendar.index')
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
            $calendar = Calendar::find($id);
            $calendar->delete();
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