<?php

namespace App\Http\Middleware\Lawyer;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NoSubscriptionMiddleware
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
                abort(403, 'Unauthorized');
            }

            return $next($request);
            
        }

        return redirect()->route('lawyer.dashboard');
    }
}
