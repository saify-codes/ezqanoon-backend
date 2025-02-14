<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define rate limit for Sanctum authenticated API users
        RateLimiter::for('api', function (Request $request) {
            // Default rate limit: 10 requests per minute per user
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
