<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AdminLoginController extends Controller
{
    use AuthenticatesUsers;
    protected $redirectTo = '/admin/dashboard';
    public function __construct()
    {
        $this->middleware('guest.admin')->except('logout');
    }
    public function guard()
    {
     return Auth::guard('admin');
    }

    public function showLoginForm()
    {
        return view('admin.login');
    }
    public function logout(Request $request)
    {
        Auth::guard("admin")->logout();
        $request->session()->forget('site_id');
        return redirect('/admin');
    }
}
