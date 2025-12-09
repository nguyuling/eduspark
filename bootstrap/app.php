<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add your custom route middleware aliases here
        $middleware->alias([
            // Existing aliases might be here, DO NOT remove them
            // 'auth' => \App\Http\Middleware\Authenticate::class,
            // 'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,

            // --- ADD YOUR CUSTOM MIDDLEWARE HERE ---
            'teacher' => \App\Http\Middleware\TeacherMiddleware::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
            // ---------------------------------------
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
