<?php

namespace App\Http\Controllers\Admin;

use App\Models\AllowanceIncreaseDetail;
use App\Models\AllowanceIncrease;
use App\Models\EmployeeAllowance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AllowanceIncreaseDetailController extends Controller
{
    public function read(Request $request)
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
        $allowance_increase_id = $request->allowance_increase_id;

        //Count Data
        $query = DB::table('allowance_increase_details');
        $query->select(
            'allowance_increase_details.*',
            'employees.name as employee_name',
            'employees.nid as nid',
            'titles.name as position',
            'departments.name as department'
            
        );
        $query->leftJoin('employees', 'employees.id', '=', 'allowance_increase_details.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->where('allowance_increase_details.allowance_increase_id', $allowance_increase_id);
        if ($employee_id) {
            $query->where('employees.id', $employee_id);
        }
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
        $query = DB::table('allowance_increase_details');
        $query->select(
            'allowance_increase_details.*',
            'employees.name as employee_name',
            'employees.nid as nid',
            'titles.name as position',
            'departments.name as department'
            
        );
        $query->leftJoin('employees', 'employees.id', '=', 'allowance_increase_details.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->where('allowance_increase_details.allowance_increase_id', $allowance_increase_id);
        if ($employee_id) {
            $query->where('employees.id', $employee_id);
        }
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
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            
            $data[] = $employee;
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
        DB::beginTransaction();
        if(count($request->employee_id) > 0)
        {
            // dd($request->employee_id);
            foreach($request->employee_id as $item => $v)
            {
                $allowanceincrease = AllowanceIncrease::where('id', $request->allowance_increase_id)->first();
                $employeeallowance = EmployeeAllowance::where('employee_id', $request->employee_id[$item])->where('allowance_id', $allowanceincrease->allowance_id)->first();
                $upcoming_amount = 0;
                if ($employeeallowance->type == 'percentage' && $allowanceincrease->type_value == 'Percentage') {
                    $upcoming_amount = (int)$request->current_salary[$item] + $allowanceincrease->value;
                }else if($allowanceincrease->type_value == 'Percentage')
                {
                    $upcoming_amount = (int)$request->current_salary[$item] + ($request->current_salary[$item] * ($allowanceincrease->value / 100));
                } else {
                    $upcoming_amount = (int)$request->current_salary[$item] + $allowanceincrease->value;
                }
                // DB::beginTransaction();
                $allowanceincreasedetail = AllowanceIncreaseDetail::create([
                    'employee_id'           => $request->employee_id[$item],
                    'allowance_increase_id' => $allowanceincrease->id,
                    'current_salery'        => $request->current_salary[$item],
                    'amount'                => $upcoming_amount,
                    'type'                  => $allowanceincrease->type_value,
                ]);
                if ($allowanceincreasedetail) {
                    $employeeallowance->value = $upcoming_amount;
                    $employeeallowance->save();
                    // dd($employeeallowance);
                    
                    if (!$employeeallowance) {
                        DB::rollback();
                        return response()->json([
                            'status' => false,
                            'message' => $employeeallowance
                        ], 400);
                    }
                }

                if (!$allowanceincreasedetail) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message' => $allowanceincreasedetail
                    ], 400);
                }
            }
            
        }
        

        DB::commit();
        return response()->json([
            'status'     => true,
            'message'   => 'Allowance Increases successfully',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AllowanceIncreaseDetail  $allowanceIncreaseDetail
     * @return \Illuminate\Http\Response
     */
    public function show(AllowanceIncreaseDetail $allowanceIncreaseDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AllowanceIncreaseDetail  $allowanceIncreaseDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(AllowanceIncreaseDetail $allowanceIncreaseDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AllowanceIncreaseDetail  $allowanceIncreaseDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AllowanceIncreaseDetail $allowanceIncreaseDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AllowanceIncreaseDetail  $allowanceIncreaseDetail
     * @return \Illuminate\Http\Response
     */
    public function updateeployeeapdate(Request $request, $id)
    {
        

    }
    public function destroy($id)
    {
        try {
            $allowanceincreasedetail = AllowanceIncreaseDetail::find($id);
            $allowanceincrease = AllowanceIncrease::where('id', $allowanceincreasedetail->allowance_increase_id)->first();
            $emloyeeallowance = EmployeeAllowance::where('employee_id', $allowanceincreasedetail->employee_id)->where('allowance_id', $allowanceincrease->allowance_id)->first();
            // dd($allowanceincrease->type_value);
            
            $emloyeeallowance->value = $allowanceincreasedetail->current_salery;
            $emloyeeallowance->save();
            $allowanceincreasedetail->delete();

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
