<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    // ... other properties ...

    /**
     * Define your route model bindings, pattern filters, and other route configurations.
     */
    public function boot(): void
    {
        // ... other boot logic ...
    }

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home'; // <-- Ensure this is /home (or /dashboard), NOT /teacher/quizzes
}