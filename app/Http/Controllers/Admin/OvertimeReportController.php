<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\Title;
use App\Models\WorkGroup;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use PHPExcel;
use PHPExcel_Style;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Fill;
use PHPExcel_Style_NumberFormat;

class OvertimeReportController extends Controller
{
    function __construct() {
        View::share('menu_active', url('admin/'.'overtimereport'));
    }

    /**
     * Get data from overtime to show in overtime report
     *
     * @param Request $request
     */
    public function read(Request $request)
    {
        $start          = $request->start;
        $length         = $request->length;
        $query          = $request->search['value'];
        // $sort           = $request->columns[$request->order[0]['column']]['data'];
        // $dir            = $request->order[0]['dir'];
        $employee_id    = strtoupper(str_replace("'", "''", $request->employee_id));
        $nid            = $request->nid;
        $department_id  = $request->department_id ? explode(',', $request->department_id) : null;
        $workgroup_id   = $request->workgroup_id ? explode(',', $request->workgroup_id) : null;
        $position_id    = $request->position_id ? explode(',', $request->position_id) : null;
        $month          = $request->month;
        $year           = $request->year;
        
        // Count Data
        $query      = Overtime::select(
            'overtimes.day',
            DB::raw('SUM(hour) as total_hour'),
            DB::raw('SUM(final_salary) as total_overtime'),
            'overtimes.date',
            'employees.name as employee_name',
            'employees.nid',
            'work_groups.name as workgroup_name',
            'departments.name as department_name',
            'titles.name as title_name'
        );
        $query->leftJoin('employees', 'overtimes.employee_id', '=', 'employees.id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        if ($month && $year) {
            $query->whereMonth('overtimes.date', $month);
            $query->whereYear('overtimes.date', $year);
        }
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("nid like '%$nid%'");
        }
        if ($department_id) {
            $string = '';
            foreach ($department_id as $department) {
                $string .= "departments.path like '%$department%'";
                if (end($department_id) != $department) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw("($string)");
        }
        if ($workgroup_id) {
            $query->whereIn('employees.workgroup_id', $workgroup_id);
        }
        if ($position_id) {
            $query->whereIn('employees.title_id', $position_id);
        }
        $query->where('final_salary', '!=', 0);
        $query->orderBy('employees.name', 'asc');
        $query->orderBy('overtimes.date', 'asc');
        $query->groupBy('overtimes.date', 'overtimes.day', 'employees.name', 'employees.nid', 'work_groups.name', 'departments.name', 'titles.name');
        $recordsTotal = $query->get()->count();

        $query      = Overtime::select(
            'overtimes.day',
            DB::raw('SUM(hour) as total_hour'),
            DB::raw('SUM(final_salary) as total_overtime'),
            'overtimes.date',
            'employees.name as employee_name',
            'employees.nid',
            'work_groups.name as workgroup_name',
            'departments.name as department_name',
            'titles.name as title_name'
        );
        $query->leftJoin('employees', 'overtimes.employee_id', '=', 'employees.id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin('work_groups', 'work_groups.id', '=', 'employees.workgroup_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        if ($month && $year) {
            $query->whereMonth('overtimes.date', $month);
            $query->whereYear('overtimes.date', $year);
        }
        if ($employee_id) {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid) {
            $query->whereRaw("nid like '%$nid%'");
        }
        if ($department_id) {
            $string = '';
            foreach ($department_id as $department) {
                $string .= "departments.path like '%$department%'";
                if (end($department_id) != $department) {
                    $string .= ' or ';
                }
            }
            $query->whereRaw("($string)");
        }
        if ($workgroup_id) {
            $query->whereIn('employees.workgroup_id', $workgroup_id);
        }
        if ($position_id) {
            $query->whereIn('employees.title_id', $position_id);
        }
        $query->where('final_salary', '!=', 0);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('employees.name', 'asc');
        $query->orderBy('overtimes.date', 'asc');
        $query->groupBy('overtimes.date', 'overtimes.day', 'employees.name', 'employees.nid', 'work_groups.name', 'departments.name', 'titles.name');
        $overtimes = $query->get();

        $data = [];
        foreach ($overtimes as $overtime) {
            $overtime->no   = ++$start;
            $data[]         = $overtime;
        }
        
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
        $employee       = Employee::where('status', 1);
        $employees      = $employee->get();
        $department     = Department::orderBy('path', 'asc');
        $departments    = $department->get();
        $workgroups     = WorkGroup::all();
        $titles         = Title::all();
        return view('admin.overtimereport.index', compact('employees', 'departments', 'workgroups', 'titles'));
    }

    /**
     * Export data to excel
     *
     * @param Request $request
     * @return void
     */
    public function export1(Request $request)
    {
        $month      = $request->month;
        $year       = $request->year;

        $object     = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $styleHeaderData = [
            'font' => [
                'bold' => true
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'wrap'       => true,
            ],
            'fill' => [
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color'=> [
                    'rgb'   => 'B4C6E7',
                ],
            ],
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ]
            ]
        ];

        $styleData = [
            'fill' => [
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color'=> [
                    'rgb'   => 'ffffff',
                ],
            ],
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ]
            ]
        ];

        $styleDataOdd = [
            'fill' => [
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color'=> [
                    'rgb'   => 'D9D9D9',
                ],
            ],
            'borders' => [
                'allborders' => [
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ]
            ]
        ];

        $dates = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $amonth = [];
        $select = '';
        $select .= "employees.nid as nik,employees.name as employee_name, work_groups.name as workgroup_name, departments.name as department_name,";
        for ($i = 1; $i <= $dates; $i++) {
            $amonth[] = $i;
            $date = Carbon::createFromDate($year, $month, $i)->toDateString();
            $select .= "max(case when overtimes.date = '$date' then overtimes.ot_15 else 0 end) as _".$i."_15,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.ot_20 else 0 end) as _".$i."_20,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.ot_30 else 0 end) as _".$i."_30,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.ot_40 else 0 end) as _".$i."_40,";
        }
        $select .= ' null';
        
        // $employee   = Employee::selectRaw($select);
        $employee   = Employee::selectRaw("$select");
        $employee->leftJoin('work_groups', 'employees.workgroup_id', '=', 'work_groups.id');
        $employee->leftJoin('departments', 'employees.department_id', '=', 'departments.id');
        $employee->leftJoin(DB::raw("(select employee_id,date,sum(case when amount = 1.5 then hour else 0 end) ot_15,sum(case when amount = 2 then hour else 0 end) ot_20,sum(case when amount = 3 then hour else 0 end) ot_30,sum(case when amount = 4 then hour else 0 end) ot_40 from overtimes group by  employee_id,date) overtimes"), 'employees.id', '=', 'overtimes.employee_id');
        $employee->where('employees.status', 1);
        $employee->groupBy('employees.nid', 'employees.name', 'work_groups.name', 'departments.name');
        $employee->orderBy('departments.name', 'asc');
        $employees = $employee->get();

        /* Detail Info Cell */
        $sheet->mergeCells('A1:D2');
        $sheet->setCellValue('A1', 'BOSUNG ABSENT GRID')->getStyle('A1')->getFont()->setSize(24);
        $sheet->setCellValue('A3', 'Bulan');
        $sheet->setCellValue('B3', $month);
        $sheet->setCellValue('C3', 'Tahun');
        $sheet->setCellValue('D3', $year);
        /* .Detail Info Cell */

        /* Header Cell */
        $sheet->mergeCells('A6:E6');
        $sheet->setCellValue('A7', 'No');
        $sheet->setCellValue('B7', 'NIK');
        $sheet->setCellValue('C7', 'Nama');
        $sheet->setCellValue('D7', 'Workgroup');
        $sheet->setCellValue('E7', 'Department');

        $columnWT = 5;
        foreach ($amonth as $key => $value) {
            $ColumnDateTemp = $columnWT + 3;
            $columnScheme150 = $columnWT;
            $columnScheme200 = $columnScheme150 + 1;
            $columnScheme300 = $columnScheme200 + 1;
            $columnScheme400 = $columnScheme300 + 1;
            $sheet->setCellValueByColumnAndRow($columnWT, 6, $value);
            $sheet->mergeCellsByColumnAndRow($columnWT, 6, $ColumnDateTemp, 6)->getStyleByColumnAndRow($columnWT, 6, $ColumnDateTemp, 6)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValueByColumnAndRow($columnScheme150, 7, '150%');
            $sheet->setCellValueByColumnAndRow($columnScheme200, 7, '200%');
            $sheet->setCellValueByColumnAndRow($columnScheme300, 7, '300%');
            $sheet->setCellValueByColumnAndRow($columnScheme400, 7, '400%');
            $columnWT = $ColumnDateTemp;
            $columnWT++;
        }

        $row_number = 8;
        $number = 1;

        foreach ($employees as $employee) {
            $sheet->setCellValue('A' . $row_number, $number);
            $sheet->setCellValue('B' . $row_number, $employee->nik);
            $sheet->setCellValue('C' . $row_number, $employee->employee_name);
            $sheet->setCellValue('D' . $row_number, $employee->workgroup_name);
            $sheet->setCellValue('E' . $row_number, $employee->department_name);
            $columnDataWT = 5;
            foreach ($amonth as $key => $value) {
                $columnScheme150 = $columnDataWT;
                $columnScheme200 = $columnScheme150 + 1;
                $columnScheme300 = $columnScheme200 + 1;
                $columnScheme400 = $columnScheme300 + 1;
                $alias15 = "_$value"."_15";
                $alias20 = "_$value"."_20";
                $alias30 = "_$value"."_30";
                $alias40 = "_$value"."_40";
                $sheet->setCellValueByColumnAndRow($columnScheme150, $row_number, $employee->{$alias15} ? $employee->{$alias15} : 0);
                $sheet->setCellValueByColumnAndRow($columnScheme200, $row_number, $employee->{$alias20} ? $employee->{$alias20} : 0);
                $sheet->setCellValueByColumnAndRow($columnScheme300, $row_number, $employee->{$alias30} ? $employee->{$alias30} : 0);
                $sheet->setCellValueByColumnAndRow($columnScheme400, $row_number, $employee->{$alias40} ? $employee->{$alias40} : 0);
                $columnDataWT = $columnDataWT + 4;
            }
            $row_number++;
            $number++;
        }
        /* .Header Cell */

        /* Styling Cell */
        $sheet->getStyleByColumnAndRow(0, 8, $columnWT - 1, $row_number - 1)->applyFromArray($styleData);
        $sheet->getStyleByColumnAndRow(0, 6, $columnWT - 1, 7)->applyFromArray($styleHeaderData);
        for ($i=8; $i < $row_number; $i++) { 
            if ($i % 2 != 0) {
                $sheet->getStyleByColumnAndRow(0, $i, $columnWT - 1, $i)->applyFromArray($styleDataOdd);
            }
        }
        /* .Styling Cell */
        
        foreach (range(0, $columnWT - 1) as $column) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($employees->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-overtime-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Overtime Report",
                'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
            ], 200);
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
        }
    }

    /**
     * Export data to excel
     *
     * @param Request $request
     * @return void
     */
    public function export2(Request $request)
    {
        $month      = $request->month;
        $year       = $request->year;

        $object     = new \PHPExcel();
        $object->getProperties()->setCreator('Bosung Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $dates = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $amonth = [];
        $select = '';
        $select .= "employees.nid as nik,employees.name as employee_name, work_groups.name as workgroup_name, departments.name as department_name,";
        for ($i = 1; $i <= $dates; $i++) {
            $amonth[] = $i;
            $date = Carbon::createFromDate($year, $month, $i)->toDateString();
            $select .= "max(case when overtimes.date = '$date' then overtimes.ot_15 else 0 end) as _".$i."_15,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.ot_20 else 0 end) as _".$i."_20,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.ot_30 else 0 end) as _".$i."_30,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.ot_40 else 0 end) as _".$i."_40,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.otn_15 else 0 end) as _".$i."n_15,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.otn_20 else 0 end) as _".$i."n_20,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.otn_30 else 0 end) as _".$i."n_30,";
            $select .= "max(case when overtimes.date = '$date' then overtimes.otn_40 else 0 end) as _".$i."n_40,";
        }
        $select .= ' null';
        
        // $employee   = Employee::selectRaw($select);
        $employee   = Employee::selectRaw("$select");
        $employee->leftJoin('work_groups', 'employees.workgroup_id', '=', 'work_groups.id');
        $employee->leftJoin('departments', 'employees.department_id', '=', 'departments.id');
        $employee->leftJoin(DB::raw("(select 
        employee_id,
        date,
        sum(case when amount = 1.5 then hour else 0 end) ot_15,
        sum(case when amount = 1.5 then final_salary else 0 end) otn_15,
        sum(case when amount = 2 then hour else 0 end) ot_20,
        sum(case when amount = 2 then final_salary else 0 end) otn_20,
        sum(case when amount = 3 then hour else 0 end) ot_30,
        sum(case when amount = 3 then final_salary else 0 end) otn_30,
        sum(case when amount = 4 then hour else 0 end) ot_40,
        sum(case when amount = 4 then final_salary else 0 end) otn_40
        from overtimes group by  employee_id,date) overtimes"), 'employees.id', '=', 'overtimes.employee_id');
        $employee->where('employees.status', 1);
        // $employee->where('employees.id', 4049);
        $employee->groupBy('employees.nid', 'employees.name', 'work_groups.name', 'departments.name');
        $employees = $employee->get();

        /* Detail Info Cell */
        $sheet->mergeCells('A1:F2');
        $sheet->setCellValue('A1', 'BOSUNG ABSENT GRID')->getStyle('A1')->getFont()->setSize(36);
        $sheet->setCellValue('A3', 'Bulan');
        $sheet->setCellValue('B3', ": $month");
        $sheet->setCellValue('C3', 'Tahun');
        $sheet->setCellValue('D3', ": $year");
        /* .Detail Info Cell */

        /* Header Cell */
        $sheet->mergeCells('A6:A7')->getStyle('A6:A7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('A6', 'No');
        $sheet->mergeCells('B6:B7')->getStyle('B6:B7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('B6', 'NIK');
        $sheet->mergeCells('C6:C7')->getStyle('C6:C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('C6', 'Nama');
        $sheet->mergeCells('D6:D7')->getStyle('D6:D7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('D6', 'Workgroup');
        $sheet->mergeCells('E6:E7')->getStyle('E6:E7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('E6', 'Department');
        $sheet->mergeCells('F6:F7')->getStyle('F6:F7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->setCellValue('F6', 'Overtime');

        $columnWT = 6;
        foreach ($amonth as $key => $value) {
            $ColumnDateTemp = $columnWT + 1;
            $columnHour = $columnWT;
            $columnNominal = $columnHour + 1;
            $sheet->setCellValueByColumnAndRow($columnWT, 6, $value);
            $sheet->mergeCellsByColumnAndRow($columnWT, 6, $ColumnDateTemp, 6)->getStyleByColumnAndRow($columnWT, 6, $ColumnDateTemp, 6)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValueByColumnAndRow($columnHour, 7, 'Hour')->getStyleByColumnAndRow($columnHour, 7, $columnHour, 7)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValueByColumnAndRow($columnNominal, 7, 'Nominal')->getStyleByColumnAndRow($columnNominal, 7, $columnNominal, 7)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $columnWT = $ColumnDateTemp;
            $columnWT++;
        }
        $sheet->setCellValueByColumnAndRow($columnWT, 6, 'Total');
        $sheet->mergeCellsByColumnAndRow($columnWT, 6, $columnWT + 1, 6)->getStyleByColumnAndRow($columnWT, 6, $columnWT + 1, 6)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValueByColumnAndRow($columnWT, 7, 'Total OT')->getStyleByColumnAndRow($columnWT, 7)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValueByColumnAndRow($columnWT + 1, 7, 'Total Rp')->getStyleByColumnAndRow($columnWT + 1, 7)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValueByColumnAndRow($columnWT + 2, 6, 'Grand Total');
        $sheet->mergeCellsByColumnAndRow($columnWT + 2, 6, $columnWT + 3, 6)->getStyleByColumnAndRow($columnWT + 2, 6, $columnWT + 3, 6)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValueByColumnAndRow($columnWT + 2, 7, 'Total OT')->getStyleByColumnAndRow($columnWT + 2, 7)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValueByColumnAndRow($columnWT + 3, 7, 'Total Rp')->getStyleByColumnAndRow($columnWT + 3, 7)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $row_number = 8;
        $number = 1;

        $grandTotalOT = 0;
        $grandTotalNominal = 0;
        foreach ($employees as $employee) {
            $sheet->mergeCellsByColumnAndRow(0, $row_number, 0, $row_number + 3)->getStyleByColumnAndRow(0, $row_number, 0, $row_number + 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('A' . $row_number, $number);
            $sheet->mergeCellsByColumnAndRow(1, $row_number, 1, $row_number + 3)->getStyleByColumnAndRow(1, $row_number, 1, $row_number + 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('B' . $row_number, $employee->nik);
            $sheet->mergeCellsByColumnAndRow(2, $row_number, 2, $row_number + 3)->getStyleByColumnAndRow(2, $row_number, 2, $row_number + 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('C' . $row_number, $employee->employee_name);
            $sheet->mergeCellsByColumnAndRow(3, $row_number, 3, $row_number + 3)->getStyleByColumnAndRow(3, $row_number, 3, $row_number + 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('D' . $row_number, $employee->workgroup_name);
            $sheet->mergeCellsByColumnAndRow(4, $row_number, 4, $row_number + 3)->getStyleByColumnAndRow(4, $row_number, 4, $row_number + 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->setCellValue('E' . $row_number, $employee->department_name);
            $sheet->setCellValueByColumnAndRow(5, $row_number, '150%')->getStyleByColumnAndRow(5, $row_number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValueByColumnAndRow(5, $row_number + 1, '200%')->getStyleByColumnAndRow(5, $row_number + 1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValueByColumnAndRow(5, $row_number + 2, '300%')->getStyleByColumnAndRow(5, $row_number + 2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValueByColumnAndRow(5, $row_number + 3, '400%')->getStyleByColumnAndRow(5, $row_number + 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $columnDataWT = 6;
            $totalOT15 = 0;
            $totalOT20 = 0;
            $totalOT30 = 0;
            $totalOT40 = 0;
            $totalNominalOT15 = 0;
            $totalNominalOT20 = 0;
            $totalNominalOT30 = 0;
            $totalNominalOT40 = 0;
            foreach ($amonth as $key => $value) {
                // $rowDataWT = 8;
                $rowScheme150 = $row_number;
                $rowScheme200 = $rowScheme150 + 1;
                $rowScheme300 = $rowScheme200 + 1;
                $rowScheme400 = $rowScheme300 + 1;
                $alias15 = "_$value"."_15";
                $alias20 = "_$value"."_20";
                $alias30 = "_$value"."_30";
                $alias40 = "_$value"."_40";
                $aliasn15 = "_$value"."n_15";
                $aliasn20 = "_$value"."n_20";
                $aliasn30 = "_$value"."n_30";
                $aliasn40 = "_$value"."n_40";
                $totalOT15 += $employee->{$alias15};
                $totalOT20 += $employee->{$alias20};
                $totalOT30 += $employee->{$alias30};
                $totalOT40 += $employee->{$alias40};
                $totalNominalOT15 += $employee->{$aliasn15};
                $totalNominalOT20 += $employee->{$aliasn20};
                $totalNominalOT30 += $employee->{$aliasn30};
                $totalNominalOT40 += $employee->{$aliasn40};
                $sheet->setCellValueByColumnAndRow($columnDataWT, $rowScheme150, $employee->{$alias15} ? $employee->{$alias15} : 0);
                $sheet->setCellValueByColumnAndRow($columnDataWT, $rowScheme200, $employee->{$alias20} ? $employee->{$alias20} : 0);
                $sheet->setCellValueByColumnAndRow($columnDataWT, $rowScheme300, $employee->{$alias30} ? $employee->{$alias30} : 0);
                $sheet->setCellValueByColumnAndRow($columnDataWT, $rowScheme400, $employee->{$alias40} ? $employee->{$alias40} : 0);
                $sheet->setCellValueByColumnAndRow($columnDataWT + 1, $rowScheme150, $employee->{$aliasn15} ? $employee->{$aliasn15} : 0)->getStyleByColumnAndRow($columnDataWT + 1, $rowScheme150)->getNumberFormat()->setFormatCode("#,##0");
                $sheet->setCellValueByColumnAndRow($columnDataWT + 1, $rowScheme200, $employee->{$aliasn20} ? $employee->{$aliasn20} : 0)->getStyleByColumnAndRow($columnDataWT + 1, $rowScheme200)->getNumberFormat()->setFormatCode("#,##0");
                $sheet->setCellValueByColumnAndRow($columnDataWT + 1, $rowScheme300, $employee->{$aliasn30} ? $employee->{$aliasn30} : 0)->getStyleByColumnAndRow($columnDataWT + 1, $rowScheme300)->getNumberFormat()->setFormatCode("#,##0");
                $sheet->setCellValueByColumnAndRow($columnDataWT + 1, $rowScheme400, $employee->{$aliasn40} ? $employee->{$aliasn40} : 0)->getStyleByColumnAndRow($columnDataWT + 1, $rowScheme400)->getNumberFormat()->setFormatCode("#,##0");
                $columnDataWT = $columnDataWT + 2;
                // $rowDataWT++;
            }
            $grandTotalOT = $totalOT15 + $totalOT20 + $totalOT30 + $totalOT40;
            $grandTotalNominal = $totalNominalOT15 + $totalNominalOT20 + $totalNominalOT30 + $totalNominalOT40;
            $sheet->setCellValueByColumnAndRow($columnDataWT, $row_number, $totalOT15);
            $sheet->setCellValueByColumnAndRow($columnDataWT, $row_number + 1, $totalOT20);
            $sheet->setCellValueByColumnAndRow($columnDataWT, $row_number + 2, $totalOT30);
            $sheet->setCellValueByColumnAndRow($columnDataWT, $row_number + 3, $totalOT40);
            $sheet->setCellValueByColumnAndRow(++$columnDataWT, $row_number, $totalNominalOT15)->getStyleByColumnAndRow($columnDataWT, $row_number)->getNumberFormat()->setFormatCode("#,##0");
            $sheet->setCellValueByColumnAndRow($columnDataWT, $row_number + 1, $totalNominalOT20)->getStyleByColumnAndRow($columnDataWT, $row_number + 1)->getNumberFormat()->setFormatCode("#,##0");
            $sheet->setCellValueByColumnAndRow($columnDataWT, $row_number + 2, $totalNominalOT30)->getStyleByColumnAndRow($columnDataWT, $row_number + 2)->getNumberFormat()->setFormatCode("#,##0");
            $sheet->setCellValueByColumnAndRow($columnDataWT, $row_number + 3, $totalNominalOT40)->getStyleByColumnAndRow($columnDataWT, $row_number + 3)->getNumberFormat()->setFormatCode("#,##0");
            $sheet->setCellValueByColumnAndRow(++$columnDataWT, $row_number, $grandTotalOT);
            $sheet->mergeCellsByColumnAndRow($columnDataWT, $row_number, $columnDataWT, $row_number + 3)->getStyleByColumnAndRow($columnDataWT, $row_number, $columnDataWT, $row_number + 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $sheet->setCellValueByColumnAndRow(++$columnDataWT, $row_number, $grandTotalNominal)->getStyleByColumnAndRow($columnDataWT, $row_number)->getNumberFormat()->setFormatCode("#,##0");
            $sheet->mergeCellsByColumnAndRow($columnDataWT, $row_number, $columnDataWT, $row_number + 3)->getStyleByColumnAndRow($columnDataWT, $row_number, $columnDataWT, $row_number + 3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $totalOT15 = 0;
            $totalOT20 = 0;
            $totalOT30 = 0;
            $totalOT40 = 0;
            $totalNominalOT15 = 0;
            $totalNominalOT20 = 0;
            $totalNominalOT30 = 0;
            $totalNominalOT40 = 0;

            // $sheet->getStyleByColumnAndRow(0, 8, $columnDataWT, $row_number)->applyFromArray($borderStyle);

            // Set Color
            // if ($number % 2 == 0) {
            //     $sheet->getStyleByColumnAndRow(0, $row_number, $columnDataWT, $row_number + 3)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d9d9d9');
            // }
            $row_number = $row_number + 3;
            $row_number++;
            $number++;
        }

        /* .Header Cell */
        $sheet->getColumnDimensionByColumn(0)->setWidth(10);
        $sheet->getColumnDimensionByColumn(1)->setWidth(12);
        $sheet->getColumnDimensionByColumn(2)->setWidth(30);
        $sheet->getColumnDimensionByColumn(3)->setWidth(15);
        $sheet->getColumnDimensionByColumn(4)->setWidth(25);
        $sheet->getColumnDimensionByColumn(5)->setWidth(10);
        for ($i=0; $i < 6; $i++) { 
            $sheet->getStyleByColumnAndRow($i, 6, $i, 7)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('fff2cd');
        }
        foreach (range(6, $columnWT) as $column) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }
        // $sheet->getStyleByColumnAndRow(0, 6, $columnWT, 7)->getFont()->setBold(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($employees->count() > 0) {
            return response()->json([
                'status'     => true,
                'name'        => 'data-overtime-' . date('d-m-Y') . '.xlsx',
                'message'    => "Success Download Overtime Report",
                'file'         => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($export)
            ], 200);
        } else {
            return response()->json([
                'status'     => false,
                'message'    => "Data not found",
            ], 400);
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
}