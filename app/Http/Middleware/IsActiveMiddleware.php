<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->status == 'active') // is active
        {
            return $next($request);
        }
        return response(['message' => 'Your account is not active yet. Please contact admin.'], 406);
    }
}
