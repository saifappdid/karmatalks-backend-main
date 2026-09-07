<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LogoutAppUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = "appUser")
    {


        if (auth()->guard($guard)->check()) {

            auth()->guard($guard)->logout();
            return redirect(route('/'));
        } else {
            return redirect(route('/'));
        }
    }
}
