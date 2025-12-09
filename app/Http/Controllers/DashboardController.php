<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirects users to the appropriate dashboard based on their role.
     */
    public function index()
    {
        if (Auth::user()->role === 'teacher') {
            // Redirect teacher to their quiz management index
            return redirect()->route('teacher.quizzes.index');
        }
        
        // Redirect student to the available quizzes index
        return redirect()->route('student.quizzes.index');
    }
}