<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lesson;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'teacher') {
            // This is the view for the Teacher dashboard
            return view('dashboard.teacher_home', [
                'user' => $user
            ]);
        } 

        // Get filters from request
        $filters = [
            'q' => $request->query('q'),
            'file_type' => $request->query('file_type'),
            'date_from' => $request->query('date_from'),
            'date_to' => $request->query('date_to'),
        ];

        // Build query for lessons
        $query = Lesson::query();

        if ($filters['q']) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['q'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['q'] . '%');
            });
        }

        if ($filters['file_type']) {
            $query->where('file_name', 'like', '%.' . $filters['file_type']);
        }

        if ($filters['date_from']) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to']) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $lessons = $query->get();

        return view('home', [
            'user' => $user,
            'lessons' => $lessons,
            'filters' => $filters
        ]);
    }
}