<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'teacher') {
            // This is the view for the Teacher dashboard
            return view('dashboard.teacher_home', [
                'user' => $user
            ]);
        } 

        return view('home', [
            'user' => $user
        ]);
    }
}