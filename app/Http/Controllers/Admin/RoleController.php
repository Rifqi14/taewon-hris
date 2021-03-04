<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class RoleController extends Controller
{
    function __construct(){
        View::share('menu_active', url('admin/'.'role'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.role.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request){
        $start = $request->page?$request->page - 1:0;
        $length = $request->limit;
        $display_name = strtoupper($request->display_name);

        //Count Data
        $query = DB::table('roles');
        $query->select('roles.*');
        $query->whereRaw("upper(display_name) like '%$display_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('roles');
        $query->select('roles.*');
        $query->whereRaw("upper(display_name) like '%$display_name%'");
        $query->offset($start);
        $query->limit($length);
        $roles = $query->get();

        $data = [];
        foreach($roles as $role){
            $role->no = ++$start;
			$data[] = $role;
		}
        return response()->json([
			'total'=>$recordsTotal,
			'rows'=>$data
        ], 200);
    }
    public function read(Request $request){
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $name = strtoupper($request->name);
        $display_name = strtoupper($request->display_name);

        //Count Data
        $query = DB::table('roles');
        $query->select('roles.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->whereRaw("upper(display_name) like '%$display_name%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('roles');
        $query->select('roles.*');
        $query->whereRaw("upper(name) like '%$name%'");
        $query->whereRaw("upper(display_name) like '%$display_name%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $roles = $query->get();

        $data = [];
        foreach($roles as $role){
            $role->no = ++$start;
			$data[] = $role;
		}
        return response()->json([
            'draw'=>$request->draw,
			'recordsTotal'=>$recordsTotal,
			'recordsFiltered'=>$recordsTotal,
			'data'=>$data
        ], 200);
    }
    public function create()
    {
        return view('admin.role.create');
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
            'name' 	    => 'required|unique:roles',
            'display_name' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $role = Role::create([
            'name' 	=> $request->name,
			'display_name' 	=> $request->display_name,
			'description' 	=> $request->description
        ]);
        if (!$role) {
            return response()->json([
                'status' => false,
                'message' 	=> $role
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('role.index'),
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
        $role = Role::find($id);
        if($role){
            $query = DB::table('menus');
            $query->select('menus.*');
            $query->orderBy('menus.menu_sort', 'asc');
            $menus = $query->get();
            foreach($menus as $menu){
                $rolemenu = RoleMenu::where('menu_id',$menu->id)
                                    ->where('role_id',$id)
                                    ->get()->first();
                if(!$rolemenu){
                    RoleMenu::create([
                        'role_id'=> $id,
                        'menu_id'=> $menu->id,
                        'role_access'=>0
                    ]);
                }    
            }
            $rolemenus = RoleMenu::select('role_menus.*','menus.menu_name','menus.parent_id')
                                ->where('role_id',$id)
                                ->leftJoin('menus', 'menus.id', '=', 'role_menus.menu_id')
                                ->orderBy('menus.menu_sort','asc')     
                                ->get();
            return view('admin.role.detail',compact('role','rolemenus'));
        }
        else{
            abort(404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        if($role){
            return view('admin.role.edit',compact('role'));
        }
        else{
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
        $validator = Validator::make($request->all(), [
            'name' 	    => 'required|unique:roles,name,'.$id,
            'display_name' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $role = Role::find($id);
        $role->name = $request->name;
        $role->display_name = $request->display_name;
        $role->description = $request->description;
        $role->save();
        if (!$role) {
            return response()->json([
                'status' => false,
                'message' 	=> $role
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'results' 	=> route('role.index'),
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
            $role = Role::find($id);
            $role->delete();
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
