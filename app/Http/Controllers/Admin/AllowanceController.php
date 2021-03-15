<?php

namespace App\Http\Controllers\Admin;

use App\Models\Allowance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WorkingtimeAllowance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AllowanceController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'allowance'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $allowance = strtoupper($request->name);

        //Count Data
        $query = DB::table('allowances');
        $query->select('allowances.*');
        $query->whereRaw("upper(allowance) like '%$allowance%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('allowances');
        $query->select('allowances.*');
        $query->whereRaw("upper(allowance) like '%$allowance%'");
        $query->offset($start);
        $query->limit($length);
        $allowances = $query->get();

        $data = [];
        foreach ($allowances as $allow) {
            $allow->no = ++$start;
            $data[] = $allow;
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
        $allowance = strtoupper($request->allowance);

        //Count Data
        $query = DB::table('allowances');
        $query->select('allowances.*', 'accounts.acc_category as account_category', 'accounts.acc_code as account_code', 'accounts.acc_name as account_name', 'group_allowances.name as groupallowance');
        $query->leftJoin('accounts', 'accounts.id', '=', 'allowances.account_id');
        $query->leftJoin('group_allowances', 'group_allowances.id', '=', 'allowances.group_allowance_id');
        $query->whereRaw("upper(allowance) like '%$allowance%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('allowances');
        $query->select('allowances.*', 'accounts.acc_category as account_category', 'accounts.acc_code as account_code', 'accounts.acc_name as account_name',  'group_allowances.name as groupallowance');
        $query->leftJoin('accounts', 'accounts.id', '=', 'allowances.account_id');
        $query->leftJoin('group_allowances', 'group_allowances.id', '=', 'allowances.group_allowance_id');
        $query->whereRaw("upper(allowance) like '%$allowance%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $roles = $query->get();

        $data = [];
        foreach ($roles as $role) {
            $role->no = ++$start;
            $role->category = @config('enums.allowance_category')[$role->category];
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
        return view('admin.allowance.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.allowance.create');
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
            'allowance'     => 'required',
            'category'      => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $allowance = Allowance::create([
            'allowance'     => $request->allowance,
            'category'      => $request->category,
            'account_id'    => $request->account,
            'group_allowance_id'    => $request->groupallowance,
            'reccurance'    => $request->recurrence,
            'working_type'  => $request->working_type,
            'days_devisor'  => $request->days_devisor,
            'basic_salary'  => $request->basic_salary,
            'notes'         => $request->notes,
            'status'        => $request->status
        ]);
        if ($allowance) {
            $workingtimes = explode(',', $request->working_time);
            $arr_workingtime = array();
            foreach ($workingtimes as $key => $value) {
                $arr_workingtime[] = array(
                    'allowance_id'      => $allowance->id,
                    'workingtime_id'    => $value,
                    'created_at'        => Carbon::now()->toDateTimeString(),
                    'updated_at'        => Carbon::now()->toDateTimeString()
                );
            }
            if (isset($arr_workingtime)) {
                $workingtime_allowance = WorkingtimeAllowance::insert($arr_workingtime);
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $allowance
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('allowance.index')
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
        $allowance = Allowance::with('account','groupallowance')->find($id);
        if ($allowance) {
            return view('admin.allowance.edit', compact('allowance'));
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
            'allowance'     => 'required',
            'category'      => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $allowance = Allowance::find($id);
        $allowance->allowance = $request->allowance;
        $allowance->category = $request->category;
        $allowance->account_id = $request->account;
        $allowance->reccurance = $request->recurrence;
        $allowance->group_allowance_id = $request->groupallowance;
        $allowance->working_type = $request->working_type;
        $allowance->days_devisor = $request->days_devisor;
        $allowance->basic_salary = $request->basic_salary;
        $allowance->notes = $request->notes;
        $allowance->status = $request->status;
        $allowance->save();
        if ($allowance) {
            if ($request->working_time) {
                $delete = WorkingtimeAllowance::where('allowance_id', $id)->delete();
                $workingtime_allowances = explode(',', $request->working_time);
                foreach ($workingtime_allowances as $key => $value) {
                    $arr_workingtime[] = array(
                        'allowance_id'      => $allowance->id,
                        'workingtime_id'    => $value,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString()
                    );
                }
                if (isset($arr_workingtime)) {
                    $workingtime_allowance = WorkingtimeAllowance::insert($arr_workingtime);
                }
            } else {
                $delete = WorkingtimeAllowance::where('allowance_id', $id)->delete();
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $allowance
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('allowance.index'),
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
            $allowance = Allowance::find($id);
            $allowance->delete();
            $this->destroychild($allowance->id);
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
    function destroychild($parent_id)
    {
        $allowances = Allowance::where('parent_id', '=', $parent_id)->get();
        foreach ($allowances as $allowance) {
            try {
                Allowance::find($allowance->id)->delete();
            } catch (\Illuminate\Database\QueryException $e) {
            }
        }
    }
}