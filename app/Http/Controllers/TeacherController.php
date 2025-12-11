<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Option;


class TeacherController extends Controller
{
    // --- READ: Index ---
    public function index(Request $request)
    {
        // 1. Initialize Filters from Request or defaults (matching the Blade file defaults)
        $filters = $request->only(['unique_id', 'title', 'creator_email', 'publish_date_range', 'scope']);
        $filters = array_merge([
            'unique_id' => '',
            'title' => '',
            'creator_email' => '',
            'publish_date_range' => '',
            'scope' => 'all'
        ], $filters);

        // 2. Start the base query (assuming relationship counters are managed in the Quiz Model)
        $query = Quiz::withoutGlobalScopes()
            ->withCount(['questions', 'attempts'])
            ->with('creator'); 

        // 3. Apply the 'scope' filter
        if ($filters['scope'] === 'mine') {
            // Fix 1: Ensure only quizzes created by the logged-in teacher are shown if 'mine' is checked.
            $query->where('user_id', Auth::id());
        }

        // 4. Apply other dynamic filters from the request
        if ($filters['unique_id']) {
            $query->where('unique_code', 'LIKE', '%' . $filters['unique_id'] . '%');
        }

        if ($filters['title']) {
            $query->where('title', 'LIKE', '%' . $filters['title'] . '%');
        }

        if ($filters['creator_email']) {
            // This requires joining the users table, as creator_email is on the related user model
            $query->whereHas('user', function ($q) use ($filters) {
                $q->where('email', 'LIKE', '%' . $filters['creator_email'] . '%');
            });
        }
        
        if ($filters['publish_date_range']) {
            // Example date range logic (requires the 'created_at' or a 'published_at' field)
            $date = now();
            if ($filters['publish_date_range'] === 'today') {
                $query->whereDate('created_at', $date->toDateString());
            } elseif ($filters['publish_date_range'] === 'month') {
                $query->where('created_at', '>=', $date->startOfMonth());
            } elseif ($filters['publish_date_range'] === '3months') {
                $query->where('created_at', '>=', $date->subMonths(3));
            } elseif ($filters['publish_date_range'] === 'year') {
                $query->where('created_at', '>=', $date->startOfYear());
            }
        }

        // Fix 2: Order by creation date descending to show the newest first
        $quizzes = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('teacher.quizzes.index', compact('quizzes', 'filters'));
    }

        // --- CREATE: Show Form ---
    public function create()
    {
        return view('teacher.quizzes.create');
    }

    // --- CREATE: Store Data ---
    public function store(Request $request)
    {
        // 1. Validation 
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_attempts' => 'required|integer|min:1',
            'due_at' => 'nullable|date|after:now',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,checkbox,true_false,short_answer',
            'questions.*.points' => 'required|integer|min:1',
        ]);

        $is_published = $request->has('publish');
        $unique_code = null;

        // Generate Unique Code upon initial creation and publishing
        if ($is_published) {
            do {
                // Generates an 8-character unique alphanumeric code (upper/lower case/numbers)
                $code = Str::random(8); 
            } while (Quiz::where('unique_code', $code)->exists());
            $unique_code = $code;
        }

        // 2. Create the Quiz record (Header data)
        
        $quiz = Quiz::create([

            'teacher_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'max_attempts' => $request->max_attempts,
            'due_at' => $request->due_at,
            'is_published' => $is_published,
            'unique_code' => $unique_code,
        ]);

        $this->syncQuestions($quiz, $request->questions);

        return redirect()->route('teacher.quizzes.index')
                        ->with('success', 'Quiz "' . $quiz->title . '" created successfully!');
    }

    // --- UPDATE: Show Form ---
    public function edit(Quiz $quiz)
    {
        // 1. Authorization check
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'You do not have permission to edit this quiz.');
        }

        // 2. Eager load
        $quiz->load('questions.options');

        return view('teacher.quizzes.edit', compact('quiz'));
    }

    // --- UPDATE: Store Data ---
    public function update(Request $request, Quiz $quiz)
    {
        // 1. Authorization check
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'You do not have permission to update this quiz.');
        }

        // 2. Validation (omitted for brevity, but should include question/option validation)
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'max_attempts' => 'required|integer|min:1',
            'due_at' => 'nullable|date|after:now',
            'questions' => 'required|array|min:1', // Ensure questions array is present
            // ... add more specific question/option validation here
        ]);

        // 3. Update the main Quiz record
        $is_published = $request->has('publish');
        $unique_code = $quiz->unique_code; // Preserve existing code

        // Generate Unique Code if being published AND it doesn't already have one
        if ($is_published && !$unique_code) {
            do {
                $code = Str::random(8);
            } while (Quiz::where('unique_code', $code)->exists());
            $unique_code = $code;
        } elseif (!$is_published && $unique_code) {
             // Clear the code if it's being reverted from Published to Draft
             $unique_code = null;
        }


        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'max_attempts' => $request->max_attempts,
            'due_at' => $request->due_at,
            'is_published' => $is_published,
            'unique_code' => $unique_code,   
        ]);

        $quiz->questions()->delete();

        // 5. Re-create Questions and Options
        $this->syncQuestions($quiz, $request->questions);

        return redirect()->route('teacher.quizzes.index') 
                        ->with('success', 'Quiz "' . $quiz->title . '" updated successfully!');
    }
    
    // --- PRIVATE HELPER METHOD ---
    /**
     * Loops through question data and saves/syncs questions and options for a given quiz.
     */
    private function syncQuestions(Quiz $quiz, array $questionsData)
    {
        foreach ($questionsData as $qData) {
            // 1. Create the Question
            $question = $quiz->questions()->create([
                'question_text' => $qData['text'],
                'type' => $qData['type'],
                'points' => $qData['points'],
            ]);

            // 2. Create the Options (Assuming options key exists and is an array)
            if (isset($qData['options']) && is_array($qData['options'])) {
                foreach ($qData['options'] as $oData) {
                    $question->options()->create([
                        'option_text' => $oData['text'],
                        // The form should send a boolean/1/0 indicating if it's the correct answer
                        'is_correct' => $oData['is_correct'] ?? false, 
                    ]);
                }
            }
            // Handle Short Answer: Short answers usually have one 'correct' text stored as an option
            elseif ($qData['type'] === 'short_answer' && isset($qData['correct_text'])) {
                 $question->options()->create([
                    'option_text' => $qData['correct_text'],
                    'is_correct' => true, 
                ]);
            }
        }
    }


    // --- DELETE: Destroy ---
    public function destroy(Quiz $quiz)
    {
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'You do not have permission to delete this quiz.');
        }
        
        $title = $quiz->title;
        $quiz->delete();

        return redirect()->route('teacher.quizzes.index')
                        ->with('success', 'Quiz "' . $title . '" and all related data have been successfully deleted.');
    }
    
    // --- RESULTS: View All ---
    public function viewAllResults(Quiz $quiz)
    {
        // ... (method remains the same) ...
        if ($quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $attempts = $quiz->attempts()
                        ->whereNotNull('submitted_at')
                        ->with('student')
                        ->orderBy('score', 'desc')
                        ->get();

        $totalScores = $attempts->pluck('score');
        $averageScore = $totalScores->avg();
        $highestScore = $totalScores->max();
        $lowestScore = $totalScores->min();

        $statistics = [
            'average' => number_format($averageScore, 2),
            'highest' => $highestScore,
            'lowest' => $lowestScore,
            'total_students' => $attempts->count(),
        ];

        return view('teacher.quiz_results', compact('quiz', 'attempts', 'statistics'));
    }

    /**
     * Add teacher feedback/remark to a student's attempt (UC0012-02).
     */
    public function addRemark(Request $request, QuizAttempt $attempt)
    {
        // ... (method remains the same) ...
        if ($attempt->quiz->teacher_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'remark' => 'nullable|string|max:1000',
        ]);

        $attempt->update([
            'teacher_remark' => $request->remark,
        ]);

        return back()->with('success', 'Remark added successfully.');
    }
}