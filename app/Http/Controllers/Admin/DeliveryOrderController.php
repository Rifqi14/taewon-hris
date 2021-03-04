<?php

namespace App\Http\Controllers\Admin;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DriverAllowance;
use App\Models\DriverAllowanceList;
use App\Models\DriverList;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class DeliveryOrderController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'deliveryorder'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLatestId()
    {
        $read = DeliveryOrder::max('id');
        return $read + 1;
    }

    public function print($id)
    {
        $deliveryorder = DeliveryOrder::with('deliveryorderdetail', 'driver')->find($id);
        return view('admin.deliveryorder.print', compact('deliveryorder'));
    }

    public function select_employee(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = Str::upper($request->name);

        // Count Data
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->whereRaw("upper(departments.path) like '%DRIVER%'");
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->whereRaw("upper(departments.path) like '%DRIVER%'");
        $query->offset($start);
        $query->limit($length);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no   = ++$start;
            $data[]         = $employee;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows'  => $data
        ], 200);
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $driver_id = strtoupper(str_replace("'","''",$request->driver_id));
        $department_id = $request->department_id;
        $nid = strtoupper($request->nid);
        $workgroup_id = $request->workgroup_id;

        $police_no = $request->police_no;
        $destination = $request->destination;
        $do_number = $request->do_number;
        $date_from = $request->date_from ? Carbon::parse(changeSlash($request->date_from))->endOfDay()->toDateTimeString() : '';
        $date_to = $request->date_to ? Carbon::parse(changeSlash($request->date_to))->endOfDay()->toDateTimeString() : '';

        //Count Data
        $query = DB::table('delivery_orders');
        $query->select(
            'delivery_orders.*',
            'driver.name as driver_name',
            'driver.nid',
            'driver.department_id',
            'driver.workgroup_id',
            'departments.name as department_name',
            'work_groups.name as workgroup_name'
        );
        $query->leftJoin('employees as driver', 'driver.id', '=', 'delivery_orders.driver_id');
        $query->leftJoin('departments', 'departments.id', '=', 'driver.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'driver.workgroup_id');

        if ($driver_id != "") {
            $query->whereRaw("upper(driver.name) like '%$driver_id%'");
        }
        if ($nid) {
            $query->whereRaw("driver.nid like '%$nid%'");
        }
        if ($department_id) {
            $query->where('driver.department_id', $department_id);
        }
        if ($workgroup_id) {
            $query->where('driver.workgroup_id', $workgroup_id);
        }

        if ($police_no) {
            $query->whereIn('delivery_orders.police_no', $police_no);
        }
        if ($destination) {
            $query->whereIn('delivery_orders.destination', $destination);
        }
        if ($do_number) {
            $query->whereIn('do_number', $do_number);
        }
        // if ($date_from) {
        //     $query->where('date','>=', $date_from);
        // }
        // if ($date_to) {
        //     $query->where('date','<=', $date_to);
        // }
        if ($date_from && $date_to) {
            $query->whereRaw("date::date >= '$date_from'");
            $query->whereRaw("date::date <= '$date_to'");
        }

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('delivery_orders');
        $query->select(
            'delivery_orders.*',
            'driver.name as driver_name',
            'driver.nid',
            'driver.department_id',
            'driver.workgroup_id',
            'departments.name as department_name',
            'work_groups.name as workgroup_name'
        );
        $query->leftJoin('employees as driver', 'driver.id', '=', 'delivery_orders.driver_id');
        $query->leftJoin('departments', 'departments.id', '=', 'driver.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'driver.workgroup_id');

        if ($driver_id != "") {
            $query->whereRaw("upper(driver.name) like '%$driver_id%'");
        }
        if ($nid) {
            $query->whereRaw("driver.nid like '%$nid%'");
        }
        if ($department_id) {
            $query->where('driver.department_id', $department_id);
        }
        if ($workgroup_id) {
            $query->where('driver.workgroup_id', $workgroup_id);
        }

        if ($police_no) {
            $query->whereIn('delivery_orders.police_no', $police_no);
        }
        if ($destination) {
            $query->whereIn('delivery_orders.destination', $destination);
        }
        if ($do_number) {
            $query->whereIn('do_number', $do_number);
        }
        // if ($date_from) {
        //     $query->where('date','>=', $date_from);
        // }
        // if ($date_to) {
        //     $query->where('date','<=', $date_to);
        // }
        if ($date_from && $date_to) {
            $query->whereRaw("date::date >= '$date_from'");
            $query->whereRaw("date::date <= '$date_to'");
        }

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

    public function read_donumber(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $do_number = strtoupper($request->name);

        //Count Data
        $query = DB::table('delivery_orders');
        $query->select('delivery_orders.*');
        $query->whereRaw("upper(delivery_orders.do_number) like '%$do_number%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('delivery_orders');
        $query->select(
            'delivery_orders.*'
        );
        $query->whereRaw("upper(delivery_orders.do_number) like '%$do_number%'");
        $query->offset($start);
        $query->limit($length);
        $donumbers = $query->get();

        $data = [];
        foreach ($donumbers as $row) {
            $row->no = ++$start;
            $data[] = $row;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
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
        $query = DB::table('delivery_orders');
        $query->select('delivery_orders.*');
        $donumbers = $query->get();
        $query = DB::table('delivery_orders');
        $query->select('delivery_orders.police_no');
        $query->groupBy('delivery_orders.police_no');
        $query->orderBy('police_no', 'asc');
        $police_nomer = $query->get();
        $query = DB::table('delivery_orders');
        $query->select('delivery_orders.destination');
        $query->groupBy('delivery_orders.destination');
        $query->orderBy('destination', 'asc');
        $desti = $query->get();
        return view('admin.deliveryorder.index', compact('employees', 'donumbers', 'police_nomer', 'desti'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.deliveryorder.create');
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
        $deliveryorder = DeliveryOrder::create([
            'date'          => changeDateFormat('Y-m-d H:i:s', changeSlash($request->date)),
            'type_truck'    => $request->type_truck,
            'do_number'     => $request->do_number,
            'driver_id'     => $request->driver_id,
            'police_no'     => $request->police_no,
            'destination'   => $request->destination,
            'group'         => $request->kloter
        ]);

        if ($deliveryorder) {
            if (isset($request->product_item)) {
                foreach ($request->product_item as $key => $value) {
                    $deliveryorderdetail = DeliveryOrderDetail::create([
                        'delivery_order_id'  => $deliveryorder->id,
                        'po_number'          => $request->po_number[$key],
                        'item_name'          => $request->item_name[$key],
                        'size'               => $request->size[$key],
                        'qty'                => $request->qty[$key],
                        'remarks'            => $request->remarks[$key],
                    ]);
                    if (!$deliveryorderdetail) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $deliveryorderdetail
                        ], 400);
                    }
                }
            }
            $latestrit = DriverAllowanceList::whereDate('date', changeDateFormat('Y-m-d', changeSlash($request->date)))->where('driver_id', $request->driver_id)->where('group', $request->kloter)->whereRaw("truck like '%$request->type_truck%'")->max('rit');
            $driverallowance = DriverAllowanceList::create([
                'driver_id'     => $request->driver_id,
                'date'          => changeDateFormat('Y-m-d', changeSlash($request->date)),
                'rit'           => $latestrit ? ++$latestrit : 1,
                'truck'         => $request->type_truck,
                'group'         => $request->kloter
            ]);
            $driverlist = DriverList::where('type', $request->type_truck)->where('rit', ($driverallowance->rit >= 3) ? 3 : $driverallowance->rit)->first();
            if (!$driverlist) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => "Error: value for this truck " . $request->type_truck . " and this RIT " . $driverallowance->rit . " not found"
                ], 400);
            } else {
                $driverallowance->value = $driverlist->value;
                $driverallowance->update();
            }
            if (!$driverallowance) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $driverallowance
                ], 400);
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $deliveryorder
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('deliveryorder.index')
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
        $deliveryorder = DeliveryOrder::with('deliveryorderdetail')->find($id);
        $query = DB::table('delivery_orders');
        $query->select('delivery_orders.*', 'deliveryorderdetail.po_number');

        if ($deliveryorder) {
            return view('admin.deliveryorder.edit', compact('deliveryorder'));
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
        $deliveryorder = DeliveryOrder::find($id);
        $date_changed = $deliveryorder->date;
        $truck_changed = $deliveryorder->type_truck;
        $driver_changed = $deliveryorder->driver_id;
        $kloter = $deliveryorder->group;
        $deliveryorder->date            = changeDateFormat('Y-m-d H:i:s', changeSlash($request->date));
        $deliveryorder->type_truck      = $request->type_truck;
        $deliveryorder->do_number       = $request->do_number;
        $deliveryorder->driver_id       = $request->driver_id;
        $deliveryorder->police_no       = $request->police_no;
        $deliveryorder->destination     = $request->destination;
        $deliveryorder->group           = $request->kloter;
        $deliveryorder->save();
        if ($deliveryorder) {
            if (isset($request->product_item)) {
                $lists = DeliveryOrderDetail::where('delivery_order_id', '=', $id);
                $lists->delete();
                foreach ($request->product_item as $key => $value) {
                    $deliveryorderdetail = DeliveryOrderDetail::create([
                        'delivery_order_id'  => $id,
                        'po_number'          => $request->po_number[$key],
                        'item_name'          => $request->item_name[$key],
                        'size'               => $request->size[$key],
                        'qty'                => $request->qty[$key],
                        'remarks'            => $request->remarks[$key],
                    ]);
                    if (!$deliveryorderdetail) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $deliveryorderdetail
                        ], 400);
                    }
                }
            }
            $latestrit = DriverAllowanceList::whereDate('date', changeDateFormat('Y-m-d', $date_changed))->where('driver_id', $driver_changed)->where('group', $kloter)->whereRaw("truck like '%$truck_changed%'")->max('rit');
            if ($latestrit) {
                $del_rit = DriverAllowanceList::whereDate('date', changeDateFormat('Y-m-d', $date_changed))->where('driver_id', $driver_changed)->where('group', $kloter)->whereRaw("truck like '%$truck_changed%'")->where('rit', $latestrit);
                $del_rit->delete();
            }
            $latestrit_new = DriverAllowanceList::whereDate('date', changeDateFormat('Y-m-d', changeSlash($request->date)))->where('driver_id', $request->driver_id)->where('group', $request->kloter)->whereRaw("truck like '%$request->type_truck%'")->max('rit');
            $driverallowance = DriverAllowanceList::create([
                'driver_id'     => $request->driver_id,
                'date'          => changeDateFormat('Y-m-d', changeSlash($request->date)),
                'rit'           => $latestrit_new ? ++$latestrit_new : 1,
                'truck'         => $request->type_truck,
                'group'         => $request->kloter
            ]);
            $driverlist = DriverList::where('type', $request->type_truck)->where('rit', ($driverallowance->rit >= 3) ? 3 : $driverallowance->rit)->first();
            if (!$driverlist) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => "Error: value for this truck " . $request->type_truck . " and this RIT " . $driverallowance->rit . " not found"
                ], 400);
            } else {
                $driverallowance->value = $driverlist->value;
                $driverallowance->update();
            }
            if (!$driverallowance) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $driverallowance
                ], 400);
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $deliveryorder
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('deliveryorder.index')
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
            $list = DeliveryOrderDetail::where('delivery_order_id', '=', $id);
            $list->delete();
            $deliveryorder = DeliveryOrder::find($id);
            $deliveryorder->delete();

            $deleteallowance = DriverAllowanceList::whereDate('date', '=', changeDateFormat('Y-m-d', changeSlash($deliveryorder->date)))->where('driver_id', $deliveryorder->driver_id)->where('group', $deliveryorder->group)->whereRaw("truck like '%$deliveryorder->type_truck%'")->orderBy('rit', 'desc')->first();
            $deleteallowance->delete();
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