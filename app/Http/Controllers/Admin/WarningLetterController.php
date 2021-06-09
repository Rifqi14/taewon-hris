<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\WarningLetter;
use App\Models\Employee;
use App\Models\Title;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_Cell_DataType;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Font;

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
        $employee_id = strtoupper(str_replace("'","''",$request->employee_id));
        $get_employee_id = $request->get_employee_id?$request->get_employee_id:null;
        $nid = $request->nid;
        $department_ids = $request->department?$request->department:null;
        $position = $request->position;
        $status = $request->status;

        // Count Data
        $query = DB::table('warning_letters');
        $query->select(
            'warning_letters.*',
            'employees.name as employee_name',
            'employees.title_id as title_id',
            'employees.nid as nid',
            'employees.nik as nik',
            'employees.join_date as join_date',
            'titles.name as title_name',
            'departments.name as department_name',
            'wl.aktif',
            'wl.nonaktif'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'warning_letters.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin(DB::Raw('(select employee_id,sum(case when status = 0 then 1 else 0 end) as aktif, sum(case when status = 1 then 1 else 0 end) as nonaktif from warning_letters group by employee_id) as wl'),'wl.employee_id', '=', 'warning_letters.employee_id');
        if($get_employee_id !=""){
            $query->where('warning_letters.employee_id', '=', $get_employee_id);
        }
        if ($employee_id != '') {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid != '') {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($department_ids) {
        $string = '';
        $uniqdepartments = [];
        foreach($department_ids as $dept){
            if(!in_array($dept,$uniqdepartments)){
                $uniqdepartments[] = $dept;
            }
        }
        $department_ids = $uniqdepartments;
        foreach ($department_ids as $dept) {
            $string .= "departments.path like '%$dept%'";
            if (end($department_ids) != $dept) {
            $string .= ' or ';
            }
        }
        $query->whereRaw('(' . $string . ')');
        }
        if ($position != '') {
            $query->whereIn('employees.title_id', $position);
        }
        if ($status != '') {
            $query->where('warning_letters.status', $status);
        }
        $recordsTotal = $query->get()->count();

        // Select Pagination
        $query = DB::table('warning_letters');
        $query->select(
            'warning_letters.*',
            'employees.name as employee_name',
            'employees.title_id as title_id',
            'employees.nid as nid',
            'employees.nik as nik',
            'employees.join_date as join_date',
            'titles.name as title_name',
            'departments.name as department_name',
            'wl.aktif',
            'wl.nonaktif'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'warning_letters.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin(DB::Raw('(select employee_id,sum(case when status = 0 then 1 else 0 end) as aktif, sum(case when status = 1 then 1 else 0 end) as nonaktif from warning_letters group by employee_id) as wl'),'wl.employee_id', '=', 'warning_letters.employee_id');
        if($get_employee_id !=""){
            $query->where('warning_letters.employee_id', '=', $get_employee_id);
        }
        if ($employee_id != '') {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid != '') {
            $query->whereRaw("employees.nid like '%$nid%'");
        }
        if ($department_ids) {
        $string = '';
        $uniqdepartments = [];
        foreach($department_ids as $dept){
            if(!in_array($dept,$uniqdepartments)){
                $uniqdepartments[] = $dept;
            }
        }
        $department_ids = $uniqdepartments;
        foreach ($department_ids as $dept) {
            $string .= "departments.path like '%$dept%'";
            if (end($department_ids) != $dept) {
            $string .= ' or ';
            }
        }
        $query->whereRaw('(' . $string . ')');
        }
        if ($position != '') {
            $query->whereIn('employees.title_id', $position);
        }
        if ($status != '') {
            $query->where('warning_letters.status', $status);
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
        $employees = Employee::all();
        $query = DB::table('departments');
        $query->select('departments.*');
        $query->orderBy('path','asc');
        $departments = $query->get();
        $titles = Title::all();
        return view('admin.warningletter.index', compact('employees','departments','titles'));
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
        $d         = strtotime("+6 months",strtotime(changeDateFormat('Y-m-d', changeSlash($request->from))));
        $to_date =    date("Y-m-d",$d);

        $validator = Validator::make($request->all(), [
            'employee'      => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $readNumbers = WarningLetter::where([
                            ['employee_id', $request->employee_id],
                            ['status', 0]
                        ])->orderBy('id', 'DESC')->first();
        if($readNumbers){
            if($readNumbers->to < changeDateFormat('Y-m-d', changeSlash($request->from))){
                DB::table('warning_letters')
                ->where('employee_id', $readNumbers->employee_id)
                ->update(['status' => 1]);
            }
        }
        $readNumbers = WarningLetter::where([
                            ['employee_id', $request->employee_id],
                            ['status', 0]
                        ])->orderBy('id', 'DESC')->first();

        if($readNumbers){
            $number_warning_letter =  $readNumbers->number_warning_letter + 1;
        }else{
            $number_warning_letter =  isset($readNumbers->number_warning_letter) + 1;
        }
        
        DB::beginTransaction();
        if($number_warning_letter > 3){
            return response()->json([
                'status' => true,
                'message' => 'Warning Letter Lebih dari 3 kali'
            ], 400);
        }else{
            $warningletter = WarningLetter::create([
                // 'id'                    => $id,
                'employee_id'           => $request->employee_id,
                'status'                => 0,
                'number_warning_letter' => $number_warning_letter,
                'notes'                 => $request->reason,
                'from'                  => changeDateFormat('Y-m-d', changeSlash($request->from)),
                'to'                    => $to_date,
            ]);
            if($warningletter->to < date("Y-m-d'")){
                $updateStatus = WarningLetter::where('id', $warningletter->id)->first();
                $updateStatus->status  = 1;
                $updateStatus->save(); 
            }
            $status = WarningLetter::select(DB::raw('sum(case when status = 0 then 1 else 0 end) as aktif, sum(case when status = 1 then 1 else 0 end) as nonaktif'))
                                    ->where('employee_id',$request->employee_id)
                                    ->first();
            $warningletter->sp_active  = $status->aktif;
            $warningletter->sp_non_active  = $status->nonaktif;
            $warningletter->save(); 
            if (!$warningletter) {
                return response()->json([
                    'status' => false,
                    'message' 	=> $warningletter
                ], 400);
            }
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
        $d         = strtotime("+6 months",strtotime(changeDateFormat('Y-m-d', changeSlash($request->from))));
        $to_date =    date("Y-m-d",$d);

        $validator = Validator::make($request->all(), [
            'employee'      => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $readNumbers = WarningLetter::where([
                            ['employee_id', $request->employee_id],
                            ['status', 0]
                        ])->orderBy('id', 'DESC')->first();

        if($readNumbers){
            if($readNumbers->to < changeDateFormat('Y-m-d', changeSlash($request->from)) && $readNumbers->to > date('Y-m-d')){
                DB::table('warning_letters')
                ->where('employee_id', $readNumbers->employee_id)
                ->update(['status' => 1]);
            }
        }
        $readNumbers = WarningLetter::where([
                            ['employee_id', $request->employee_id],
                            ['status', 0]
                        ])->orderBy('id', 'DESC')->first();

        if($readNumbers){
            $number_warning_letter =  $readNumbers->number_warning_letter + 1;
        }else{
            $number_warning_letter =  isset($readNumbers->number_warning_letter) + 1;
        }

        DB::beginTransaction();
        $warningletter = WarningLetter::find($id);
        $warningletter->employee_id = $request->employee_id;
        $warningletter->status = 0;
        $warningletter->notes = $request->reason;
        $warningletter->number_warning_letter = $request->number_warning_letter;
        $warningletter->from = changeDateFormat('Y-m-d', changeSlash($request->from));
        $warningletter->to = $to_date;
        $warningletter->save();
        if($warningletter->to < date("Y-m-d'")){
                $updateStatus = WarningLetter::where('id', $warningletter->id)->first();
                $updateStatus->status  = 1;
                $updateStatus->save(); 
            }
            $status = WarningLetter::select(DB::raw('sum(case when status = 0 then 1 else 0 end) as aktif, sum(case when status = 1 then 1 else 0 end) as nonaktif'))
                                    ->where('employee_id',$request->employee_id)
                                    ->first();
            $warningletter->sp_active  = $status->aktif;
            $warningletter->sp_non_active  = $status->nonaktif;
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

    public function exportwl(Request $request)
    {
        $employee_id = strtoupper(str_replace("'","''",$request->employee_name));
        $nid = $request->nid;
        $department = $request->department;
        $position = $request->position;
        $status = $request->status;

        $object = new \PHPExcel();
        $object->getProperties()->setCreator('Taewon Indonesia');
        $object->setActiveSheetIndex(0);
        $sheet = $object->getActiveSheet();

        $query = DB::table('warning_letters');
        $query->select(
            'warning_letters.from',
            'warning_letters.to',
            'warning_letters.status',
            'warning_letters.notes',
            'employees.name as employee_name',
            'employees.title_id as title_id',
            'employees.nid as nid',
            'employees.nik as nik',
            'employees.join_date as join_date',
            'titles.name as title_name',
            'departments.name as department_name',
            'wl.aktif',
            'wl.nonaktif',
            'warning_letters.sp_active',
            'warning_letters.sp_non_active'
        );
        $query->leftJoin('employees', 'employees.id', '=', 'warning_letters.employee_id');
        $query->leftJoin('titles', 'titles.id', '=', 'employees.title_id');
        $query->leftJoin('departments', 'departments.id', '=', 'employees.department_id');
        $query->leftJoin(DB::Raw('(select employee_id,sum(case when status = 0 then 1 else 0 end) as aktif, sum(case when status = 1 then 1 else 0 end) as nonaktif from warning_letters group by employee_id) as wl'),'wl.employee_id', '=', 'warning_letters.employee_id');
        if ($employee_id != '') {
            $query->whereRaw("upper(employees.name) like '%$employee_id%'");
        }
        if ($nid != '') {
            $query->whereRaw("employees.nid like '%$nid%'");
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
        if ($position != '') {
            $query->whereIn('employees.title_id', [$position]);
        }
        if ($status != '') {
            $query->where('warning_letters.status', $status);
        }
        $warning_latters = $query->get();

        // Title Column Excel
         $sheet->setCellValue('B1', 'PT. TAEWON INDONESIA');
         $sheet->setCellValue('B2', 'Surat Peringatan');
        // Header Columne Excel
        $sheet->setCellValue('A5', 'Position');
        $sheet->setCellValue('B5', 'Dept');
        $sheet->setCellValue('C5', 'NIK');
        $sheet->setCellValue('D5', 'Name');
        $sheet->setCellValue('E5', 'Join Date');
        $sheet->setCellValue('F5', 'From');
        $sheet->setCellValue('G5', 'To');
        $sheet->setCellValue('H5', 'Total SP Active');
        $sheet->setCellValue('I5', 'Total SP Non Active');
        $sheet->setCellValue('J5', 'Status');
        $sheet->setCellValue('K5', 'Reason'); 
        // $sheet->getColumnDimensionByColumn('A1:K1')->setAutoSize(true);
        $row_number = 6;
        foreach ($warning_latters as $key => $warning_latter) {
            $sheet->setCellValue('A' . $row_number, $warning_latter->title_name);
            $sheet->setCellValue('B' . $row_number, $warning_latter->department_name);
            $sheet->setCellValue('C' . $row_number, "'".$warning_latter->nid);
            $sheet->setCellValue('D' . $row_number, $warning_latter->employee_name);
            $sheet->setCellValue('E' . $row_number, $warning_latter->join_date);
            $sheet->setCellValue('F' . $row_number, $warning_latter->from);
            $sheet->setCellValue('G' . $row_number, $warning_latter->to);
            $sheet->setCellValue('H' . $row_number, $warning_latter->sp_active);
            $sheet->setCellValue('I' . $row_number, $warning_latter->sp_non_active);
            $sheet->setCellValue('J' . $row_number, $warning_latter->status == 0 ? 'Active' : 'Non Active');
            $sheet->setCellValue('K' . $row_number, $warning_latter->notes);
            $row_number++;
        }

        
        $sheet->mergeCells('B1:C1');
        $sheet->mergeCells('B2:C2');
        foreach (range(0, 10) as $column) {
        $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        // $sheet->getCellByColumnAndRow($column, 1)->getStyle()->getFont()->setBold(true);
        // $sheet->getCellByColumnAndRow($column, 2)->getStyle()->getFont()->setBold(true);
        // $sheet->getCellByColumnAndRow($column, 1)->getStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $sheet->getPageSetup()->setFitToWidth(1);
        $objWriter = \PHPExcel_IOFactory::createWriter($object, 'Excel2007');
        ob_start();
        $objWriter->save('php://output');
        $export = ob_get_contents();
        ob_end_clean();
        header('Content-Type: application/json');
        if ($warning_latters->count() > 0) {
        return response()->json([
            'status'     => true,
            'name'        => 'warning-letter-' . date('d-m-Y') . '.xlsx',
            'message'    => "Success Download Warning Latter Data",
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
