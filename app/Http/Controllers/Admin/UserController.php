<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Role;
use App\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    function __construct()
    {
        View::share('menu_active', url('admin/' . 'user'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.user.index');
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $display_name = strtoupper($request->display_name);
        $name = strtoupper($request->name);
        $username = strtoupper($request->username);
        $email = strtoupper($request->email);
        $status = strtoupper($request->status);

        //Count Data
        $query = DB::table('users');
        $query->select('users.*');
        $query->leftJoin('role_user', 'role_user.user_id', '=', 'users.id');
        $query->leftJoin('roles', 'role_user.role_id', '=', 'roles.id');
        $query->whereRaw("upper(display_name) like '%$display_name%'");
        $query->whereRaw("upper(users.name) like '%$name%'");
        $query->whereRaw("upper(email) like '%$email%'");
        if ($request->status != '') {
            $query->where('status', '=', $request->status);
        }
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('users');
        $query->select('users.*', 'roles.display_name');
        $query->leftJoin('role_user', 'role_user.user_id', '=', 'users.id');
        $query->leftJoin('roles', 'role_user.role_id', '=', 'roles.id');
        $query->whereRaw("upper(display_name) like '%$display_name%'");
        $query->whereRaw("upper(users.name) like '%$name%'");
        $query->whereRaw("upper(email) like '%$email%'");
        if ($request->status != '') {
            $query->where('status', '=', $request->status);
        }
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $users = $query->get();

        $data = [];
        foreach ($users as $user) {
            $user->no = ++$start;
            $data[] = $user;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function info()
    {
        View::share('menu_active', url('admin/' . 'user/info'));
        return view('admin.user.info');
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
            'role_id'     => 'required',
            'name'         => 'required',
            'username'     => 'required|unique:users',
            'email'     => 'required|email|unique:users',
            'password'     => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'     => $request->email,
            'username'     => $request->username,
            'password'    => Hash::make($request->password),
            'status'     => $request->status ? 1 : 0,
        ]);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message'     => $user
            ], 400);
        }
        $role = Role::find($request->role_id);
        $user->attachRole($role);
        return response()->json([
            'status'     => true,
            'results'     => route('user.index'),
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
        $query = DB::table('users');
        $query->select('users.*', 'roles.display_name', 'role_user.role_id');
        $query->leftJoin('role_user', 'role_user.user_id', '=', 'users.id');
        $query->leftJoin('roles', 'role_user.role_id', '=', 'roles.id');
        $query->where('users.id', '=', $id);
        $user = $query->get()->first();
        if ($user) {
            return view('admin.user.detail', compact('user'));
        } else {
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
        $query = DB::table('users');
        $query->select('users.*', 'roles.display_name', 'role_user.role_id');
        $query->leftJoin('role_user', 'role_user.user_id', '=', 'users.id');
        $query->leftJoin('roles', 'role_user.role_id', '=', 'roles.id');
        $query->where('users.id', '=', $id);
        $user = $query->get()->first();
        if ($user) {
            return view('admin.user.edit', compact('user'));
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
        $validator = Validator::make($request->all(), [
            'role_id'     => 'required',
            'name'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'     => false,
                'message'     => $validator->errors()->first()
            ], 400);
        }

        $user = User::find($id);
        $user->name = $request->name;
        $user->status = $request->status ? 1 : 0;
        $user->save();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message'     => $user
            ], 400);
        }
        $user->roles()->sync([]);
        $role = Role::find($request->role_id);
        $user->attachRole($role);
        return response()->json([
            'status'     => true,
            'results'     => route('user.index'),
        ], 200);
    }

    public function updatepassword(Request $request, $id)
    {
        $user = Auth::guard('admin')->user();

        if ($request->password) {
            Validator::extend('passcheck', function ($attribute, $value, $parameters) {
                return Hash::check($value, $parameters[0]);
            });
            $validator = Validator::make($request->all(), [
                'password' => 'required|passcheck:' . $user->password,
                'newpassword' => 'required|confirmed|min:6',
            ], [
                'password.passcheck'    => 'Wrong password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'     => false,
                    'message'     => $validator->errors()->first()
                ], 400);
            }

            $user->password = Hash::make($request->newpassword);
            $user->language = $request->language;
            $user->save();

            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message'     => $user
                ], 400);
            }
        }
        $user->language = $request->language;
        $user->save();
        
        $request->session()->put('locale', $user->language);
        return response()->json([
            'status'     => true,
            'results'     => route('user.info'),
            'message'     => 'Password changed successfully'
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
            $user = User::find($id);
            $user->delete();
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

    public function log(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];

        //Count Data
        $query = DB::table('logs');
        $query->select('logs.*');
        $query->where('user_id', $request->user_id);
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('logs');
        $query->select('logs.*');
        $query->where('user_id', $request->user_id);
        $query->offset($start);
        $query->limit($length);
        $query->orderBy($sort, $dir);
        $logs = $query->get();

        $data = [];
        foreach ($logs as $log) {
            $log->no = ++$start;
            $data[] = $log;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data
        ], 200);
    }
}