<?php

namespace App\Http\Middleware;

use App\Models\Config;
use Closure;
use Illuminate\Support\Facades\App;

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
            $language = Config::where('option', 'language')->first();
            $request->session()->put('locale', $language->value);
            App::setLocale($request->session()->get('locale'));
        }
        
        // Lanjutkan request.
        return $next($request);
    }
}