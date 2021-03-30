<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\EmployeeAllowance;
use App\Models\Employee;
use App\Models\WorkGroup;
use App\Models\Allowance;
use App\Models\WorkgroupAllowance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeAllowanceController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'employees'));
    }

    public function readdetail(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;
        $allowance = $request->allowance;
        $montly = $request->montly;
        $year = $request->year;
        // dd($allowance);

        //Count Data
        $query = DB::table('employee_detailallowances');
        $query->select('employee_detailallowances.*');
        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_detailallowances.allowance_id');
        $query->where('employee_detailallowances.employee_id', '=', $employee_id);
        if ($allowance) {
            $query->where('employee_detailallowances.allowance_id', '=', $allowance);
        }
        if ($montly != '') {
            $query->whereMonth('employee_detailallowances.month', '=', $montly);
        }
        if ($year != '') {
            $query->whereYear('employee_detailallowances.year', '=', $year);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_detailallowances');
        $query->select(
            'employee_detailallowances.*',
            'allowances.allowance as allowance'
        );
        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_detailallowances.allowance_id');
        $query->where('employee_detailallowances.employee_id', '=', $employee_id);
        if ($allowance) {
            $query->where('employee_detailallowances.allowance_id', '=', $allowance);
        }
        if ($montly != '') {
            $query->whereMonth('employee_detailallowances.month', '=', $montly);
        }
        if ($year != '') {
            $query->whereYear('employee_detailallowances.year', '=', $year);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, 'asc');
        $roles = $query->get();
        // dd($roles);
        $data = [];
        foreach ($roles as $role) {
            $role->no = ++$start;
            $data[] = $role;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $employee_id = $request->employee_id;
        $montly = $request->montly;
        $year = $request->year;
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        //Count Data
        $query = DB::table('employee_allowances');
        $query->select('employee_allowances.*');
        $query->leftJoin('workgroup_allowances', 'workgroup_allowances.id', '=', 'employee_allowances.allowance_id');
        $query->leftJoin('allowances', 'allowances.id', '=', 'workgroup_allowances.allowance_id');
        $query->where('employee_allowances.employee_id', '=', $employee_id);
        if ($montly != '') {
            $query->where('employee_allowances.month', '=', $montly);
        }
        if ($year != '') {
            $query->where('employee_allowances.year', '=', $year);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_allowances');
        $query->select(
            'employee_allowances.*',
            'allowances.allowance as allowance',
            'allowances.category as category',
            'allowances.reccurance as reccurance'
        );
        $query->leftJoin('allowances', 'allowances.id', '=', 'employee_allowances.allowance_id');
        $query->where('employee_allowances.employee_id', '=', $employee_id);
        if ($montly != '') {
            $query->where('employee_allowances.month', '=', $montly);
        }
        if ($year != '') {
            $query->where('employee_allowances.year', '=', $year);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $roles = $query->get();
        // dd($roles);
        $data = [];
        foreach ($roles as $role) {
            $role->no = ++$start;
            $role->category = @config('enums.allowance_category')[$role->category];
            $role->factor = $role->factor ? number_format($role->factor, 1) : null;
            $data[] = $role;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
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
        $month = date('m');
        $year = date('Y');
        $employeeallowance = EmployeeAllowance::create([
            'employee_id' => $request->employee_id,
            'allowance_id'  => $request->allowance_id,
            'status' => 1,
            'month' => $request->month,
            'year' => $request->year
        ]);
        $get_allowance = Allowance::where('id', $employeeallowance->allowance_id)->first();
        if ($get_allowance->reccurance == 'monthly') {
            $employeeallowance->factor = 1;
            $employeeallowance->save();
        } else {
            $employeeallowance->factor = 0;
            $employeeallowance->save();
        }
        if (!$employeeallowance) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message'     => $employeeallowance
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success add data'
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
        $employeeallowance = EmployeeAllowance::with('allowance')->find($id);
        return response()->json([
            'status'     => true,
            'data' => $employeeallowance
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
            'type'    => 'required',
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeeallowance = EmployeeAllowance::find($id);
        $employeeallowance->type      = $request->type;
        $employeeallowance->value   = $request->value;
        $employeeallowance->factor   = $request->factor;
        $employeeallowance->save();

        if (!$employeeallowance) {
            return response()->json([
                'status' => false,
                'message'     => $employeeallowance
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => $employeeallowance,
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
        //
    }
    public function generate(Request $request)
    {
        // Get employee
        // $validator = Validator::make($request->all(), [
        //     'year'    => 'required',
        //     'month' => 'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status'    => false,
        //         'message'   => $validator->errors()->first()
        //     ], 400);
        // }
        $employee = Employee::where(['id' => $request->employee_id])->where('employees.status', 1)->firstOrFail();
        // Get workgroup berdasarkan employee_id
        $workgroup = WorkGroup::where('id', $employee->workgroup_id)->firstOrFail();
        if ($workgroup) {
            // Get workgroup allowance berdasarkan workgroup_id dan default = 1
            $workgroup_allowance = WorkgroupAllowance::where(['workgroup_id' => $employee->workgroup_id, 'is_default' => 1])->get();
            if ($workgroup_allowance->count() > 0) {
                // Looping workgroup allowance
                foreach ($workgroup_allowance as $wg_allowance) {
                    $allowances = array(); // Variabel buat nampung allowance yang sudah dipunya employee (demi menghindari duplikat data)
                    // Cari employee allowance berdasarkan allowance_id dari workgroup dan employee_id
                    $employeeallowance = EmployeeAllowance::where(['allowance_id' => $wg_allowance->allowance_id, 'employee_id' => $employee->id, 'month' => $request->montly])->get();
                    // Kalau employee allowance ketemu
                    if ($employeeallowance->count() > 0) {
                        // Looping employee allowance
                        foreach ($employeeallowance as $e_allowance) {
                            // Simpan employee allowance yang sudah ada pada variabel yang telah disiapkan
                            $allowances[] = $e_allowance->allowance_id;
                        }
                    }

                    // Jika data employee allowance bukan data yang sama
                    if (!in_array($wg_allowance->allowance_id, $allowances)) {
                        // Buat employee allowance yang baru
                        $employeeAllowance = EmployeeAllowance::create([
                            'employee_id' => $employee->id,
                            'allowance_id' => $wg_allowance->allowance_id,
                            'value' => $wg_allowance->value,
                            'type' => $wg_allowance->type,
                            'month' => $request->montly,
                            'year' => $request->year,
                            'status' => 1
                        ]);

                        $get_allowance = Allowance::where('id', $employeeAllowance->allowance_id)->first();
                        if ($get_allowance->reccurance == 'monthly') {
                            $employeeAllowance->factor = 1;
                            $employeeAllowance->save();
                        } else {
                            $employeeAllowance->factor = 0;
                            $employeeAllowance->save();
                        }
                    }
                }
                return response()->json(["message" => "Berhasil Generate"], 200);
            } else {
                return response()->json(["message" => "Gagal Generate"], 200);
            }
        }
    }
}
