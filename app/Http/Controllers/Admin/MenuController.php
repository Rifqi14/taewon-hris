<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public $sort;
    function __construct(){
        View::share('menu_active', url('admin/'.'menu'));
    }
    function setSort($sort){
		$this->sort = $sort;
	}

	function getSort(){
		return $this->sort;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = DB::table('menus');
        $query->select('menus.id','menus.parent_id','menus.menu_name as description','menus.menu_route as url');
        $query->orderBy('menus.menu_sort', 'asc');
        $menus = $query->get();
        return view('admin.menu.index',compact('menus'));
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
            'menu_name' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $menu = Menu::create([
            'parent_id' => 0,
            'menu_name' => $request->menu_name,
			'menu_route' 	=> $request->menu_route,
			'menu_icon' 	=> $request->menu_icon,
        ]);
        if (!$menu) {
            return response()->json([
                'status' => false,
                'message' 	=> $menu
            ], 400);
        }
        $menu->menu_sort = $menu->id;
        $menu->save();
        return response()->json([
            'status' 	=> true,
            'data' => [
                'id' => $menu->id,
                'menu_name' => $menu->menu_name,
                'menu_route' => $menu->menu_route
            ]
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu = Menu::find($id);
        return response()->json([
            'status' 	=> true,
            'data' => [
                'id' => $menu->id,
                'menu_name' => $menu->menu_name,
                'menu_route' => $menu->menu_route,
                'menu_icon' => $menu->menu_icon,
            ]
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
            'menu_name' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $menu = Menu::find($id);
        $menu->menu_name = $request->menu_name;
        $menu->menu_route = $request->menu_route;
        $menu->menu_icon = $request->menu_icon;
        $menu->save();
        if (!$menu) {
            return response()->json([
                'status' => false,
                'message' 	=> $menu
            ], 400);
        }
        return response()->json([
            'status' 	=> true,
            'data' => [
                'id' => $menu->id,
                'menu_name' => $menu->menu_name,
                'menu_route' => $menu->menu_route
            ]
        ], 200);
    }
    public function order(Request $request){
        $validator = Validator::make($request->all(), [
            'order' => 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }
        $this->setSort(1);
        $order = json_decode($request->order);
        foreach($order as $parent){
            $menu = Menu::find($parent->id);
            $menu->parent_id = 0;
            $menu->menu_sort = $this->getSort();
            $menu->save();
			$this->setSort($this->getSort() + 1);
			if(isset($parent->children)){
				$this->orderChild($parent->children,$parent->id);
			}
        }
        
        return response()->json([
            'status' 	=> true,
            'message' => 'Success Update Data'
        ], 200);
    }
    function orderChild($childrens,$parent_id){
		foreach($childrens as $children){
            $menu = Menu::find($children->id);
            $menu->parent_id = $parent_id;
            $menu->menu_sort = $this->getSort();
            $menu->save();
			$this->setSort($this->getSort() + 1);
			if(isset($children->children)){
				$this->orderChild($children->children,$children->id);
			}
		}
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
            $menu = Menu::find($id);
            $menu->delete();
            $this->destroychild($menu->id);
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

    function destroychild($parent_id){
        $menus = Menu::where('parent_id','=',$parent_id)->get();
		foreach($menus as $menu){
            try {
                Menu::find($menu->id)->delete();
                $this->destroychild($menu->id);
            } catch (\Illuminate\Database\QueryException $e) {
                
            }
		}
		
	}
}
