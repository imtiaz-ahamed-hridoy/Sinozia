<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckUserEnabledMiddleware;
use App\Http\Middleware\GuestMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/rahat_api.php'));

            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->group(base_path('routes/backend.php'));

            Route::middleware(['web', 'auth', 'admin'])
                ->prefix('admin')
                ->group(base_path('routes/admin_setting.php'));

            Route::middleware(['web'])
                ->group(base_path('routes/frontend.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'guest' => GuestMiddleware::class,
            'enabled' => CheckUserEnabledMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'api/revenuecat/webhook',
        ]);
    })
    ->withBroadcasting(__DIR__.'/../routes/channels.php', ['prefix' => 'api', 'middleware' => ['auth:sanctum']])

    ->withExceptions(function (Exceptions $exceptions): void {})

    ->withSchedule(function ($schedule) {
        $schedule->command('kling:poll-pending')->everyFiveMinutes();
        // $schedule->job(new RefreshMonthlyCreditsJob)->monthlyOn(1, '00:00');// Not needed
    })
    ->create();
