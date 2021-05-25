<?php

namespace App\Http\Controllers\Admin;

use App\Models\OvertimeAllowance;
use Illuminate\Http\Request;
use App\Models\Allowance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OvertimeAllowanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function read(Request $request)
    {
        $start              = $request->start;
        $length             = $request->length;
        $query              = $request->search['value'];
        $sort               = $request->columns[$request->order[0]['column']]['data'];
        $dir                = $request->order[0]['dir'];
        $overtime_scheme_id    = $request->overtime_scheme_id;

        // Count Data
        $allowance          = Allowance::with(['groupallowance','allowanceovertimescheme' => function ($query) use ($overtime_scheme_id) {
            $query->where('overtime_scheme_id', $overtime_scheme_id);
        }])->get();
        $recordsTotal       = $allowance->count();

        // Select Pagination
        $allowance          = Allowance::with(['groupallowance','allowanceovertimescheme' => function ($query) use ($overtime_scheme_id) {
            $query->where('overtime_scheme_id', $overtime_scheme_id);
        }]);
        $allowance->paginate($length);
        $allowance->orderBy($sort, $dir);
        $allowances         = $allowance->get();

        $data               = [];
        foreach ($allowances as $allowance) {
            $allowance->no = ++$start;
            $allowance->category = @config('enums.allowance_category')[$allowance->category];
            $data[] = $allowance;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
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
        $status = $request->status;

        DB::beginTransaction();
        if ($status == 1) {
            $allowance = OvertimeAllowance::create([
                'allowance_id'     => $request->allowanceID,
                'overtime_scheme_id'    => $request->overtime_scheme_id,
            ]);
            if (!$allowance) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => $allowance
                ], 400);
            } else {
                DB::commit();
                return response()->json([
                    'status'    => true,
                    'message'   => 'Success add allowance',
                ], 200);
            }
        } else {
            $allowance = OvertimeAllowance::where('allowance_id', $request->allowanceID)->where('overtime_scheme_id', $request->overtime_scheme_id)->first();
            if ($allowance) {
                $allowance->delete();
                DB::commit();
                return response()->json([
                    'status'    => true,
                    'message'   => 'Success remove allowance',
                ], 200);
            } else {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Data not found',
                ], 400);
            }
        }
    }

    public function updateAll(Request $request)
    {
        $status = $request->status;
        DB::beginTransaction();
        if ($status == 1) {
            $allowances = Allowance::getActive();
            $deleteAll = OvertimeAllowance::ByOvertimeScheme($request->overtime_scheme_id)->delete();
            foreach ($allowances as $key => $value) {
                $create = OvertimeAllowance::create([
                    'allowance_id'     => $value->id,
                    'overtime_scheme_id'    => $request->overtime_scheme_id,
                ]);

                if (!$create) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Error add data all allowances'
                    ], 400);
                }
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success add data all allowances'
            ], 200);
        } else {
            $deleteAll = OvertimeAllowance::ByOvertimeScheme($request->overtime_scheme_id)->delete();
            if (!$deleteAll) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Error remove all allowances',
                ], 400);
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success remove all departemnts'
            ], 200);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\OvertimeAllowance  $overtimeAllowance
     * @return \Illuminate\Http\Response
     */
    public function show(OvertimeAllowance $overtimeAllowance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\OvertimeAllowance  $overtimeAllowance
     * @return \Illuminate\Http\Response
     */
    public function edit(OvertimeAllowance $overtimeAllowance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\OvertimeAllowance  $overtimeAllowance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OvertimeAllowance $overtimeAllowance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\OvertimeAllowance  $overtimeAllowance
     * @return \Illuminate\Http\Response
     */
    public function destroy(OvertimeAllowance $overtimeAllowance)
    {
        //
    }
}
