<?php

namespace App\Http\Controllers\Admin;

use App\Models\RoleMenu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RoleMenuController extends Controller
{
    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'id' 	=> 'required',
            'role_access' 	=> 'required'
        ]);

        if ($validator->fails()) {
        	return response()->json([
        		'status' 	=> false,
        		'message' 	=> $validator->errors()->first()
        	], 400);
        }

        $rolemenu = RoleMenu::find($request->id);
        $rolemenu->role_access = $request->role_access;
        $rolemenu->save();
        if (!$rolemenu) {
            return response()->json([
                'success' => false,
                'message' 	=> $rolemenu
            ], 400);
        }
        return response()->json([
        	'status' 	=> true,
        	'message' 	=> 'Role access has been updated',
        ], 200);
    }
}
