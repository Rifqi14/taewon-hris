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
        // Cek apakah session 'locale' ada?
        if ($request->session()->has('locale')) {
            // Jika ada, maka set App Locale sesuai nilai yang ada di session 'locale'.
            App::setLocale($request->session()->get('locale'));
        } else {
            $request->session()->put('locale', Auth::guard('admin')->user()->language);
            App::setLocale($request->session()->get('locale'));
        }
        
        // Lanjutkan request.
        return $next($request);
    }
}