<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Models\Spl;
use App\Models\Employee;
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

        //Count Data
        $query = DB::table('spls');
        $query->select(
            'spls.*',
            'employees.name as employee_name'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'spls.employee_id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('spls');
        $query->select(
            'spls.*',
            'employees.name as employee_name'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'spls.employee_id');
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
        return view('admin.spl.index');
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
        $dateTimeStart = str_replace('/','-',$request->start_overtime);
        $dateTimeFinish = str_replace('/','-',$request->finish_overtime);
        $spl = Spl::create([
            'employee_id' => $request->employee_name,
            'nik' => $request->nik,
            'spl_date' => $request->date,
            'start_overtime' => $dateTimeStart,
            'finish_overtime' => $dateTimeFinish,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);
        $spl->duration = floor((strtotime($dateTimeFinish) - strtotime($dateTimeStart)) / (60*60));
        // $jam = floor($spl->duration/(60*60));
        // dd($spl);
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
            // dd($request->working_shift);
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
        // return response()->json([
        //     'status'     => true,
        //     'results'     => 'Berhasil Simpan Data',
        // ], 200);
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
        $dateTimeStart = str_replace('/','-',$request->start_overtime);
        $dateTimeFinish = str_replace('/','-',$request->finish_overtime);
        $spl = Spl::find($id);
        $spl->employee_id = $request->employee_name;
        $spl->spl_date = $request->date;
        $spl->nik = $request->nik;
        $spl->start_overtime = $request->start_overtime;
        $spl->finish_overtime = $request->finish_overtime;
        $spl->notes = $request->notes;
        $spl->status = $request->status;
        $spl->duration = floor((strtotime($dateTimeFinish) - strtotime($dateTimeStart)) / (60*60));
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
        } catch (\Illuminate\Database\QueryException $e) {
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
                if (is_numeric($sheet->getCellByColumnAndRow(3, $row)->getValue())){
                    $start_overtime = date('Y-m-d H:i:s', strtotime("-7 hours", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(3, $row)->getValue())));
                }else{
                    $start_overtime = date('Y-m-d H:i:s', strtotime($sheet->getCellByColumnAndRow(3, $row)->getValue()));
                }
                if (is_numeric($sheet->getCellByColumnAndRow(4, $row)->getValue())){
                    $finish_overtime = date('Y-m-d H:i:s', strtotime("-7 hours", \PHPExcel_Shared_Date::ExcelToPHP($sheet->getCellByColumnAndRow(4, $row)->getValue())));
                }else{
                    $finish_overtime = date('Y-m-d H:i:s', strtotime($sheet->getCellByColumnAndRow(4, $row)->getValue()));
                }
                $employee = Employee::whereRaw("upper(name) like '%$employee_name%'")->first();
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
                    'nik' => $nid,
                    'start_overtime' => $start_overtime,
                    'finish_overtime' => $finish_overtime,
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
            // $check = Spl::where('nik', $spl->nik)->first();
            // if (!$check) {
                $splimport = Spl::create([
                    'employee_id' => $spl->employee_id,
                    'nik' => $spl->nik,
                    'spl_date' => $spl->date,
                    'start_overtime' => $spl->start_overtime,
                    'finish_overtime' => $spl->finish_overtime,
                    'status' => 1
                ]);
                if (!$splimport) {
                    DB::rollback();
                    return response()->json([
                        'status' => false,
                        'message'   => $splimport
                    ], 400);
                }
            // }
        }
        DB::commit();
        return response()->json([
            'status' => true,
            'results' => route('spl.index'),
        ], 200);
    }
}
