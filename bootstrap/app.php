<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        // Use web middleware group and replace VerifyCsrfToken with our custom middleware
        // $middleware->web(replace: [
        //     \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class => \App\Http\Middleware\SkipCsrfToken::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        return response()->json([
            'message' => 'An error occurred while processing your request.',
            'error' => $exceptions,
        ], 500);
    })->create();
