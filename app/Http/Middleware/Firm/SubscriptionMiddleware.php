<?php

namespace App\Http\Middleware\Firm;

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
        $user = Auth::guard('firm')->user();

        if (!$user->subscription) {
           
            return redirect()->route('firm.subscription');
        }
        
        return $next($request);
    }
}
