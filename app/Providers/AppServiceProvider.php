<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

use App\Models\Quiz;
use App\Policies\QuizPolicy; 
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * We keep this here since AuthServiceProvider is missing.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Quiz::class => QuizPolicy::class,
    ];
    
    public function register(): void
    {
        //
    }
    
    public function boot(): void
    {
        Route::aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
                Gate::policy(Quiz::class, QuizPolicy::class);
    }
}