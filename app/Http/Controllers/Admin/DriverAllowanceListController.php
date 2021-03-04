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

        // Count Data
        $query = DriverAllowanceList::where('driver_id', $employee_id)->whereMonth('date', $month)->whereYear('date', $year);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('driver_allowance_lists');
        $query->select('driver_allowance_lists.date as date', DB::raw('max(rit) as rit'), DB::raw('sum(value) as value'), 'driver_allowance_lists.truck', 'driver_allowance_lists.driver_id', 'driver_allowance_lists.group');
        $query->where('driver_id', $employee_id);
        $query->whereMonth('date', $month);
        $query->whereYear('date', $year);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('date', 'asc');
        $query->groupBy('driver_allowance_lists.date', 'driver_allowance_lists.truck', 'driver_allowance_lists.driver_id', 'driver_allowance_lists.group');
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
        $query = DeliveryOrder::where('driver_id', $driver)->whereDate('date', $date)->whereRaw("type_truck like '%$truck%'")->where('group', $group);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DeliveryOrder::where('driver_id', $driver)->whereDate('date', $date)->whereRaw("type_truck like '%$truck%'")->where('group', $group)->offset($start)->limit($length)->orderBy('date', 'asc');
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