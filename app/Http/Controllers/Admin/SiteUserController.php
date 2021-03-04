<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\SiteUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SiteUserController extends Controller
{
    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];

        //Count Data
        $query = DB::table('site_users');
        $query->select('site_users.id','sites.code','sites.name');
        $query->leftJoin('sites', 'sites.id', '=', 'site_users.site_id');
        $query->where('site_users.user_id','=',$request->user_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('site_users');
        $query->select('site_users.id','sites.code','sites.name');
        $query->leftJoin('sites', 'sites.id', '=', 'site_users.site_id');
        $query->where('site_users.user_id','=',$request->user_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $siteusers = $query->get();

        $data = [];
        foreach($siteusers as $siteuser){
            $siteuser->no = ++$start;
			$data[] = $siteuser;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }

    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $name = strtoupper($request->name);
        $id_except = [];
        if($request->user_id){
            $siteusers = SiteUser::where('user_id','=',$request->user_id)
            ->get();
            foreach($siteusers as $siteuser){
                array_push($id_except,$siteuser->site_id);
            }
        }
        //Count Data
        $query = DB::table('sites');
        $query->select('sites.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($request->user_id){
            $query->whereNotIn('sites.id', $id_except);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('sites');
        $query->select('sites.*');
        $query->whereRaw("upper(name) like '%$name%'");
        if($request->user_id){
            $query->whereNotIn('sites.id', $id_except);
        }
        $query->offset($start);
        $query->limit($length);
        $siteusers = $query->get();

        $data = [];
        foreach($siteusers as $siteuser){
            $siteuser->no = ++$start;
			$data[] = $siteuser;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' 	=> 'required',
            'site_id' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $siteuser = SiteUser::where('user_id','=',$request->user_id)
                            ->where('site_id','=',$request->site_id)
                            ->get()
                            ->first();
        if(!$siteuser){
            $siteuser = SiteUser::create([
                'user_id' => $request->user_id,
                'site_id' 	=> $request->site_id
            ]);
            if (!$siteuser) {
                return response()->json([
                    'status' => false,
                    'message' 	=> $siteuser
                ], 400);
            }
            return response()->json([
                'status' => true,
                'message' 	=> 'Site has been added'
            ], 200);
        }                   
        else{
            return response()->json([
                'status'     => false,
                'message' 	=> 'Existing Site , Select Another'
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $siteuser = SiteUser::find($id);
            $siteuser->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'     => false,
                'message'     => 'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'     => true,
            'message' => 'Success delete data'
        ], 200);
    }
}
