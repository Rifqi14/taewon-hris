<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllowanceIncrease;
use App\Models\WorkGroup;
use App\Models\Title;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;

class AllowanceIncreaseController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'allowanceincreases'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $note = $request->note;
        $year = $request->year;
        $month = $request->month;

        //Count Data
        $query = DB::table('allowance_increases');
        $query->select('allowance_increases.*');
        if ($note) {
            $query->whereRaw("allowance_increases.note like '%$note%'");
        }
        if ($year) {
            $query->where('year', $year);
        }
        if ($month) {
            $query->where('month', $month);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('allowance_increases');
        $query->select('allowance_increases.*',DB::Raw('(select count(*) from allowance_increase_details as aid where aid.allowance_increase_id = allowance_increases.id) as total_employee'));
        if ($note) {
            $query->whereRaw("allowance_increases.note like '%$note%'");
        }
        if ($year) {
            $query->where('year', $year);
        }
        if ($month) {
            $query->where('month', $month);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $allowanceincreases = $query->get();

        $data = [];
        foreach ($allowanceincreases as $allowanceincrease) {
            $allowanceincrease->no = ++$start;
            $data[] = $allowanceincrease;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'sort' => $sort,
            'data' => $data
        ], 200);
    }
    public function reademployee(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $employee_id = $request->employee_id;
        $nid = $request->nid;
        $departments = $request->departments;
        $workgroup = $request->workgroup;
        $position = $request->position;
        $year = $request->year;
        $allowance_id = $request->allowance_id;
        $month = date('m',mktime($request->month));

        // dd($allowance_id, $month, $year);


        //Count Data
        $query = DB::table('employee_allowances');
        $query->select('employee_allowances.*',
            'employees.name',
            'employees.nid',
            'employees.id',
            // 'employee_salarys.employee_id',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.name as title_name',
            'work_groups.id as workgroup_id',
            'work_groups.name as workgroup_name',
            'employee_allowances.value as current_salary'
            // DB::raw("(SELECT MAX(value) FROM employee_allowances where employee_id = employees.id) as current_salary")
        );
        $query->leftJoin('employees', 'employees.id', '=', 'employee_allowances.employee_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->where('employee_allowances.type', '!=', 'automatic');
        $query->where('employee_allowances.year', $year);
        $query->where('employee_allowances.allowance_id', $allowance_id);
        $query->where('employee_allowances.month', $month);
        if ($employee_id) {
            $query->where('employees.id', $employee_id);
        }
        // if ($dataa) {
        //     $query->whereNotIn('employees.id', $dataa);
        // }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($departments) {
            foreach ($departments as $department) {
                $query->whereRaw("departments.path like '%$department%'");
            }
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($position) {
            $query->whereIn('employees.title_id', $position);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_allowances');
        $query->select('employee_allowances.*',
            'employees.name',
            'employees.nid',
            'employees.id',
            // 'employee_salarys.employee_id',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.name as title_name',
            'work_groups.id as workgroup_id',
            'work_groups.name as workgroup_name',
            'employee_allowances.value as current_salary'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'employee_allowances.employee_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->where('employee_allowances.type', '!=', 'automatic');
        $query->where('employee_allowances.year', $year);
        $query->where('employee_allowances.allowance_id', $allowance_id);
        $query->where('employee_allowances.month', $month);
        if ($employee_id) {
            $query->where('employees.id', $employee_id);
        }
        // if ($dataa) {
        //     $query->whereNotIn('employees.id', $dataa);
        // }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($departments) {
            foreach ($departments as $department) {
                $query->whereRaw("departments.path like '%$department%'");
            }
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($position) {
            $query->whereIn('employees.title_id', $position);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $allowanceincreases = $query->get();

        $data = [];
        foreach ($allowanceincreases as $allowanceincrease) {
            $allowanceincrease->no = ++$start;
            $data[] = $allowanceincrease;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'sort' => $sort,
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
        // $employees = Employee::all();
        $allowanceincrease = DB::table('allowance_increases');
        $allowanceincrease->select('allowance_increases.*');
        $allowanceincreases = $allowanceincrease->get();

        $emp = DB::table('employees');
        $emp->select('employees.*');
        $emp->where('status', 1);
        $employees = $emp->get();
        // $departments = Department::all();
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->orderBy('path', 'asc');
        $departments = $query->get();
        $workgroups = WorkGroup::all();
        $titles = Title::all();
        return view('admin.allowanceincrease.index', compact('allowanceincreases', 'employees', 'departments', 'workgroups', 'titles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.allowanceincrease.create');
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
            'year'            => 'required',
            'month'           => 'required',
            'allowance_id'    => 'required',
            'type_value'      => 'required',
            'value'           => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $allowanceIncrease = AllowanceIncrease::create([
            'year'        => $request->year,
            'month'       => $request->month,
            'allowance_id'=> $request->allowance_id,
            'type_value'  => $request->type_value,
            'value'       => $request->value,
            'note'        => $request->note
        ]);

        if (!$allowanceIncrease) {
            return response()->json([
                'status' => false,
                'message'     => $allowanceIncrease
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'   => route('allowanceincrease.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AllowanceIncrease  $allowanceIncrease
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // echo('aaaaaa');
        $allowanceincrease = AllowanceIncrease::find($id);
        $month = date('F',mktime(0,0,0,$allowanceincrease->month,10));

        // $employees = Employee::all();
        $emp = DB::table('employees');
        $emp->select('employees.*');
        $emp->where('status', 1);
        $employees = $emp->get();
        // $departments = Department::all();
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->orderBy('path', 'asc');
        $departments = $query->get();
        $workgroups = WorkGroup::all();
        $titles = Title::all();

       
        // return response()->json([$allowanceincrease->month, $month]);
        return view('admin.allowanceincrease.show', compact('allowanceincrease', 'month','employees', 'departments', 'workgroups', 'titles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AllowanceIncrease  $allowanceIncrease
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $allowanceincrease = AllowanceIncrease::find($id);
        return view('admin.allowanceincrease.edit', compact('allowanceincrease'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AllowanceIncrease  $allowanceIncrease
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'year'            => 'required',
            'month'           => 'required',
            'allowance_id'    => 'required',
            'type_value'      => 'required',
            'value'           => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $allowanceIncrease = AllowanceIncrease::find($id);
        $allowanceIncrease->year         = $request->year;
        $allowanceIncrease->month        = $request->month;
        $allowanceIncrease->allowance_id = $request->allowance_id;
        $allowanceIncrease->type_value   = $request->type_value;
        $allowanceIncrease->value        = $request->value;
        $allowanceIncrease->note         = $request->note;
        $allowanceIncrease->save();

        if (!$allowanceIncrease) {
            return response()->json([
                'status' => false,
                'message' => $allowanceIncrease
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'   => route('allowanceincrease.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AllowanceIncrease  $allowanceIncrease
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $allowanceIncrease = AllowanceIncrease::find($id);
            $allowanceIncrease->delete();
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
