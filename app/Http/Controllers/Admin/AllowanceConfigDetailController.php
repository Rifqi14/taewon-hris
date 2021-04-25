<?php

namespace App\Http\Controllers\Admin;


use App\Models\AllowanceConfigDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Allowance;
use Illuminate\Support\Facades\DB;

class AllowanceConfigDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        $start      = $request->start;
        $sort       = $request->columns[$request->order[0]['column']]['data'];
        $dir        = $request->order[0]['dir'];
        $allowanceconfig  = $request->allowance_config_id;

        // Count Data
        $allowance = Allowance::with(['allowanceconfigdetail' => function ($q) use ($allowanceconfig) {
            if ($allowanceconfig) {
                $q->ByAllowanceConfig($allowanceconfig);
            }
        }])->Active();
        $recordsTotal = $allowance->get()->count();

        // Select Pagination
        $allowance = Allowance::with(['allowanceconfigdetail' => function ($q) use ($allowanceconfig) {
            if ($allowanceconfig) {
                $q->ByAllowanceConfig($allowanceconfig);
            }
        }]);
        $allowance->orderBy('path', $dir);
        $allowances = $allowance->get();

        $data = [];
        foreach ($allowances as $key => $allowance) {
            $allowance->no = ++$start;
            $allowance->path = str_replace("->", " <i class='fas fa-angle-right'></i>", $allowance->path);
            $data[] = $allowance;
        }

        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'sort'              => $sort,
            'data'              => $data
        ], 200);
    }
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AllowanceConfigDetail  $allowanceConfigDetail
     * @return \Illuminate\Http\Response
     */
    public function show(AllowanceConfigDetail $allowanceConfigDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AllowanceConfigDetail  $allowanceConfigDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(AllowanceConfigDetail $allowanceConfigDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AllowanceConfigDetail  $allowanceConfigDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AllowanceConfigDetail $allowanceConfigDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AllowanceConfigDetail  $allowanceConfigDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(AllowanceConfigDetail $allowanceConfigDetail)
    {
        //
    }
}
