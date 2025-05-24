<?php

namespace App\Http\Middleware\Lawyer;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get all guard names defined in auth config
        $guards = array_keys(config('auth.guards'));

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect()->route($guard . '.dashboard');
            }
        }

        return $next($request);
    }
}
