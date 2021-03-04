<?php

namespace App\Http\Controllers\Admin;

use App\Models\DailyReportDriver;
use App\Models\DailyReportDriverDetail;
use App\Models\DailyReportDriverAdditional;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

class DailyReportDriverController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'dailyreportdriver'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLatestId()
    {
        $read = DailyReportDriver::max('id');
        return $read + 1;
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $driver_id = strtoupper(str_replace("'","''",$request->driver_id));
        $police_no = $request->police_no;
        $exp_passenger = $request->exp_passenger;
        $date_from = date('Y-m-d', strtotime(changeSlash($request->date_from)));
        $date_to = date('Y-m-d', strtotime(changeSlash($request->date_to)));

        //Count Data
        $query = DB::table('daily_report_drivers');
        $query->select(
            'daily_report_drivers.*',
            'driver.name as driver_name',
            'driver.nid',
            DB::raw("(SELECT SUM(daily_report_driver_details.arrival_km - daily_report_driver_details.departure_km) FROM daily_report_driver_details
                        WHERE daily_report_driver_details.daily_report_driver_id = daily_report_drivers.id) as total_km"),
            DB::raw("(SELECT MAX(daily_report_driver_details.arrival) FROM daily_report_driver_details
                        WHERE daily_report_driver_details.daily_report_driver_id = daily_report_drivers.id) as last_arrival")
        );
        $query->leftJoin('employees as driver', 'driver.id', '=', 'daily_report_drivers.driver_id');
        // $query->leftJoin('daily_report_driver_details','daily_report_driver_details.daily_report_driver_id','=','daily_report_drivers.id');

        if ($driver_id != "") {
            $query->whereRaw("upper(driver.name) like '%$driver_id%'");
        }
        if ($police_no) {
            $query->whereIn("daily_report_drivers.police_no", $police_no);
        }
        if ($exp_passenger) {
            $query->whereIn("daily_report_drivers.exp_passenger", $exp_passenger);
        }
        if ($date_from) {
            $query->where('date', '>=', $date_from);
        }
        if ($date_to) {
            $query->where('date', '<=', $date_to);
        }

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('daily_report_drivers');
        $query->select(
            'daily_report_drivers.*',
            'driver.name as driver_name',
            'driver.nid',
            DB::raw("(SELECT SUM(daily_report_driver_details.arrival_km - daily_report_driver_details.departure_km) FROM daily_report_driver_details
                        WHERE daily_report_driver_details.daily_report_driver_id = daily_report_drivers.id) as total_km"),
            DB::raw("(SELECT MAX(daily_report_driver_details.arrival) FROM daily_report_driver_details
                        WHERE daily_report_driver_details.daily_report_driver_id = daily_report_drivers.id) as last_arrival")
        );
        $query->leftJoin('employees as driver', 'driver.id', '=', 'daily_report_drivers.driver_id');
        // $query->leftJoin('daily_report_driver_details','daily_report_driver_details.daily_report_driver_id','=','daily_report_drivers.id');
        if ($driver_id != "") {
            $query->whereRaw("upper(driver.name) like '%$driver_id%'");
        }
        if ($police_no) {
            $query->whereIn("daily_report_drivers.police_no", $police_no);
        }
        if ($exp_passenger) {
            $query->whereIn("daily_report_drivers.exp_passenger", $exp_passenger);
        }
        if ($date_from) {
            $query->where('date', '>=', $date_from);
        }
        if ($date_to) {
            $query->where('date', '<=', $date_to);
        }

        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $drivers = $query->get();

        $data = [];
        foreach ($drivers as $driv) {
            $driv->no = ++$start;
            // $driv->arrival = $this->getArrival($driv->id)->data;
            $data[] = $driv;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }
    public function getArrival($daily_report_driver_id)
    {
        // $start = $request->start;
        // $length = $request->length;
        // $query = $request->search['value'];
        // $sort = $request->columns[$request->order[0]['column']]['data'];
        // $dir = $request->order[0]['dir'];
        // $daily_report_driver_id = strtoupper($request->daily_report_driver_id);

        //Count Data
        $query = DB::table('daily_report_driver_details');
        $query->select('daily_report_driver_details.*');

        if ($daily_report_driver_id != "") {
            $query->where("daily_report_driver_details.daily_report_driver_id", $daily_report_driver_id);
        }

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('daily_report_driver_details');
        $query->select('daily_report_driver_details.*');
        if ($daily_report_driver_id != "") {
            $query->where("daily_report_driver_details.daily_report_driver_id", $daily_report_driver_id);
        }

        // $query->offset($start);
        // $query->limit($length);
        // $query->orderBy($sort, $dir);
        $drivers = $query->get();

        $data = [];
        foreach ($drivers as $driv) {
            // $driv->no = ++$start;
            $data[] = $driv;
        }
        return response()->json([
            // 'draw'              => $request->draw,
            // 'recordsTotal'      => $recordsTotal,
            // 'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }
    public function readcalculation(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $dailyreportdriver_id = $request->dailyreportdriver_id;
        $driver_id = $request->driver_id ? $request->driver_id : 0;
        $exclude_ids = $request->exclude_ids;

        //Count Data
        $query = DB::table('daily_report_driver_details');
        $query->select('daily_report_driver_details.*', 'daily_report_drivers.driver_id');
        $query->leftJoin('daily_report_drivers', 'daily_report_drivers.id', '=', 'daily_report_driver_details.daily_report_driver_id');

        $query->where("daily_report_driver_details.daily_report_driver_id", $dailyreportdriver_id);
        // $query->where("daily_report_drivers.driver_id",$driver_id);
        $query->where("daily_report_driver_details.status", 0);
        // $query->where("daily_report_driver_details.id", '<>',$exclude_ids);

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('daily_report_driver_details');
        $query->select('daily_report_driver_details.*', 'daily_report_drivers.driver_id');
        $query->leftJoin('daily_report_drivers', 'daily_report_drivers.id', '=', 'daily_report_driver_details.daily_report_driver_id');

        $query->where("daily_report_driver_details.daily_report_driver_id", $dailyreportdriver_id);
        // $query->where("daily_report_drivers.driver_id",$driver_id);
        $query->where("daily_report_driver_details.status", 0);
        // $query->where("daily_report_driver_details.id", '<>',$exclude_ids);

        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $drivers = $query->get();

        $data = [];
        foreach ($drivers as $driv) {
            $driv->no = ++$start;
            $data[] = $driv;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    public function readadditional(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $dailyreportdriver_id = $request->dailyreportdriver_id;
        $driver_id = $request->driver_id ? $request->driver_id : 0;
        $exclude_ids = $request->exclude_ids;

        //Count Data
        $query = DB::table('daily_report_driver_additionals');
        $query->select('daily_report_driver_additionals.*', 'daily_report_drivers.driver_id');
        $query->leftJoin('daily_report_drivers', 'daily_report_drivers.id', '=', 'daily_report_driver_additionals.daily_report_driver_id');

        $query->where("daily_report_driver_additionals.daily_report_driver_id", $dailyreportdriver_id);
        // $query->where("daily_report_drivers.driver_id",$driver_id);
        $query->where("daily_report_driver_additionals.status", 0);
        // $query->where("daily_report_driver_additionals.id", '<>',$exclude_ids);

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('daily_report_driver_additionals');
        $query->select('daily_report_driver_additionals.*', 'daily_report_drivers.driver_id');
        $query->leftJoin('daily_report_drivers', 'daily_report_drivers.id', '=', 'daily_report_driver_additionals.daily_report_driver_id');

        $query->where("daily_report_driver_additionals.daily_report_driver_id", $dailyreportdriver_id);
        // $query->where("daily_report_drivers.driver_id",$driver_id);
        $query->where("daily_report_driver_additionals.status", 0);
        // $query->where("daily_report_driver_additionals.id", '<>',$exclude_ids);

        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $drivers = $query->get();

        $data = [];
        foreach ($drivers as $driv) {
            $driv->no = ++$start;
            $data[] = $driv;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    public function index()
    {
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->where('employees.status', 1);
        $query->whereRaw("upper(departments.path) like '%DRIVER%'");
        $employees = $query->get();
        $query = DB::table('daily_report_drivers');
        $query->select('daily_report_drivers.police_no');
        $query->groupBy('daily_report_drivers.police_no');
        $daily_report = $query->get();
        $query = DB::table('daily_report_drivers');
        $query->select('daily_report_drivers.exp_passenger');
        $query->groupBy('daily_report_drivers.exp_passenger');
        $passengers = $query->get();
        return view('admin.dailyreportdriver.index', compact('employees','daily_report','passengers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.dailyreportdriver.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = $this->getLatestId();
        DB::beginTransaction();
        $dailyreportdriver       = DailyReportDriver::create([
            'date'                  => changeDateFormat('Y-m-d', changeSlash($request->date)),
            'code'                  => $request->code,
            'driver_id'          => $request->driver_id,
            'police_no'          => $request->police_no,
            'exp_passenger'      => $request->exp_passenger,
            'subtotal'           => $request->subtotal,
            'subtotaladditional' => $request->subtotaladditional,
            'grandtotal'         => $request->grandtotal,
        ]);
        if ($dailyreportdriver) {
            if (isset($request->product_item)) {
                foreach ($request->product_item as $key => $value) {
                    $dailyreportdriverdetail = DailyReportDriverDetail::create([
                        'daily_report_driver_id'  => $dailyreportdriver->id,
                        'destination'             => $request->destination[$key],
                        'departure'               => $request->departure[$key],
                        'departure_km'            => $request->departure_km[$key],
                        'arrival'                 => $request->arrival[$key],
                        'arrival_km'              => $request->arrival_km[$key],
                        'parking'                 => $request->parking[$key],
                        'toll_money'              => $request->toll_money[$key],
                        'oil'                     => $request->oil[$key],
                        'etc'                     => $request->etc[$key],
                        'total'                   => $request->total[$key],
                    ]);
                    $dailyreportdriverdetail->reff_detail = $dailyreportdriverdetail->reff_detail;
                    $dailyreportdriverdetail->save();
                    if (!$dailyreportdriverdetail) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $dailyreportdriverdetail
                        ], 400);
                    }
                }
            }
            if (isset($request->product_additional)) {
                foreach ($request->product_additional as $key => $value) {
                    $dailyreportdriveradditional = DailyReportDriverAdditional::create([
                        'daily_report_driver_id'  => $dailyreportdriver->id,
                        'additional_name'         => $request->additional_name[$key],
                        'additional_total'        => $request->additional_total[$key],
                    ]);
                    $dailyreportdriveradditional->reff_additional = $dailyreportdriveradditional->reff_additional;
                    $dailyreportdriveradditional->save();
                    if (!$dailyreportdriveradditional) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $dailyreportdriveradditional
                        ], 400);
                    }
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $dailyreportdriver
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('dailyreportdriver.index')
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
        $dailyreportdriver = DailyReportDriver::with('dailyreportdriverdetail', 'dailyreportdriveradditional')->find($id);
        $query = DB::table('daily_report_drivers');
        $query->select('daily_report_drivers.*');

        if ($dailyreportdriver) {
            return view('admin.dailyreportdriver.edit', compact('dailyreportdriver'));
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
        DB::beginTransaction();
        $dailyreportdriver                    = DailyReportDriver::find($id);
        $dailyreportdriver->date              = changeDateFormat('Y-m-d', changeSlash($request->date));
        $dailyreportdriver->code               = $request->code;
        $dailyreportdriver->driver_id          = $request->driver_id;
        $dailyreportdriver->police_no         = $request->police_no;
        $dailyreportdriver->exp_passenger     = $request->exp_passenger;
        $dailyreportdriver->subtotal          = $request->subtotal;
        $dailyreportdriver->subtotaladditional = $request->subtotaladditional;
        $dailyreportdriver->grandtotal        = $request->grandtotal;
        $dailyreportdriver->save();
        if ($dailyreportdriver) {
            if (isset($request->product_item)) {
                $detail = DailyReportDriverDetail::where('daily_report_driver_id', '=', $id);
                $detail->delete();
                $additional = DailyReportDriverAdditional::where('daily_report_driver_id', '=', $id);
                $additional->delete();
                foreach ($request->product_item as $key => $value) {
                    $dailyreportdriverdetail = DailyReportDriverDetail::create([
                        'daily_report_driver_id'  => $id,
                        'destination'             => $request->destination[$key],
                        'departure'               => $request->departure[$key],
                        'departure_km'            => $request->departure_km[$key],
                        'arrival'                 => $request->arrival[$key],
                        'arrival_km'              => $request->arrival_km[$key],
                        'parking'                 => $request->parking[$key],
                        'toll_money'              => $request->toll_money[$key],
                        'oil'                     => $request->oil[$key],
                        'etc'                     => $request->etc[$key],
                        'total'                   => $request->total[$key],
                    ]);
                    if (!$dailyreportdriverdetail) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $dailyreportdriverdetail
                        ], 400);
                    }
                }
            }
            if (isset($request->product_additional)) {
                foreach ($request->product_additional as $key => $value) {
                    $dailyreportdriveradditional = DailyReportDriverAdditional::create([
                        'daily_report_driver_id'  => $id,
                        'additional_name'         => $request->additional_name[$key],
                        'additional_total'        => $request->additional_total[$key],
                    ]);
                    if (!$dailyreportdriveradditional) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $dailyreportdriveradditional
                        ], 400);
                    }
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $dailyreportdriver
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('dailyreportdriver.index')
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
            $detail = DailyReportDriverDetail::where('daily_report_driver_id', '=', $id);
            $detail->delete();
            $additional = DailyReportDriverAdditional::where('daily_report_driver_id', '=', $id);
            $additional->delete();
            $deliveryorder = DailyReportDriver::find($id);
            $deliveryorder->delete();
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