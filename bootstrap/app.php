<?php

use App\Http\Middleware\Admin\AuthMiddleware as AdminAuthMiddleware;
use App\Http\Middleware\Admin\GuestMiddleware as AdminGuestMiddleware;
use App\Http\Middleware\Firm\AuthMiddleware as FirmAuthMiddleware;
use App\Http\Middleware\Firm\NoSubscriptionMiddleware as FirmNoSubscriptionMiddleware;
use App\Http\Middleware\Firm\SubscriptionMiddleware as FirmSubscriptionMiddleware;
use App\Http\Middleware\Lawyer\AuthMiddleware;
use App\Http\Middleware\Lawyer\GuestMiddleware; 
use App\Http\Middleware\Lawyer\NoSubscriptionMiddleware;
use App\Http\Middleware\Lawyer\SubscriptionMiddleware;
use App\Http\Middleware\Team\AuthMiddleware as TeamAuthMiddleware;
use App\Http\Middleware\Team\SubscriptionMiddleware as TeamSubscriptionMiddleware;
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
            'admin.auth'                => AdminAuthMiddleware::class,
            'admin.guest'               => AdminGuestMiddleware::class,
            'lawyer.auth'               => AuthMiddleware::class,
            'lawyer.subscribed'         => SubscriptionMiddleware::class,
            'lawyer.unsubscribed'       => NoSubscriptionMiddleware::class,
            'firm.auth'                 => FirmAuthMiddleware::class,
            'firm.subscribed'           => FirmSubscriptionMiddleware::class,
            'firm.unsubscribed'         => FirmNoSubscriptionMiddleware::class,
            'team.auth'                 => TeamAuthMiddleware::class,
            'team.owner_subscribed'     => TeamSubscriptionMiddleware::class,
            'guest'                     => GuestMiddleware::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/api/foo' // <-- exclude this route
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
