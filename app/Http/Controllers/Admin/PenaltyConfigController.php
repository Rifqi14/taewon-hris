<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Allowance;
use App\Models\PenaltyConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class PenaltyConfigController extends Controller
{
    /**
     * First method to called whenever this class initiated
     */
    function __construct() {
        View::share('menu_active', url('admin/' . 'penaltyconfig'));
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.penaltyconfig.index');
    }

    /**
     * Method to get Penalty config data and detail with specific parameter
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        $start              = $request->start;
        $length             = $request->length;
        $query              = $request->search['value'];
        $sort               = $request->columns[$request->order[0]['column']]['data'];
        $dir                = $request->order[0]['dir'];
        $workgroupID        = $request->workgroupID;
        $leaveSettingID     = $request->leaveSettingID;

        // Count Data
        $penaltyConfig      = PenaltyConfig::with(['allowance', 'leave', 'workgroup']);
        if ($workgroupID) {
            $penaltyConfig->ByWorkgroup($workgroupID);
        }
        if ($leaveSettingID) {
            $penaltyConfig->leave()->wherePivot('leave_setting_id', $leaveSettingID);
        }
        $recordsTotal       = $penaltyConfig->get()->count();

        // Select for pagination
        $penaltyConfig      = PenaltyConfig::with(['allowance', 'leave', 'workgroup']);
        if ($workgroupID) {
            $penaltyConfig->ByWorkgroup($workgroupID);
        }
        if ($leaveSettingID) {
            $penaltyConfig->leave()->wherePivot('leave_setting_id', $leaveSettingID);
        }
        $penaltyConfig->paginate($length);
        $penaltyConfig->orderBy($sort, $dir);
        $penaltyConfigs     = $penaltyConfig->get();

        $data               = [];
        foreach ($penaltyConfigs as $key => $config) {
            $config->no         = ++$start;
            $config->leave_name = '';
            foreach ($config->leave as $keyLeave => $leave) {
                $config->leave_name .= $leave['leave_name'];
                if ($keyLeave != $config->leave()->count() - 1) {
                    $config->leave_name .= " - ";
                }
            }
            $config->type       = ucwords(strtolower($config->type), " ");
            $data[]             = $config;
        }

        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.penaltyconfig.create');
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
            'workgroupID'   => 'required',
            'leaveSettingID'=> 'required',
            'type'          => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $penaltyConfig = PenaltyConfig::create([
            'workgroup_id'      => $request->workgroupID,
            'notes'             => $request->notes,
            'type'              => $request->type,
            'is_basic_salary'   => strpos($request->type, 'BASIC') === false ? 'NO' : 'YES',
            'status'            => $request->status,
        ]);

        if (!$penaltyConfig) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => 'Error penalty config data'
            ], 400);
        }
        
        $leaveSettingID         = explode(',', $request->leaveSettingID);
        $penaltyConfig->leave()->toggle($leaveSettingID);
        if (strpos($penaltyConfig->type, 'ALLOWANCE') !== false) {
            $penaltyConfig->allowance()->toggle($request->allowanceID);
        }
        
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('penaltyconfig.index'),
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
     * Method to get allowance data where is checked
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function readAllowance(Request $request)
    {
        $start              = $request->start;
        $length             = $request->length;
        $query              = $request->search['value'];
        $sort               = $request->columns[$request->order[0]['column']]['data'];
        $dir                = $request->order[0]['dir'];
        $penaltyConfigID    = $request->penaltyConfigID;

        // Count Data
        $allowance          = Allowance::with(['groupallowance', 'detail' => function($query) use ($penaltyConfigID) {
            $query->where('penalty_config_id', $penaltyConfigID);
        }])->get();
        $recordsTotal       = $allowance->count();

        // Select Pagination
        $allowance          = Allowance::with(['groupallowance', 'detail' => function($query) use ($penaltyConfigID) {
            $query->where('penalty_config_id', $penaltyConfigID);
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
     * Method to update attach / detach all allowance to penalty_config_details table
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
        $status         = $request->status;
        DB::beginTransaction();
        if ($status == 1) {
            $allowances = Allowance::all()->pluck('id')->toArray();
            $penaltyConfig = PenaltyConfig::find($request->penaltyConfigID);
            $penaltyConfig->allowance()->attach($allowances);

            if (!$penaltyConfig) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Error add all allowance'
                ], 400);
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success add all allowance'
            ], 200);
        } else {
            $penaltyConfig = PenaltyConfig::find($request->penaltyConfigID);
            $penaltyConfig->allowance()->detach();
            
            if (!$penaltyConfig) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Error remove all allowance'
                ], 400);
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success remove all allowance'
            ], 200);
        }
    }

    /**
     * Method to update attach / detach selected allowance to penalty_config_details table
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateAllowance(Request $request)
    {
        $status     = $request->status;

        DB::beginTransaction();
        if ($status == 1) {
            $penaltyConfig = PenaltyConfig::find($request->penaltyConfigID);
            $penaltyConfig->allowance()->attach($request->allowanceID);
            
            if (!$penaltyConfig) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Error to add allowance'
                ], 400);
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success add allowance'
            ], 200);
        } else {
            $penaltyConfig = PenaltyConfig::find($request->penaltyConfigID);
            $penaltyConfig->allowance()->detach($request->allowanceID);
            
            if (!$penaltyConfig) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => 'Error to remove allowance'
                ], 400);
            }
            DB::commit();
            return response()->json([
                'status'    => true,
                'message'   => 'Success remove allowance'
            ], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $penaltyConfig = PenaltyConfig::with(['allowance', 'leave'])->find($id);
        if ($penaltyConfig) {
            return view('admin.penaltyconfig.edit', compact('penaltyConfig'));
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
        $validator  = Validator::make($request->all(), [
            'workgroupID'       => 'required',
            'leaveSettingID'    => 'required',
            'type'              => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        $penaltyConfig = PenaltyConfig::find($id);
        $penaltyConfig->workgroup_id        = $request->workgroupID;
        $penaltyConfig->notes               = $request->notes;
        $penaltyConfig->type                = $request->type;
        $penaltyConfig->is_basic_salary     = strpos($request->type, 'BASIC') === false ? 'NO' : 'YES';
        $penaltyConfig->status              = $request->status;
        $penaltyConfig->update();

        if (!$penaltyConfig) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => 'Error update penalty config data'
            ], 400);
        }

        $leaveSettingID     = explode(',', $request->leaveSettingID);
        $penaltyConfig->leave()->sync($leaveSettingID);
        if (strpos($penaltyConfig->type, 'ALLOWANCE') === false) {
            $penaltyConfig->allowance()->detach();
        }

        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('penaltyconfig.index'),
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
            $penaltyConfig      = PenaltyConfig::find($id);
            $penaltyConfig->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'    => false,
                'message'   => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }
}