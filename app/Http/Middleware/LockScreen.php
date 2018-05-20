<?php

namespace App\Http\Middleware;

use Closure;

class LockScreen
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
        info('middleWareHasCheck', [$request->session()->has('locked')]);
        if ($request->session()->has('locked')) {

            return redirect('/lockScreen');

        }

        return $next($request);
    }
}
