<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth; // <-- Added Auth facade
use App\Providers\RouteServiceProvider; // Assuming you have this defined

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     * * NOTE: This property is maintained by the AuthenticatesUsers trait, but the 
     * redirectTo() method below overrides it for dynamic redirection.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME; 
    
    /**
     * Get the post login redirect path based on the user's role.
     * This method overrides the default behavior of the AuthenticatesUsers trait.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = Auth::user();
        
        // Check the user's role (assuming 'role' column exists in your User model)
        if ($user->role === 'teacher') {
            // Redirect teachers to the teacher dashboard route
            return route('teacher.quizzes.index');
        } 
        
        if ($user->role === 'student') {
            // Redirect students to the performance/Prestasi page
            return route('performance.student_view');
        }

        // Fallback to the generic home route
        return $this->redirectTo; 
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}