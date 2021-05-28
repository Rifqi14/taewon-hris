<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Models\Spl;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SPLController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'spl'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function selectemployee(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employees.id as employee_id',
            'employees.nid as nid',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.id as title_id',
            'titles.name as title_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        $query->where('employees.status', '=', 1);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employees');
        $query->select(
            'employees.*',
            'employees.id as employee_id',
            'employees.nid as nid',
            'departments.id as department_id',
            'departments.name as department_name',
            'titles.id as title_id',
            'titles.name as title_name'
        );
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->whereRaw("upper(employees.name) like '%$name%'");
        $query->where('employees.status', '=', 1);
        $query->offset($start);
        $query->limit($length);
        $employees = $query->get();

        $data = [];
        foreach ($employees as $employee) {
            $employee->no = ++$start;
            $data[] = $employee;
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
        $employee_id = strtoupper(str_replace("'","''",$request->employee_id));
        $nid = strtoupper($request->nid);
        $start_date = $request->start_date ? Carbon::parse(changeSlash($request->start_date))->endOfDay()->toDateTimeString() : '';
        $finish_date = $request->finish_date ? Carbon::parse(changeSlash($request->finish_date))->endOfDay()->toDateTimeString() : '';

        //Count Data
        $query = DB::table('spls');
        $query->select(
            'spls.*',
            'employees.name as employee_name',
            'employees.nid as nid'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'spls.employee_id');
        if ($employee_id != "") {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($start_date && $finish_date) {
            $query->whereRaw("spls.start_date >= '$start_date'");
            $query->whereRaw("spls.finish_date <= '$finish_date'");
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('spls');
        $query->select(
            'spls.*',
            'employees.name as employee_name',
            'employees.nid as nid'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'spls.employee_id');
        if ($employee_id != "") {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($start_date && $finish_date) {
            $query->whereRaw("spls.start_date >= '$start_date'");
            $query->whereRaw("spls.finish_date <= '$finish_date'");
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $spls = $query->get();

        $data = [];
        foreach ($spls as $spl) {
            $spl->no = ++$start;
            $data[] = $spl;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    public function index()
    {
        $query = DB::table('employees');
        $query->select('employees.*');
        $query->where('employees.status', 1);
        $query->orderBy('employees.name', 'asc');
        $employees = $query->get();
        return view('admin.spl.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.spl.create');
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
            'employee_name'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        // $dateTimeStart = str_replace('/','-',$request->start_overtime);
        // $dateTimeFinish = str_replace('/','-',$request->finish_overtime);
        $spl = Spl::create([
            'employee_id' => $request->employee_name,
            'spl_date'    => $request->spl_date,
            'start_date'  => $request->start_date,
            'start_time'  => $request->start_time,
            'finish_date' => $request->finish_date,
            'finish_time' => $request->finish_time,
            'notes'       => $request->notes,
            'status'      => $request->status,
        ]);
        $spl->duration = floor((strtotime(str_replace('/','-',$request->start_date).' '.$request->finish_time) - strtotime(str_replace('/','-',$request->finish_date).' '.$request->start_time)) / (60*60));
        $spl->save();
        if (!$spl) {
            return response()->json([
                'status' => false,
                'message'     => $spl
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('spl.index'),
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
    public function durationupdate(Request $request)
    {
        if (isset($request->duration)) {
            $duration = Spl::find($request->spl_id);
            $duration->duration = $request->duration;
            $duration->save();
        } 
        if ($duration) {
            return response()->json([
                'success' => true,
                'results'     => route('spl.index'),
            ], 200);
        }
    }
    public function edit($id)
    {
        $spl = Spl::with('employee')->find($id);
        if($spl){
            return view('admin.spl.edit', compact('spl'));
        }else{
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
            'employee_name'         => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        // $dateTimeStart = str_replace('/','-',$request->start_overtime);
        // $dateTimeFinish = str_replace('/','-',$request->finish_overtime);
        $spl = Spl::find($id);
        $spl->employee_id = $request->employee_name;
        $spl->spl_date    = $request->spl_date;
        $spl->start_date  = $request->start_date;
        $spl->start_time  = $request->start_time;
        $spl->finish_date = $request->finish_date;
        $spl->finish_time = $request->finish_time;
        $spl->notes       = $request->notes;
        $spl->status      = $request->status;
        $spl->duration    = floor((strtotime(str_replace('/','-',$request->start_date).' '.$request->finish_time) - strtotime(str_replace('/','-',$request->finish_date).' '.$request->start_time)) / (60*60));
        $spl->save();

        if (!$spl) {
            return response()->json([
                'status'    => false,
                'message'   => $spl
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('spl.index'),
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
            $spl = Spl::find($id);
            $spl->delete();
        }catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
    public function import(Request $request)
    {
        return view('admin.spl.import');
    }
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file'         => 'required|mimes:xlsx'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $file = $request->file('file');
        try {
            $filetype     = \PHPExcel_IOFactory::identify($file);
            $objReader = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel = $objReader->load($file);
        } catch (\Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
        $data     = [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            $nid = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            if ($nid) {
                if (is_numeric($sheet->getCellByColumnAndRow(0, $row)->getValue())){
                    $date = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(0, $row)->getValue()));
                }else{
                    $date = date('Y-m-d', strtotime($sheet->getCellByColumnAndRow(0, $row)->getValue()));
                }
                $employee_name = strtoupper($sheet->getCellByColumnAndRow(2, $row)->getValue());
                // if (is_numeric($sheet->getCellByColumnAndRow(3, $row)->getValue())){
                //     $start_overtime = date('Y-m-d H:i:s', strtotime("-7 hours", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(3, $row)->getValue())));
                // }else{
                //     $start_overtime = date('Y-m-d H:i:s', strtotime($sheet->getCellByColumnAndRow(3, $row)->getValue()));
                // }
                if (is_numeric($sheet->getCellByColumnAndRow(3, $row)->getValue())){
                    $start_date = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(3, $row)->getValue()));
                }else{
                    $start_date = date('Y-m-d', strtotime($sheet->getCellByColumnAndRow(3, $row)->getValue()));
                }
                if (is_numeric($sheet->getCellByColumnAndRow(4, $row)->getValue())){
                    $start_time = date('H:i:s', strtotime("-7 hours", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(4, $row)->getValue())));
                }else{
                    $start_time = date('H:i:s', strtotime($sheet->getCellByColumnAndRow(4, $row)->getValue()));
                }
                if (is_numeric($sheet->getCellByColumnAndRow(5, $row)->getValue())){
                    $finish_date = date('Y-m-d', \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(5, $row)->getValue()));
                }else{
                    $finish_date = date('Y-m-d', strtotime($sheet->getCellByColumnAndRow(5, $row)->getValue()));
                }
                if (is_numeric($sheet->getCellByColumnAndRow(6, $row)->getValue())){
                    $finish_time = date('H:i:s', strtotime("-7 hours", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(6, $row)->getValue())));
                }else{
                    $finish_time = date('H:i:s', strtotime($sheet->getCellByColumnAndRow(6, $row)->getValue()));
                }
                $employee = Employee::whereRaw("upper(nid) = '$nid'")->first();
                $status = 1;
                $error_message = '';
                if (!$employee) {
                    $status = 0;
                    if (!$employee) {
                        $error_message .= 'Employee Name Not Found</br>';
                    }
                }else{
                    $error_message .= 'the data is all right</br>';
                }
                $data[] = array(
                    'index' => $no,
                    'date' => $date,
                    'employee_name' => $employee_name,
                    'employee_id' => $employee ? $employee->id : null,
                    'nid' => $nid,
                    'start_date' => $start_date,
                    'start_time' => $start_time,
                    'finish_date' => $finish_date,
                    'finish_time' => $finish_time,
                    'error_message' => $error_message,
                    'status' => $status
                );
                $no++;
            }
            // dd($data);
        }
        return response()->json([
            'status'     => true,
            'data'     => $data
        ], 200);
    }
    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $spls = json_decode($request->spls);
        DB::beginTransaction();
        foreach ($spls as $spl) {
            $check = Spl::where('spl_date', $spl->date)->where('employee_id', $spl->employee_id)->first();
            if($check){
                $check->delete();
            }
            $splimport = Spl::create([
                'employee_id' => $spl->employee_id,
                'spl_date'    => $spl->date,
                'start_date'  => $spl->start_date,
                'start_time'  => $spl->start_time,
                'finish_date' => $spl->finish_date,
                'finish_time' => $spl->finish_time,
                'status' => 1
            ]);
            $splimport->duration = floor((strtotime($spl->finish_time) - strtotime($spl->start_time)) / (60*60));
            $splimport->save();
            if (!$splimport) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'   => $splimport
                ], 400);
            }
        }
        DB::commit();
        return response()->json([
            'status' => true,
            'results' => route('spl.index'),
        ], 200);
    }
}
