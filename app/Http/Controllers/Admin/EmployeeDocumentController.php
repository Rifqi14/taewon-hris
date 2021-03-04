<?php

namespace App\Http\Controllers\Admin;

use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EmployeeDocumentController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'employeedocument'));
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
        $name = strtoupper($request->name);
        $employee_id = $request->employee_id;

        //Count Data
        $query = DB::table('employee_documents');
        $query->select('employee_documents.*');
        $query->whereRaw("upper(employee_documents.name) like '%$name%'");
        $query->where('employee_id', $employee_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('employee_documents');
        $query->select('employee_documents.*');
        $query->whereRaw("upper(employee_documents.name) like '%$name%'");
        $query->where('employee_id', $employee_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $employeedocuments = $query->get();

        $data = [];
        foreach($employeedocuments as $employeedocument){
            $employeedocument->no = ++$start;
            $employeedocument->link = url($employeedocument->file);
			$data[] = $employeedocument;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function index()
    {
        //
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
        $validator = Validator::make($request->all(), [
            'employee_id' 	=> 'required',
            'name'          => 'required',
            'file'          => 'required',
            'category'      => 'required',
            
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $employeedocument = EmployeeDocument::create([
            'employee_id' => $request->employee_id,
            'name'        => $request->name,
            'file'        => '',
            'category'    => $request->category,
            'description' => $request->description
        ]);
        $file = $request->file('file');
        if($file){    
            $filename = 'document.'. $request->file->getClientOriginalExtension();
            $src = 'assets/employee/document/'.$employeedocument->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $file->move($src,$filename);
            $employeedocument->file = $src.'/'.$filename;
            $employeedocument->save();
        }
        if (!$employeedocument) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeedocument
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'message' => 'Success add data'
        ], 200);

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\EmployeeDocument  $employeeDocument
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeDocument $employeeDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmployeeDocument  $employeeDocument
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employeedocument = EmployeeDocument::find($id);
        $employeedocument->link = url($employeedocument->file);
        return response()->json([
            'status' 	=> true,
            'data'      => $employeedocument
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EmployeeDocument  $employeeDocument
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' 	=> 'required',
            'name'          => 'required',
            'category'      => 'required',
            
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $employeedocument = EmployeeDocument::find($id);
        $employeedocument->employee_id = $request->employee_id;
        $employeedocument->name        = $request->name;
        $employeedocument->category    = $request->category;
        $employeedocument->description = $request->description;
        $employeedocument->save();

        $file = $request->file('file');
        if($file){  
            $filename = 'document.'. $request->file->getClientOriginalExtension(); 
            if(file_exists($employeedocument->file)){
                unlink($employeedocument->file);
            } 
            
            $src = 'assets/employee/document/'.$employeedocument->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $file->move($src,$filename);
            $employeedocument->file = $src.'/'.$filename;
            $employeedocument->save();
        }
        if (!$employeedocument) {
            return response()->json([
                'status' => false,
                'message' 	=> $employeedocument
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $employeedocument
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EmployeeDocument  $employeeDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
   {
        try {
            $employeedocument = EmployeeDocument::find($id);
            if(file_exists($employeedocument->file)){
                unlink($employeedocument->file);
            } 
            $employeedocument->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     =>  'Error delete data'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
