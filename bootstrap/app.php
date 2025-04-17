<?php

use App\Http\Middleware\Lawyer\AuthMiddleware;
use App\Http\Middleware\Lawyer\GuestMiddleware;
use App\Http\Middleware\Lawyer\NoSubscriptionMiddleware;
use App\Http\Middleware\Lawyer\SubscriptionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'lawyer.auth'               => AuthMiddleware::class,
            'lawyer.guest'              => GuestMiddleware::class,
            'lawyer.has_subscription'   => SubscriptionMiddleware::class,
            'lawyer.no_subscription'    => NoSubscriptionMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/api/foo' // <-- exclude this route
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
