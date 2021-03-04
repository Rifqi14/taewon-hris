<?php

namespace App\Http\Controllers\Admin;

use App\Models\OutsourcingDocument;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class OutsourcingDocumentController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'outsourcingdocument'));
    }
    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $outsourcing_id = $request->outsourcing_id;

        //Count Data
        $query = DB::table('outsourcing_documents');
        $query->select('outsourcing_documents.*');
        $query->whereRaw("upper(outsourcing_documents.name) like '%$name%'");
        $query->where('outsourcing_id', $outsourcing_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('outsourcing_documents');
        $query->select('outsourcing_documents.*');
        $query->whereRaw("upper(outsourcing_documents.name) like '%$name%'");
        $query->where('outsourcing_id', $outsourcing_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $principledocuments = $query->get();

        $data = [];
        foreach($principledocuments as $principledocument){
            $principledocument->no = ++$start;
            // $partner->category = $category[$partner->category];
			$data[] = $principledocument;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            'outsourcing_id' 	=> 'required',
            'phone'        => 'required',
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

        $outsourcingdocument = OutsourcingDocument::create([
            'outsourcing_id' => $request->outsourcing_id,
            'phone'      => $request->phone,
            'name'        => $request->name,
            'file'        => '',
            'category'    => $request->category,
            'description' => $request->description
        ]);
        $file = $request->file('file');
        if($file){    
            $filename = 'foto.'. $request->file->getClientOriginalExtension();
            $src = 'assets/outsourcing/document/'.$outsourcingdocument->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $file->move($src,$filename);
            $outsourcingdocument->file = $src.'/'.$filename;
            $outsourcingdocument->save();
        }
        if (!$outsourcingdocument) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcingdocument
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
        $outsourcingdocument = OutsourcingDocument::find($id);
        return response()->json([
            'status' 	=> true,
            'data'      => $outsourcingdocument
        ], 200);
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
            'outsourcing_id' 	=> 'required',
            'phone'        => 'required',
            'name'          => 'required',
            'category'      => 'required',
            
        ]);
        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $outsourcingdocument = OutsourcingDocument::find($id);
        $outsourcingdocument->outsourcing_id = $request->outsourcing_id;
        $outsourcingdocument->phone      = $request->phone;
        $outsourcingdocument->name        = $request->name;
        $outsourcingdocument->category    = $request->category;
        $outsourcingdocument->description = $request->description;
        $outsourcingdocument->file        = 'data-foto.jpg';
        $outsourcingdocument->save();

        $file = $request->file('file');
        if($file){  
            $filename = 'foto.'. $request->file->getClientOriginalExtension(); 
            if(file_exists($outsourcingdocument->file)){
                unlink($outsourcingdocument->file);
            } 
            
            $src = 'assets/ousourcing/document/'.$outsourcingdocument->id;
            if(!file_exists($src)){
                mkdir($src,0777,true);
            }
            $file->move($src,$filename);
            $outsourcingdocument->file = $src.'/'.$filename;
            $outsourcingdocument->save();
        }
        if (!$outsourcingdocument) {
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcingdocument
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $outsourcingdocument
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
            $outsourcingdocument = OutsourcingDocument::find($id);
            if(file_exists($outsourcingdocument->file)){
                unlink($outsourcingdocument->file);
            } 
            $outsourcingdocument->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     =>  'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
