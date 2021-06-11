<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\EmployeeDetailAllowance;
use App\Models\Overtime;
use App\Models\Workingtime;
use App\Models\Employee;
use App\Models\WorkingtimeDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_NumberFormat;

class DailyReportController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'dailyreport'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;

        //Count Data
        $query = DB::table('attendances');
        $query->select('attendances.*');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('attendances');
        $query->select('attendances.*');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->offset($start);
        $query->limit($length);
        $logs = $query->get();

        $data = [];
        foreach ($logs as $log) {
            $log->no = ++$start;
            $data[] = $log;
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
        $nid = $request->nid;
        $department = $request->department ? explode(',', $request->department) : null;
        $workgroup = $request->workgroup ? explode(',', $request->workgroup) : null;
        $overtime = $request->overtime;
        $status = $request->status;
        $workingtime = $request->workingtime;
        $checkincheckout = $request->checkincheckout;
        $month = $request->month;
        $year = $request->year;
        $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;

        //Count Data
        $query = DB::table('attendances');
        $query->select('attendances.*', 'employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'departments.name as department_name', 'titles.name as title_name', 'work_groups.name as workgroup_name', 'overtime_scheme.scheme_name as scheme_name');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->leftJoin('overtime_schemes', 'overtime_schemes.id', '=', 'attendances.overtime_scheme_id');
        if ($status) {
            $query->where('attendances.status', $status);
        }
        if ($overtime != '') {
            $query->where('attendances.adj_over_time', $overtime);
        }
        if ($from && $to) {
            $query->whereBetween('attendances.attendance_date', [$from, $to]);
        }
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($checkincheckout == 'checkin') {
            $query->where("attendances.attendance_in", '!=', null)->where("attendances.attendance_out", null);
        }
        if ($checkincheckout == 'checkout') {
            $query->where("attendances.attendance_out", '!=', null)->where("attendances.attendance_in", null);
        }
        if ($checkincheckout == 'checkin_checkout') {
            $query->where("attendances.attendance_in", '!=', null)->where("attendances.attendance_out", '!=', null);
        }
        if ($checkincheckout == '!checkin_checkout') {
            $query->where("attendances.attendance_in", null)->where("attendances.attendance_out", null);
        }
        if ($department) {
            $string = '';
            foreach ($department as $dept) {
                $string .= "departments.path like '%$dept%'";
                if (end($department) != $dept) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($workingtime) {
            $query->whereIn('attendances.workingtime_id', $workingtime);
        }
        
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('attendances');
        $query->select('attendances.*', 'employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_type', 'workingtimes.description as description', 'departments.name as department_name', 'titles.name as title_name', 'work_groups.name as workgroup_name','overtime_schemes.scheme_name as scheme_name');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->leftJoin('overtime_schemes', 'overtime_schemes.id', '=', 'attendances.overtime_scheme_id');
        if ($status) {
            $query->where('attendances.status', $status);
        }
        if ($from && $to) {
            $query->whereBetween('attendances.attendance_date', [$from, $to]);
        }
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($overtime != '') {
            $query->where('attendances.adj_over_time', $overtime);
        }
        if ($department) {
            $string = '';
            foreach ($department as $dept) {
                $string .= "departments.path like '%$dept%'";
                if (end($department) != $dept) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        if ($workingtime) {
            $query->whereIn('attendances.workingtime_id', $workingtime);
        }
        if ($checkincheckout == 'checkin') {
            $query->where("attendances.attendance_in", '!=', null)->where("attendances.attendance_out", null);
        }
        if ($checkincheckout == 'checkout') {
            $query->where("attendances.attendance_out", '!=', null)->where("attendances.attendance_in", null);
        }
        if ($checkincheckout == 'checkin_checkout') {
            $query->where("attendances.attendance_in", '!=', null)->where("attendances.attendance_out", '!=', null);
        }
        if ($checkincheckout == '!checkin_checkout') {
            $query->where("attendances.attendance_in", null)->where("attendances.attendance_out", null);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $attendances = $query->get();

        $data = [];
        foreach ($attendances as $attendance) {
            $workingtime = WorkingtimeDetail::where('workingtime_id', '=', $attendance->workingtime_id)->where('day', '=', $attendance->day)->first();
            $attendance->no = ++$start;
            $attendance->time_in = $attendance->attendance_in;
            $attendance->time_out = $attendance->attendance_out;
            $attendance->date_in = $attendance->attendance_in ? changeDateFormat('Y-m-d', $attendance->attendance_in) : null;
            $attendance->date_out = $attendance->attendance_out ? changeDateFormat('Y-m-d', $attendance->attendance_out) : null;
            $attendance->attendance_in = $attendance->attendance_in ? changeDateFormat('H:i', $attendance->attendance_in) : null;
            $attendance->attendance_out = $attendance->attendance_out ? changeDateFormat('H:i', $attendance->attendance_out) : null;
            $attendance->start_time = $workingtime ? $workingtime->start : null;
            $attendance->finish_time = $workingtime ? $workingtime->finish : null;
            if ($attendance->attendance_in) {
                $attendance->diff_in = (new Carbon(changeDateFormat('H:i', $attendance->time_in)))->diff(new Carbon(changeDateFormat('H:i', $workingtime->start)))->format('%H:%I');
            }
            if ($attendance->attendance_out) {
                if ($workingtime->start > $workingtime->finish) {
                    $attendance->diff_out = (new Carbon($attendance->time_out))->diff(new Carbon($attendance->date_out . ' ' . $workingtime->finish))->format('%H:%I');
                } else {
                    if ($attendance->date_in < $attendance->date_out) {
                        $attendance->diff_out = (new Carbon($attendance->time_out))->diff(new Carbon($attendance->date_in . ' ' . $workingtime->finish))->format('%H:%I');
                    } else {
                        $attendance->diff_out = (new Carbon($attendance->time_out))->diff(new Carbon($attendance->date_out . ' ' . $workingtime->finish))->format('%H:%I');
                    }
                }
            }
            $data[] = $attendance;
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
        // $employees = Employee::all();
        $emp = DB::table('employees');
        $emp->select('employees.*');
        $emp->where('status', 1);
        $employees = $emp->get();
        $workingtimes = Workingtime::all();
        return view('admin.dailyreport.index', compact('employees', 'workingtimes'));
    }

    /**
     * Show the form for detail the specified resource.category
     * 
     */
    public function detail($id)
    {
        $attendances = Attendance::with('workingtime')->with('employee')->find($id);

        if ($attendances) {
            return view('admin.dailyreport.detail', compact('attendances'));
        } else {
            abort(404);
        }
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
        //
    }

    public function export(Request $request)
    {
        $employee = $request->name;
        $department = $request->department ? explode(',', $request->department) : null;
        $workgroup = $request->workgroup ? explode(',', $request->workgroup) : null;
        $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;
        $month = Carbon::parse($request->to)->month;
        $year = Carbon::parse($request->to)->year;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $query = Attendance::select('attendances.attendance_date','attendances.attendance_in','attendances.attendance_out','attendances.adj_working_time','work_groups.name as workgroup_name', 'departments.name as department_name', 'employees.name as employee_name', 'employees.nid as nik','gross_salary',"ot_1","ot_15","ot_20","ot_30","ot_40", "value_1", "value_15","value_20","value_30","value_40","makansiang","makansore","makanmalam","transport", "tunjanganmakan");
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->leftJoin(DB::raw("(select employee_id, period, gross_salary from salary_reports where EXTRACT(MONTH from period) = $month AND EXTRACT(YEAR from period) = $year) salary_reports"),function($join){
            $join->on('attendances.employee_id','=','salary_reports.employee_id');
        });
        $query->leftJoin(DB::raw("(select employee_id,date,
        sum(case when amount = 1 then hour else 0 end) ot_1,
        sum(case when amount = 1.5 then hour else 0 end) ot_15,
        sum(case when amount = 2 then hour else 0 end) ot_20,
        sum(case when amount = 3 then hour else 0 end) ot_30,
        sum(case when amount = 4 then hour else 0 end) ot_40,
        sum(case when amount = 1 then final_salary::numeric else 0 end) value_1,
        sum(case when amount = 1.5 then final_salary::numeric else 0 end) value_15,
        sum(case when amount = 2 then final_salary::numeric else 0 end) value_20,
        sum(case when amount = 3 then final_salary::numeric else 0 end) value_30,
        sum(case when amount = 4 then final_salary::numeric else 0 end) value_40
        from overtimes group by employee_id,date) overtimes"),function($join){
            $join->on('attendances.employee_id','=','overtimes.employee_id');
            $join->on('attendances.attendance_date','=','overtimes.date');
        });
        $query->leftJoin(DB::raw("(select employee_allowances.employee_id, group_allowances.name as desc,employee_allowances.is_penalty as is_penalty, allowances.group_allowance_id as group_allowance_id, employee_allowances.type as type, max(allowances.allowance) as allowance_name,sum(case when lower(allowances.allowance) like '%tunjangan makan%' and employee_allowances.factor > 0 then employee_allowances.value::numeric * employee_allowances.factor else 0 end) tunjanganmakan from employee_allowances
        left join allowances on allowances.id = employee_allowances.allowance_id
        left join group_allowances on group_allowances.id = allowances.group_allowance_id
        group by group_allowances.name,employee_allowances.is_penalty, allowances.group_allowance_id, employee_allowances.type, employee_allowances.employee_id) as employee_allowances"),function($join){
            $join->on('attendances.employee_id','=', 'employee_allowances.employee_id');
        });
        $query->leftJoin(DB::raw("(select employee_id,tanggal_masuk,sum(case when lower(allowances.allowance) like '%makan siang%' then employee_detailallowances.value::numeric else 0 end) makansiang,sum(case when lower(allowances.allowance) like '%makan sore%' then employee_detailallowances.value::numeric else 0 end) makansore,sum(case when lower(allowances.allowance) like '%makan malam%' then employee_detailallowances.value::numeric else 0 end) makanmalam,sum(case when lower(allowances.allowance) like '%transport%' then employee_detailallowances.value::numeric else 0 end) transport from employee_detailallowances
        left join allowances on allowances.id = employee_detailallowances.allowance_id
        group by employee_id,tanggal_masuk) as employee_detailallowances"), function ($join) {
            $join->on('attendances.employee_id', '=', 'employee_detailallowances.employee_id');
            $join->on('attendances.attendance_date', '=', 'employee_detailallowances.tanggal_masuk');
        });
        if ($from && $to) {
            $query->whereBetween('attendances.attendance_date', [$from, $to]);
        }
        if (!$from && $to) {
            $query->where('attendances.attendance_date', '<=', $to);
        }
        if ($department) {
            $string = '';
            foreach ($department as $dept) {
                $string .= "departments.path like '%$dept%'";
                if (end($department) != $dept) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw('(' . $string . ')');
        }
        if ($workgroup) {
            $query->whereIn('employees.workgroup_id', $workgroup);
        }
        $query->where('attendances.status', 1);
        $attendances = $query->get();
        // dd($attendances);

        // Header Columne Excel
        $sheet->setCellValue('A1', 'Wokrgroup Combination');
        $sheet->setCellValue('B1', 'Department');
        $sheet->setCellValue('C1', 'NIK');
        $sheet->setCellValue('D1', 'Nama');
        $sheet->setCellValue('E1', 'Date');
        $sheet->setCellValue('F1', 'First In');
        $sheet->setCellValue('G1', 'Last Out');
        $sheet->setCellValue('H1', 'WT');
        $sheet->setCellValue('I1', 'Gross / Hari');
        $sheet->setCellValue('J1', 'OT 1');
        $sheet->setCellValue('K1', 'Value 1');
        $sheet->setCellValue('L1', 'OT 1,5');
        $sheet->setCellValue('M1', 'Value 1.5');
        $sheet->setCellValue('N1', 'OT 2');
        $sheet->setCellValue('O1', 'Value 2');
        $sheet->setCellValue('P1', 'OT 3');
        $sheet->setCellValue('Q1', 'Value 3');
        $sheet->setCellValue('R1', 'OT 4');
        $sheet->setCellValue('S1', 'Value 4');
        $sheet->setCellValue('T1', 'Makan Siang');
        $sheet->setCellValue('U1', 'Makan Sore');
        $sheet->setCellValue('V1', 'Makan Malam');
        $sheet->setCellValue('W1', 'Tunjangan Transport');
        $sheet->setCellValue('X1', 'Tunjangan Makan');

        $row_number = 2;

        foreach ($attendances as $attendance) {
            // dd($attendance->tunjanganmakan);
            // $ot1 = Overtime::select('date', DB::raw('sum(hour) as hour'), 'employee_id', 'amount')->where('date', $attendance->attendance_date)->where('employee_id', $attendance->employee_id)->where('amount', 1.5)->groupBy('date', 'employee_id', 'amount')->first();
            // $ot2 = Overtime::select('date', DB::raw('sum(hour) as hour'), 'employee_id', 'amount')->where('date', $attendance->attendance_date)->where('employee_id', $attendance->employee_id)->where('amount', 2)->groupBy('date', 'employee_id', 'amount')->first();
            // $ot3 = Overtime::select('date', DB::raw('sum(hour) as hour'), 'employee_id', 'amount')->where('date', $attendance->attendance_date)->where('employee_id', $attendance->employee_id)->where('amount', 3)->groupBy('date', 'employee_id', 'amount')->first();
            // $ot4 = Overtime::select('date', DB::raw('sum(hour) as hour'), 'employee_id', 'amount')->where('date', $attendance->attendance_date)->where('employee_id', $attendance->employee_id)->where('amount', 4)->groupBy('date', 'employee_id', 'amount')->first();
            // $makansiang = EmployeeDetailAllowance::select('employee_detailallowances.value as value')->leftJoin('allowances', 'allowances.id', '=', 'employee_detailallowances.allowance_id')->whereRaw("lower(allowances.allowance) like '%makan siang%'")->where('employee_detailallowances.tanggal_masuk', $attendance->attendance_date)->where('employee_detailallowances.employee_id', $attendance->employee_id)->first();
            // $makansore = EmployeeDetailAllowance::select('employee_detailallowances.value as value')->leftJoin('allowances', 'allowances.id', '=', 'employee_detailallowances.allowance_id')->whereRaw("lower(allowances.allowance) like '%makan sore%'")->where('employee_detailallowances.tanggal_masuk', $attendance->attendance_date)->where('employee_detailallowances.employee_id', $attendance->employee_id)->first();
            // $makanmalam = EmployeeDetailAllowance::select('employee_detailallowances.value as value')->leftJoin('allowances', 'allowances.id', '=', 'employee_detailallowances.allowance_id')->whereRaw("lower(allowances.allowance) like '%makan malam%'")->where('employee_detailallowances.tanggal_masuk', $attendance->attendance_date)->where('employee_detailallowances.employee_id', $attendance->employee_id)->first();
            // $transport = EmployeeDetailAllowance::select('employee_detailallowances.value as value')->leftJoin('allowances', 'allowances.id', '=', 'employee_detailallowances.allowance_id')->whereRaw("lower(allowances.allowance) like '%transport%'")->where('employee_detailallowances.tanggal_masuk', $attendance->attendance_date)->where('employee_detailallowances.employee_id', $attendance->employee_id)->first();

            $sheet->setCellValue('A' . $row_number, $attendance->workgroup_name);
            $sheet->setCellValue('B' . $row_number, $attendance->department_name);
            $sheet->setCellValue('C' . $row_number, $attendance->nik);
            $sheet->setCellValue('D' . $row_number, $attendance->employee_name);
            $sheet->setCellValue('E' . $row_number, changeDateFormat('d-m-Y', $attendance->attendance_date));
            $sheet->setCellValue('F' . $row_number, changeDateFormat('d-m-Y H:i:s', $attendance->attendance_in));
            $sheet->setCellValue('G' . $row_number, changeDateFormat('d-m-Y H:i:s', $attendance->attendance_out));
            $sheet->setCellValue('H' . $row_number, $attendance->adj_working_time);
            $sheet->setCellValue('I' . $row_number, (int)str_replace(",",".", $attendance->gross_salary/30));
            $sheet->setCellValue('J' . $row_number, $attendance->ot_1 ? $attendance->ot_1 : 0);
            $sheet->setCellValue('K' . $row_number, (int)str_replace(",", ".", $attendance->value_1 ? $attendance->value_1 : 0));
            $sheet->setCellValue('L' . $row_number, $attendance->ot_15?$attendance->ot_15:0);
            $sheet->setCellValue('M' . $row_number, (int)str_replace(",",".", $attendance->value_15?$attendance->value_15:0));
            $sheet->setCellValue('N' . $row_number, $attendance->ot_20?$attendance->ot_20:0);
            $sheet->setCellValue('O' . $row_number, (int)str_replace(",",".", $attendance->value_20?$attendance->value_20:0));
            $sheet->setCellValue('P' . $row_number, $attendance->ot_30?$attendance->ot_30:0);
            $sheet->setCellValue('Q' . $row_number, (int)str_replace(",",".", $attendance->value_30?$attendance->value_30:0));
            $sheet->setCellValue('R' . $row_number, $attendance->ot_40?$attendance->ot_40:0);
            $sheet->setCellValue('S' . $row_number, (int)str_replace(",",".", $attendance->value_40?$attendance->value_40:0));
            $sheet->setCellValue('T' . $row_number, $attendance->makansiang?$attendance->makansiang:0);
            $sheet->setCellValue('U' . $row_number, $attendance->makansore?$attendance->makansore:0);
            $sheet->setCellValue('V' . $row_number, $attendance->makanmalam?$attendance->makanmalam:0);
            $sheet->setCellValue('W' . $row_number, $attendance->transport?$attendance->transport:0);
            $sheet->setCellValue('X' . $row_number, $attendance->tunjanganmakan ? $attendance->tunjanganmakan : 0);
            $row_number++;
        }
        foreach (range('A', 'U') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getStyle('I')->getNumberFormat()->setFormatCode('#,###.##');
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($attendances->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-absensi-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Attendance Data",
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