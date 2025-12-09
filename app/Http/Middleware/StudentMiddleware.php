<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if the user is logged in
        if (!Auth::check()) {
            return redirect('login'); // Redirect unauthenticated users to login
        }

        // 2. Check if the authenticated user has the 'student' role
        if (Auth::user()->role !== 'student') {
            // If not a student, abort with a 403 Forbidden error
            abort(403, 'Unauthorized. Access is restricted to Students.');
        }

        return $next($request);
    }
}

