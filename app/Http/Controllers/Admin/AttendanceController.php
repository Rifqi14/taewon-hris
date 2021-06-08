<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\Spl;
use App\Models\AttendanceMachine;
use App\Models\AttendanceMachine\AttTransaction;
use App\Models\Calendar;
use App\Models\Employee;
use App\Models\Config;
use App\Models\AttendanceCutOff;
use App\Models\Workingtime;
use App\Models\CalendarException;
use App\Models\OvertimeSchemeList;
use App\Models\WorkingtimeDetail;
use App\Models\CalendarShiftSwitch;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AttendanceController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'attendance'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;

        //Count Data
        $query = DB::table('attendance_logs');
        $query->select('attendance_logs.*');
        $query->leftJoin('attendances', 'attendances.id', '=', 'attendance_logs.attendance_id');
        $query->leftJoin('employees', 'employees.id', '=', 'attendance_logs.employee_id');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('attendance_logs');
        $query->select('attendance_logs.*');
        $query->leftJoin('attendances', 'attendances.id', '=', 'attendance_logs.attendance_id');
        $query->leftJoin('employees', 'employees.id', '=', 'attendance_logs.employee_id');
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
        $employee = strtoupper(str_replace("'","''",$request->employee));
        $nik = $request->nik;
        $working_group = $request->working_group;
        $status = $request->status;
        $from = $request->from ? Carbon::parse($request->from)->startOfDay()->toDateTimeString() : null;
        $to = $request->to ? Carbon::parse($request->to)->endOfDay()->toDateTimeString() : null;

        //Count Data
        $query = DB::table('attendance_logs');
        $query->select('attendance_logs.*', 'employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_group', 'workingtimes.description as description');
        $query->leftJoin('attendances', 'attendances.id', '=', 'attendance_logs.attendance_id');
        $query->leftJoin('employees', 'employees.id', '=', 'attendance_logs.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        if ($working_group) {
            // $query->where(function (Builder $q) use ($working_group) {
            //     foreach ($working_group as $key => $value) {
            //         if ($key == 0) {
            //             $q->Where('workingtimes.working_time_type', 'like', "%$value%");
            //         }
            //         $q->orWhere('workingtimes.working_time_type', 'like', "%$value%");
            //     }
            // });
            $query->whereIn('workingtimes.working_time_type', $working_group);
        }
        if ($employee) {
            $query->whereRaw("upper(employees.name) like '%$employee%'");
        }
        if ($nik) {
            $query->where("employees.nid", 'like', "%$nik%");
        }
        if ($status) {
            $query->whereIn('attendance_logs.type', $status);
        }
        if ($from && $to) {
            $query->whereBetween('attendance_logs.attendance_date', [$from, $to]);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('attendance_logs');
        $query->select('attendance_logs.*', 'employees.name as name', 'employees.nid as nid', 'workingtimes.working_time_type as working_group', 'workingtimes.description as description');
        $query->leftJoin('attendances', 'attendances.id', '=', 'attendance_logs.attendance_id');
        $query->leftJoin('employees', 'employees.id', '=', 'attendance_logs.employee_id');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        if ($working_group) {
            // $query->where(function (Builder $q) use ($working_group) {
            //     foreach ($working_group as $key => $value) {
            //         if ($key == 0) {
            //             $q->Where('workingtimes.working_time_type', 'like', "%$value%");
            //         }
            //         $q->orWhere('workingtimes.working_time_type', 'like', "%$value%");
            //     }
            // });
            $query->whereIn('workingtimes.working_time_type', $working_group);
        }
        if ($employee) {
            $query->whereRaw("upper(employees.name) like '%$employee%'");
        }
        if ($nik) {
            $query->where("employees.nid", 'like', "%$nik%");
        }
        if ($status) {
            $query->whereIn('attendance_logs.type', $status);
        }
        if ($from && $to) {
            $query->whereBetween('attendance_logs.attendance_date', [$from, $to]);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $attendances = $query->get();

        $data = [];
        foreach ($attendances as $attendance) {
            $attendance->no = ++$start;
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
        $query = DB::table('employees');
        $query->select('employees.name','employees.nid', 'employees.status');
        $query->where('employees.status', 1);
        $employees = $query->get();
        return view('admin.attendance.index', compact('employees'));
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

    public function import()
    {
        return view('admin.attendance.import');
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

    public function syncPage()
    {
        return view('admin.attendance.sync');
    }

    /**
     * Method to get data from attendance machine
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sync(Request $request)
    {
        $validator  = Validator::make($request->all(), [
            'period'            => 'required',
            'time'              => 'required',
            'attendanceMachine' => 'required',
            'no' => 'required',
        ]);
        $time       = explode(" - ", $request->time);
        $date1      = changeDateFormat('Y-m-d H:i:s', dbDate($request->period) . ' ' . $time[0]);
        $date2      = changeDateFormat('Y-m-d H:i:s', dbDate($request->period) . ' ' . $time[1]);
        $attTransaction     = AttTransaction::whereBetween('att_datetime', [$date1, $date2])->where('device_sn', $request->attendanceMachine)->orderBy('att_datetime', 'asc')->get();

        $data       = [];
        $no = $request->no;
        foreach ($attTransaction as $key => $value) {
            $employee       = Employee::whereRaw("upper(nid) like '%$value->pers_person_pin%'")->first();
            $pointName      = AttendanceMachine::where('device_sn', $request->attendanceMachine)->first();
            $data[]     = array(
                'index'             => $no,
                'employee_id'       => $employee ? $employee->id : null,
                'personel_id'       => $value->pers_person_pin,
                'first_name'        => $value->pers_person_name,
                'last_name'         => $value->pers_person_last_name,
                'department_name'   => $value->auth_dept_name,
                'attendance_area'   => $value->auth_area_name,
                'serial_number'     => $value->device_sn,
                'device_name'       => null,
                'point_name'        => $pointName->point_name,
                'attendance_date'   => $value->att_datetime,
                'date_source'       => $value->mark == 'acc' ? 'Access Control Device' : null
            );
            $no++;
        }

        return response()->json([
            'status'    => true,
            'data'      => $data,
            'last'      => $no,
        ], 200);    
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
            $filetype       = \PHPExcel_IOFactory::identify($file);
            $objReader      = \PHPExcel_IOFactory::createReader($filetype);
            $objPHPExcel    = $objReader->load($file);
        } catch (\Exception $e) {
            die('Error loading file "' . pathinfo($file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        }
        $data     = [];
        $no = 1;
        $sheet = $objPHPExcel->getActiveSheet(0);
        $highestRow = $sheet->getHighestRow();
        for ($row = 3; $row <= $highestRow; $row++) {
            $personel_id     = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $first_name      = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $last_name       = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $department_name = $sheet->getCellByColumnAndRow(3, $row)->getValue();
            $attendance_area = $sheet->getCellByColumnAndRow(4, $row)->getValue();
            $serial_number   = $sheet->getCellByColumnAndRow(5, $row)->getValue();
            $device_name     = $sheet->getCellByColumnAndRow(6, $row)->getValue();
            $point_name      = $sheet->getCellByColumnAndRow(7, $row)->getValue();
            $attendance_date = $sheet->getCellByColumnAndRow(8, $row)->getValue();
            $date_source     = $sheet->getCellByColumnAndRow(9, $row)->getValue();
            $attendancemachine = AttendanceMachine::where('device_sn',$serial_number)->first();
            if ($personel_id && $attendancemachine) {
                $employee = Employee::whereRaw("upper(nid) = '$personel_id'")->get()->first();
                $data[] = array(
                    'index' => $no,
                    'employee_id'     => $employee ? $employee->id : null,
                    'personel_id'     => $personel_id,
                    'first_name'      => $first_name,
                    'last_name'       => $last_name,
                    'department_name' => $department_name,
                    'attendance_area' => $attendance_area,
                    'serial_number'   => $serial_number,
                    'device_name'     => $device_name,
                    'point_name'      => $attendancemachine->point_name,
                    'attendance_date' => $attendance_date,
                    'date_source'     => $date_source
                );
                $no++;
            }
        }
        return response()->json([
            'status'     => true,
            'data'     => $data
        ], 200);
    }

    public function employee_calendar($id)
    {
        $query_calendar = DB::table('employees');
        $query_calendar->select('calendar_exceptions.*');
        $query_calendar->leftJoin('calendars', 'calendars.id', '=', 'employees.calendar_id');
        $query_calendar->leftJoin('calendar_exceptions', 'calendar_exceptions.calendar_id', '=', 'calendars.id');
        $query_calendar->where('employees.id', '=', $id);
        $query_calendar->whereRaw("coalesce(calendar_exceptions.is_switch_day,'') != 'YES'");
        $calendar = $query_calendar->get();
        $exception_date = [];
        foreach ($calendar as $date) {
            $exception_date[] = $date->date_exception;
        }

        return $exception_date;
    }

    public function employee_worktime($id)
    {
        $query = DB::table('attendances');
        $query->select('attendances.*', 'workingtime_details.workingtime_id as workingtime_id', 'workingtime_details.start as start', 'workingtime_details.finish as finish', 'workingtime_details.min_in as min_in', 'workingtime_details.max_out as max_out', 'workingtime_details.workhour as workhour', 'workingtime_details.day as day', 'employees.working_time as working_time');
        $query->leftJoin('workingtimes', 'workingtimes.id', '=', 'attendances.workingtime_id');
        $query->leftJoin('workingtime_details', 'workingtime_details.workingtime_id', '=', 'workingtimes.id');
        $query->leftJoin('employees', 'employees.id', '=', 'attendances.employee_id');
        $query->where('employees.id', '=', $id);
        $worktime = $query->first();

        return $worktime;
    }
    public function attendance_cutoff($id)
    {
        $query = DB::table('attendance_cut_offs');
        $query->select('attendance_cut_offs.*');
        $query->leftJoin('attendance_cut_off_departments', 'attendance_cut_off_departments.attendance_cut_off_id', '=', 'attendance_cut_offs.id');
        $query->where('attendance_cut_offs.status', 1);
        $query->where('attendance_cut_off_departments.department_id', '=', $id);
        $attendance_cutoff = $query->first();
        
        return $attendance_cutoff;
    }

    public function get_workingtime($day, $department_id)
    {
        $query = DB::table('workingtimes');
        $query->select('workingtimes.*', 'workingtime_details.workingtime_id as workingtime_id', 'workingtime_details.start as start', 'workingtime_details.finish as finish', 'workingtime_details.min_in as min_in', 'workingtime_details.max_out as max_out', 'workingtime_details.workhour as workhour', 'workingtime_details.day as day');
        $query->leftJoin('workingtime_details', 'workingtime_details.workingtime_id', '=', 'workingtimes.id');
        $query->leftJoin('department_shifts', 'department_shifts.workingtime_id', '=', 'workingtimes.id');
        $query->where('workingtimes.working_time_type', '!=', 'Non-Shift');
        $query->where('workingtime_details.status', '=', 1);
        $query->where('workingtime_details.day', '=', $day);
        $query->where('department_shifts.department_id', '=', $department_id);

        return $query->get();
    }

    public function get_breaktime($workgroup)
    {
        $query = DB::table('break_times');
        $query->select('break_times.*');
        $query->leftJoin('breaktime_departments', 'breaktime_departments.breaktime_id', '=', 'break_times.id');
        $query->where('breaktime_departments.department_id', '=', $workgroup);

        return $query->get();
    }

    public function get_shift($day)
    {
        $query = DB::table('workingtimes');
        $query->select('workingtimes.*', 'workingtime_details.workingtime_id as workingtime_id', 'workingtime_details.start as start', 'workingtime_details.finish as finish', 'workingtime_details.min_in as min_in', 'workingtime_details.max_out as max_out', 'workingtime_details.workhour as workhour', 'workingtime_details.day as day');
        $query->leftJoin('workingtime_details', 'workingtime_details.workingtime_id', '=', 'workingtimes.id');
        $query->where('workingtimes.working_time_type', '=', 'Non-Shift');
        $query->where('workingtime_details.status', '=', 1);
        $query->where('workingtime_details.day', '=', $day);
       

        return $query->first();
    }

    public function checkWorkingtime($id, $day)
    {
        $query = WorkingtimeDetail::whereNotNull('min_workhour')->where('workingtime_id', $id)->where('day', $day);

        return $query->first();
    }

    public function checkWorkingtimeSwitch($id, $day)
    {
        $query = CalendarShiftSwitch::whereNotNull('min_workhour')->where('workingtime_id', $id)->where('day', $day);

        return $query->first();
    }

    public function overtimeSchemeList($department_id, $day)
    {
        $query = DB::table('overtime_scheme_lists');
        $query->select('overtime_scheme_lists.*');
        $query->leftJoin('overtime_schemes', 'overtime_schemes.id', '=', 'overtime_scheme_lists.overtime_scheme_id');
        $query->leftJoin('overtimescheme_departments', 'overtimescheme_departments.overtime_scheme_id', '=', 'overtime_schemes.id');
        $query->where('overtimescheme_departments.department_id', $department_id);
        $query->where('overtime_scheme_lists.recurrence_day', $day);
        
        return $query->first();
    }

    public function storemass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attendance'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $attendances = json_decode($request->attendance);
        $dates = cal_days_in_month(CAL_GREGORIAN, $request->month, $request->year);
        $amonth = [];
        for ($i = 1; $i <= $dates; $i++) {
            $amonth[] = $i;
        }
        $employees = Employee::where('status', 1)->get();
        $data_attend = [];
        DB::beginTransaction();
        $readConfigs = Config::where('option', 'cut_off')->first();
        foreach ($amonth as $key1 => $value) {
            foreach ($employees as $key => $attendance) {
                $new_date = changeDateFormat('Y-m-d', $request->year . '-' . $request->month . '-' . $value);
                if ($new_date <= date('Y-m-d')) {
                    $check = Attendance::where('employee_id', $attendance->id)->where('attendance_date', '=', $new_date)->first();
                    // Initiate attendance data
                    if (!$check) {
                        $exception_date = $this->employee_calendar($attendance->id);
                        $date = $new_date;
                        $cut_off = $readConfigs->value;
                        if (date('d', strtotime($date)) > $cut_off) {
                            $month = date('m', strtotime($date));
                            $year = date('Y', strtotime($date));
                            $month = date('m', mktime(0, 0, 0, $month + 1, 1, $year));
                            $year = date('Y', mktime(0, 0, 0, $month + 1, 1, $year));
                        } else {
                            $month =  date('m', strtotime($date));
                            $year =  date('Y', strtotime($date));
                        }
                        $createAttendance = Attendance::create([
                            'employee_id'       => $attendance->id,
                            'attendance_date'   => $new_date,
                            'adj_working_time'  => 0,
                            'adj_over_time'     => 0,
                            'day'               => (in_array($date, $exception_date)) ? 'Off' : changeDateFormat('D', $date),
                            'created_at'        => Carbon::now()->toDateTimeString(),
                            'updated_at'        => Carbon::now()->toDateTimeString(),
                            'month'             => $month,
                            'year'              => $year
                        ]);
                        if (!$createAttendance) {
                            return response()->json([
                                'status'     => false,
                                'message'    => $createAttendance
                            ], 400);
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            }
        }
        
        $batch_name = "FILE/" . date('Y-m-d H:i');
        foreach ($attendances as $key => $attendance) {
            if ($attendance->employee_id) {
                $new_date = changeDateFormat('Y-m-d', $attendance->attendance_date);
                $check = Attendance::where('employee_id', '=', $attendance->employee_id)->where('attendance_date', '=', $new_date)->first();
                if ($check) {
                    $createAttendanceLog = Attendancelog::create([
                        'attendance_id'     => $check->id,
                        'employee_id'       => $attendance->employee_id,
                        'serial_number'     => $attendance->serial_number,
                        'device_name'       => $attendance->device_name,
                        'attendance_area'   => $attendance->attendance_area,
                        'type'              => strtoupper($attendance->point_name) == 'MASUK' ? 1 : 0,
                        'attendance_date'   => $attendance->attendance_date,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString(),
                        'batch_upload'      => $batch_name,
                    ]);
                    if (!$createAttendanceLog) {
                        return response()->json([
                            'status'     => false,
                            'message'     => $createAttendanceLog
                        ], 400);
                    }
                } else {
                    $createAttendanceLog = AttendanceLog::create([
                        'attendance_id'     => null,
                        'employee_id'       => $attendance->employee_id,
                        'serial_number'     => $attendance->serial_number,
                        'device_name'       => $attendance->device_name,
                        'attendance_area'   => $attendance->attendance_area,
                        'type'              => strtoupper($attendance->point_name) == 'MASUK' ? 1 : 0,
                        'attendance_date'   => $attendance->attendance_date,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString(),
                        'batch_upload'      => $batch_name,
                    ]);
                    if (!$createAttendanceLog) {
                        return response()->json([
                            'status'     => false,
                            'message'     => $createAttendanceLog
                        ], 400);
                    }
                }
            }
        }
        foreach ($attendances as $key => $updatelog) {
            $new_date = changeDateFormat('Y-m-d', $updatelog->attendance_date);
            $update = Attendance::where('employee_id', $updatelog->employee_id)->where('attendance_date', '=', $new_date)->where('status', '<>', 1)->first();
            if ($update) {
                $employee = Employee::find($updatelog->employee_id);
                $attendance_in = AttendanceLog::where('attendance_id', $update->id)->where('employee_id', $update->employee_id)->where('type', 1)->min('attendance_date');
                if ($attendance_in) {
                    $attendance_out = AttendanceLog::where('attendance_date', '>', $attendance_in)->where('attendance_id', $update->id)->where('employee_id', $update->employee_id)->where('type', 0)->max('attendance_date');
                } else {
                    $attendance_out = AttendanceLog::where('attendance_id', $update->id)->where('employee_id', $update->employee_id)->where('type', 0)->max('attendance_date');
                }
                if ($attendance_in && !$attendance_out) {
                    if (changeDateFormat('H:i', $attendance_in) > changeDateFormat('H:i', '15:00')) {
                        $date_in = changeDateFormat('Y-m-d', $attendance_in);
                        $date_max = Carbon::parse($date_in)->endOfDay()->toDateTimeString();
                        $out_between = AttendanceLog::where('employee_id', $update->employee_id)->whereBetween('attendance_date', [$attendance_in, $date_max])->where('type', 0)->max('attendance_date');
                        if ($out_between) {
                            $attendance_out = $out_between;
                        } else {
                            $date_out = date('Y-m-d', strtotime('+1 day', strtotime($update->attendance_date)));
                            $date_day = changeDateFormat('Y-m-d H:i:s', $date_out . ' 09:00:00');
                            $outs = AttendanceLog::whereBetween('attendance_date', [$attendance_in, $date_day])->where('employee_id', '=', $update->employee_id)->where('type', '=', 0)->get();
                            if ($outs->count() > 0) {
                                $attendance_out = $outs->max('attendance_date');
                                foreach ($outs as $key => $value) {
                                    $value->attendance_id = $update->id;
                                    $value->save();
                                }
                            } else {
                                $attendance_out = null;
                            }
                        }
                    } else {
                        $attendance_out = AttendanceLog::where('attendance_id', $update->id)->where('employee_id', $update->employee_id)->where('type', 0)->max('attendance_date');
                    }
                }
                $update->attendance_in = $attendance_in ? $attendance_in : null;
                $update->attendance_out = $attendance_out && $attendance_out > $attendance_in ? $attendance_out : null;
                $exception_date = $this->employee_calendar($update->employee_id);
                if (!$exception_date) {
                    return response()->json([
                        'status'     => false,
                        'message'     => 'Calendar for this employee name ' . $employee->name . ' not found. Please set employee calendar first.'
                    ], 400);
                }
                $date = $update->attendance_date;
                $update->day = (in_array($date, $exception_date)) ? 'Off' : changeDateFormat('D', $date);
                $overtime_list = OvertimeSchemeList::where('recurrence_day','=', $update->day)->first();
                if (!$overtime_list) {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Overtime schema list for this day ' . $update->day . ' and this date '. $update->attendance_date . ' and this employee id ' . $update->employee_id . ' not found'
                    ], 400);
                }
                $update->overtime_scheme_id = $overtime_list->overtime_scheme_id;
                $update->save();

                $adjustment = Attendance::find($update->id);
                if ($adjustment->attendance_in || $adjustment->attendance_out) {
                    $attendance_in = $adjustment->attendance_in ? $adjustment->attendance_in : null;
                    $attendance_out = $adjustment->attendance_out ? $adjustment->attendance_out : null;
                    // Find closest shift
                    $workingtimes = $this->get_workingtime($adjustment->day, $employee->department_id); //Get master shift sesuai hari
                    if (!$workingtimes) {
                        return response()->json([
                            'status'     => false,
                            'message'     => 'Working Shift for this day ' . $adjustment->day . ' not found. Please check master shift.'
                        ], 400);
                    }
                    $breaktimes = $this->get_breaktime($employee->workgroup_id);
                    if (!$breaktimes) {
                        return response()->json([
                            'status'     => false,
                            'message'     => 'Break time for this employee workgroup ' . $employee->workgroup->name . ' not found. Please check master break.'
                        ], 400);
                    }
                    $attendance_hour = array('attendance_in' => $attendance_in, 'attendance_out' => $attendance_out);
                    $shift = shiftBetween($workingtimes, $attendance_hour);
                    
                    $shift2 = findShift($shift, $attendance_hour);



                    $worktime = $this->employee_worktime($adjustment->employee_id);

                    $adjustment->workingtime_id = $worktime->working_time ? $worktime->working_time : $shift2->id;
                   
                    $getworkingtime = $this->checkWorkingtime($adjustment->workingtime_id, $adjustment->day);
                    

                    // Variable to check working time and over time
                    if (!$getworkingtime) {
                        return response()->json([
                            'status'     => false,
                            'message'     => 'Working shift for employee name ' . $employee->name . ' and attendance date ' . $adjustment->attendance_date . ' and this day ' . $adjustment->day . ' not found. Please check master shift.'
                        ], 400);
                    }
                    //WT && OT
                    if (($getworkingtime->start >= changeDateFormat('H:i:s', $attendance_in)) && (changeDateFormat('H:i:s', $attendance_in) >= $getworkingtime->min_in)) {
                        $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $attendance_in) . ' ' . $getworkingtime->start);
                        $work_time = roundedTime(countWorkingTime($start_shift, $attendance_out));
                    } else {
                        $work_time = roundedTime(countWorkingTime($attendance_in, $attendance_out));
                    }
                    // $breaktime = breaktime($breaktimes, $attendance_hour);
                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);//menghitung total istirahat jam kerja
                    $getbreakovertime = getBreaktimeOvertime($breaktimes, $attendance_hour, $getworkingtime);//menghitung istirahat jam lembur
                    $workhour = $getworkingtime->workhour;
                    $min_workhour = $getworkingtime->min_workhour;
                    //overtime
                    if (changeDateFormat('H:i:s', $attendance_out) < $getworkingtime->finish) {
                        $adj_over_time = 0;
                    } else {
                        $adj_over_time = roundedTime(countOverTime($getworkingtime->finish, changeDateFormat('H:i:s', $attendance_out))) > 10 ? 0 : roundedTime(countOverTime($getworkingtime->finish, changeDateFormat('H:i:s', $attendance_out)));
                    }
                    //end overtime
                    //workingtime
                    if ($work_time >= $workhour) {
                        $adj_working_time = $min_workhour;
                        $nextDay = Carbon::parse($adjustment->attendance_date)->addDays(1)->toDateString();
                        $finishNow = changeDateFormat('Y-m-d H:i:s', $adjustment->attendance_date . ' ' . $getworkingtime->finish);
                        $finishTomorrow = changeDateFormat('Y-m-d H:i:s', $nextDay . ' ' . $getworkingtime->finish);
                        $finishShift = $getworkingtime->finish < $getworkingtime->start ? $finishTomorrow : $finishNow;
                        $diff = Carbon::parse($finishShift)->diffInMinutes(Carbon::parse($attendance_out));
                        if ($diff >= 60 && $finishShift < $attendance_out) {
                            $adj_over_time = $attendance_out < $finishShift ? 0 : roundedTime(countOverTime($finishShift, $attendance_out));
                        }
                    } else {
                        $adj_working_time = $work_time - $adj_over_time;
                    }
                    //end workingtime
                    //End WT && OT
                    // dd($getbreakworkingtime);

                    // Store to column adj_working_time & adj_over_time
                    if ($adjustment->attendance_in) {
                        if ($adjustment->day == 'Off') {
                            //overtime yes 
                            if ($employee->overtime == 'yes') {
                                if ($employee->timeout == 'yes') {

                                    $adjustment->adj_over_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                                    $adjustment->adj_working_time = 0;
                                    $adjustment->code_case  = "A01/BW$getbreakworkingtime/BO$getbreakovertime";
                                } else {
                                    //in out lengkap
                                    if ($adjustment->attendance_in && $adjustment->attendance_out) {
                                        $adjustment->adj_over_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                                        $adjustment->adj_working_time = 0;
                                        $adjustment->code_case = "A02/BW$getbreakworkingtime/BO$getbreakovertime";
                                    } elseif ($adjustment->attendance_in && !$attendance_out) {
                                        //in lengkap out pakai shift keluar
                                        $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString(); //attendance_in + min_workhour
                                        $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                        $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                        $adjustment->attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();
                                        $adjustment->adj_over_time = $getworkingtime->min_workhour + $getbreakworkingtime;
                                        $adjustment->adj_working_time = 0;
                                        $adjustment->code_case = "A03/BW$getbreakworkingtime/BO$getbreakovertime";

                                        $log = AttendanceLog::create([
                                            'attendance_id'     => $adjustment->id,
                                            'employee_id'       => $adjustment->employee_id,
                                            'type'              => 0,
                                            'attendance_date'   => $adjustment->attendance_out,
                                        ]);
                                        if (!$log) {
                                            return response()->json([
                                                'status'    => false,
                                                'message'   => "Error create log from attendance date '$adjustment->attendance_date', employee name '$employee->name' where is timeout no set default to finish"
                                            ], 400);
                                        }
                                    }
                                }
                            } else {
                                //overtime no
                                if ($employee->timeout == 'yes') {
                                    $adjustment->adj_over_time = 0;
                                    $adjustment->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                                    $adjustment->code_case = "A04/BW$getbreakworkingtime/BO$getbreakovertime";
                                } else {
                                    if ($adjustment->attendance_in && $adjustment->attendance_out) {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                                        $adjustment->code_case = "A05/BW$getbreakworkingtime/BO$getbreakovertime";
                                    } elseif ($adjustment->attendance_in && !$attendance_out) {
                                        // if (changeDateFormat('H:i', $adjustment->attendance_in) > changeDateFormat('H:i', '18:00')) {
                                        //     $tomorrow = Carbon::parse($adjustment->attendance_in)->addDay()->toDateString();
                                        //     $time_out = changeDateFormat('Y-m-d H:i:s', $tomorrow . ' ' . $getworkingtime->finish);
                                        // } else {
                                        //     $time_out = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $adjustment->attendance_in) . ' ' . $getworkingtime->finish);
                                        // }
                                        $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString();
                                        $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                        $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                        $adjustment->attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $getworkingtime->min_workhour + $getbreakworkingtime;
                                        $adjustment->code_case = "A06/BW$getbreakworkingtime/BO$getbreakovertime";

                                        $log = AttendanceLog::create([
                                            'attendance_id'     => $adjustment->id,
                                            'employee_id'       => $adjustment->employee_id,
                                            'type'              => 0,
                                            'attendance_date'   => $adjustment->attendance_out,
                                        ]);
                                        if (!$log) {
                                            return response()->json([
                                                'status'    => false,
                                                'message'   => "Error create log from attendance date '$adjustment->attendance_date', employee name '$employee->name' where is timeout no set default to finish"
                                            ], 400);
                                        }
                                    }
                                }
                            }
                        } elseif ($adjustment->day == 'Sat') {
                            if ($employee->overtime == 'yes') {
                                if ($employee->timeout == 'yes') {
                                    $adjustment->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                                    $adjustment->adj_working_time = ($adj_working_time == $min_workhour) ? $adj_working_time : $adj_working_time - $getbreakworkingtime;
                                    $adjustment->code_case = "A07/BW$getbreakworkingtime/BO$getbreakovertime";
                                } else {
                                    if ($adjustment->attendance_in && $adjustment->attendance_out) {
                                        $adjustment->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                                        $adjustment->adj_working_time = $adj_working_time - $getbreakworkingtime;
                                        $adjustment->code_case = "A08/BW$getbreakworkingtime/BO$getbreakovertime";
                                    } elseif ($adjustment->attendance_in && !$attendance_out) {
                                        $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString();
                                        $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                        $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                        $adjustment->attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();
                                        $adjustment->adj_over_time = $getworkingtime->min_workhour + $getbreakworkingtime;
                                        $adjustment->adj_working_time = 0;
                                        $adjustment->code_case = "A09/BW$getbreakworkingtime/BO$getbreakovertime";

                                        $log = AttendanceLog::create([
                                            'attendance_id'     => $adjustment->id,
                                            'employee_id'       => $adjustment->employee_id,
                                            'type'              => 0,
                                            'attendance_date'   => $adjustment->attendance_out,
                                        ]);
                                        if (!$log) {
                                            return response()->json([
                                                'status'    => false,
                                                'message'   => "Error create log from attendance date '$adjustment->attendance_date', employee name '$employee->name' where is timeout no set default to finish"
                                            ], 400);
                                        }
                                    }
                                }
                            } else {
                                if ($employee->timeout == 'yes') {
                                    $adjustment->adj_over_time = 0;
                                    $adjustment->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                                    $adjustment->code_case = "A10/BW$getbreakworkingtime/BO$getbreakovertime";
                                } else {
                                    if ($adjustment->attendance_in && $adjustment->attendance_out) {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                                        $adjustment->code_case = "A11/BW$getbreakworkingtime/BO$getbreakovertime";
                                    } elseif ($adjustment->attendance_in && !$attendance_out) {
                                        $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString();
                                        $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                        $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                        $adjustment->attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $getworkingtime->min_workhour + $getbreakworkingtime;
                                        $adjustment->code_case = "A12/BW$getbreakworkingtime/BO$getbreakovertime";

                                        $log = AttendanceLog::create([
                                            'attendance_id'     => $adjustment->id,
                                            'employee_id'       => $adjustment->employee_id,
                                            'type'              => 0,
                                            'attendance_date'   => $adjustment->attendance_out,
                                        ]);
                                        if (!$log) {
                                            return response()->json([
                                                'status'    => false,
                                                'message'   => "Error create log from attendance date '$adjustment->attendance_date', employee name '$employee->name' where is timeout no set default to finish"
                                            ], 400);
                                        }
                                    }
                                }
                            }
                        } else {
                            if ($employee->overtime == 'yes') {
                                if ($employee->timeout == 'yes') {
                                    $adjustment->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                                    $adjustment->adj_working_time = ($adj_working_time == $min_workhour) ? $adj_working_time : $adj_working_time - $getbreakworkingtime;
                                    $adjustment->code_case = "A13/BW$getbreakworkingtime/BO$getbreakovertime";
                                } else {
                                    if ($adjustment->attendance_in && $adjustment->attendance_out) {
                                        $adjustment->adj_over_time = ($adj_over_time - $getbreakovertime) < 1 ? 0 : $adj_over_time - $getbreakovertime;
                                        $adjustment->adj_working_time = $adj_working_time - $getbreakworkingtime;
                                        $adjustment->code_case = "A14/BW$getbreakworkingtime/BO$getbreakovertime";
                                    } elseif ($adjustment->attendance_in && !$adjustment->attendance_out) {
                                        $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString();
                                        $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                        $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                        $adjustment->attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();
                                        $adjustment->adj_over_time = $getworkingtime->min_workhour + $getbreakworkingtime;
                                        $adjustment->adj_working_time = 0;
                                        $adjustment->code_case = "A15/BW$getbreakworkingtime/BO$getbreakovertime";

                                        $log = AttendanceLog::create([
                                            'attendance_id'     => $adjustment->id,
                                            'employee_id'       => $adjustment->employee_id,
                                            'type'              => 0,
                                            'attendance_date'   => $adjustment->attendance_out,
                                        ]);
                                        if (!$log) {
                                            return response()->json([
                                                'status'    => false,
                                                'message'   => "Error create log from attendance date '$adjustment->attendance_date', employee name '$employee->name' where is timeout no set default to finish"
                                            ], 400);
                                        }
                                    }
                                }
                            } else {
                                if ($employee->timeout == 'yes') {
                                    $adjustment->adj_over_time = 0;
                                    $adjustment->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                                    $adjustment->code_case = "A16/BW$getbreakworkingtime/BO$getbreakovertime";
                                } else {
                                    if ($adjustment->attendance_in && $adjustment->attendance_out) {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $work_time - $getbreakworkingtime - $getbreakovertime;
                                        $adjustment->code_case = "A17/BW$getbreakworkingtime/BO$getbreakovertime";
                                    } elseif ($adjustment->attendance_in && !$attendance_out) {
                                        $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString();
                                        $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                        $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                        $adjustment->attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $getworkingtime->min_workhour + $getbreakworkingtime;
                                        $adjustment->code_case = "A18/BW$getbreakworkingtime/BO$getbreakovertime";

                                        $log = AttendanceLog::create([
                                            'attendance_id'     => $adjustment->id,
                                            'employee_id'       => $adjustment->employee_id,
                                            'type'              => 0,
                                            'attendance_date'   => $adjustment->attendance_out,
                                        ]);
                                        if (!$log) {
                                            return response()->json([
                                                'status'    => false,
                                                'message'   => "Error create log from attendance date '$adjustment->attendance_date', employee name '$employee->name' where is timeout no set default to finish"
                                            ], 400);
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $adjustment->adj_over_time = 0;
                        $adjustment->adj_working_time = 0;
                        $adjustment->code_case = "A19";
                    }
                }
                if (($adjustment->attendance_in && $adjustment->attendance_out) && $adjustment->status == -1) {
                    $adjustment->status = 0;
                    $adjustment->code_case = 'A20';
                }
                $adjustment->save();
                if (!$adjustment) {
                    DB::rollBack();
                    return response()->json([
                        'status'     => false,
                        'message'     => $adjustment
                    ], 400);
                }
            } else {
                continue;
            }
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('attendance.index'),
        ], 200);
    }
    public function new_storemass(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'attendance'    => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        $attendances = json_decode($request->attendance);

        if($request->period){
            $month   = date('m',strtotime(dbDate($request->period)));
            $year    = date('Y', strtotime(dbDate($request->period)));
        }else{
            $month = $request->month;
            $year = $request->year;
        }
        $dates = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $amonth = [];
        for ($i = 1; $i <= $dates; $i++) {
            $amonth[] = $i;
        }
        $employees = Employee::where('status', 1)->get();
        $data_attend = [];
        DB::beginTransaction();
        foreach ($amonth as $key1 => $value) {
            foreach ($employees as $key => $attendance) {
                $new_date = changeDateFormat('Y-m-d', $year . '-' . $month . '-' . $value);
                if ($new_date <= date('Y-m-d')) {
                    $check = Attendance::where('employee_id', $attendance->id)->where('attendance_date', '=', $new_date)->first();
                    // Initiate attendance data
                    if (!$check) {
                        $exception_date = $this->employee_calendar($attendance->id);
                        $date = $new_date;
                        $createAttendance = Attendance::create([
                            'employee_id'       => $attendance->id,
                            'attendance_date'   => $new_date,
                            'adj_working_time'  => 0,
                            'adj_over_time'     => 0,
                            'day'               => (in_array($date, $exception_date)) ? 'Off' : changeDateFormat('D', $date),
                            'created_at'        => Carbon::now()->toDateTimeString(),
                            'updated_at'        => Carbon::now()->toDateTimeString(),
                            'breaktime'         => 0,
                        ]);
                        if (!$createAttendance) {
                            return response()->json([
                                'status'     => false,
                                'message'    => $createAttendance
                            ], 400);
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            }
        }

        $batch_name = "FILE/" . date('Y-m-d H:i');
        foreach ($attendances as $key => $attendance) {
            if ($attendance->employee_id) {
                $new_date = changeDateFormat('Y-m-d', $attendance->attendance_date);
                $check = Attendance::where('employee_id', '=', $attendance->employee_id)->where('attendance_date', '=', $new_date)->first();
                if ($check) {
                    $createAttendanceLog = Attendancelog::create([
                        'attendance_id'     => $check->id,
                        'employee_id'       => $attendance->employee_id,
                        'serial_number'     => $attendance->serial_number,
                        'device_name'       => $attendance->device_name,
                        'attendance_area'   => $attendance->attendance_area,
                        'type'              => strtoupper($attendance->point_name) == 'MASUK' ? 1 : 0,
                        'attendance_date'   => $attendance->attendance_date,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString(),
                        'batch_upload'      => $batch_name,
                    ]);
                    if (!$createAttendanceLog) {
                        return response()->json([
                            'status'     => false,
                            'message'     => $createAttendanceLog
                        ], 400);
                    }
                } else {
                    $createAttendanceLog = AttendanceLog::create([
                        'attendance_id'     => null,
                        'employee_id'       => $attendance->employee_id,
                        'serial_number'     => $attendance->serial_number,
                        'device_name'       => $attendance->device_name,
                        'attendance_area'   => $attendance->attendance_area,
                        'type'              => strtoupper($attendance->point_name) == 'MASUK' ? 1 : 0,
                        'attendance_date'   => $attendance->attendance_date,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString(),
                        'batch_upload'      => $batch_name,
                    ]);
                    if (!$createAttendanceLog) {
                        return response()->json([
                            'status'     => false,
                            'message'     => $createAttendanceLog
                        ], 400);
                    }
                }
            }
        }
        foreach ($attendances as $key => $updatelog) {
            $new_date = changeDateFormat('Y-m-d', $updatelog->attendance_date);
            $update = Attendance::where('employee_id', $updatelog->employee_id)->where('attendance_date', '=', $new_date)->where('status', '<>', 1)->first();
            // log in and log out
            // dd($update);
            if ($update) {
                $employee = Employee::find($updatelog->employee_id);
                $attendance_in = AttendanceLog::where('attendance_id', $update->id)->where('employee_id', $update->employee_id)->where('type', 1)->min('attendance_date');
                // dd($attendance_in);
                if ($attendance_in) {
                    $attendance_out = AttendanceLog::where('attendance_date', '>', $attendance_in)->where('attendance_id', $update->id)->where('employee_id', $update->employee_id)->where('type', 0)->max('attendance_date');
                } else {
                    $attendance_out = AttendanceLog::where('attendance_id', $update->id)->where('employee_id', $update->employee_id)->where('type', 0)->max('attendance_date');
                }
                if ($attendance_in && !$attendance_out) {
                    /* 
                        Cros Date
                    */
                    //if (changeDateFormat('H:i', $attendance_in) > changeDateFormat('H:i', '15:00')) {
                        $date_in = changeDateFormat('Y-m-d', $attendance_in);
                        $date_max = Carbon::parse($date_in)->endOfDay()->toDateTimeString();
                        $out_between = AttendanceLog::where('employee_id', $update->employee_id)->whereBetween('attendance_date', [$attendance_in, $date_max])->where('type', 0)->max('attendance_date');
                        if ($out_between) {
                            $attendance_out = $out_between;
                        } else {
                            $date_out = date('Y-m-d', strtotime('+1 day', strtotime($update->attendance_date)));
                            $date_day = changeDateFormat('Y-m-d H:i:s', $date_out . '09:00:00');
                            // Attendance Cut Off
                            $attendance_cutoff = $this->attendance_cutoff($employee->department_id);
                            if($attendance_cutoff){
                                if ($attendance_cutoff->option == 'Static') {
                                    $date_day = changeDateFormat('Y-m-d H:i:s', $date_out . $attendance_cutoff->hour);
                                }
                                if ($attendance_cutoff->option == 'Flexible') {
                                    $date_day = date('Y-m-d H:i:s', strtotime($attendance_in . '+' . $attendance_cutoff->duration . 'Hours'));
                                }
                            }
                            
                            // End Attendance Cut Off
                            $outs = AttendanceLog::whereBetween('attendance_date', [$attendance_in, $date_day])->where('employee_id', '=', $update->employee_id)->where('type', '=', 0)->get();
                            if ($outs->count() > 0) {
                                $attendance_out = $outs->max('attendance_date');
                                foreach ($outs as $key => $value) {
                                    $value->attendance_id = $update->id;
                                    $value->save();
                                }
                            } else {
                                $attendance_out = null;
                            }
                        }
                    // } else {
                    //     $attendance_out = AttendanceLog::where('attendance_id', $update->id)->where('employee_id', $update->employee_id)->where('type', 0)->max('attendance_date');
                    // }
                }

                //Check Out
                if(!$attendance_in && $attendance_out){
                    /* 
                        Cros Date
                    */
                    //if (changeDateFormat('H:i', $attendance_in) > changeDateFormat('H:i', '15:00')) {
                        $date_out = changeDateFormat('Y-m-d', $attendance_out);
                        $date_start = date('Y-m-d',strtotime($date_out.'-1 days'));
                        $in_between = AttendanceLog::where('employee_id', $update->employee_id)->whereBetween('attendance_date', [$date_start, $date_out])->where('type', 1)->orderBy('attendance_date','asc')->first();
                        if ($in_between) {
                            if($in_between->attendance_id && !$in_between->attendance_out){
                                $attendance_in = $in_between->attendance_date;
                                $update->attendance_in = null;
                                $update->attendance_out = null;
                                $update->workingtime_id = null;
                                $update->overtime_scheme_id = null;
                                $update->save();
                                $outs = AttendanceLog::whereBetween('attendance_date', [$date_start, $date_out])->where('employee_id', '=', $update->employee_id)->where('type', '=', 0)->get();
                                if ($outs->count() > 0) {
                                    foreach ($outs as $key => $value) {
                                        $value->attendance_id = $in_between->attendance_id;
                                        $value->save();
                                    }
                                } 
                                $update = Attendance::find($in_between->attendance_id);
                            }
                        } 
                    // } else {
                    //     $attendance_out = AttendanceLog::where('attendance_id', $update->id)->where('employee_id', $update->employee_id)->where('type', 0)->max('attendance_date');
                    // }
                }
                /*Check Out Different File*/
                $update->attendance_in = $attendance_in ? $attendance_in : null;
                $update->attendance_out = $attendance_out && $attendance_out > $attendance_in ? $attendance_out : null;
                $exception_date = $this->employee_calendar($update->employee_id);
                if (!$exception_date) {
                    return response()->json([
                        'status'     => false,
                        'message'     => 'Calendar for this employee name ' . $employee->name . ' not found. Please set employee calendar first.'
                    ], 400);
                }
                $date = $update->attendance_date;
                $update->day = (in_array($date, $exception_date)) ? 'Off' : changeDateFormat('D', $date);
                // $overtime_list = OvertimeSchemeList::where('recurrence_day', '=', $update->day)->first();
                $overtime_list = $this->overtimeSchemeList($employee->department_id, $update->day);
                // dd($overtime_list);
                if (!$overtime_list) {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Overtime schema list for this day ' . $update->day . ' and this date ' . $update->attendance_date . ' and this employee id ' . $update->employee_id . ' not found'
                    ], 400);
                }
                $update->overtime_scheme_id = $overtime_list->overtime_scheme_id;
                $update->save();
                // dd($update);

                //end log in/ log out

                $adjustment = Attendance::find($update->id);
                if ($adjustment->attendance_in || $adjustment->attendance_out) {
                    $attendance_in = $adjustment->attendance_in ? $adjustment->attendance_in : null;
                    $attendance_out = $adjustment->attendance_out ? $adjustment->attendance_out : null;
                    // Find closest shift
                    $workingtimes = $this->get_workingtime($adjustment->day, $employee->department_id); //Get master shift sesuai hari
                    if (!$workingtimes) {
                        return response()->json([
                            'status'     => false,
                            'message'     => 'Working Shift for this day ' . $adjustment->day . ' not found. Please check master shift.'
                        ], 400);
                    }
                    $breaktimes = $this->get_breaktime($employee->department_id);
                    // dd($breaktimes);
                    if (!$breaktimes) {
                        return response()->json([
                            'status'     => false,
                            'message'     => 'Break time for this employee workgroup ' . $employee->workgroup->name . ' not found. Please check master break.'
                        ], 400);
                    }
                    $attendance_hour = array('attendance_in' => $attendance_in, 'attendance_out' => $attendance_out);
                    $shift = shiftBetween($workingtimes, $attendance_hour);
                    $shift2 = findShift($shift, $attendance_hour);
                    // dd($shift2);

                    $worktime = $this->employee_worktime($adjustment->employee_id);

                    if (!$worktime->working_time) {
                        if (!$shift2) {
                            return response()->json([
                                'status'     => false,
                                'message'     => 'Shift for this employee workgroup ' . $employee->name . ' not found. Please check master break.',
                                'shifts' => $shift,
                                'attendance_hour' => $attendance_hour
                            ], 400);
                        }
                    }

                    $adjustment->workingtime_id = $worktime->working_time ? $worktime->working_time : $shift2->id;
                    
                    // check Switch Workingtime
                    $switchworkintime = CalendarException::where('calendar_id', $employee->calendar_id)->first();
                    if ($switchworkintime->is_switch_day == 'YES') {
                        $getworkingtime = $this->checkWorkingtimeSwitch($adjustment->workingtime_id, $adjustment->day);
                    } else {
                        $getworkingtime = $this->checkWorkingtime($adjustment->workingtime_id, $adjustment->day);
                    }
                    // $getworkingtime = $this->checkWorkingtime($adjustment->workingtime_id, $adjustment->day);
                    // Variable to check working time and over time
                    if (!$getworkingtime) {
                        return response()->json([
                            'status'     => false,
                            'message'     => 'Working shift for employee name ' . $employee->name . ' and attendance date ' . $adjustment->attendance_date . ' and this day ' . $adjustment->day . ' not found. Please check master shift.'
                        ], 400);
                    }
                    
                    if (($getworkingtime->start >= changeDateFormat('H:i:s', $attendance_in)) && (changeDateFormat('H:i:s', $attendance_in) >= $getworkingtime->min_in)) {
                        $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $attendance_in) . ' ' . $getworkingtime->start);
                        $work_time = roundedTime(countWorkingTime($start_shift, $attendance_out));
                    } else {
                        $work_time = roundedTime(countWorkingTime($attendance_in, $attendance_out));
                    }
                    // $breaktime = breaktime($breaktimes, $attendance_hour);

                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime); //menghitung total istirahat jam kerja
                    $getbreakovertime = getBreaktimeOvertime($breaktimes, $attendance_hour, $getworkingtime); //menghitung istirahat jam lembur

                    // dd($getbreakovertime);

                    $workhour = $getworkingtime->workhour;
                    $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $attendance_in) . ' ' . $getworkingtime->start);
                    $cek_minworkhour = roundedTime(countWorkingTime($start_shift, $attendance_out));

                    $min_workhour = $getworkingtime->min_workhour;
                    
                    if($cek_minworkhour >= $min_workhour){
                        $min_workhour = $getworkingtime->min_workhour;
                    }else{
                        $min_workhour = $cek_minworkhour - $getbreakworkingtime;
                    }
                    // if($adjustment->attendance_out)
                    //overtime

                    // WT && OT
                    if (changeDateFormat('H:i:s', $attendance_out) < $getworkingtime->finish) {
                        $adj_over_time = 0;
                    } else {
                        $adj_over_time = roundedTime(countOverTime($getworkingtime->finish, changeDateFormat('H:i:s', $attendance_out))) > 10 ? 0 : roundedTime(countOverTime($getworkingtime->finish, changeDateFormat('H:i:s', $attendance_out)));
                    }
                    //end overtime
                    //workingtime
                    if ($work_time >= $workhour) {
                        $adj_working_time = $min_workhour;
                        $nextDay = Carbon::parse($adjustment->attendance_date)->addDays(1)->toDateString();
                        $finishNow = changeDateFormat('Y-m-d H:i:s', $adjustment->attendance_date . ' ' . $getworkingtime->finish);
                        $finishTomorrow = changeDateFormat('Y-m-d H:i:s', $nextDay . ' ' . $getworkingtime->finish);
                        $finishShift = $getworkingtime->finish < $getworkingtime->start ? $finishTomorrow : $finishNow;
                        $diff = Carbon::parse($finishShift)->diffInMinutes(Carbon::parse($attendance_out));
                        if ($diff >= 60 && $finishShift < $attendance_out) {
                            $adj_over_time = $attendance_out < $finishShift ? 0 : roundedTime(countOverTime($finishShift, $attendance_out));
                        }
                    } else {
                        $adj_working_time = $work_time - $adj_over_time;
                    }
                    // Store to column adj_working_time & adj_over_time
                    if ($adjustment->attendance_in) {
                        // total jam
                        $total_jam = Carbon::parse($adjustment->attendance_out)->diffInHours($adjustment->attendance_in);
                        // total breaktime
                        $break_time = $getbreakworkingtime + $getbreakovertime;
                        // WT
                        $wt_attendance = $total_jam - $break_time;
                        

                        if ($adjustment->day == 'Off') {
                            // Check timeout
                            if($employee->timeout == 'yes'){
                                if($adjustment->attendance_in && $adjustment->attendance_out){
                                    
                                    // cek overtime
                                    if ($employee->overtime == 'yes') {
                                        // cek spl
                                        $ot= $wt_attendance;

                                        if ($employee->spl == 'yes') {
                                            $cekspl = Spl::where('spl_date', $adjustment->attendance_date)->where('employee_id',$employee->id)->first();
                                            // ketika ada spl
                                            if ($cekspl) {

                                                if($cekspl->duration < $ot ){
                                                    $adjustment->adj_over_time = $cekspl->duration;
                                                }else{
                                                    $adjustment->adj_over_time = $ot;
                                                }
                                                $adjustment->adj_working_time = 0;
                                                $adjustment->code_case  = "A01/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            } else {
                                                $adjustment->adj_over_time = 0;
                                                $adjustment->adj_working_time = 0;
                                                $adjustment->code_case  = "A02/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            }
                                        } else {
                                            //  spl not
                                            $adjustment->adj_over_time = $ot;
                                            $adjustment->adj_working_time = 0;
                                            $adjustment->code_case  = "A03/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $adjustment->breaktime = $break_time;
                                        }
                                    } else {
                                        // overtime no
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = 0;
                                        $adjustment->code_case  = "A04/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $adjustment->breaktime = $break_time;  
                                    }
                                } elseif ($adjustment->attendance_in && !$attendance_out) {
                                    $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString();
                                    $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                    $adjustment->attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();

                                    $total_jam = Carbon::parse($adjustment->attendance_out)->diffInHours($adjustment->attendance_in);
                                    $break_time = $getbreakworkingtime + $getbreakovertime;
                                    $wt_attendance = $total_jam - $break_time;

                                    if ($employee->overtime == 'yes') {
                                        $ot = $wt_attendance - $min_workhour;
                                        
                                        if ($employee->spl == 'yes') {
                                            $cekspl = Spl::where('spl_date', $adjustment->attendance_date)->where('employee_id', $employee->id)->first();
                                            // ketika ada spl
                                            if ($cekspl) {

                                                if ($cekspl->duration < $ot) {
                                                    $adjustment->adj_over_time = $cekspl->duration;
                                                } else {
                                                    $adjustment->adj_over_time = $ot;
                                                }
                                                $adjustment->adj_working_time = 0;
                                                $adjustment->code_case  = "A05/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            } else {
                                                $adjustment->adj_over_time = 0;
                                                $adjustment->adj_working_time = 0;
                                                $adjustment->code_case  = "A06/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            }
                                        } else {
                                            //  spl not
                                            $adjustment->adj_over_time = $ot;
                                            $adjustment->adj_working_time = 0;
                                            $adjustment->code_case  = "A07/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $adjustment->breaktime = $break_time;
                                        }
                                    } else {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = 0;
                                        $adjustment->code_case  = "A08/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $adjustment->breaktime = $break_time;
                                    }
                                }
                                
                            }else{
                                if ($adjustment->attendance_in && $adjustment->attendance_out) {

                                    if ($employee->overtime == 'yes') {
                                        // $cekadjustment = Attendance::find($update->id);dd
                                        $ot = $wt_attendance - $min_workhour;

                                        if ($employee->spl == 'yes') {
                                            $cekspl = Spl::where('spl_date', $adjustment->attendance_date)->where('employee_id', $employee->id)->first();
                                            // ketika ada spl dan adjustment
                                            if ($cekspl) {

                                                if ($cekspl->duration < $ot) {
                                                    $adjustment->adj_over_time = $cekspl->duration;
                                                } else {
                                                    $adjustment->adj_over_time = $ot;
                                                }

                                                $adjustment->adj_working_time = 0;
                                                $adjustment->code_case  = "A09/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            } else {
                                                $adjustment->adj_over_time = 0;
                                                $adjustment->adj_working_time = 0;
                                                $adjustment->code_case  = "A10/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            }
                                        } else {
                                            //  spl not
                                            $adjustment->adj_over_time = $ot;
                                            $adjustment->adj_working_time = 0;
                                            $adjustment->code_case  = "A11/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $adjustment->breaktime = $break_time;
                                        }
                                    } else {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = 0;
                                        $adjustment->code_case  = "A12/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $adjustment->breaktime = $break_time;
                                    }
                                } elseif ($adjustment->attendance_in && !$attendance_out) {
                                    $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString();
                                    $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                    $adjustment->attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();

                                    $total_jam = Carbon::parse($adjustment->attendance_out)->diffInHours($adjustment->attendance_in);
                                    $break_time = $getbreakworkingtime + $getbreakovertime;
                                    $wt_attendance = $total_jam - $break_time;

                                    if ($employee->overtime == 'yes') {
                                        $ot = $wt_attendance - $min_workhour;

                                        if ($employee->spl == 'yes') {
                                            $cekspl = Spl::where('spl_date', $adjustment->attendance_date)->where('employee_id', $employee->id)->first();
                                            // ketika ada spl dan adjustment
                                            if ($cekspl) {

                                                if ($cekspl->duration < $ot) {
                                                    $adjustment->adj_over_time = $cekspl->duration;
                                                } else {
                                                    $adjustment->adj_over_time = $ot;
                                                }
                                                $adjustment->adj_working_time = 0;
                                                $adjustment->code_case  = "A13/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            } else {
                                                $adjustment->adj_over_time = 0;
                                                $adjustment->adj_working_time = 0;
                                                $adjustment->code_case  = "A14/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            }
                                        } else {
                                            //  spl not
                                            $adjustment->adj_over_time = $ot;
                                            $adjustment->adj_working_time = 0;
                                            $adjustment->code_case  = "A15/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $adjustment->breaktime = $break_time;
                                        }
                                    } else {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = 0;
                                        $adjustment->code_case  = "A16/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $adjustment->breaktime = $break_time;
                                    }
                                } 
                            }
                        } else {

                            // dd([
                            //     'out', $adjustment->attendance_out, 'in', $adjustment->attendance_in, 'total jam', $total_jam, 'breakWT', $getbreakworkingtime,
                            //     'breakOT', $getbreakovertime, 'breaktime', $break_time, 'wt_attendance', $wt_attendance, 'min_workhour', $min_workhour
                            // ]);
                            
                            if ($employee->timeout == 'yes') {
                                if ($adjustment->attendance_in && $adjustment->attendance_out) {

                                    if ($employee->overtime == 'yes') {
                                        $ot = $wt_attendance - $min_workhour;

                                        // dd($ot);

                                        if ($employee->spl == 'yes') {
                                            $cekspl = Spl::where('spl_date', $adjustment->attendance_date)->where('employee_id', $employee->id)->first();
                                            // ketika ada spl dan adjustment
                                            if ($cekspl) {

                                                if ($cekspl->duration < $ot) {
                                                    $adjustment->adj_over_time = $cekspl->duration;
                                                } else {
                                                    $adjustment->adj_over_time = $ot;
                                                }

                                                $adjustment->adj_working_time = $min_workhour;
                                                $adjustment->code_case  = "A17/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                                
                                            } else {
                                                $adjustment->adj_over_time = 0;
                                                $adjustment->adj_working_time = $min_workhour;
                                                $adjustment->code_case  = "A18/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            }
                                        } else {
                                            //  spl not
                                            $adjustment->adj_over_time = $ot;
                                            $adjustment->adj_working_time = $min_workhour;
                                            $adjustment->code_case  = "A19/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $adjustment->breaktime = $break_time;
                                        }
                                    } else {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $wt_attendance;
                                        $adjustment->code_case  = "A20/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $adjustment->breaktime = $break_time;
                                    }
                                } elseif ($adjustment->attendance_in && !$attendance_out) {
                                    $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString();
                                    $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                    $attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();
                                    $total_jam = Carbon::parse($attendance_out)->diffInHours($adjustment->attendance_in);
                                    $break_time = $getbreakworkingtime + $getbreakovertime;
                                    $wt_attendance = $total_jam - $break_time;

                                    if ($employee->overtime == 'yes') {
                                        $ot = $wt_attendance - $min_workhour;

                                        if ($employee->spl == 'yes') {
                                            $cekspl = Spl::where('spl_date', $adjustment->attendance_date)->where('employee_id', $employee->id)->first();
                                            // ketika ada spl dan adjustment
                                            if ($cekspl) {

                                                if ($cekspl->duration < $ot) {
                                                    $adjustment->adj_over_time = $cekspl->duration;
                                                } else {
                                                    $adjustment->adj_over_time = $ot;
                                                }
                                                $adjustment->adj_working_time = $min_workhour;
                                                $adjustment->code_case  = "A21/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            } else {
                                                $adjustment->adj_over_time = 0;
                                                $adjustment->adj_working_time = $min_workhour;
                                                $adjustment->code_case  = "A22/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            }
                                        } else {
                                            //  spl not
                                            $adjustment->adj_over_time = $ot;
                                            $adjustment->adj_working_time = $min_workhour;
                                            $adjustment->code_case  = "A23/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $adjustment->breaktime = $break_time;
                                        }
                                    } else {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $wt_attendance;
                                        $adjustment->code_case  = "A24/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $adjustment->breaktime = $break_time;
                                    }
                                }
                                // Check Overtime

                            } else {
                                if ($adjustment->attendance_in && $adjustment->attendance_out) {
                                    if ($employee->overtime == 'yes') {

                                        $ot = $wt_attendance - $min_workhour;

                                        if ($employee->spl == 'yes') {
                                            $cekspl = Spl::where('spl_date', $adjustment->attendance_date)->where('employee_id', $employee->id)->first();
                                            // ketika ada spl dan adjustment
                                            if ($cekspl) {

                                                if ($cekspl->duration < $ot) {
                                                    $adjustment->adj_over_time = $cekspl->duration;
                                                } else {
                                                    $adjustment->adj_over_time = $ot;
                                                }

                                                $adjustment->adj_working_time = $min_workhour;
                                                $adjustment->code_case  = "A25/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            } else {
                                                $adjustment->adj_over_time = 0;
                                                $adjustment->adj_working_time = $min_workhour;
                                                $adjustment->code_case  = "A26/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            }
                                        } else {
                                            //  spl not
                                            $adjustment->adj_over_time = $ot;
                                            $adjustment->adj_working_time = $min_workhour;
                                            $adjustment->code_case  = "A27/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $adjustment->breaktime = $break_time;
                                        }
                                    } else {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $wt_attendance;
                                        $adjustment->code_case  = "A28/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $adjustment->breaktime = $break_time;
                                    }
                                } elseif ($adjustment->attendance_in && !$attendance_out) {
                                    $time_out = Carbon::parse($adjustment->attendance_in)->addHours($getworkingtime->min_workhour)->toDateTimeString();
                                    $attendance_hour = array('attendance_in' => $adjustment->attendance_in, 'attendance_out' => $time_out);
                                    $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $getworkingtime);
                                    $adjustment->attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();

                                    $total_jam = Carbon::parse($adjustment->attendance_out)->diffInHours($adjustment->attendance_in);

                                    $break_time = $getbreakworkingtime + $getbreakovertime;
                                    $wt_attendance = $total_jam - $break_time;

                                    if ($employee->overtime == 'yes') {

                                        $ot = $wt_attendance - $min_workhour;

                                        if ($employee->spl == 'yes') {
                                            $cekspl = Spl::where('spl_date', $adjustment->attendance_date)->where('employee_id', $employee->id)->first();
                                            // ketika ada spl dan adjustment
                                            if ($cekspl) {
                                                if ($cekspl->duration < $ot) {
                                                    $adjustment->adj_over_time = $cekspl->duration;
                                                } else {
                                                    $adjustment->adj_over_time = $ot;
                                                }
                                                $adjustment->adj_working_time = $min_workhour;
                                                $adjustment->code_case  = "A29/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            } else {
                                                $adjustment->adj_over_time = 0;
                                                $adjustment->adj_working_time = $min_workhour;
                                                $adjustment->code_case  = "A30/BW$getbreakworkingtime/BO$getbreakovertime";
                                                $adjustment->breaktime = $break_time;
                                            }
                                        } else {
                                            
                                            $adjustment->adj_over_time = $ot;
                                            $adjustment->adj_working_time = $min_workhour;
                                            $adjustment->code_case  = "A31/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $adjustment->breaktime = $break_time;
                                        }
                                    } else {
                                        $adjustment->adj_over_time = 0;
                                        $adjustment->adj_working_time = $wt_attendance;
                                        $adjustment->code_case  = "A32/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $adjustment->breaktime = $break_time;
                                    }

                                }
                            }
                        }
                    }
                }
                if (($adjustment->attendance_in && $adjustment->attendance_out) && $adjustment->status == -1) {
                    $adjustment->status = 0;
                    $adjustment->code_case = 'A33';
                }
                $adjustment->save();
                if (!$adjustment) {
                    DB::rollBack();
                    return response()->json([
                        'status'     => false,
                        'message'     => $adjustment
                    ], 400);
                }
            } else {
                continue;
            }
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('attendance.index'),
        ], 200);
    }

    /**
     * Method to sync mass
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function syncMass(Request $request)
    {
        $validator      = Validator::make($request->all(), [
            'attendance'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $attendances    = json_decode($request->attendance); /* Get request data from preview */

        $period         = Carbon::createFromFormat('Y-m-d'
    );
        $firstMonth     = Carbon::createFromDate($period->year, $period->month, 1);
        $amonth         = CarbonPeriod::create($firstMonth, $period);

        $employees      = Employee::GetActiveEmployees()->get();
        
        DB::beginTransaction();
        $request->employees     = $employees;
        $request->amonth        = $amonth;
        $request->period        = $period;
        $request->batch_name    = "SYNC/" . date('Y-m-d H:i');
        $this->createHeaderAttendance($request);
        $this->createAttendanceLog($request);
        $this->updateHeaderAttendance($request);
        DB::commit();
        return response()->json([
            'status'    => true,
            'message'   => 'Success Syncronize Data',
        ], 200);
    }

    public function updateHeaderAttendance(Request $request)
    {
        $attendances    = json_decode($request->attendance);
        if ($attendances) {
            foreach ($attendances as $key => $update) {
                $checkUpdate    =   $this->checkUpdateStatus($update);
                if ($checkUpdate) {
                    $employee       = Employee::find($update->employee_id);
                    $exception_date     = $this->employee_calendar($employee->id);
                    if (!$exception_date) {
                        return response()->json([
                            'status'     => false,
                            'message'     => 'Calendar for this employee name ' . $employee->name . ' not found. Please set employee calendar first.'
                        ], 400);
                    }

                    $attendanceIn   = AttendanceLog::where('attendance_id', $checkUpdate->id)->where('employee_id', $checkUpdate->employee_id)->where('type', 1)->min('attendance_date');
                    if ($attendanceIn) {
                        $attendanceOut  = AttendanceLog::where('attendance_date', '>', $attendanceIn)->where('attendance_id', $checkUpdate->id)->where('employee_id', $checkUpdate->employee_id)->where('type', 0)->max('attendance_date');
                    } else {
                        $attendanceOut  = AttendanceLog::where('attendance_id', $checkUpdate->id)->where('employee_id', $checkUpdate->employee_id)->where('type', 0)->max('attendance_date');
                    }
                    if ($attendanceIn && !$attendanceOut) {
                        $attendanceOut  = $this->getAttendanceOut($checkUpdate, $attendanceIn, $employee->department_id);
                    }
                    $checkUpdate->attendance_in     = $attendanceIn;
                    $checkUpdate->attendance_out    = $attendanceOut && $attendanceOut > $attendanceIn ? $attendanceOut : null;
                    $checkUpdate->day               = in_array($checkUpdate->attendance_date, $exception_date) ? 'Off' : changeDateFormat('D', $checkUpdate->attendance_date);

                    $overtime_list = $this->overtimeSchemeList($employee->department_id, $checkUpdate->day);
                    if (!$overtime_list) {
                        return response()->json([
                            'status'    => false,
                            'message'   => 'Overtime schema list for this day ' . $checkUpdate->day . ' and this date '. $checkUpdate->attendance_date . ' and this employee id ' . $checkUpdate->employee_id . ' not found'
                        ], 400);
                    }
                    $checkUpdate->overtime_scheme_id= $overtime_list->overtime_scheme_id;
                    $checkUpdate->save();

                    $this->countWorkingtime($checkUpdate, $employee);

                    if (!$checkUpdate) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => "Error update header attendance"
                        ], 400);
                    }
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => "Error update header attendance"
            ], 400);
        }
    }

    public function countWorkingtime($attendance, $employee)
    {
        if ($attendance && $employee) {
            if ($attendance->attendance_in || $attendance->attendance_out) {
                $getWorkingShift    = $this->get_workingtime($attendance->day, $employee->department_id);
                if (!$getWorkingShift) {
                    DB::rollBack();
                    return response()->json([
                        'status'     => false,
                        'message'     => 'Working Shift for this day ' . $attendance->day . ' not found. Please check master shift.'
                    ], 400);
                }
                $breaktimes         = $this->get_breaktime($employee->department_id);
                if (!$breaktimes) {
                    return response()->json([
                        'status'     => false,
                        'message'     => 'Break time for this employee workgroup ' . $employee->workgroup->name . ' not found. Please check master break.'
                    ], 400);
                }

                $shift      = shiftBetween($getWorkingShift, ['attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out]);
                $findShift  = findShift($shift, ['attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out]);

                $employeeWorkingshift = $this->employee_worktime($employee->id);

                $attendance->workingtime_id = $employeeWorkingshift->working_time ? $employeeWorkingshift->working_time : $findShift->id;

                // check Switch Workingtime
                $switchworkintime = CalendarException::where('calendar_id', $employee->calendar_id)->first();
                if ($switchworkintime->is_switch_day == 'YES') {
                    $workingtimeDetail = $this->checkWorkingtimeSwitch($attendance->workingtime_id, $attendance->day);
                } else {
                    $workingtimeDetail = $this->checkWorkingtime($attendance->workingtime_id, $attendance->day);
                }
                if (!$workingtimeDetail) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Working shift for employee name ' . $employee->name . ' and attendance date ' . $attendance->attendance_date . ' and this day ' . $attendance->day . ' not found. Please check master shift.'
                    ], 400);
                }

                if (($workingtimeDetail->start >= changeDateFormat('H:i:s', $attendance->attendance_in)) && (changeDateFormat('H:i:s', $attendance->attendance_in) >= $workingtimeDetail->min_in)) {
                    $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $attendance->attendance_in) . ' ' . $workingtimeDetail->start);
                    $work_time = roundedTime(countWorkingTime($start_shift, $attendance->attendance_out));
                } else {
                    $work_time = roundedTime(countWorkingTime($attendance->attendance_in, $attendance->attendance_out));
                }

                $getbreakworkingtime    = getBreaktimeWorkingtime($breaktimes, ['attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out], $workingtimeDetail);
                $getbreakovertime       = getBreaktimeOvertime($breaktimes, ['attendance_in' => $attendance->attendance_in, 'attendance_out' => $attendance->attendance_out], $workingtimeDetail);
                $workhour               = $workingtimeDetail->workhour;

                $start_shift = changeDateFormat('Y-m-d H:i:s', changeDateFormat('Y-m-d', $attendance->attendance_in) . ' ' . $workingtimeDetail->start);
                $cek_minworkhour = roundedTime(countWorkingTime($start_shift,$attendance->attendance_out));
                $min_workhour = $workingtimeDetail->min_workhour;
                if ($cek_minworkhour >= $min_workhour) {
                    $min_workhour = $workingtimeDetail->min_workhour;
                } else {
                    $min_workhour = $cek_minworkhour - $getbreakworkingtime;
                }

                if (changeDateFormat('H:i:s', $attendance->attendance_out) < $workingtimeDetail->finish) {
                    $adj_over_time      = 0;
                } else {
                    $adj_over_time      = roundedTime(countOverTime($workingtimeDetail->finish, changeDateFormat('H:i:s', $attendance->attendance_out))) > 10 ? 0 : roundedTime(countOverTime($workingtimeDetail->finish, changeDateFormat('H:i:s', $attendance->attendance_out)));
                }
                if ($work_time >= $workhour) {
                    $adj_working_time = $min_workhour;
                    $nextDay = Carbon::parse($attendance->attendance_date)->addDays(1)->toDateString();
                    $finishNow = changeDateFormat('Y-m-d H:i:s', $attendance->attendance_date . ' ' . $workingtimeDetail->finish);
                    $finishTomorrow = changeDateFormat('Y-m-d H:i:s', $nextDay . ' ' . $workingtimeDetail->finish);
                    $finishShift = $workingtimeDetail->finish < $workingtimeDetail->start ? $finishTomorrow : $finishNow;
                    $diff = Carbon::parse($finishShift)->diffInMinutes(Carbon::parse($attendance->attendance_out));
                    if ($diff >= 60 && $finishShift < $attendance->attendance_out) {
                        $adj_over_time = $attendance->attendance_out < $finishShift ? 0 : roundedTime(countOverTime($finishShift, $attendance->attendance_out));
                        }
                } else {
                    $adj_working_time   = $work_time - $adj_over_time;
                }

                if ($attendance->attendance_in) {
                    // total jam
                    $total_jam = Carbon::parse($attendance->attendance_out)->diffInHours($attendance->attendance_in);
                    // total breaktime
                    $break_time = $getbreakworkingtime + $getbreakovertime;
                    // WT
                    $wt_attendance = $total_jam - $break_time;

                    if($attendance->day == 'Off'){
                        // Check timeout
                        if($employee->timeout == 'yes'){
                            if($attendance->attendance_in && $attendance->attendance_out){
                                if($employee->overtime == 'yes'){
                                    $ot = $wt_attendance;
                                    
                                    if($employee->spl == 'yes'){
                                        $cekspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();

                                        if($cekspl){
                                            if($cekspl->duration < $ot){
                                                $attendance->adj_over_time = $cekspl->duration;
                                            }else{
                                                $attendance->adj_over_time = $ot;
                                            }
                                            $attendance->adj_working_time = 0;
                                            $attendance->code_case = "A01/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        }else{
                                            $attendance->adj_over_time = 0;
                                            $attendance->adj_working_time = 0;
                                            $attendance->code_case = " A02/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        }
                                    }else{
                                        // spl no
                                        $attendance->adj_over_time = $ot;
                                        $attendance->adj_working_time = 0;
                                        $attendance->code_case  = "A03/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $attendance->breaktime = $break_time;
                                    }
                                }else{
                                    // overtime no
                                    $attendance->adj_over_time = 0;
                                    $attendance->adj_working_time = 0;
                                    $attendance->code_case = "A04/BW$getbreakworkingtime/BO$getbreakovertime";
                                    $attendance->breaktime = $break_time;
                                }
                            }elseif($attendance->attendance_in && !$attendance->attendance_out){
                                $time_out = Carbon::parse($attendance->attendance_in)->addHours($workingtimeDetail->min_workhour)->toDateTimeString();
                                $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $time_out);
                                $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $workingtimeDetail);
                                $attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();

                                $total_jam = Carbon::parse($attendance_out)->diffInHours($attendance->attendance_in);
                                $break_time = $getbreakworkingtime + $getbreakovertime;
                                $wt_attendance = $total_jam - $break_time;
                                if ($employee->overtime == 'yes') {
                                    $ot = $wt_attendance - $min_workhour;

                                    if ($employee->spl == 'yes') {
                                        $cekspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                                        // ketika ada spl
                                        if ($cekspl) {

                                            if ($cekspl->duration < $ot) {
                                                $attendance->adj_over_time = $cekspl->duration;
                                            } else {
                                                $attendance->adj_over_time = $ot;
                                            }
                                            $attendance->adj_working_time = 0;
                                            $attendance->code_case  = "A05/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        } else {
                                            $attendance->adj_over_time = 0;
                                            $attendance->adj_working_time = 0;
                                            $attendance->code_case  = "A06/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        }
                                    } else {
                                        //  spl not
                                        $attendance->adj_over_time = $ot;
                                        $attendance->adj_working_time = 0;
                                        $attendance->code_case  = "A07/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $attendance->breaktime = $break_time;
                                    }
                                } else {
                                    $attendance->adj_over_time = 0;
                                    $attendance->adj_working_time = 0;
                                    $attendance->code_case  = "A08/BW$getbreakworkingtime/BO$getbreakovertime";
                                    $attendance->breaktime = $break_time;
                                }
                            }
                        }else{
                            if ($attendance->attendance_in && $attendance->attendance_out) {

                                if ($employee->overtime == 'yes') {
                                    // $cekattendance = Attendance::find($update->id);dd
                                    $ot = $wt_attendance - $min_workhour;

                                    if ($employee->spl == 'yes') {
                                        $cekspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                                        // ketika ada spl dan adjustment
                                        if ($cekspl) {

                                            if ($cekspl->duration < $ot) {
                                                $attendance->adj_over_time = $cekspl->duration;
                                            } else {
                                                $attendance->adj_over_time = $ot;
                                            }

                                            $attendance->adj_working_time = 0;
                                            $attendance->code_case  = "A09/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        } else {
                                            $attendance->adj_over_time = 0;
                                            $attendance->adj_working_time = 0;
                                            $attendance->code_case  = "A10/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        }
                                    } else {
                                        //  spl not
                                        $attendance->adj_over_time = $ot;
                                        $attendance->adj_working_time = 0;
                                        $attendance->code_case  = "A11/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $attendance->breaktime = $break_time;
                                    }
                                } else {
                                    $attendance->adj_over_time = 0;
                                    $attendance->adj_working_time = 0;
                                    $attendance->code_case  = "A12/BW$getbreakworkingtime/BO$getbreakovertime";
                                    $attendance->breaktime = $break_time;
                                }
                            } elseif ($attendance->attendance_in && !$attendance->attendance_out) {
                                $time_out = Carbon::parse($attendance->attendance_in)->addHours($workingtimeDetail->min_workhour)->toDateTimeString();
                                $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $time_out);
                                $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $workingtimeDetail);
                                $attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();

                                $total_jam = Carbon::parse($attendance_out)->diffInHours($attendance->attendance_in);
                                $break_time = $getbreakworkingtime + $getbreakovertime;
                                $wt_attendance = $total_jam - $break_time;

                                if ($employee->overtime == 'yes') {
                                    $ot = $wt_attendance - $min_workhour;

                                    if ($employee->spl == 'yes') {
                                        $cekspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                                        // ketika ada spl dan attendance
                                        if ($cekspl) {

                                            if ($cekspl->duration < $ot) {
                                                $attendance->adj_over_time = $cekspl->duration;
                                            } else {
                                                $attendance->adj_over_time = $ot;
                                            }
                                            $attendance->adj_working_time = 0;
                                            $attendance->code_case  = "A13/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        } else {
                                            $attendance->adj_over_time = 0;
                                            $attendance->adj_working_time = 0;
                                            $attendance->code_case  = "A14/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        }
                                    } else {
                                        //  spl not
                                        $attendance->adj_over_time = $ot;
                                        $attendance->adj_working_time = 0;
                                        $attendance->code_case  = "A15/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $attendance->breaktime = $break_time;
                                    }
                                } else {
                                    $attendance->adj_over_time = 0;
                                    $attendance->adj_working_time = 0;
                                    $attendance->code_case  = "A16/BW$getbreakworkingtime/BO$getbreakovertime";
                                    $attendance->breaktime = $break_time;
                                }
                            } 
                        }
                    }else{
                        if ($employee->timeout == 'yes') {
                            if ($attendance->attendance_in && $attendance->attendance_out) {

                                if ($employee->overtime == 'yes') {
                                    $ot = $wt_attendance - $min_workhour;

                                    // dd($ot);

                                    if ($employee->spl == 'yes') {
                                        $cekspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                                        // ketika ada spl dan attendance
                                        if ($cekspl) {

                                            if ($cekspl->duration < $ot) {
                                                $attendance->adj_over_time = $cekspl->duration;
                                            } else {
                                                $attendance->adj_over_time = $ot;
                                            }

                                            $attendance->adj_working_time = $min_workhour;
                                            $attendance->code_case  = "A17/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        } else {
                                            $attendance->adj_over_time = 0;
                                            $attendance->adj_working_time = $min_workhour;
                                            $attendance->code_case  = "A18/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        }
                                    } else {
                                        //  spl not
                                        $attendance->adj_over_time = $ot;
                                        $attendance->adj_working_time = $min_workhour;
                                        $attendance->code_case  = "A19/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $attendance->breaktime = $break_time;
                                    }
                                } else {
                                    $attendance->adj_over_time = 0;
                                    $attendance->adj_working_time = $wt_attendance;
                                    $attendance->code_case  = "A20/BW$getbreakworkingtime/BO$getbreakovertime";
                                    $attendance->breaktime = $break_time;
                                }
                            } elseif ($attendance->attendance_in && !$attendance->attendance_out) {
                                $time_out = Carbon::parse($attendance->attendance_in)->addHours($workingtimeDetail->min_workhour)->toDateTimeString();
                                $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $time_out);
                                $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $workingtimeDetail);
                                $attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();
                                $total_jam = Carbon::parse($attendance_out)->diffInHours($attendance->attendance_in);
                                $break_time = $getbreakworkingtime + $getbreakovertime;
                                $wt_attendance = $total_jam - $break_time;

                                if ($employee->overtime == 'yes') {
                                    $ot = $wt_attendance - $min_workhour;

                                    if ($employee->spl == 'yes') {
                                        $cekspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                                        // ketika ada spl dan attendance
                                        if ($cekspl) {

                                            if ($cekspl->duration < $ot) {
                                                $attendance->adj_over_time = $cekspl->duration;
                                            } else {
                                                $attendance->adj_over_time = $ot;
                                            }
                                            $attendance->adj_working_time = $min_workhour;
                                            $attendance->code_case  = "A21/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        } else {
                                            $attendance->adj_over_time = 0;
                                            $attendance->adj_working_time = $min_workhour;
                                            $attendance->code_case  = "A22/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        }
                                    } else {
                                        //  spl not
                                        $attendance->adj_over_time = $ot;
                                        $attendance->adj_working_time = $min_workhour;
                                        $attendance->code_case  = "A23/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $attendance->breaktime = $break_time;
                                    }
                                } else {
                                    $attendance->adj_over_time = 0;
                                    $attendance->adj_working_time = $wt_attendance;
                                    $attendance->code_case  = "A24/BW$getbreakworkingtime/BO$getbreakovertime";
                                    $attendance->breaktime = $break_time;
                                }
                            }
                            // Check Overtime

                        } else {
                            if ($attendance->attendance_in && $attendance->attendance_out) {
                                if ($employee->overtime == 'yes') {

                                    $ot = $wt_attendance - $min_workhour;

                                    if ($employee->spl == 'yes') {
                                        $cekspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                                        // ketika ada spl dan attendance
                                        if ($cekspl) {

                                            if ($cekspl->duration < $ot) {
                                                $attendance->adj_over_time = $cekspl->duration;
                                            } else {
                                                $attendance->adj_over_time = $ot;
                                            }

                                            $attendance->adj_working_time = $min_workhour;
                                            $attendance->code_case  = "A25/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        } else {
                                            $attendance->adj_over_time = 0;
                                            $attendance->adj_working_time = $min_workhour;
                                            $attendance->code_case  = "A26/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        }
                                    } else {
                                        //  spl not
                                        $attendance->adj_over_time = $ot;
                                        $attendance->adj_working_time = $min_workhour;
                                        $attendance->code_case  = "A27/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $attendance->breaktime = $break_time;
                                    }
                                } else {
                                    $attendance->adj_over_time = 0;
                                    $attendance->adj_working_time = $wt_attendance;
                                    $attendance->code_case  = "A28/BW$getbreakworkingtime/BO$getbreakovertime";
                                    $attendance->breaktime = $break_time;
                                }
                            } elseif ($attendance->attendance_in && !$attendance->attendance_out) {
                                $time_out = Carbon::parse($attendance->attendance_in)->addHours($workingtimeDetail->min_workhour)->toDateTimeString();
                                $attendance_hour = array('attendance_in' => $attendance->attendance_in, 'attendance_out' => $time_out);
                                $getbreakworkingtime = getBreaktimeWorkingtime($breaktimes, $attendance_hour, $workingtimeDetail);
                                $attendance_out = Carbon::parse($time_out)->addHours($getbreakworkingtime)->toDateTimeString();

                                $total_jam = Carbon::parse($attendance_out)->diffInHours($attendance->attendance_in);

                                $break_time = $getbreakworkingtime + $getbreakovertime;
                                $wt_attendance = $total_jam - $break_time;

                                if ($employee->overtime == 'yes') {

                                    $ot = $wt_attendance - $min_workhour;

                                    if ($employee->spl == 'yes') {
                                        $cekspl = Spl::where('spl_date', $attendance->attendance_date)->where('employee_id', $employee->id)->first();
                                        // ketika ada spl dan attendance
                                        if ($cekspl) {
                                            if ($cekspl->duration < $ot) {
                                                $attendance->adj_over_time = $cekspl->duration;
                                            } else {
                                                $attendance->adj_over_time = $ot;
                                            }
                                            $attendance->adj_working_time = $min_workhour;
                                            $attendance->code_case  = "A29/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        } else {
                                            $attendance->adj_over_time = 0;
                                            $attendance->adj_working_time = $min_workhour;
                                            $attendance->code_case  = "A30/BW$getbreakworkingtime/BO$getbreakovertime";
                                            $attendance->breaktime = $break_time;
                                        }
                                    } else {

                                        $attendance->adj_over_time = $ot;
                                        $attendance->adj_working_time = $min_workhour;
                                        $attendance->code_case  = "A31/BW$getbreakworkingtime/BO$getbreakovertime";
                                        $attendance->breaktime = $break_time;
                                    }
                                } else {
                                    $attendance->adj_over_time = 0;
                                    $attendance->adj_working_time = $wt_attendance;
                                    $attendance->code_case  = "A32/BW$getbreakworkingtime/BO$getbreakovertime";
                                    $attendance->breaktime = $break_time;
                                }
                            }
                        }
                    }

                } else {
                    $attendance->adj_over_time = 0;
                    $attendance->adj_working_time = 0;
                    $attendance->code_case = "A33";
                }
            }
            if (($attendance->attendance_in && $attendance->attendance_out) && $attendance->status == -1) {
                $attendance->status = 0;
                $attendance->code_case = 'A34';
            }
            $attendance->save();
            if (!$attendance) {
                DB::rollBack();
                return response()->json([
                    'status'    => false,
                    'message'   => "Error update attendance"
                ], 400);
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => 'Error update attendance'
            ], 400);
        }
    }

    /**
     * Method to get attendance out with param
     *
     * @param object $attendance
     * @param $attendanceIn
     * @return \Illuminate\Http\Response
     */
    public function getAttendanceOut(object $attendance, $attendance_in, $department_id)
    {
        $attendanceIn       = Carbon::parse($attendance_in);
        $endOfDay           = Carbon::parse($attendance_in)->endOfDay();
        $nextDay            = Carbon::parse($attendance_in)->addDays(1);
        $cutOutNextDay      = Carbon::create($nextDay->year, $nextDay->month, $nextDay->day, 9, 0, 0);
        if (Carbon::createFromTime($attendanceIn->hour, $attendanceIn->minute) > Carbon::createFromTime(15, 00)) {
            $outBetween     = AttendanceLog::where('employee_id', $attendance->employee_id)->whereBetween('attendance_date', [$attendanceIn->toDateString(), $endOfDay->toDateString()])->where('type', 0)->max('attendance_date');
            if ($outBetween) {
                $attendanceOut  = $outBetween;
            } else {
                // Attendance Cut Off
                $attendance_cutoff = $this->attendance_cutoff($department_id);
                if ($attendance_cutoff) {
                    if ($attendance_cutoff->option == 'Static') {
                        $cutOutNextDay = changeDateFormat('Y-m-d H:i:s', $nextDay . $attendance_cutoff->hour);
                    }
                    if ($attendance_cutoff->option == 'Flexible') {
                        $cutOutNextDay = date('Y-m-d H:i:s', strtotime($attendance_in . '+' . $attendance_cutoff->duration . 'Hours'));
                    }
                }
                $checkOutNextDay= AttendanceLog::whereBetween('attendance_date', [$attendanceIn->toDateString(), $cutOutNextDay->toDateString()])->where('employee_id', $attendance->employee_id)->where('type', 0)->get();
                if ($checkOutNextDay->count() > 0) {
                    $attendanceOut      = $checkOutNextDay->max('attendance_date');
                    if (!$attendanceOut) {
                        return response()->json([
                            'status'    => false,
                            'message'   => "Error cant get attendance out"
                        ], 400);
                    }
                    foreach ($checkOutNextDay as $key => $out) {
                        $out->attendance_id     = $attendance->id;
                        $out->save();
                    }
                } else {
                    $attendanceOut      = null;
                }
            }
        } else {
            $attendanceOut  = AttendanceLog::where('attendance_id', $attendance->id)->where('employee_id', $attendance->employee_id)->where('type', 0)->max('attendance_date');
        }
        return $attendanceOut;
    }

    /**
     * Method to create attendance log with param from Request
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createAttendanceLog(Request $request)
    {
        $attendances    = json_decode($request->attendance);
        $employees      = $request->employees;
        $period         = $request->period;
        $batch_name     = $request->batch_name;
        if ($attendances && $employees && $period) {
            foreach ($attendances as $key => $attendance) {
                if ($attendance->employee_id) {
                    $checkAttendance = Attendance::EmployeeAttendance($attendance->employee_id)->AttendanceDate($period->toDateString())->first();
                    $createAttendanceLog    = AttendanceLog::create([
                        'attendance_id'         => $checkAttendance ? $checkAttendance->id : null,
                        'employee_id'           => $attendance->employee_id,
                        'serial_number'         => $attendance->serial_number,
                        'device_name'           => $attendance->device_name,
                        'attendance_area'       => $attendance->attendance_area,
                        'type'                  => strtoupper($attendance->point_name) == 'MASUK' ? 1 : 0,
                        'attendance_date'       => $attendance->attendance_date,
                        'batch_upload'          => $batch_name,
                    ]);
                    if (!$createAttendanceLog) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => "Error create attendance log"
                        ], 400);
                    }
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => "Error create attendance log"
            ], 400);
        }
    }

    /**
     * Method to create header attendance with param from Request
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function createHeaderAttendance(Request $request)
    {
        $employees      = $request->employees;
        $amonth         = $request->amonth;
        $period         = $request->period;
        if ($employees && $amonth && $period) {
            foreach ($employees as $key => $employee) {
                $request->join_date = $employee->join_date;
                $checkJoinDate      = $this->checkNewEmployee($request);
                if ($checkJoinDate) {
                    foreach ($amonth as $key => $date) {
                        $checkAttendance = Attendance::EmployeeAttendance($employee->id)->AttendanceDate($date->toDateString())->first();
                        if (!$checkAttendance) {
                            $exception_date     = $this->employee_calendar($employee->id);
                            $createAttendance   = Attendance::create([
                                'employee_id'           => $employee->id,
                                'attendance_date'       => $date->toDateString(),
                                'adj_working_time'      => 0,
                                'adj_over_time'         => 0,
                                'day'                   => (in_array($date->toDateString(), $exception_date)) ? 'Off' : changeDateFormat('D', $date),
                            ]);
                            if (!$createAttendance) {
                                DB::rollBack();
                                return response()->json([
                                    'status'    => false,
                                    'message'   => "Error create header attendance for new employee $employee->name at this date " . $date->toDateString(),
                                ], 400);
                            }
                        }
                    }
                }
                $checkAttendance = Attendance::EmployeeAttendance($employee->id)->AttendanceDate($period->toDateString())->first();
                if (!$checkAttendance) {
                    $exception_date     = $this->employee_calendar($employee->id);
                    $createAttendance   = Attendance::create([
                        'employee_id'           => $employee->id,
                        'attendance_date'       => $period->toDateString(),
                        'adj_working_time'      => 0,
                        'adj_over_time'         => 0,
                        'day'                   => (in_array($period->toDateString(), $exception_date)) ? 'Off' : changeDateFormat('D', $period),
                    ]);
                    if (!$createAttendance) {
                        DB::rollBack();
                        return response()->json([
                            'status'    => false,
                            'message'   => "Error create header attendance for new employee $employee->name at this date " . $period->toDateString(),
                        ], 400);
                    }
                }
            }
        } else {
            DB::rollBack();
            return response()->json([
                'status'    => false,
                'message'   => "Error create header attendance because employee data or date data or periode generate not found, please check input method",
            ], 400);
        }
    }

    /**
     * Method to check update attendance
     *
     * @param $attendance
     * @return \Illuminate\Http\Response
     */
    public function checkUpdateStatus($attendance)
    {
        $date       = Carbon::parse($attendance->attendance_date);
        $check      = Attendance::EmployeeAttendance($attendance->employee_id)->AttendanceDate($date->toDateString())->where('status', '<>', 1)->first();
        if ($check) {
            return $check;
        } else {
            return false;
        }
    }

    /**
     * Method to check the employee is new employee
     *
     * @param Request $request
     * @return bool
     */
    public function checkNewEmployee(Request $request)
    {
        $periodeGenerate    = Carbon::createFromFormat('Y-m-d', dbDate($request->period));
        $employeeJoinDate   = Carbon::parse($request->join_date);
        if (($periodeGenerate->year == $employeeJoinDate->year) && ($periodeGenerate->month == $employeeJoinDate->month)) {
            return true;
        } else {
            return false;
        }
    }

    public function storeheader(Request $request){
        $order = $request->order;
        $limit = $request->limit;
        if($request->period){
            $month   = date('m',strtotime(dbDate($request->period)));
            $year    = date('Y', strtotime(dbDate($request->period)));
        }else{
            $month = $request->month;
            $year = $request->year;
        }
        $dates = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $amonth = [];
        for ($i = 1; $i <= $dates; $i++) {
            $amonth[] = $i;
        }
        $employees = Employee::where('status', 1)->get();
        $employee = Employee::limit($limit)->offset($order)->orderBy('id','asc')->get();
        foreach ($amonth as $key1 => $value) {
            foreach($employee as $row){
                $new_date = changeDateFormat('Y-m-d', $year . '-' . $month . '-' . $value);
                if ($new_date <= date('Y-m-d')) {
                    $check = Attendance::where('employee_id', $row->id)->where('attendance_date', '=', $new_date)->first();
                    // Initiate attendance data
                    if (!$check) {
                        $exception_date = $this->employee_calendar($row->id);
                        $date = $new_date;
                        $createAttendance = Attendance::create([
                            'employee_id'       => $row->id,
                            'attendance_date'   => $new_date,
                            'adj_working_time'  => 0,
                            'adj_over_time'     => 0,
                            'day'               => (in_array($date, $exception_date)) ? 'Off' : changeDateFormat('D', $date),
                            'created_at'        => Carbon::now()->toDateTimeString(),
                            'updated_at'        => Carbon::now()->toDateTimeString(),
                            'breaktime'         => 0,
                        ]);
                        if (!$createAttendance) {
                            return response()->json([
                                'status'     => false,
                                'message'    => $createAttendance
                            ], 400);
                        }
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            }
            
        }
        $order = $order + $employee->count();
        if($order == $employees->count()){
            sleep(3);
        }
        return response()->json([
            'status'    => true,
            'order'     => $order,
            'total'     => $employees->count()
            //'total'     => 10
        ], 200);
    }

    function storelog(Request $request){
        $order = $request->order;
        $total = $request->total;
        $attendances = json_decode($request->attendance);
        $batch_name = "FILE/" . date('Y-m-d H:i');
        foreach($attendances as $attendance){
            if ($attendance->employee_id) {
                $new_date = changeDateFormat('Y-m-d', $attendance->attendance_date);
                $check = Attendance::where('employee_id', '=', $attendance->employee_id)->where('attendance_date', '=', $new_date)->first();
                if ($check) {
                    $createAttendanceLog = Attendancelog::create([
                        'attendance_id'     => $check->id,
                        'employee_id'       => $attendance->employee_id,
                        'serial_number'     => $attendance->serial_number,
                        'device_name'       => $attendance->device_name,
                        'attendance_area'   => $attendance->attendance_area,
                        'type'              => strtoupper($attendance->point_name) == 'MASUK' ? 1 : 0,
                        'attendance_date'   => $attendance->attendance_date,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString(),
                        'batch_upload'      => $batch_name,
                    ]);
                    if (!$createAttendanceLog) {
                        return response()->json([
                            'status'     => false,
                            'message'     => $createAttendanceLog
                        ], 400);
                    }
                } else {
                    $createAttendanceLog = AttendanceLog::create([
                        'attendance_id'     => null,
                        'employee_id'       => $attendance->employee_id,
                        'serial_number'     => $attendance->serial_number,
                        'device_name'       => $attendance->device_name,
                        'attendance_area'   => $attendance->attendance_area,
                        'type'              => strtoupper($attendance->point_name) == 'MASUK' ? 1 : 0,
                        'attendance_date'   => $attendance->attendance_date,
                        'created_at'        => Carbon::now()->toDateTimeString(),
                        'updated_at'        => Carbon::now()->toDateTimeString(),
                        'batch_upload'      => $batch_name,
                    ]);
                    if (!$createAttendanceLog) {
                        return response()->json([
                            'status'     => false,
                            'message'     => $createAttendanceLog
                        ], 400);
                    }
                }
            }
        }
        $order = $order + count($attendances);
        if($order == $total){
            sleep(3);
        }
        return response()->json([
            'status'    => true,
            'order'     => $order,
            'total'     => $total
        ], 200);
    }

    function storeupdatelog(Request $request){
        $order = $request->order;
        $total = $request->total;
        $attendance = json_decode($request->attendance);
        $new_date = changeDateFormat('Y-m-d', $attendance->attendance_date);
        $attendance = Attendance::where('employee_id', $attendance->employee_id)->where('attendance_date', '=', $new_date)->where('status', '<>', 1)->first();
        if ($attendance) {
            $employee = Employee::find($attendance->employee_id);
            $attendance_in = AttendanceLog::where('attendance_id', $attendance->id)->where('employee_id', $attendance->employee_id)->where('type', 1)->min('attendance_date');
            if ($attendance_in) {
                $attendance_out = AttendanceLog::where('attendance_date', '>', $attendance_in)->where('attendance_id', $attendance->id)->where('employee_id', $attendance->employee_id)->where('type', 0)->max('attendance_date');
            } else {
                $attendance_out = AttendanceLog::where('attendance_id', $attendance->id)->where('employee_id', $attendance->employee_id)->where('type', 0)->max('attendance_date');
            }
            /* Attendance Out Not Found*/
            if ($attendance_in && !$attendance_out) {
                $date_in = changeDateFormat('Y-m-d', $attendance_in);
                $date_max = Carbon::parse($date_in)->endOfDay()->toDateTimeString();
                $out_between = AttendanceLog::where('employee_id', $attendance->employee_id)->whereBetween('attendance_date', [$attendance_in, $date_max])->where('type', 0)->max('attendance_date');
                if ($out_between) {
                    $attendance_out = $out_between;
                } else {
                    $date_out = date('Y-m-d', strtotime('+1 day', strtotime($attendance->attendance_date)));
                    $date_day = changeDateFormat('Y-m-d H:i:s', $date_out . '09:00:00');
                    
                    // Attendance Cut Off
                    $attendance_cutoff = $this->attendance_cutoff($employee->department_id);
                    if($attendance_cutoff){
                        if ($attendance_cutoff->option == 'Static') {
                            $date_day = changeDateFormat('Y-m-d H:i:s', $date_out . $attendance_cutoff->hour);
                        }
                        if ($attendance_cutoff->option == 'Flexible') {
                            $date_day = date('Y-m-d H:i:s', strtotime($attendance_in . '+' . $attendance_cutoff->duration . 'Hours'));
                        }
                    }
                    // End Attendance Cut Off

                    $outs = AttendanceLog::whereBetween('attendance_date', [$attendance_in, $date_day])->where('employee_id', '=', $attendance->employee_id)->where('type', '=', 0)->get();
                    if ($outs->count() > 0) {
                        $attendance_out = $outs->max('attendance_date');
                        foreach ($outs as $key => $value) {
                            $value->attendance_id = $attendance->id;
                            $value->save();
                        }
                    } else {
                        $attendance_out = null;
                    }
                }
            }
            /*End If Attendance Out Not Found*/

            /*Attendance In Different File*/
            if(!$attendance_in && $attendance_out){
                
                $date_out = changeDateFormat('Y-m-d', $attendance_out);
                $date_start = date('Y-m-d',strtotime($date_out.'-1 days'));
                $in_between = AttendanceLog::where('employee_id', $attendance->employee_id)->whereBetween('attendance_date', [$date_start, $date_out])->where('type', 1)->orderBy('attendance_date','asc')->first();
                if ($in_between) {
                    if($in_between->attendance_id && !$in_between->attendance_out){
                        $attendance_in = $in_between->attendance_date;
                        $attendance->attendance_in = null;
                        $attendance->attendance_out = null;
                        $attendance->workingtime_id = null;
                        $attendance->overtime_scheme_id = null;
                        $attendance->save();
                        $outs = AttendanceLog::whereBetween('attendance_date', [$date_start, $date_out])->where('employee_id', '=', $attendance->employee_id)->where('type', '=', 0)->get();
                        if ($outs->count() > 0) {
                            foreach ($outs as $key => $value) {
                                $value->attendance_id = $in_between->attendance_id;
                                $value->save();
                            }
                        } 
                        $attendance = Attendance::find($in_between->attendance_id);
                    }
                } 
            }
            /*End If Attendance In Different File*/

            $attendance->attendance_in = $attendance_in ? $attendance_in : null;
            $attendance->attendance_out = $attendance_out && $attendance_out > $attendance_in ? $attendance_out : null;
            
            /*Exception Date*/
            $exception_date = $this->employee_calendar($attendance->employee_id);
            if (!$exception_date) {
                return response()->json([
                    'status'     => false,
                    'message'     => 'Calendar for this employee name ' . $employee->name . ' not found. Please set employee calendar first.'
                ], 400);
            }

            $attendance->day = (in_array($attendance->attendance_date, $exception_date)) ? 'Off' : changeDateFormat('D', $attendance->attendance_date);
            $overtime_list = $this->overtimeSchemeList($employee->department_id, $attendance->day);
            if (!$overtime_list) {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Overtime schema list for this day ' . $attendance->day . ' and this date ' . $attendance->attendance_date . ' and this employee id ' . $attendance->employee_id . ' not found'
                ], 400);
            }
            $attendance->overtime_scheme_id = $overtime_list->overtime_scheme_id;
            $attendance->save();
            /*Exception Date*/

            /*Process Find And Calculate Attendance*/
            $attendance = Attendance::find($attendance->id);
            if ($attendance->attendance_in || $attendance->attendance_out) {
                $attendance_in = $attendance->attendance_in ? $attendance->attendance_in : null;
                $attendance_out = $attendance->attendance_out ? $attendance->attendance_out : null;
                
                /* Get Working Time*/
                $workingtimes = $this->get_workingtime($attendance->day, $employee->department_id);
                if (!$workingtimes) {
                    return response()->json([
                        'status'     => false,
                        'message'     => 'Working Shift for this day ' . $attendance->day . ' not found. Please check master shift.'
                    ], 400);
                }
                $attendance_hour = array('attendance_in' => $attendance_in, 'attendance_out' => $attendance_out);
                $shift = findShift(shiftBetween($workingtimes, $attendance_hour), $attendance_hour);

                $worktime = $this->employee_worktime($attendance->employee_id);
                if (!$worktime->working_time) {
                    if (!$shift) {
                        return response()->json([
                            'status'     => false,
                            'message'     => 'Shift for this employee workgroup ' . $employee->name . ' not found. Please check master break.',
                            'shifts' => shiftBetween($workingtimes, $attendance_hour),
                            'attendance_hour' => $attendance_hour
                        ], 400);
                    }
                }

                $attendance->workingtime_id = $worktime->working_time ? $worktime->working_time : $shift->id;
                /*End Get Working Time*/
            }
            if (($attendance->attendance_in && $attendance->attendance_out) && $attendance->status == -1) {
                $attendance->status = 0;
                $attendance->code_case = 'A33';
            }
            $attendance->save();
            calculateAttendance($attendance);
            /*End Process Find And Calculate Attendance*/

        }
        if($order == $total){
            sleep(3);
        }
        return response()->json([
            'status'    => true,
            'order'     => ++$order,
            'total'     => $total
        ], 200);
    }
}