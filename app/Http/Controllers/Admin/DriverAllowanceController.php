<?php

namespace App\Http\Controllers\Admin;

use App\Models\DriverAllowance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DriverList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class DriverAllowanceController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'driverallowance'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $driver = strtoupper($request->driver);
        $allowance = strtoupper($request->allowance);
        $category = strtoupper($request->category);

        //Count Data
        $query = DB::table('driver_allowances');
        $query->select('driver_allowances.*');
        if ($driver != "") {
            $query->whereRaw("upper(driver_allowances.driver) like '%$driver%'");
        }
        $query->whereRaw("upper(driver_allowances.allowance) like '%$allowance%'");
        if ($category != "") {
            $query->whereRaw("upper(driver_allowances.category) like '%$category%'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('driver_allowances');
        $query->select('driver_allowances.*');
        if ($driver != "") {
            $query->whereRaw("upper(driver_allowances.driver) like '%$driver%'");
        }
        $query->whereRaw("upper(driver_allowances.allowance) like '%$allowance%'");
        if ($category != "") {
            $query->whereRaw("upper(driver_allowances.category) like '%$category%'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $drivers = $query->get();

        $data = [];
        foreach ($drivers as $driv) {
            $driv->no = ++$start;
            $driv->category = @config('enums.allowance_category')[$driv->category];
            $data[] = $driv;
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
        return view('admin.driverallowance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.driverallowance.create');
    }

    public function getLatestId()
    {
        $read = DriverAllowance::max('id');
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
        if ($request->driver_allowance == 'pribadi') {
            $validator = Validator::make($request->all(), [
                'driver_allowance'      => 'required',
                'allowance'             => 'required',
                'category'              => 'required',
                'value.*'               => 'numeric'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'driver_allowance'      => 'required',
                'allowance'             => 'required',
                'category'              => 'required',
                'rit_value.*'           => 'numeric',
                'rit.*'                 => 'numeric'
            ]);
        }
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $id = $this->getLatestId();
        DB::beginTransaction();
        $driver = DriverAllowance::create([
            'id'            => $id,
            'driver'        => $request->driver_allowance,
            'allowance'     => $request->allowance,
            'category'      => $request->category
        ]);
        if ($driver) {
            if ($request->driver_allowance == 'pribadi') {
                foreach ($request->recurrence as $key => $value) {
                    foreach ($request->recurrence_choose as $key1 => $value1) {
                        $list = DriverList::create([
                            'driver_allowance_id'   => $id,
                            'recurrence_day'        => $value,
                            'start'                 => $request->start[$key1],
                            'finish'                => $request->finish[$key1],
                            'value'                 => $request->value[$key1]
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
                foreach ($request->type_choose as $key => $value) {
                    $list = DriverList::create([
                        'driver_allowance_id'   => $id,
                        'type'                  => $request->type,
                        'rit'                   => $request->rit[$key],
                        'value'                 => str_replace(['.', ','], '', $request->rit_value[$key])
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
        } else if (!$driver) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $driver
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('driverallowance.index')
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DriverAllowance  $driverAllowance
     * @return \Illuminate\Http\Response
     */
    public function show(DriverAllowance $driverAllowance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DriverAllowance  $driverAllowance
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $driver = DriverAllowance::with('driverlist')->find($id);
        $query = DB::table('driver_lists');
        $query->select('driver_lists.start', 'driver_lists.finish', 'driver_lists.value');
        $query->leftJoin('driver_allowances', 'driver_allowances.id', '=', 'driver_lists.driver_allowance_id');
        $query->where('driver_lists.driver_allowance_id', '=', $id);
        $query->whereNotNull('driver_lists.recurrence_day');
        $list = $query->distinct()->get();

        $query1 = DB::table('driver_lists');
        $query1->select('driver_lists.recurrence_day as id');
        $query1->leftJoin('driver_allowances', 'driver_allowances.id', '=', 'driver_lists.driver_allowance_id');
        $query1->where('driver_lists.driver_allowance_id', '=', $id);
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
        if ($driver) {
            return view('admin.driverallowance.edit', compact('driver', 'list', 'day'));
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DriverAllowance  $driverAllowance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->driver_allowance == 'pribadi') {
            $validator = Validator::make($request->all(), [
                'driver_allowance'      => 'required',
                'allowance'             => 'required',
                'category'              => 'required',
                'value.*'               => 'numeric'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'driver_allowance'      => 'required',
                'allowance'             => 'required',
                'category'              => 'required',
                'rit_value.*'           => 'numeric',
                'rit.*'                 => 'numeric'
            ]);
        }
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $driver = DriverAllowance::find($id);
        $driver->driver         = $request->driver_allowance;
        $driver->allowance      = $request->allowance;
        $driver->category       = $request->category;
        $driver->save();
        if ($driver) {
            $lists = DriverList::where('driver_allowance_id', '=', $id);
            $lists->delete();
            if ($request->driver_allowance == 'pribadi' && isset($request->recurrence_choose)) {
                foreach ($request->recurrence as $key => $value) {
                    foreach ($request->recurrence_choose as $key1 => $value1) {
                        if (isset($request->start[$key1]) && isset($request->finish[$key1]) && isset($request->value[$key1])) {
                            $list = DriverList::create([
                                'driver_allowance_id'   => $id,
                                'recurrence_day'        => $value,
                                'start'                 => $request->start[$key1],
                                'finish'                => $request->finish[$key1],
                                'value'                 => $request->value[$key1]
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
            } elseif ($request->driver_allowance == 'truck' && isset($request->type_choose)) {
                foreach ($request->type_choose as $key => $value) {
                    $list = DriverList::create([
                        'driver_allowance_id'   => $id,
                        'type'                  => $request->type,
                        'rit'                   => $request->rit[$key],
                        'value'                 => str_replace(['.', ','], '', $request->rit_value[$key])
                    ]);
                    if (!$list) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => $list
                        ], 400);
                    }
                }
            } else {
                DB::rollBack();
                return response()->json([
                    'status'    => 'refresh',
                    'message'   => 'You must add at least one list please refresh your web browser'
                ], 400);
            }
        } else if (!$driver) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $driver
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('driverallowance.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DriverAllowance  $driverAllowance
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $list = DriverList::where('driver_allowance_id', '=', $id);
            $list->delete();
            $driver = DriverAllowance::find($id);
            $driver->delete();
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
