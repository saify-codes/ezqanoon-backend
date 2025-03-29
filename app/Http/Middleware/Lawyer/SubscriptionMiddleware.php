<?php

namespace App\Http\Middleware\Lawyer;

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
        $user = Auth::user();

        if (!$user->subscription) {
           
            if ($user->role == 'USER') {
                abort(403, 'Account suspended, please contact support');
            }

            return redirect()->route('lawyer.subscription');
            
        }
        
        return $next($request);
    }
}
