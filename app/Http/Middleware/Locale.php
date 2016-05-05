<?php

namespace App\Http\Middleware;

use Closure, Auth, App;

class Locale
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
        App::setLocale(Auth::user()->language);
        return $next($request);
    }
}
