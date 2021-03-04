<?php

namespace App\Http\Controllers\Admin;

use App\Models\Site;
use App\Models\DocumentManagement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DocumentManagementController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'. 'documentmanagement'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = DB::table('document_management');
        $query->select('document_management.name');
        $query->orderBy('name', 'asc');
        $documents = $query->get();

        $query = DB::table('document_management');
        $query->select('document_management.code');
        $query->orderBy('code', 'asc');
        $nodocs = $query->get();

        $query = DB::table('document_management');
        $query->select('document_management.pic');
        $query->groupBy('document_management.pic');
        $query->orderBy('pic', 'asc');
        $pics = $query->get();

        return view('admin.documentmanagement.index', compact('documents','nodocs','pics'));
    }

    public function read(Request $request)
    {
        $start  = $request->start;
        $length = $request->length;
        $query  = $request->search['value'];
        $sort   = $request->columns[$request->order[0]['column']]['data'];
        $dir    = $request->order[0]['dir'];
        $date_from = $request->date_from ? Carbon::parse(changeSlash($request->date_from))->endOfDay()->toDateTimeString() : '';
        $date_to = $request->date_to ? Carbon::parse(changeSlash($request->date_to))->endOfDay()->toDateTimeString() : '';
        $code = strtoupper($request->code);
        $pic = $request->pic;
        $name   = strtoupper($request->name);

        //Count Data
        $query = DB::table('document_management');
        $query->select('document_management.*');
        if ($date_from && $date_to) {
            $query->whereBetween('expired_date', [$date_from, $date_to]);
        }
        if ($name) {
            $query->whereRaw("upper(name) like '%$name%'");
        }
        if ($code) {
            $query->whereRaw("upper(code) like '%$code%'");
        }
        if ($pic) {
            $query->whereIn("document_management.pic", $pic);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('document_management');
        $query->select('document_management.*');
        if ($date_from && $date_to) {
            $query->whereBetween('expired_date', [$date_from, $date_to]);
        }
        if ($name) {
            $query->whereRaw("upper(name) like '%$name%'");
        }
        if ($code) {
            $query->whereRaw("upper(code) like '%$code%'");
        }
        if ($pic) {
            $query->whereIn("document_management.pic", $pic);
        }
        // $query->orderBy('expired_date', 'asc');
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $document_managements = $query->get();

        $data = [];
        foreach($document_managements as $document_management){
            $document_management->no = ++$start;
            $document_management->link = url($document_management->file);
			$data[] = $document_management;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        //Count Data
        $query = DB::table('document_management');
        $query->select('document_management.*');
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('document_management');
        $query->select('document_management.*');
        $query->offset($start);
        $query->limit($length);
        $document_managements = $query->get();

        $data = [];
        foreach ($document_managements as $document_management) {
            $document_management->no = ++$start;
            $data[] = $document_management;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.documentmanagement.create');
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
            'name'          => 'required',
            'file'          => 'required',
            'expired_date'  => 'required',
            'code'          => 'required',
            'pic'           => 'required'
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        // return response()->json($request->code);
        $documentmanagement = DocumentManagement::create([
            'name'         => $request->name,
            'code'         => $request->code,
            'file'         => '',
            'expired_date' => $request->expired_date,
            'description'  => $request->description,
            'status'       => '',
            'reminder_type'=> 'Days',
            'nilai' 	   => $request->nilai,
            'pic' 	       => $request->pic
        ]);


        $now = Carbon::now()->format('Y-m-d');

        if ($request->expired_date < $now) {
            $documentmanagement->status = 'Expired';
            $documentmanagement->save();
        } else {
            $documentmanagement->status = 'Active';
            $documentmanagement->save();
        }
        

        $file = $request->file('file');
        if($file){    
            $filename = 'document.'. $request->file->getClientOriginalExtension();
            $src = 'assets/document/management'.$documentmanagement->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $file->move($src,$filename);
            $documentmanagement->file = $src.'/'.$filename;
            $documentmanagement->save();
        }
        if (!$documentmanagement) {
            return response()->json([
                'status' => false,
                'message' 	=> $documentmanagement
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
     * @param  \App\DocumentManagement  $documentManagement
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentManagement $documentManagement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DocumentManagement  $documentManagement
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $documentmanagement = DocumentManagement::find($id);
        $documentmanagement->link = url($documentmanagement->file);
        return response()->json([
            'status' 	=> true,
            'data'      => $documentmanagement
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DocumentManagement  $documentManagement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        
        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'expired_date'  => 'required',
            'code'          => 'required',
            'pic'           => 'required'
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
       
        

        $documentmanagement = DocumentManagement::find($id);
        $documentmanagement->code         = $request->code;
        $documentmanagement->name         = $request->name;
        $documentmanagement->expired_date = $request->expired_date;
        $documentmanagement->description  = $request->description;
        $documentmanagement->status       = $request->status;
        $documentmanagement->pic          = $request->pic;
        $documentmanagement->nilai        = $request->nilai;
        $documentmanagement->save();

        $now = Carbon::now()->format('Y-m-d');
        
        if($request->expired_date < $now)
        {
            $documentmanagement->status = 'Expired';
            $documentmanagement->save();
        }else{
            $documentmanagement->status = 'Active';
            $documentmanagement->save();
        }

        $file = $request->file('file');
        if($file){  
            $filename = 'document.'. $request->file->getClientOriginalExtension(); 
            if(file_exists($documentmanagement->file)){
                unlink($documentmanagement->file);
            } 
            
            $src = 'assets/document/management'.$documentmanagement->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $file->move($src,$filename);
            $documentmanagement->file = $src.'/'.$filename;
            $documentmanagement->save();
        }
        if (!$documentmanagement) {
            return response()->json([
                'status' => false,
                'message' 	=> $documentmanagement
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $documentmanagement
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DocumentManagement  $documentManagement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $documentmanagement = DocumentManagement::find($id);
            if(file_exists($documentmanagement->file)){
                unlink($documentmanagement->file);
            } 
            $documentmanagement->delete();
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
