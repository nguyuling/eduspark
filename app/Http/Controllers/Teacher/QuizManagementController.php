<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class QuizManagementController extends Controller
{
    /**
     * Display a listing of quizzes created by the authenticated teacher, with filtering.
     */
    public function index(Request $request)
    {
        $teacherId = Auth::id();
        
        // 1. Get filters from the request
        $filters = $request->only(['unique_id', 'title', 'status', 'due_before']);

        // 2. Start the query, limiting to the current teacher's quizzes
        $query = Quiz::where('creator_id', $teacherId)
                    ->withCount('questions'); // Count questions for display

        // 3. Filter by Unique ID (Exact Match)
        if (!empty($filters['unique_id'])) {
            $query->where('unique_code', $filters['unique_id']);
        }

        // 4. Filter by Title (Case-insensitive LIKE)
        if (!empty($filters['title'])) {
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($filters['title']) . '%']);
        }
        
        // 5. Filter by Publish Status
        if (isset($filters['status']) && $filters['status'] !== '') {
            if ($filters['status'] === 'published') {
                $query->where('is_published', true);
            } elseif ($filters['status'] === 'draft') {
                $query->where('is_published', false);
            }
        }
        
        // 6. Filter by Due Date (Quizzes due before a specific date)
        if (!empty($filters['due_before'])) {
            try {
                $dueDate = Carbon::parse($filters['due_before'])->endOfDay();
                $query->where('due_at', '<=', $dueDate);
            } catch (\Exception $e) {
                // Ignore malformed date filter
            }
        }

        // 7. Execute the query
        $quizzes = $query->orderBy('created_at', 'desc')->paginate(10); // Use paginate for longer lists

        // 8. Return view with quizzes and filters
        return view('teacher.quizzes.index', compact('quizzes', 'filters'));
    }
    
    // NOTE: You would add other methods like create(), store(), edit(Quiz $quiz), update(), and destroy() here.
}