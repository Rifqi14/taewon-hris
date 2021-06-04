<?php

namespace App\Http\Controllers\admin;

use App\Models\Partner;
use App\Models\Department;
use App\Models\Truck;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class PartnerController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'partner'));
    }
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $truck_id = $request->truck_id;
        $department_id = $request->department_id;

        //Count Data
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->leftJoin('departments','departments.id','=','partners.department_id');
        $query->leftJoin('trucks','trucks.id','=','partners.truck_id');
        $query->whereRaw("upper(partners.name) like '%$name%'");
        if($truck_id){
            $query->where('truck_id',$truck_id);
        }
        if($department_id){
            $query->where('department_id',$department_id);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('partners');
        $query->select('partners.*','departments.name as department_name','trucks.name as truck_name');
        $query->leftJoin('departments','departments.id','=','partners.department_id');
        $query->leftJoin('trucks','trucks.id','=','partners.truck_id');
        $query->whereRaw("upper(partners.name) like '%$name%'");
        if($truck_id){
            $query->where('truck_id',$truck_id);
        }
        if($department_id){
            $query->where('department_id',$department_id);
        }
        $query->offset($start);
        $query->limit($length);
        $partners = $query->get();

        $data = [];
        foreach($partners as $partner){
            $partner->no = ++$start;
            $partner->rit = number_format($partner->rit,0,'.',',');
            $data[] = $partner;
        }
        return response()->json([
            'total'=>$recordsTotal,
            'rows'=>$data
        ], 200);
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);

        //Count Data
        $query = DB::table('partners');
        $query->select('partners.*');
        $query->leftJoin('departments','departments.id','=','partners.department_id');
        $query->leftJoin('trucks','trucks.id','=','partners.truck_id');
        $query->whereRaw("upper(partners.name) like '%$name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('partners');
        $query->select('partners.*','departments.name as department_name','trucks.name as truck_name');
        $query->leftJoin('departments','departments.id','=','partners.department_id');
        $query->leftJoin('trucks','trucks.id','=','partners.truck_id');
        $query->whereRaw("upper(partners.name) like '%$name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $partners = $query->get();

        $data = [];
        foreach ($partners as $partner) {
            $partner->no = ++$start;
            $partner->rit = number_format($partner->rit,0,',','.');
            $data[] = $partner;
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
        return view('admin.partner.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $trucks = Truck::where('status',1)->get();
        $departments = Department::where('driver','yes')->get();
        return view('admin.partner.create',compact('trucks','departments'));
    }

    /**
     * {{__('general.imp')}} a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'rit'      => 'required',
            'truck_id'      => 'required',
            'department_id'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }
        DB::beginTransaction();
        $partner = Partner::create([
            'code'     => '',
            'site_id'  => Session::get('site_id'),
            'name'     => $request->name,
            'address'  => $request->address,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'rit'      => $request->rit,
            'status'   => $request->status,
            'truck_id'   => $request->truck_id,
            'department_id'   => $request->department_id,
        ]);
        if ($request->code) {
            $partner->code = $request->code;
            $partner->save();
        } else {
            $partner->code = $partner->code_system;
            $partner->save();
        }
        if (!$partner) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message'     => $partner
            ], 400);
        }
        DB::commit();
        return response()->json([
            'status'     => true,
            'results'     => route('partner.index'),
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function show(Partner $partner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $partner = Partner::find($id);
        $trucks = Truck::where('status',1)->get();
        $departments = Department::where('driver','yes')->get();
        return view('admin.partner.edit', compact('partner','trucks','departments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'rit'      => 'required',
            'truck_id'      => 'required',
            'department_id'      => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $partner = Partner::find($id);
        $partner->code    = $request->code;
        $partner->name    = $request->name;
        $partner->address = $request->address;
        $partner->email   = $request->email;
        $partner->phone   = $request->phone;
        $partner->rit     = $request->rit;
        $partner->status  = $request->status;
        $partner->truck_id  = $request->truck_id;
        $partner->department_id  = $request->department_id;
        $partner->save();

        if (!$partner) {
            return response()->json([
                'status' => false,
                'message'     => $partner
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'results'     => route('partner.index'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Partner  $partner
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $partner = Partner::find($id);
            $partner->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'    => 'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'  => true,
            'message' => 'Success delete data'
        ], 200);
    }

    public function import()
    {
        return view('admin.partner.import');
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
        for ($row = 2; $row <= $highestRow; $row++) {
            $name           = $sheet->getCellByColumnAndRow(0, $row)->getValue();
            $phone          = $sheet->getCellByColumnAndRow(1, $row)->getValue();
            $email          = $sheet->getCellByColumnAndRow(2, $row)->getValue();
            $department     = strtoupper($sheet->getCellByColumnAndRow(3, $row)->getValue());
            $truck          = strtoupper($sheet->getCellByColumnAndRow(4, $row)->getValue());
            $rit            = $sheet->getCellByColumnAndRow(5, $row)->getValue();
            $address        = $sheet->getCellByColumnAndRow(6, $row)->getValue();
            $active         = strtoupper($sheet->getCellByColumnAndRow(7, $row)->getValue());
            $truck_id       = Truck::whereRaw("upper(name) = '$truck'")->first();
            $department_id  = Department::whereRaw("upper(name) = '$department'")->first();
           
            // $departure_time = $sheet->getCellByColumnAndRow(5, $row)->getValue();
            // $arrived_time = $sheet->getCellByColumnAndRow(6, $row)->getValue();
            $status = 1;
            $error_message = '';
            if (!$name || !$department_id || !$truck_id || !$rit) {
                $status = 0;
                if (!$name) {
                    $error_message .= 'Customer Name Not Found</br>';
                }
                if (!$truck_id) {
                    $error_message .= 'Truck Not Found</br>';
                }
                if (!$department_id) {
                    $error_message .= 'Department Not Found</br>';
                }
                if (!$rit) {
                    $error_message .= 'Rit Time Not Found</br>';
                }
            }
            if ($name) {
                $data[] = array(
                    'index'         => $no,
                    'name'           => $name,
                    'email'         => $email ? $email : null,
                    'phone'         => $phone ? $phone : null,
                    'department'    => $department,
                    'department_id' => $department_id ? $department_id->id : null,
                    'truck_id'      => $truck ? $truck_id->id : null,
                    'truck'         => $truck,
                    'active'        => $active == 'ACTIVE' ? 1 : 0,
                    'rit'           => $rit,
                    'address'       => $address ? $address : null,
                    'error_message' => $error_message,
                    'status'        => $status
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
        // echo'aaaaaaaaa';
        // return;
        $validator = Validator::make($request->all(), [
            // 'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }
        $partners = json_decode($request->partners);
        DB::beginTransaction();
        foreach ($partners as $partner){
            $insert = Partner::create([
                'code'     =>  '',
                'site_id'  => Session::get('site_id'),
                'name'     => $partner->name,
                'email'    => $partner->email,
                'phone'    => $partner->phone,
                'address'  => $partner->address,
                'rit'      => $partner->rit,
                'truck_id' => $partner->truck_id,
                'department_id' => $partner->department_id,
                'status'    => $partner->active,
            ]);

            // dd($insert);
            $insert->code = $insert->code_system;
            $insert->save();

            if (!$insert) {
                DB::rollback();
                return response()->json([
                    'status' => false,
                    'message'   => $insert
                ], 400);
            }
        }
        DB::commit();
        return response()->json([
            'status' => true,
            'results' => route('partner.index'),
        ], 200);
    }
}
