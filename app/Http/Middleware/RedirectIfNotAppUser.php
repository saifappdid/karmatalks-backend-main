<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfNotAppUser
{

    public function handle($request, Closure $next, $guard = "appUser")
    {


        if (!auth()->guard($guard)->check()) {

            return redirect(route('user-login'));
        }
        return $next($request);
    }
}
