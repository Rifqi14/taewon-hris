<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AccountController extends Controller
{
    public function __construct()
    {
        View::share('menu_active', url('admin/' . 'account'));
    }

    public function select(Request $request)
    {
        $start = $request->page ? $request->page - 1 : 0;
        $length = $request->limit;
        $account = strtoupper($request->acc_name);

        //Count Data
        $query = DB::table('accounts');
        $query->select('accounts.*');
        $query->whereRaw("upper(acc_name) like '%$account%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('accounts');
        $query->select('accounts.*');
        $query->whereRaw("upper(acc_name) like '%$account%'");
        $query->offset($start);
        $query->limit($length);
        $accounts = $query->get();

        $data = [];
        foreach ($accounts as $acc) {
            $acc->no = ++$start;
            $data[] = $acc;
        }
        return response()->json([
            'total' => $recordsTotal,
            'rows' => $data
        ], 200);
    }

    public function read(Request $request)
    {
        $start = $request->start;
        $length = $request->length;
        $query = $request->search['value'];
        $sort = $request->columns[$request->order[0]['column']]['data'];
        $dir = $request->order[0]['dir'];
        $category = strtoupper($request->acc_category);
        $account = strtoupper($request->acc_name);

        //Count Data
        $query = DB::table('accounts');
        $query->select('accounts.*');
        $query->leftJoin('accounts as parent', 'parent.id', '=', 'accounts.parent_id');
        $query->whereRaw("upper(accounts.acc_name) like '%$account%'");
        $query->whereRaw("upper(accounts.acc_category) like '%$category%'");
        $recordsTotal = $query->count();

        //Select Pagination
        $query = DB::table('accounts');
        $query->select('accounts.*', 'parent.acc_name as parent_name');
        $query->leftJoin('accounts as parent', 'parent.id', '=', 'accounts.parent_id');
        $query->whereRaw("upper(accounts.acc_name) like '%$account%'");
        $query->whereRaw("upper(accounts.acc_category) like '%$category%'");
        $query->offset($start);
        $query->limit($length);
        $query->orderBy('path', $dir);
        $accounts = $query->get();

        $data = [];
        foreach ($accounts as $acc) {
            $acc->no = ++$start;
            $acc->acc_category = @config('enums.account_category')[$acc->acc_category];
            $acc->path = str_replace('->', ' <i class="fas fa-angle-right"></i> ', $acc->path);
            $data[] = $acc;
        }
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'sort' => $sort,
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
        return view('admin.account.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.account.create');
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
            'acc_code'  => 'required|alpha_dash',
            'acc_name'  => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $account = Account::create([
            'parent_id'        => $request->parent_id ? $request->parent_id : 0,
            'acc_category'     => $request->acc_category,
            'acc_code'         => $request->acc_code,
            'acc_name'         => $request->acc_name
        ]);
        $account->path = implode(' -> ', $this->createPath($account->id, []));
        $account->level = count($this->createLevel($account->id, []));
        $account->save();
        if (!$account) {
            return response()->json([
                'status'    => false,
                'message'   => $account
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('account.index'),
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
        $account = Account::find($id);
        if ($account) {
            return view('admin.account.edit', compact('account'));
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
            'acc_code'  => 'required|alpha_dash',
            'acc_name'  => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first()
            ], 400);
        }

        $account = Account::find($id);
        $account->parent_id = $request->parent_id ? $request->parent_id : 0;
        $account->acc_category = $request->acc_category;
        $account->acc_code = $request->acc_code;
        $account->acc_name = $request->acc_name;
        $account->save();
        $account->path = implode(' -> ', $this->createPath($id, []));
        $account->level = count($this->createLevel($id, []));
        $account->save();
        $this->updatePath($id);
        $this->updateLevel($id);
        if (!$account) {
            return response()->json([
                'status'    => false,
                'message'   => $account
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'results'   => route('account.index')
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
            $account = Account::find($id);
            $account->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'    => false,
                'message'   => 'Data has been used to another page'
            ], 400);
        }
        return response()->json([
            'status'    => true,
            'message'   => 'Success delete data'
        ], 200);
    }

    public function createPath($id, $path)
    {
        $account = Account::find($id);
        array_unshift($path, $account->acc_name);
        if ($account->parent_id) {
            return $this->createPath($account->parent_id, $path);
        }
        return $path;
    }

    public function updatePath($id)
    {
        $accounts = Account::where('parent_id', $id)->get();
        foreach ($accounts as $account) {
            $account->path = implode(' -> ', $this->createPath($account->id, []));
            $account->save();
            $this->updatePath($account->id);
        }
    }

    public function createLevel($id, $level)
    {
        $account = Account::find($id);
        array_unshift($level, $account->acc_name);
        if ($account->parent_id) {
            return $this->createLevel($account->parent_id, $level);
        }
        return $level;
    }

    public function updateLevel($id)
    {
        $accounts = Account::where('parent_id', $id)->get();
        foreach ($accounts as $account) {
            $account->level = count($this->createLevel($account->id, []));
            $account->save();
            $this->updateLevel($account->id);
        }
    }
}