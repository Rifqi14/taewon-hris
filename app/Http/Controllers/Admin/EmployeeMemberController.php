<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class EmployeeMemberController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'employeemember'));
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $employee_id = $request->employee_id;

        //Count Data
        $query = DB::table('employee_members');
        $query->select('employee_members.*');
        $query->leftJoin('employees', 'employees.id', '=', 'employee_members.employee_id');
        $query->where('employee_members.employee_id', '=', $employee_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_members');
        $query->select(
            'employee_members.*'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'employee_members.employee_id');
        $query->where('employee_members.employee_id', '=', $employee_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('id', 'asc');
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
         $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'nik'        => 'required',
            'relation'   => 'required',
            'address'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeemember = EmployeeMember::create([
            'employee_id'  => $request->employee_id,
            'name'         => $request->name,
            'nik'          => $request->nik,
            'relation'     => $request->relation,
            'address'      => $request->address
        ]);

        if (!$employeemember) {
            return response()->json([
                'status' => false,
                'message'   => $employeemember
            ], 400);
        }
        return response()->json([
            'status' => true,
            'results' => 'Success Add Data!',
        ], 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeMember  $employeeMember
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeMember $employeeMember)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeMember  $employeeMember
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeemember = EmployeeMember::find($id);
        return response()->json([
            'status' 	=> true,
            'data'      => $employeemember
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeMember  $employeeMember
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'nik'        => 'required',
            'ralation'   => 'required',
            'address'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $employeemember = EmployeeMember::find($id);
        $employeemember->employee_id = $request->employee_id;
        $employeemember->name        = $request->name;
        $employeemember->nik         = $request->nik;
        $employeemember->relation    = $request->relation;
        $employeemember->addreess    = $request->addreess;
        $employeemember->save();

        if (!$employeemember) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeemember
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $employeemember
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeMember  $employeeMember
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $employeemember = EmployeeMember::find($id);
            $employeemember->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
