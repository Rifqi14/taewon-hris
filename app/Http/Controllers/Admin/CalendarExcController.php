<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CalendarException;
use App\Models\CalendarShiftSwitch;
use App\Models\Workingtime;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class CalendarExcController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'calendarexception'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $calendar_id = $request->calendar_id;

        //Count Data
        $query = DB::table('calendar_exceptions');
        $query->select('calendar_exceptions.*');
        $query->where('calendar_id', $calendar_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('calendar_exceptions');
        $query->select(
            'calendar_exceptions.*',
            'calendars.name as name'
        );
        $query->leftJoin('calendars', 'calendars.id', '=', 'calendar_exceptions.calendar_id');
        $query->where('calendar_id', $calendar_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('date_exception', 'asc');
        $calendarexcs = $query->get();

        $data = [];
        foreach ($calendarexcs as $calendarexc) {
            $calendarexc->no = ++$start;
            $calendarexc->date_exception = standardDate($calendarexc->date_exception);
            $data[] = $calendarexc;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function calendar($id)
    {
        $query = DB::table('calendar_exceptions');
        $query->select('calendar_exceptions.description as description', 'calendar_exceptions.is_switch_day as switch_day', 'calendar_exceptions.date_exception as start', 'calendar_exceptions.label_color as color', 'calendar_exceptions.text_color as textColor');
        $query->where('calendar_exceptions.calendar_id', '=', $id);
        $calendars = $query->get();

        $data = [];
        foreach ($calendars as $cal) {
            $cal->title = $cal->description;
            $data[] = $cal;
        }
        return response()->json($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $format = 'Y-m-d';
        $validator = Validator::make($request->all(), [
            'description'   => 'required',
            'reccurence_day' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        if (!isset($request->reccurence_day) && isset($request->day)) {
            return response()->json([
                'status'    => false,
                'message'   => 'Please check reccurrence if you want to create exceptions by days'
            ], 400);
        }

        if (dbDate($request->start_range) > dbDate($request->finish_range) || dbDate($request->start_specific) > dbDate($request->finish_specific)) {
            return response()->json([
                'status'    => false,
                'message'   => 'Start date exceeds Finish date'
            ], 400);
        }

        if ($request->reccurence_day == 'reccurence_day' && !isset($request->day)) {
            $start  = dbDate($request->start_range);
            $end    = dbDate($request->finish_range);

            $period = getDatesFromRange($start, $end, $format, '1 day');
            foreach ($period as $date) {
                $query = DB::table('calendar_exceptions');
                $query->select('calendar_exceptions.*');
                $query->where('date_exception', $date);
                $cal_exc = $query->get()->first();

                if ($cal_exc) {
                    $dates = date('d-m-Y', strtotime($cal_exc->date_exception));
                    $desc = $cal_exc->description;
                    $results['status'] = false;
                    $results['message'] = 'There is already date ' . $dates . ' with description ' . $desc . '.';
                    return response()->json($results, 400);
                } elseif (!$cal_exc) {
                    $exception = CalendarException::create([
                        'calendar_id'      => $request->calendar_id,
                        'date_exception'   => $date,
                        'description'      => $request->description,
                        'label_color'      => $request->label_exception,
                        'text_color'       => $request->text_color,
                        'day'              => date('D', strtotime($date))
                    ]);

                    if (!$exception) {
                        DB::rollBack();
                        $results['status'] = false;
                        $results['message'] = 'Wrong';
                    } else {
                        $results['status'] = true;
                        $results['message'] = 'Success add data';
                    };
                }
            }
            if ($results['status'] == true) {
                return response()->json($results, 200);
            } elseif ($results['status'] == false) {
                return response()->json($results, 400);
            }
        }

        if ($request->reccurence_day == 'reccurence_day' && isset($request->day)) {
            $start = date("Y-m-d", strtotime(dbDate($request->start_range)));
            $end   = date("Y-m-d", strtotime(dbDate($request->finish_range)));

            $period = getDatesFromRange($start, $end, $format, '1 weeks');
            foreach ($period as $week) {
                $w = date('w', strtotime($week));

                $days = $request->day;
                foreach ($days as $day) {
                    if ($day == 'sunday') {
                        $newdate = date('Y-m-d', strtotime(0 - $w . " day", strtotime($week))); //Sunday
                    }
                    if ($day == 'monday') {
                        $newdate = date('Y-m-d', strtotime(1 - $w . " day", strtotime($week))); //Monday
                    }
                    if ($day == 'tuesday') {
                        $newdate = date('Y-m-d', strtotime(2 - $w . " day", strtotime($week))); //Thursday
                    }
                    if ($day == 'wednesday') {
                        $newdate = date('Y-m-d', strtotime(3 - $w . " day", strtotime($week))); //Wednesday
                    }
                    if ($day == 'thursday') {
                        $newdate = date('Y-m-d', strtotime(4 - $w . " day", strtotime($week))); //Tuesday
                    }
                    if ($day == 'friday') {
                        $newdate = date('Y-m-d', strtotime(5 - $w . " day", strtotime($week))); //Friday
                    }
                    if ($day == 'saturday') {
                        $newdate = date('Y-m-d', strtotime(6 - $w . " day", strtotime($week))); //Saturday
                    }

                    if ($newdate >= $start && $newdate <= $end) {
                        $query = DB::table('calendar_exceptions');
                        $query->select('calendar_exceptions.*');
                        $query->where('calendar_id', $request->calendar_id);
                        $query->where('date_exception', date($format, strtotime($newdate)));
                        $cal_exc = $query->get()->first();

                        if ($cal_exc) {
                            $dates = date('d-m-Y', strtotime($cal_exc->date_exception));
                            $desc = $cal_exc->description;
                            $results['status'] = false;
                            $results['message'] = 'There is already date ' . $dates . ' with description ' . $desc . '.';
                            return response()->json($results, 400);
                        } elseif (!$cal_exc) {
                            $exception = CalendarException::create([
                                'calendar_id'       => $request->calendar_id,
                                'date_exception'    => $newdate,
                                'description'       => $request->description,
                                'label_color'       => $request->label_exception,
                                'text_color'        => $request->text_color,
                                'day'               => date('D', strtotime($newdate))
                            ]);

                            if (!$exception) {
                                DB::rollBack();
                                $results['status'] = false;
                                $results['message'] = 'Wrong';
                            } else {
                                $results['status'] = true;
                                $results['message'] = 'Success add data';
                            };
                        }
                    }
                }
            }
            if ($results['status'] == true) {
                return response()->json($results, 200);
            } elseif ($results['status'] == false) {
                return response()->json($results, 400);
            }
        }

        if ($request->reccurence_day == 'specific_day') {
            $exc_date = date('m-d', strtotime(dbDate($request->specific_date)));
            $start_year = date('Y', strtotime(dbDate($request->start_specific)));

            $start = date("Y-m-d", strtotime($start_year . '-' . $exc_date));
            $end   = date("Y-m-d", strtotime(dbDate($request->finish_specific)));
            $period = getDatesFromRange($start, $end, $format, "1 years");
            foreach ($period as $years) {
                $query = DB::table('calendar_exceptions');
                $query->select('calendar_exceptions.*');
                $query->where('calendar_id', $request->calendar_id);
                $query->where('date_exception', $years);
                $cal_exc = $query->get()->first();

                if ($cal_exc) {
                    $dates = date('d/m/Y', strtotime($cal_exc->date_exception));
                    $desc = $cal_exc->description;
                    $results['status'] = false;
                    $results['message'] = 'There is already date ' . $dates . ' with description ' . $desc . '.';
                } elseif (!$cal_exc) {
                    $exception = CalendarException::create([
                        'calendar_id'   => $request->calendar_id,
                        'date_exception' => $years,
                        'description'   => $request->description,
                        'label_color'      => $request->label_exception,
                        'text_color'       => $request->text_color,
                        'day'           => date('D', strtotime($years))
                    ]);

                    if (!$exception) {
                        DB::rollBack();
                        $results['status'] = false;
                        $results['message'] = 'Wrong';
                    } else {
                        $results['status'] = true;
                        $results['message'] = 'Success add data';
                    };
                }
            }
            if ($results['status'] == true) {
                return response()->json($results, 200);
            } elseif ($results['status'] == false) {
                return response()->json($results, 400);
            }
        }
    }

    public function addcalendar(Request $request)
    {
        $format = 'Y-m-d';
        $validator = Validator::make($request->all(), [
            'calendar_desc_add'   => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $calendar = CalendarException::create([
            'calendar_id'       => $request->id_calendar,
            'date_exception'    => dbDate($request->calendar_date),
            'description'       => $request->calendar_desc_add,
            'day'               => changeDateFormat('D', $request->calendar_date),
            'label_color'       => $request->calendar_label,
            'text_color'        => $request->calendar_text,
            'is_switch_day'     => isset($request->is_switch_day) ? 'YES' : 'NO'
        ]);
        if (!$calendar) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $calendar
            ], 400);
        } else {
            if ($calendar->is_switch_day == 'YES') {
                foreach ($request->workingtime_id as $key => $value) {
                    $workingshift = CalendarShiftSwitch::create([
                        'calendar_exceptions_id'    => $calendar->id,
                        'workingtime_id'            => $value,
                        'start'                     => $request->start[$key],
                        'finish'                    => $request->finish[$key],
                        'min_in'                    => $request->min_in[$key],
                        'max_out'                   => $request->max_out[$key],
                        'workhour'                  => $request->start[$key] > $request->finish[$key] ? Carbon::parse($request->start[$key])->diffInHours(Carbon::parse($request->finish[$key])) : Carbon::parse($request->start[$key])->diffInHours(Carbon::parse($request->finish[$key])->addDay()),
                        'day'                       => $calendar->day,
                        'min_workhour'              => $request->min_wt[$key],
                    ]);
                    if (!$workingshift) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $workingshift
                        ], 400);
                    }
                }
            }
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'message'   => 'Success add data'
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
        $calendar_exc = CalendarException::with('calendar')->find($id);
        return response()->json([
            'status'     => true,
            'data' => $calendar_exc
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
            'exception_date'    => 'required',
            'exception_desc'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $query = DB::table('calendar_exceptions');
        $query->select('calendar_exceptions.*');
        $query->where('calendar_id', $request->calendar_id);
        $query->where('date_exception', dbDate($request->exception_date));
        $query->where('description', dbDate($request->exception_desc));
        $cal_exc = $query->get()->first();

        if ($cal_exc) {
            return response()->json([
                'status'    => false,
                'message'     => 'There is already exception with date ' . date('d/m/Y', strtotime(dbDate($request->exception_date))) . ' and description is ' . $request->exception_desc . '.'
            ], 400);
        }

        $calendar_exc = CalendarException::find($id);
        $calendar_exc->date_exception  = dbDate($request->exception_date);
        $calendar_exc->description     = $request->exception_desc;
        $calendar_exc->label_color     = $request->exception_label;
        $calendar_exc->text_color      = $request->exception_text;
        $calendar_exc->save();

        if (!$calendar_exc) {
            return response()->json([
                'status'    => false,
                'message'     => $calendar_exc
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message'   => 'Success edit data'
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
            $calendar_exc = CalendarException::find($id);
            $calendar_exc->delete();
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