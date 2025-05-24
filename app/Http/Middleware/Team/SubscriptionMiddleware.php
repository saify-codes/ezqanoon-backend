<?php

namespace App\Http\Middleware\Team;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user  = Auth::guard('team')->user();

        if (!$user->owner->subscription) {           
            abort(403, "Account Expired");
        }
        
        return $next($request);
    }
}
