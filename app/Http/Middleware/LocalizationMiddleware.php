<?php

namespace App\Http\Middleware;

use App\Models\Config;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       
        if(Auth::guard('admin')->check()){
            $request->session()->put('locale', Auth::guard('admin')->user()->language?Auth::guard('admin')->user()->language:'en');
            App::setLocale($request->session()->get('locale'));
        }
        else{
            App::setLocale('en');
        }
        // Lanjutkan request.
        return $next($request);
    }
}