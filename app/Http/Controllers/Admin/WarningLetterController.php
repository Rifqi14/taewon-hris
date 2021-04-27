<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\WarningLetter;
use Illuminate\Support\Facades\Validator;

class WarningLetterController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'warningletter'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        // $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        // $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;
        $nik = $request->nik;
        $name = strtoupper(str_replace("'","''",$request->name));

        // Count Data
        $query = DB::table('warning_letters');
        $query->select(
            'warning_letters.*',
            'employees.name as employee_name',
            'employees.nid as employee_id',
            'employees.join_date as join_date',
            'titles.name as title_name',
            'departments.name as department_name',
        );
        $query->leftJoin('employees', 'employees.id', '=', 'warning_letters.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        if ($name) {
            $query->whereRaw("upper(employees.name) like '%$name%'");
        }
        if ($nik) {
            $query->whereRaw("employees.nid like '%$nik%'");
        }
        $recordsTotal = $query->get()->count();

        // Select Pagination
        $query = DB::table('warning_letters');
        $query->select(
            'warning_letters.*',
            'employees.name as employee_name',
            'employees.nid as employee_id',
            'employees.join_date as join_date',
            'titles.name as title_name',
            'departments.name as department_name',
        );
        $query->leftJoin('employees', 'employees.id', '=', 'warning_letters.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        if ($name) {
            $query->whereRaw("upper(employees.name) like '%$name%'");
        }
        if ($nik) {
            $query->whereRaw("employees.nid like '%$nik%'");
        }
        $query->offset($start);
        $query->limit($length);
        // $query->orderBy($sort, $dir);
        $leaves = $query->get();
        $data = [];
        foreach ($leaves as $leave) {
            $leave->no      = ++$start;
            $data[]         = $leave;
        }
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
        ], 200);
    }
    public function getLatestId()
    {
        $read = WarningLetter::max('id');
        return $read + 1;
    }
    public function index()
    {
        return view('admin.warningletter.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.warningletter.create');
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
            'employee'      => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $id = $this->getLatestId();
        DB::beginTransaction();
        $warningletter = WarningLetter::create([
            'id'                    => $id,
            'employee_id'           => $request->employee_id,
            'status'                => 0,
            'number_warning_letter' => $request->number_warning_letter,
            'notes'                 => $request->reason,
            'from'                  => changeDateFormat('Y-m-d', changeSlash($request->from)),
            'to'                    => changeDateFormat('Y-m-d', changeSlash($request->to)),
        ]);
        if (!$warningletter) {
            return response()->json([
                'status' => false,
                'message' 	=> $warningletter
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('warningletter.index')
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
        $warningletter = WarningLetter::find($id);
        if ($warningletter) {
            return view('admin.warningletter.edit', compact('warningletter'));
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
            'employee'      => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $warningletter = WarningLetter::find($id);
        $warningletter->employee_id = $request->employee_id;
        $warningletter->status = 0;
        $warningletter->notes = $request->reason;
        $warningletter->number_warning_letter = $request->number_warning_letter;
        $warningletter->from = changeDateFormat('Y-m-d', changeSlash($request->from));
        $warningletter->to = changeDateFormat('Y-m-d', changeSlash($request->to));
        $warningletter->save();
        if (!$warningletter) {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => $warningletter
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'    => true,
            'results'   => route('warningletter.index')
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
            $warningletter = WarningLetter::find($id);
            $warningletter->delete();
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
