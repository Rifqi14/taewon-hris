<?php

namespace App\Http\Controllers\Admin;

use App\Models\DriverAllowanceList;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DeliveryOrder;
use Illuminate\Support\Facades\DB;

class DriverAllowanceListController extends Controller
{

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->column[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;
        $month = $request->month;
        $year = $request->year;
        // DB::enableQueryLog();
        // Count Data
        // $query = DeliveryOrder::where('driver_id', $employee_id)->whereMonth('departure_time', $month)->whereYear('departure_time', $year);
        $query = DB::table('delivery_orders');
        // $query->select('driver_allowance_lists.date as date', DB::raw('max(rit) as rit'), DB::raw('sum(value) as value'), 'driver_allowance_lists.truck', 'driver_allowance_lists.driver_id', 'driver_allowance_lists.group');
        $query->select('delivery_orders.departure_time as date', 
                    'delivery_orders.type_truck as truck', 
                    'delivery_orders.group as kloter', 
                    'partners.rit as rit',
                    'partners.name as customer',
                    'delivery_orders.driver_id',
                    'driver_lists.value as value',
                    'driver_lists.rit as rule',
                    'driver_lists.type',
                    'delivery_orders.list_order',
                    'driver_lists.type_value');
        $query->leftJoin('partners', 'partners.id','=','delivery_orders.partner_id');
        // $query->leftJoin('driver_lists', 'driver_lists.type','=','delivery_orders.type_truck');
        $query->leftJoin('driver_lists', function($join){
            $join->on( 'driver_lists.type','=','delivery_orders.type_truck');
            $join->on( 'driver_lists.rit','=','delivery_orders.list_order');
        });
        // $query->where('delivery_orders.list_order', 1);
        // $query->leftJoin(DB::raw("(select)"), DB::raw('driver_lists.type'),'=','delivery_orders.type_truck');
        // $query->leftJoin('driver_lists','driver_lists.driver_allowance_id','=','driver_allowance_lists.id');
        $query->where('delivery_orders.driver_id', $employee_id);
        // $query->where('delivery_orders.list_order', 1);
        $query->whereMonth('delivery_orders.departure_time', $month);
        $query->whereYear('delivery_orders.departure_time', $year);
        $recordsTotal = $query->count();

        // $query = DB::table('delivery_orders');
        // $query->select('delivery_orders.driver_id',
        //             'partners.rit');
        // $query->leftJoin('partners','partners.id','=','delivery_orders.partners_id');
        // $query->orderBy('partners.rit', 'desc');
        // $query = DB::table('delivery_orders');
        // $query->select('delivery_orders.tyoe_truck');
        // $query->groupBy()

        // Select Pagination
        $query = DB::table('delivery_orders');
        $query->select('delivery_orders.departure_time as date', 
                    'delivery_orders.type_truck as truck', 
                    'delivery_orders.group as kloter', 
                    'partners.rit as rit',
                    'partners.name as customer',
                    'delivery_orders.driver_id',
                    'driver_lists.value as value',
                    'driver_lists.rit as rule',
                    'driver_lists.type',
                    'delivery_orders.list_order',
                    'driver_lists.type_value');
        $query->leftJoin('partners', 'partners.id','=','delivery_orders.partner_id');
        // $query->leftJoin('driver_lists','driver_lists.type','=','delivery_orders.type_truck');
        $query->leftJoin('driver_lists', function($join){
            $join->on( 'driver_lists.type','=','delivery_orders.type_truck');
            $join->on( 'driver_lists.rit','=','delivery_orders.list_order');
        });
        $query->where('delivery_orders.driver_id', $employee_id);
        // $query->where('delivery_orders.list_order', 'driver_lists.rit');
        // $query->where('driver_lists.value','=', 'partners.rit');
        $query->whereMonth('delivery_orders.departure_time', $month);
        $query->whereYear('delivery_orders.departure_time', $year);
        // $query->select('driver_allowance_lists.date as date', DB::raw('max(rit) as rit'), DB::raw('sum(value) as value'), 'driver_allowance_lists.truck', 'driver_allowance_lists.driver_id', 'driver_allowance_lists.group');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('driver_lists.rit', 'asc');
        // $query->groupBy('delivery_orders.driver_id','delivery_orders.departure_time', 'delivery_orders.type_truck', 'delivery_orders.group','partners.rit','partners.name','driver_lists.value','driver_lists.rit','driver_lists.type', 'delivery_orders.list_order','driver_lists.type_value');
        $driverallowances = $query->get();
        // dd($driverallowances);
        // dd(DB::getQueryLog());

        $data = [];
        foreach ($driverallowances as $driverallowance) {
            $driverallowance->no = ++$start;
            $driverallowance->total_value =($driverallowance->value/100) * $driverallowance->rit ;
            $data[] = $driverallowance;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    public function read_detail(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->column[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $date = $request->date;
        $driver = $request->driver;
        $truck = $request->truck;
        $group = $request->group;

        // Count Data
        $query = DeliveryOrder::where('driver_id', $driver)->where('departure_time', $date)->where('group', $group);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DeliveryOrder::where('driver_id', $driver)->where('departure_time', $date)->where('group', $group)->offset($start)->limit($length)->orderBy('date', 'asc');
        $driverallowances = $query->get();

        $data = [];
        foreach ($driverallowances as $driverallowance) {
            $driverallowance->no = ++$start;
            $data[] = $driverallowance;
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DriverAllowanceList  $driverAllowanceList
     * @return \Illuminate\Http\Response
     */
    public function show(DriverAllowanceList $driverAllowanceList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DriverAllowanceList  $driverAllowanceList
     * @return \Illuminate\Http\Response
     */
    public function edit(DriverAllowanceList $driverAllowanceList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DriverAllowanceList  $driverAllowanceList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DriverAllowanceList $driverAllowanceList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DriverAllowanceList  $driverAllowanceList
     * @return \Illuminate\Http\Response
     */
    public function destroy(DriverAllowanceList $driverAllowanceList)
    {
        //
    }
}