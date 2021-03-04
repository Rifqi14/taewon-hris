<?php

namespace App\Http\Controllers\Admin;

use App\Models\DailyReportDriver;
use App\Models\DailyReportDriverDetail;
use App\Models\DailyReportDriverAdditional;
use App\Models\Reimbursement;
use App\Models\ReimbursementCalculation;
use App\Models\ReimbursementAllowance;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class ReimbursementController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'reimbursement'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLatestId()
    {
        $read = Reimbursement::max('id');
        return $read;
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $driver_id = strtoupper(str_replace("'","''",$request->driver_id));
        $date_from = date('Y-m-d', strtotime(changeSlash($request->date_from)));
        $date_to = date('Y-m-d', strtotime(changeSlash($request->date_to)));

        //Count Data
        $query = DB::table('reimbursements');
        $query->select('reimbursements.*', 'driver.name as driver_name');
        $query->leftJoin('employees as driver', 'driver.id', '=', 'reimbursements.driver_id');

        if ($driver_id != "") {
            $query->whereRaw("upper(driver.name) like '%$driver_id%'");
        }
        if ($date_from) {
            $query->where('date', '>=', $date_from);
        }
        if ($date_to) {
            $query->where('date', '<=', $date_to);
        }

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('reimbursements');
        $query->select('reimbursements.*', 'driver.name as driver_name');
        $query->leftJoin('employees as driver', 'driver.id', '=', 'reimbursements.driver_id');
        if ($driver_id != "") {
            $query->whereRaw("upper(driver.name) like '%$driver_id%'");
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
        $reimbursements = $query->get();

        $data = [];
        foreach ($reimbursements as $driv) {
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

    public function readdailydriver(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $department_id = $request->department_id;
        $department_multi_id = $request->department_multi_id;
        $path = strtoupper($request->path);
        $list_department_multi_id = [69, 119, 120];
        $title_id = $request->title_id;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('daily_report_drivers');
        $query->select('daily_report_drivers.*', 'employees.name', 'employees.department_id', 'employees.title_id');
        // $query->leftJoin('daily_report_driver_details','daily_report_driver_details.id','=','daily_report_drivers.daily_report_driver_id');
        $query->leftJoin('employees', 'employees.id', '=', 'daily_report_drivers.driver_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if ($department_multi_id) {
            $query->whereIn('employees.department_id', $list_department_multi_id);
        }
        if ($path) {
            $query->whereRaw("upper(departments.path) like '%$path%'");
        }
        if ($department_id) {
            $query->where('employees.department_id', '=', $department_id);
        }
        if ($title_id) {
            $query->where('employees.title_id', '=', $title_id);
        }

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('daily_report_drivers');
        $query->select('daily_report_drivers.*', 'employees.name', 'employees.department_id', 'employees.title_id');
        // $query->leftJoin('daily_report_driver_details','daily_report_driver_details.id','=','daily_report_drivers.daily_report_driver_id');
        $query->leftJoin('employees', 'employees.id', '=', 'daily_report_drivers.driver_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        if ($path) {
            $query->whereRaw("upper(departments.path) like '%$path%'");
        }
        if ($department_id) {
            $query->where('employees.department_id', '=', $department_id);
        }
        if ($title_id) {
            $query->where('employees.title_id', '=', $title_id);
        }

        $query->offset($start);
        $query->limit($length);
        // $query->orderBy('daily_report_drivers.driver_id', 'asc');
        $drivers = $query->get();

        $data = [];
        foreach ($drivers as $driv) {
            $driv->no = ++$start;
            $data[] = $driv;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }
    public function getdata(Request $request)
    {

        $daily_report_driver_details = DB::table('daily_report_driver_details')
            ->where('daily_report_driver_id', $request->dailyreportdriver_id)
            ->max('arrival');
        return response()->json($daily_report_driver_details);
    }

    public function readallowance(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $max_arrival = $request->max_arrival;
        $get_day = $request->get_day;
        $do_ids = $request->do_ids;

        //Count Data
        $query = DB::table('driver_lists');
        $query->select('driver_lists.*', 'driver_allowances.allowance');
        $query->leftJoin('driver_allowances', 'driver_allowances.id', '=', 'driver_lists.driver_allowance_id');

        $query->where("driver_lists.recurrence_day", date('D', strtotime($get_day)));
        if ($max_arrival) {
            // $query->where('start','>=', $max_arrival);
            // $query->where('finish','<=', $max_arrival);
            $query->whereRaw("'$max_arrival' BETWEEN start AND finish");
        }
        // $query->where("driver_lists.id", '<>',$do_ids);

        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('driver_lists');
        $query->select('driver_lists.*', 'driver_allowances.allowance');
        $query->leftJoin('driver_allowances', 'driver_allowances.id', '=', 'driver_lists.driver_allowance_id');

        $query->where("driver_lists.recurrence_day", date('D', strtotime($get_day)));
        if ($max_arrival) {
            // $query->where('start','>=', $max_arrival);
            // $query->where('finish','<=', $max_arrival);
            $query->whereRaw("'$max_arrival' BETWEEN start AND finish");
        }
        // $query->where("driver_lists.id", '<>',$do_ids);

        $query->offset($start);
        $query->limit($length);
        // $query->orderBy($sort, $dir);
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
        return view('admin.reimbursement.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.reimbursement.create');
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
        $reimbursement                  = Reimbursement::create([
            'date'                         => changeDateFormat('Y-m-d', changeSlash($request->date)),
            'notes'                     => $request->notes,
            'daily_report_driver_id'     => $request->dailyreportdriver_id,
            'driver_id'                 => $request->driver_id,
            'max_arrival'                 => $request->max_arrival,
            'get_day'                     => date('Y-m-d', strtotime($request->get_day)),
            'subtotal'                  => $request->subtotal,
            'subtotalallowance'         => $request->subtotalallowance,
            'grandtotal'                => $request->grandtotal,
        ]);
        if ($reimbursement) {
            if (isset($request->product_item)) {
                foreach ($request->product_item as $key => $value1) {
                    DB::table('daily_report_driver_details')->where('id', $request->drd_calculation_id[$key])->update(array(
                        'status' => 1,
                    ));
                    DB::table('daily_report_driver_additionals')->where('id', $request->drd_additional_id[$key])->update(array(
                        'status' => 1,
                    ));
                    $id = $this->getLatestId();
                    $reimbursementcalculation     = ReimbursementCalculation::create([
                        'reimbursement_id'        => $id,
                        'reff_detail_additional'  => $request->reff_detail_additional[$key],
                        'drd_calculation_id'      => $request->drd_calculation_id[$key],
                        'drd_additional_id'       => $request->drd_additional_id[$key],
                        'description'             => $request->description[$key],
                        'value'                   => $request->value[$key],
                    ]);
                    if (!$reimbursementcalculation) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $reimbursementcalculation
                        ], 400);
                    }
                }
            }
            if (isset($request->product_allowance)) {
                foreach ($request->product_allowance as $key => $value2) {
                    DB::table('driver_lists')->where('id', $request->driver_list_id[$key])->update(array(
                        'status' => 1,
                    ));
                    $id = $this->getLatestId();
                    $reimbursementallowance = ReimbursementAllowance::create([
                        'reimbursement_id'  => $id,
                        'driver_list_id'    => $request->driver_list_id[$key],
                        'description'       => $request->description_allowance[$key],
                        'value'             => $request->value_allowance[$key],
                    ]);
                    if (!$reimbursementallowance) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $reimbursementallowance
                        ], 400);
                    }
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $reimbursement
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('reimbursement.index')
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
        $reimbursement = Reimbursement::with('reimbursementcalculation', 'reimbursementallowance')->find($id);
        $query = DB::table('reimbursements');
        $query->select('reimbursements.*');

        if ($reimbursement) {
            return view('admin.reimbursement.edit', compact('reimbursement'));
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
        // foreach ($request->product_item as $key => $value) {
        //     dd($request->drd_calculation_id[$key]);
        //     return;
        // }return;
        DB::beginTransaction();
        $reimbursement                                  = Reimbursement::find($id);
        $reimbursement->date                         = changeDateFormat('Y-m-d', changeSlash($request->date));
        $reimbursement->notes                         = $request->notes;
        $reimbursement->daily_report_driver_id         = $request->dailyreportdriver_id;
        $reimbursement->driver_id                   = $request->driver_id;
        $reimbursement->max_arrival                 = $request->max_arrival;
        $reimbursement->get_day                     = date('Y-m-d', strtotime($request->get_day));
        $reimbursement->subtotal                    = $request->subtotal;
        $reimbursement->subtotalallowance           = $request->subtotalallowance;
        $reimbursement->grandtotal                  = $request->grandtotal;
        $reimbursement->save();
        if ($reimbursement) {
            if (isset($request->product_item)) {
                $detail = ReimbursementCalculation::where('reimbursement_id', '=', $id);
                $detail->delete();
                $allowance = ReimbursementAllowance::where('reimbursement_id', '=', $id);
                $allowance->delete();
                foreach ($request->product_item as $key => $value1) {
                    DB::table('daily_report_driver_details')->where('id', $request->drd_calculation_id[$key])->update(array(
                        'status' => 1,
                    ));
                    DB::table('daily_report_driver_additionals')->where('id', $request->drd_additional_id[$key])->update(array(
                        'status' => 1,
                    ));
                    $reimbursementcalculation     = ReimbursementCalculation::create([
                        'reimbursement_id'        => $id,
                        'reff_detail_additional'  => $request->reff_detail_additional[$key],
                        'drd_calculation_id'      => $request->drd_calculation_id[$key],
                        'drd_additional_id'       => $request->drd_additional_id[$key],
                        'description'             => $request->description[$key],
                        'value'                   => $request->value[$key],
                    ]);
                    if (!$reimbursementcalculation) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $reimbursementcalculation
                        ], 400);
                    }
                }
            }
            if (isset($request->product_allowance)) {
                foreach ($request->product_allowance as $key => $value2) {
                    DB::table('driver_lists')->where('id', $request->driver_list_id[$key])->update(array(
                        'status' => 1,
                    ));
                    $reimbursementallowance = ReimbursementAllowance::create([
                        'reimbursement_id'  => $id,
                        'driver_list_id'    => $request->driver_list_id[$key],
                        'description'       => $request->description_allowance[$key],
                        'value'             => $request->value_allowance[$key],
                    ]);
                    if (!$reimbursementallowance) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $reimbursementallowance
                        ], 400);
                    }
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $reimbursement
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('reimbursement.index')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    { {
            try {
                $ceks = ReimbursementCalculation::where('reimbursement_id', '=', $id)->get();
                foreach ($ceks as $row) {
                    // $agents = DailyReportDriverDetail::find($row->drd_calculation_id);
                    // $agents->status       = 0;
                    // $agents->save();
                    DB::table('daily_report_driver_details')->where('id', $row->drd_calculation_id)->update(array(
                        'status' => 0,
                    ));
                    DB::table('daily_report_driver_additionals')->where('id', $row->drd_additional_id)->update(array(
                        'status' => 0,
                    ));
                }

                // $agents = DailyReportDriverDetail::where('id','=', $cek->drd_calculation_id)->get(); 

                // foreach ($agents as $row) {
                //     dd($row->)
                // }


                $detail = ReimbursementCalculation::where('reimbursement_id', '=', $id);
                $detail->delete();
                $allowance = ReimbursementAllowance::where('reimbursement_id', '=', $id);
                $allowance->delete();
                $deliveryorder = Reimbursement::find($id);
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

    function updatestatusallowance(Request $request)
    {
        $allowance = DB::table('driver_lists')->where('id', $request->id)->update(array(
            'status' => 0,
        ));
        if ($allowance) {
            $delete = ReimbursementAllowance::where('driver_list_id', '=', $request->id);
            $delete->delete();
            if ($delete) {
                return response()->json([
                    'status'    => true,
                ], 200);
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => 'gagal delete data'
                ], 400);
            }
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'gagal update data'
            ], 400);
        }
    }

    function updatestatuscalculation(Request $request)
    {

        if ($request->drd_calculation_id != 0) {
            $allowance = DB::table('daily_report_driver_details')->where('id', $request->drd_calculation_id)->update(array(
                'status' => 0,
            ));
            if ($allowance) {
                $delete = ReimbursementCalculation::where('drd_calculation_id', '=', $request->drd_calculation_id);
                $delete->delete();
                if ($delete) {
                    return response()->json([
                        'status'    => true,
                    ], 200);
                } else {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'gagal delete data'
                    ], 400);
                }
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => 'gagal update data'
                ], 400);
            }
        }
        if ($request->drd_additional_id != 0) {
            $allowance = DB::table('daily_report_driver_additionals')->where('id', $request->drd_additional_id)->update(array(
                'status' => 0,
            ));
            if ($allowance) {
                $delete = ReimbursementCalculation::where('drd_additional_id', '=', $request->drd_additional_id);
                $delete->delete();
                if ($delete) {
                    return response()->json([
                        'status'    => true,
                    ], 200);
                } else {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'gagal delete data'
                    ], 400);
                }
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => 'gagal update data'
                ], 400);
            }
        }
    }

    public function exportreimbursment(Request $request)
    {
        $from = $request->date_from ? Carbon::parse(changeSlash($request->date_from))->toDateString() : null;
        $to = $request->date_to ? Carbon::parse(changeSlash($request->date_to))->toDateString() : null;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();
        $sheet->setCellValue('A1', 'Tanggal Slip');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'No. Mobil');
        $sheet->setCellValue('D1', 'Supir');
        $sheet->setCellValue('E1', 'Tujuan');
        $sheet->setCellValue('F1', 'Jam Berangkat');
        $sheet->setCellValue('G1', 'Jam Tiba');
        $sheet->setCellValue('H1', 'KM Berangkat');
        $sheet->setCellValue('I1', 'KM Tiba');
        $sheet->setCellValue('J1', 'Jarak');
        $sheet->setCellValue('K1', 'Total Uang');
        $sheet->setCellValue('L1', 'Bensin');
        $sheet->setCellValue('M1', 'Tol');
        $sheet->setCellValue('N1', 'Parkir');
        $sheet->setCellValue('O1', 'Dll');
        $sheet->setCellValue('P1', 'Cash');
        $sheet->setCellValue('Q1', 'Makan');

        $row_number = 2;

        $query = Reimbursement::select(
            'reimbursements.*',
            'drd.date as date_driver',
            'drd.police_no as no_mobil',
            'drdd.destination as tujuan',
            'drdd.departure as jam_berangkat',
            'drdd.arrival as jam_tiba',
            'drdd.departure_km as km_berangkat',
            'drdd.arrival_km as km_tiba',
            'e.name as supir',
            'drdd.parking as parkir',
            'drdd.etc as etc',
            'drdd.oil as oil',
            'drdd.toll_money as toll',
            DB::raw("(select ra.value from reimbursement_allowances ra where ra.reimbursement_id = reimbursements.id and ra.description like '%Uang Makan%') as uang_makan"),
            DB::raw("(select ra.value from reimbursement_allowances ra where ra.reimbursement_id = reimbursements.id and ra.description like '%Uang Cash%') as uang_cash")
        );
        $query->leftJoin('daily_report_drivers as drd', 'drd.id', '=', 'reimbursements.daily_report_driver_id');
        $query->leftJoin('daily_report_driver_details as drdd', 'drdd.daily_report_driver_id', '=', 'drd.id');
        $query->leftJoin('employees as e', 'e.id', '=', 'drd.driver_id');
        if ($from && $to) {
            $query->whereBetween('reimbursements.date', [$from, $to]);
        }
        $reimbursements = $query->get();

        foreach ($reimbursements as $key => $value) {
            $getvalue = 0;
            $sheet->setCellValue('A' . $row_number, $value->date);
            $sheet->setCellValue('B' . $row_number, $value->date_driver);
            $sheet->setCellValue('C' . $row_number, $value->no_mobil);
            $sheet->setCellValue('D' . $row_number, $value->supir);
            $sheet->setCellValue('E' . $row_number, $value->tujuan);
            $sheet->setCellValue('F' . $row_number, $value->jam_berangkat);
            $sheet->setCellValue('G' . $row_number, $value->jam_tiba);
            $sheet->setCellValue('H' . $row_number, $value->km_berangkat);
            $sheet->setCellValue('I' . $row_number, $value->km_tiba);
            $sheet->setCellValue('J' . $row_number, '=I' . $row_number . '-H' . $row_number);
            $sheet->setCellValue('K' . $row_number, '=SUM(L' . $row_number . ':Q' . $row_number . ')');
            $sheet->setCellValue('L' . $row_number, $value->oil ? $value->oil : 0);
            $sheet->setCellValue('M' . $row_number, $value->toll);
            $sheet->setCellValue('N' . $row_number, $value->parkir);
            $sheet->setCellValue('O' . $row_number, $value->etc);
            if ($row_number <= 2) {
                $sheet->setCellValue('P' . $row_number, $value->uang_cash ? $value->uang_cash : 0);
            } else {
                $new_row = $row_number - 1;
                $getdate = $sheet->getCell('B' . $new_row)->getValue();
                $getdriver = $sheet->getCell('D' . $new_row)->getValue();
                $sheet->setCellValue('P' . $row_number, ($getdate == $value->date_driver && $getdriver == $value->supir) ? 0 : $value->uang_cash ? $value->uang_cash : 0);
            }
            if ($row_number <= 2) {
                $sheet->setCellValue('Q' . $row_number, $value->uang_makan ? $value->uang_makan : 0);
            } else {
                $new_row = $row_number - 1;
                $getdate = $sheet->getCell('B' . $new_row)->getValue();
                $getdriver = $sheet->getCell('D' . $new_row)->getValue();
                $sheet->setCellValue('Q' . $row_number, ($getdate == $value->date_driver && $getdriver == $value->supir) ? 0 : $value->uang_makan ? $value->uang_makan : 0);
            }
            $row_number++;
        }

        foreach (range('A', 'Q') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($reimbursements->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-reimbursment-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Reimbursment Data",
                'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
            ], 200);
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
        }
    }

    public function print(Request $request)
    {
        $from = $request->date_from ? Carbon::parse(changeSlash($request->date_from))->toDateString() : null;
        $to = $request->date_to ? Carbon::parse(changeSlash($request->date_to))->toDateString() : null;
        $query = Reimbursement::select(
            'reimbursements.*',
            'drd.date as date_driver',
            'drd.police_no as no_mobil',
            'drdd.destination as tujuan',
            'drdd.departure as jam_berangkat',
            'drdd.arrival as jam_tiba',
            'drdd.departure_km as km_berangkat',
            'drdd.arrival_km as km_tiba',
            'e.name as supir',
            'drdd.parking as parkir',
            'drdd.etc as etc',
            'drdd.oil as oil',
            'drdd.toll_money as toll',
            DB::raw("(select ra.value from reimbursement_allowances ra where ra.reimbursement_id = reimbursements.id and ra.description like '%Uang Makan%') as uang_makan"),
            DB::raw("(select ra.value from reimbursement_allowances ra where ra.reimbursement_id = reimbursements.id and ra.description like '%Uang Cash%') as uang_cash")
        );
        $query->leftJoin('daily_report_drivers as drd', 'drd.id', '=', 'reimbursements.daily_report_driver_id');
        $query->leftJoin('daily_report_driver_details as drdd', 'drdd.daily_report_driver_id', '=', 'drd.id');
        $query->leftJoin('employees as e', 'e.id', '=', 'drd.driver_id');
        if ($from && $to) {
            $query->whereBetween('reimbursements.date', [$from, $to]);
        }
        $reimbursements = $query->get();
        if ($reimbursements->count() > 0) {
            return view('admin.reimbursement.print', compact('reimbursements'));
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
        }
    }
}