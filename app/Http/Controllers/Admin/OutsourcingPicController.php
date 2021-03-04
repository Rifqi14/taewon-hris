<?php

namespace App\Http\Controllers\Admin;

use App\Models\OutsourcingPic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;

class OutsourcingPicController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'outsourcingpic'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function read(Request $request)
    {
        $start        = $request->start;
        $length       = $request->length;
        $query        = $request->search['value'];
        $sort         = $request->columns[$request->order[0]['column']]['data'];
        $dir          = $request->order[0]['dir'];
        $pic_name = strtoupper($request->pic_name);
        $outsourcing_id  = $request->outsourcing_id;

        //Count Data
        $query = DB::table('outsourcing_pics');
        $query->select('outsourcing_pics.*');
        $query->whereRaw("upper(outsourcing_pics.pic_name) like '%$pic_name%'");
        $query->where('outsourcing_id', $outsourcing_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('outsourcing_pics');
        $query->select('outsourcing_pics.*');
        $query->whereRaw("upper(outsourcing_pics.pic_name) like '%$pic_name%'");
        $query->where('outsourcing_id', $outsourcing_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $outsourcingpics = $query->get();

        $data = [];
        foreach($outsourcingpics as $outsourcingpic){
            $outsourcingpic->no = ++$start;
            // $partner->category = $category[$partner->category];
			$data[] = $outsourcingpic;
		}
        return response()->json([
            'draw'           =>$request->draw,
			'recordsTotal'   =>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'           =>$data
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
            'pic_name'       => 'required',
            'pic_phone'      => 'required',
            'pic_email'      => 'required',
            'pic_address'    => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $outsourcingpic = OutsourcingPic::create([
            'outsourcing_id'   => $request->outsourcing_id,
            'pic_name'   => $request->pic_name,
            'pic_phone'  => $request->pic_phone,
            'pic_email'  => $request->pic_email,
            'pic_address'=> $request->pic_address,
            'pic_category'=> $request->pic_category,
            'default' 	     => $request->default?1:0,
        ]);
        if($request->default){
            $outsourcingpics = OutsourcingPic::where('id','<>',$outsourcingpic->id)->where('outsourcing_id',$request->outsourcing_id)->get();
            foreach($outsourcingpics as $outsourcingpic){
                $outsourcingpic->default = 0 ;
                $outsourcingpic->save();
            }
        }
        if (!$outsourcingpic) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcingpic
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
        $outsourcingpic = OutsourcingPic::find($id);
        return response()->json([
            'status' 	=> true,
            'data' => $outsourcingpic
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
            'outsourcing_id' 	  => 'required',
            'pic_name'    => 'required',
            'pic_phone'   => 'required',
            'pic_email'   => 'required',
            'pic_address' => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $outsourcingpic = OutsourcingPic::find($id);
        $outsourcingpic->outsourcing_id     = $request->outsourcing_id;
        $outsourcingpic->pic_name    = $request->pic_name;
        $outsourcingpic->pic_phone   = $request->pic_phone;
        $outsourcingpic->pic_email   = $request->pic_email;
        $outsourcingpic->pic_address = $request->pic_address;
        $outsourcingpic->default         = $request->default?1:0;
        $outsourcingpic->save();

        if($request->default){
            $outsourcingpics = OutsourcingPic::where('id','<>',$id)->where('outsourcing_id',$request->outsourcing_id)->get();
            foreach($outsourcingpics as $outsourcingpic){
                $outsourcingpic->default = 0 ;
                $outsourcingpic->save();
            }
        }
        if (!$outsourcingpic) {
            return response()->json([
                'status' => false,
                'message' 	=> $outsourcingpic
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data'      => $outsourcingpic
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
            $outsourcingpic = OutsourcingPic::find($id);
            $outsourcingpic->delete();
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
