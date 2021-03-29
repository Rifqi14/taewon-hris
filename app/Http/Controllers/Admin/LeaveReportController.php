<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class LeaveReportController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'leavereport'));
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;
        $nik = $request->nik;
        $name = strtoupper(str_replace("'","''",$request->name));

        $query = DB::table('leaves');
        $query->select(
            'leaves.*',
            'employees.name as employee_name',
            'employees.nid as employee_id',
            'titles.name as title_name',
            'departments.name as department_name',
            'leave_settings.leave_name as leave_type',
            DB::raw("(SELECT MIN(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as start_date"),
            DB::raw("(SELECT MAX(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as finish_date")
        );
        $query->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id');
        $query->leftJoin('employees', 'employees.id', '=', 'leaves.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id');
        if ($from && $to) {
            $query->whereBetween('leave_logs.date', [$from, $to]);
        }
        if ($name) {
            $query->whereRaw("upper(employees.name) like '%$name%'");
        }
        if ($nik) {
            $query->whereRaw("employees.nid like '%$nik%'");
        }
        $query->groupBy('leaves.id', 'employees.name', 'employees.nid', 'titles.name', 'departments.name', 'leave_settings.leave_name');
        $query->whereIn('leaves.status', [1, 2]);
        $recordsTotal = $query->count();

        // Select Pagination
        $query = DB::table('leaves');
        $query->select(
            'leaves.*',
            'employees.name as employee_name',
            'employees.nid as employee_id',
            'titles.name as title_name',
            'departments.name as department_name',
            'leave_settings.leave_name as leave_type',
            DB::raw("(SELECT MIN(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as start_date"),
            DB::raw("(SELECT MAX(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as finish_date")
        );
        $query->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id');
        $query->leftJoin('employees', 'employees.id', '=', 'leaves.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id');
        if ($from && $to) {
            $query->whereBetween('leave_logs.date', [$from, $to]);
        }
        if ($name) {
            $query->whereRaw("upper(employees.name) like '%$name%'");
        }
        if ($nik) {
            $query->whereRaw("employees.nid like '%$nik%'");
        }
        $query->groupBy('leaves.id', 'employees.name', 'employees.nid', 'titles.name', 'departments.name', 'leave_settings.leave_name');
        $query->whereIn('leaves.status', [1, 2]);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $leaves = $query->get();

        $data = [];
        foreach ($leaves as $leave) {
            $leave->no      = ++$start;
            $leave->date    = changeDateFormat('d-m-Y', $leave->created_at);
            $data[]         = $leave;
        };
        return response()->json([
            'draw'              => $request->draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsTotal,
            'data'              => $data
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
        $emp = DB::table('employees');
        $emp->select('employees.*');
        $emp->where('status', 1);
        $employees = $emp->get();
        return view('admin.leavereport.index', compact('employees'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $leave = Leave::find($id);
        if ($leave) {
            return view('admin.leavereport.detail', compact('leave'));
        } else {
            abort(404);
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
        //
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
        //
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
           $leave = Leave::find($id);
           $leave->delete();
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

    public function export(Request $request)
    {
        $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $query = DB::table('leaves');
        $query->select(
            'leaves.*',
            'employees.name as employee_name',
            'employees.nid as employee_id',
            'titles.name as title_name',
            'departments.name as department_name',
            'leave_settings.leave_name as leave_type',
            DB::raw("(SELECT MIN(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as start_date"),
            DB::raw("(SELECT MAX(leave_logs.date) FROM leave_logs WHERE leave_logs.leave_id = leaves.id) as finish_date")
        );
        $query->leftJoin('leave_settings', 'leave_settings.id', '=', 'leaves.leave_setting_id');
        $query->leftJoin('employees', 'employees.id', '=', 'leaves.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('leave_logs', 'leave_logs.leave_id', '=', 'leaves.id');
        $query->whereIn('leaves.status', [1, 2]);
        if ($from && $to) {
            $query->whereBetween('leave_logs.date', [$from, $to]);
        }
        $leaves = $query->get();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Department');
        $sheet->setCellValue('C1', 'Position');
        $sheet->setCellValue('D1', 'Name');
        $sheet->setCellValue('E1', 'Leave Name');
        $sheet->setCellValue('F1', 'Duration');
        $sheet->setCellValue('G1', 'From');
        $sheet->setCellValue('H1', 'To');
        $sheet->setCellValue('I1', 'Status');

        $row_number = 2;
        foreach ($leaves as $key => $leave) {
            $sheet->setCellValue('A' . $row_number, ++$key);
            $sheet->setCellValue('B' . $row_number, $leave->department_name);
            $sheet->setCellValue('C' . $row_number, $leave->title_name);
            $sheet->setCellValue('D' . $row_number, $leave->employee_name);
            $sheet->setCellValue('E' . $row_number, $leave->leave_type);
            $sheet->setCellValue('F' . $row_number, $leave->duration);
            $sheet->setCellValue('G' . $row_number, $leave->start_date);
            $sheet->setCellValue('H' . $row_number, $leave->finish_date);
            $sheet->setCellValue('I' . $row_number, $leave->status == 1 ? 'Approved' : 'Rejected');
            $row_number++;
        }
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($leaves->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-cuti-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Leave Data",
                'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
            ], 200);
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
        }
    }
}