<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;

class MiddlewareServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('role', \App\Http\Middleware\CheckRole::class);
    }
}