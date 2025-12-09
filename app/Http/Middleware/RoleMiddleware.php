<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Check if the user is logged in
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // 2. Check if the authenticated user has any of the required roles
        // We assume the user model has a 'role' column (e.g., 'teacher' or 'student')
        if (! in_array($user->role, $roles)) {
            // If the user does not have the required role, abort with 403 Forbidden
            abort(403, 'Unauthorized action. Your role does not permit access to this resource.');
        }

        // 3. If authorized, proceed with the request
        return $next($request);
    }
}