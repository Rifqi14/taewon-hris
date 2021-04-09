<?php

namespace App\Http\Controllers\Admin;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DriverAllowance;
use App\Models\DriverAllowanceList;
use App\Models\DriverList;
use App\Models\Config;
use App\Models\Partner;
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
            'partners.name as customer',
            'partners.rit as rit',
            'driver.department_id',
            'driver.workgroup_id',
            'departments.name as department_name',
            'work_groups.name as workgroup_name'
        );
        $query->leftJoin('employees as driver', 'driver.id', '=', 'delivery_orders.driver_id');
        $query->leftJoin('departments', 'departments.id', '=', 'driver.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'driver.workgroup_id');
        $query->leftJoin('partners', 'partners.id', '=', 'delivery_orders.partner_id');
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
        // if ($destination) {
        //     $query->whereIn('delivery_orders.destination', $destination);
        // }
        // if ($date_from) {
        //     $query->where('date','>=', $date_from);
        // }
        // if ($date_to) {
        //     $query->where('date','<=', $date_to);
        // }
        if ($date_from && $date_to) {
            $query->whereRaw("delivery_orders.departure_time >= '$date_from'");
            $query->whereRaw("delivery_orders.departure_time <= '$date_to'");
        }

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('delivery_orders');
        $query->select(
            'delivery_orders.*',
            'driver.name as driver_name',
            'driver.nid',
            'partners.name as customer',
            'partners.rit as rit',
            'driver.department_id',
            'driver.workgroup_id',
            'departments.name as department_name',
            'work_groups.name as workgroup_name'
        );
        $query->leftJoin('employees as driver', 'driver.id', '=', 'delivery_orders.driver_id');
        $query->leftJoin('departments', 'departments.id', '=', 'driver.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'driver.workgroup_id');
        $query->leftJoin('partners', 'partners.id', '=', 'delivery_orders.partner_id');
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
        // if ($destination) {
        //     $query->whereIn('delivery_orders.destination', $destination);
        // }
        // if ($date_from) {
        //     $query->where('date','>=', $date_from);
        // }
        // if ($date_to) {
        //     $query->where('date','<=', $date_to);
        // }
        if ($date_from && $date_to) {
            $query->whereRaw("delivery_orders.departure_time >= '$date_from'");
            $query->whereRaw("delivery_orders.departure_time <= '$date_to'");
        }

        $query->offset($start);
        $query->limit($length);
        $query->orderBy('partners.rit', 'desc');
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
        // $query = DB::table('delivery_orders');
        // $query->select('delivery_orders.*');
        // $donumbers = $query->get();
        $query = DB::table('delivery_orders');
        $query->select('delivery_orders.police_no');
        $query->groupBy('delivery_orders.police_no');
        $query->orderBy('police_no', 'asc');
        $police_nomer = $query->get();
        // $query = DB::table('delivery_orders');
        // $query->select('delivery_orders.destination');
        // $query->groupBy('delivery_orders.destination');
        // $query->orderBy('destination', 'asc');
        // $desti = $query->get();
        return view('admin.deliveryorder.index', compact('employees', 'police_nomer'));
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
            // 'date'          => changeDateFormat('Y-m-d H:i:s', changeSlash($request->date)),
            'type_truck'    => $request->type_truck,
            // 'do_number'     => $request->do_number,
            'driver_id'     => $request->driver_id,
            'police_no'     => $request->police_no,
            'partner_id'    => $request->customer,
            'departure_time'=> changeDateFormat('Y-m-d H:i:s', changeSlash($request->departure_time)),
            'arrived_time'  => changeDateFormat('Y-m-d H:i:s', changeSlash($request->arrived_time)),
            'group'         => $request->kloter
        ]);
        $partner_rit = Partner::find($request->customer);
        $partner_collection = DeliveryOrder::select("delivery_orders.id")->leftJoin('partners','partners.id','=','delivery_orders.partner_id')->where('driver_id',$request->driver_id)->where('group', $request->kloter)->where('type_truck', $request->type_truck)->orderBy('partners.rit', 'desc')->get();
        // dd($partner_collection);
        $rule_count = DriverList::where("driver_lists.type", "=", $request->type_truck)->count();
        // dd($rule_count);
        $no = 0;
        $new = false;
        foreach ($partner_collection as $key => $collection) {
            if($no <= $rule_count){
                $no++;
            }
            $collections = DeliveryOrder::find($collection->id);
            // dd($collections);
            $collections->list_order = $no;
            $collections->update();
        }

        if($deliveryorder){

            $readConfigs = Config::where('option', 'cut_off')->first();
            $cut_off = $readConfigs->value;
            if (date('d', strtotime(dbDate($request->departure_time))) > $cut_off) {
                $month = date('m', strtotime(dbDate($request->departure_time)));
                $year = date('Y', strtotime(dbDate($request->departure_time)));
                $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
            } else {
                $month =  date('m', strtotime(dbDate($request->departure_time)));
                $year =  date('Y', strtotime(dbDate($request->departure_time)));
            }
            $driverallowancelist = DriverAllowanceList::create([
                'date'          => dbDate($request->departure_time),
                'rit'           => 100,
                'truck'         => $request->type_truck,
                'value'         => $partner_rit->rit,
                'driver_id'     => $request->driver_id,
                'group'         => $request->kloter,
                'month'         => $month,
                'year'          => $year,
                'total_value'   => 0
            ]);

            if(!$driverallowancelist){
                DB::rollback();
                return response()->json([
                    'status'    => false,
                    'message'   => $driverallowancelist
                ], 400);
            }

            $checkupdates = Driverallowancelist::where('driver_id', $request->driver_id)->where('date', dbDate($request->departure_time))->where('group', $request->kloter)->orderBy('value', 'desc')->get();
            foreach($checkupdates as $key => $checkupdate){
                $rit = $key+1;
                $driverlist = DriverList::where('type', $request->type_truck)->where('rit', $rit)->first();

                if (!$driverlist) {
                    $driverlist = DriverList::where('type', $request->type_truck)->orderBy('rit', 'desc')->first();
                    
                }

                $checkupdate->rit = $driverlist->value;
                $checkupdate->total_value = ($checkupdate->value / 100) * $driverlist->value;
                $checkupdate->save();
            }
           
        
        }
        // if ($deliveryorder) {
        //     if (isset($request->product_item)) {
        //         foreach ($request->product_item as $key => $value) {
        //             $deliveryorderdetail = DeliveryOrderDetail::create([
        //                 'delivery_order_id'  => $deliveryorder->id,
        //                 'po_number'          => $request->po_number[$key],
        //                 'item_name'          => $request->item_name[$key],
        //                 'size'               => $request->size[$key],
        //                 'qty'                => $request->qty[$key],
        //                 'remarks'            => $request->remarks[$key],
        //             ]);
        //             if (!$deliveryorderdetail) {
        //                 DB::rollBack();
        //                 return response()->json([
        //                     'status'    => false,
        //                     'message'   => $deliveryorderdetail
        //                 ], 400);
        //             }
        //         }
        //     }
        //     $latestrit = DriverAllowanceList::whereDate('date', changeDateFormat('Y-m-d', changeSlash($request->date)))->where('driver_id', $request->driver_id)->where('group', $request->kloter)->whereRaw("truck like '%$request->type_truck%'")->max('rit');
        //     $driverallowance = DriverAllowanceList::create([
        //         'driver_id'     => $request->driver_id,
        //         'date'          => changeDateFormat('Y-m-d', changeSlash($request->date)),
        //         'rit'           => $latestrit ? ++$latestrit : 1,
        //         'truck'         => $request->type_truck,
        //         'group'         => $request->kloter
        //     ]);
        //     $driverlist = DriverList::where('type', $request->type_truck)->where('rit', ($driverallowance->rit >= 3) ? 3 : $driverallowance->rit)->first();
        //     if (!$driverlist) {
        //         DB::rollBack();
        //         return response()->json([
        //             'status'    => false,
        //             'message'   => "Error: value for this truck " . $request->type_truck . " and this RIT " . $driverallowance->rit . " not found"
        //         ], 400);
        //     } else {
        //         $driverallowance->value = $driverlist->value;
        //         $driverallowance->update();
        //     }
        //     if (!$driverallowance) {
        //         DB::rollBack();
        //         return response()->json([
        //             'status'    => false,
        //             'message'   => $driverallowance
        //         ], 400);
        //     }
        // } else {
        //     DB::rollBack();
        //     return response()->json([
        //         'status'    => false,
        //         'message'   => $deliveryorder
        //     ], 400);
        // }
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
        $deliveryorder = DeliveryOrder::with('deliveryorderdetail')->with('partner')->find($id);
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
        // $deliveryorder->date            = changeDateFormat('Y-m-d H:i:s', changeSlash($request->date));
        $deliveryorder->type_truck      = $request->type_truck;
        // $deliveryorder->do_number       = $request->do_number;
        $deliveryorder->driver_id       = $request->driver_id;
        $deliveryorder->police_no       = $request->police_no;
        $deliveryorder->partner_id      = $request->customer;
        $deliveryorder->group           = $request->kloter;
        $deliveryorder->departure_time  = $request->departure_time;
        $deliveryorder->arrived_time    = $request->arrived_time;
        $deliveryorder->save();
        if (!$deliveryorder) {
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
            $deliveryorder = DeliveryOrder::find($id);
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

    public function import(Request $request)
    {
        return view('admin.deliveryorder.import');
    }
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'         => 'required|mimes:xlsx'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $file = $request->file('file');
        try {
            $filetype       = \PHPExcel_IOFactory::identify($file);
            $objReader      = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel    = $objReader->load($file);
        } catch (\Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
        $data     = [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $driver_name    = strtoupper($sheet->getCellByColumnAndRow(0, $row)->getValue());
            $police_no      = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $type_truck     = strtoupper($sheet->getCellByColumnAndRow(2, $row)->getValue());
            $kloter         = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $customer       = strtoupper($sheet->getCellByColumnAndRow(4, $row)->getValue());
            if (is_numeric($sheet->getCellByColumnAndRow(5, $row)->getValue())){
                $departure_time = date('Y-m-d H:i:s', strtotime("-7 hours", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(5, $row)->getValue())));
            }else{
                $departure_time = date('Y-m-d H:i:s', strtotime($sheet->getCellByColumnAndRow(5, $row)->getValue()));
            }
            if (is_numeric($sheet->getCellByColumnAndRow(6, $row)->getValue())){
                $arrived_time = date('Y-m-d H:i:s', strtotime("-7 hours", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(6, $row)->getValue())));
            }else{
                $arrived_time = date('Y-m-d H:i:s', strtotime($sheet->getCellByColumnAndRow(6, $row)->getValue()));
            }
            $partner = Partner::whereRaw("upper(name) like '%$customer%'")->first();
            $driver = Employee::whereRaw("upper(name) like '%$driver_name%'")->get()->first();
            // $departure_time = $sheet->getCellByColumnAndRow(5, $row)->getValue();
            // $arrived_time = $sheet->getCellByColumnAndRow(6, $row)->getValue();
            $status = 1;
            $error_message = '';
            if (!$driver || !$police_no || !$type_truck || !$kloter || !$partner || !$departure_time) {
                $status = 0;
                if (!$driver) {
                    $error_message .= 'Driver Name Not Found</br>';
                }
                if (!$police_no) {
                    $error_message .= 'Police No Not Found</br>';
                }
                if (!$type_truck) {
                    $error_message .= 'Type Truck Not Found</br>';
                }
                if (!$kloter) {
                    $error_message .= 'Kloter Not Found</br>';
                }
                if (!$partner) {
                    $error_message .= 'Customer Not Found</br>';
                }
                if (!$departure_time) {
                    $error_message .= 'Departure Time Not Found</br>';
                }
            }
            if ($driver_name) {
                $data[] = array(
                    'index' => $no,
                    'driver_id' => $driver ? $driver->id : null,
                    'driver_name' => $driver_name,
                    'police_no' => $police_no,
                    'type_truck' => $type_truck,
                    'kloter' => $kloter,
                    'customer' => $customer,
                    'customer_id' => $partner ? $partner->id : null,
                    'departure_time' => $departure_time,
                    'arrived_time' => $arrived_time,
                    'error_message' => $error_message,
                    'status' => $status
                );
                $no++;
            }
        }
        return response()->json([
            'status'     => true,
            'data'     => $data
        ], 200);
    }
    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $deliveryorders = json_decode($request->deliveryorders);
        // dd($deliveryorders);
        DB::beginTransaction();
        foreach ($deliveryorders as $deliveryorder) {
            // $check = DeliveryOrder::where('destination', $deliveryorder->customer);
            // if($check){
            //     $delete = $check->delete();
            // }
            // if (!$check) {

                if (!$deliveryorder->customer_id || !$deliveryorder->driver_id || !$deliveryorder->type_truck || !$deliveryorder->police_no || !$deliveryorder->kloter) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'   => 'Data Belum Lengkap'
                    ], 400);
                }

                $doimport = DeliveryOrder::create([
                    'driver_id' => $deliveryorder->driver_id,
                    'police_no' => $deliveryorder->police_no,
                    'type_truck' => $deliveryorder->type_truck,
                    'group' => $deliveryorder->kloter,
                    'partner_id' => $deliveryorder->customer_id,
                    'departure_time' => $deliveryorder->departure_time, 
                    'arrived_time' => $deliveryorder->arrived_time 
                ]);
            if ($doimport) {
                $partner_rit = Partner::find($deliveryorder->customer_id);
                $readConfigs = Config::where('option', 'cut_off')->first();
                $cut_off = $readConfigs->value;
                if (date('d', strtotime(dbDate($deliveryorder->departure_time))) > $cut_off) {
                    $month = date('m', strtotime(dbDate($deliveryorder->departure_time)));
                    $year = date('Y', strtotime(dbDate($deliveryorder->departure_time)));
                    $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                    $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                } else {
                    $month =  date('m', strtotime(dbDate($deliveryorder->departure_time)));
                    $year =  date('Y', strtotime(dbDate($deliveryorder->departure_time)));
                }
                $driverallowancelist = DriverAllowanceList::create([
                    'date'          => dbDate($deliveryorder->departure_time),
                    'rit'           => 100,
                    'truck'         => $deliveryorder->type_truck,
                    'value'         => $partner_rit->rit,
                    'driver_id'     => $deliveryorder->driver_id,
                    'group'         => $deliveryorder->kloter,
                    'month'         => $month,
                    'year'          => $year
                ]);

                if (!$driverallowancelist) {
                    DB::rollback();
                    return response()->json([
                        'status'    => false,
                        'message'   => $driverallowancelist
                    ], 400);
                }

                $checkupdates = Driverallowancelist::where('driver_id', $deliveryorder->driver_id)->where('date', dbDate($deliveryorder->departure_time))->where('group', $deliveryorder->kloter)->orderBy('value', 'desc')->get();
                foreach ($checkupdates as $key => $checkupdate) {
                    $rit = $key + 1;
                    $driverlist = DriverList::where('type', $deliveryorder->type_truck)->where('rit', $rit)->first();
                    dd($driverlist);
                    if (!$driverlist) {
                        $driverlist = DriverList::where('type', $deliveryorder->type_truck)->orderBy('rit', 'desc')->first();
                        dd($driverlist);
                    }

                    $checkupdate->rit = $driverlist->value;
                    $checkupdate->total_value = ($checkupdate->value / 100) * $driverlist->value;
                    $checkupdate->save();
                }
            }
                if (!$doimport) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'   => $doimport
                    ], 400);
                }
            }
        // }
        DB::commit();
        return response()->json([
            'status' => true,
            'results' => route('deliveryorder.index'),
        ], 200);
    }
}