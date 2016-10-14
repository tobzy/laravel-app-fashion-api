<?php

namespace App\Http\Middleware;

use Closure;

class DesignViewAuthenticate
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
//        return redirect()->route('store');
        return $next($request);
    }
}
