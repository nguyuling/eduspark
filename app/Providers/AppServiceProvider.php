<?php

namespace App\Providers;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        // 🚨 ADD THE MIDDLEWARE ALIAS REGISTRATION HERE
        Route::aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
    }
}