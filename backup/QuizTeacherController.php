<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\QuizQuestion;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB; // Ensure DB facade is imported

class QuizTeacherController extends Controller
{

    /**
     * Display a listing of quizzes for the teacher/management panel.
     * This method is required for the update method to redirect correctly.
     */
    public function index(Request $request)
    {   
        // 1. Get Filters
        $filters = $request->only(['unique_id', 'title', 'creator_email', 'publish_date_range', 'scope']);

        // Set up the base query
        $query = Quiz::with(['creator'])
                     // Eager load counts for display in the table
                     ->withCount(['questions', 'attempts']);
        
        // 2. Filter based on publication status and user role
        // Show published quizzes to everyone
        // Show draft quizzes only to their creator
        if (Auth::check() && Auth::user()->role === 'teacher') {
            // Teachers see published quizzes + their own draft quizzes
            $query->where(function ($q) {
                $q->where('is_published', true)
                  ->orWhere(function ($q2) {
                      $q2->where('is_published', false)
                         ->where('teacher_id', Auth::id());
                  });
            });
        } else {
            // Non-authenticated or non-teacher users see only published quizzes
            $query->where('is_published', true);
        }
        
        // 3. Apply Filtering Logic (Copy from your index.blade.php assumptions)
        
        // Filter by Scope (Mine/All) - Only for teachers viewing their own quizzes
        if (Auth::check() && Auth::user()->role === 'teacher' && ($filters['scope'] ?? 'all') === 'mine') {
            $query->where('teacher_id', Auth::id());
        }

        // Filter by Unique ID
        if (!empty($filters['unique_id'])) {
            $query->where('unique_code', 'like', '%' . $filters['unique_id'] . '%');
        }

        // Filter by Title Keyword
        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        // Filter by Creator Email
        if (!empty($filters['creator_email'])) {
             // Assuming User model has a 'createdQuizzes' relationship for this to work well
             $query->whereHas('creator', function ($q) use ($filters) {
                $q->where('email', 'like', '%' . $filters['creator_email'] . '%');
             });
        }
        
        // Filter by Publish Date Range (Basic example based on your blade code)
        if (!empty($filters['publish_date_range'])) {
            switch ($filters['publish_date_range']) {
                case 'today':
                    $query->whereDate('created_at', now()->today());
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month);
                    break;
                // Add more date range logic as needed...
            }
        }


        // 4. Get limit from request (default 10, increments by 10)
        $limit = (int) $request->get('limit', 10);
        if ($limit < 10) $limit = 10;
        if ($limit > 1000) $limit = 1000; // Safety limit
        
        // 5. Get all quizzes with the specified limit
        $allQuizzes = $query->latest('created_at')->get();
        $quizzes = $allQuizzes->take($limit);
        $hasMore = count($allQuizzes) > $limit;
        $nextLimit = $limit + 10;
        
        // Ensure you return the view that corresponds to the 'teacher.quizzes.index' route
        return view('quiz.index-teacher', compact('quizzes', 'filters', 'limit', 'hasMore', 'nextLimit'));
    }


    /** 
     * Show the form for creating a new quiz.
     * This method is called by the route 'teacher.quizzes.create'.
     */
    public function create()
    {
        // This is the simplest possible implementation.
        return view('quiz.create');
    }


    /**
     * Store a newly created quiz in storage.
     */
    public function store(Request $request)
    {
        // --- PART 1: Quiz Header Validation ---
        $validatedQuizData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_attempts' => 'required|integer|min:1',
            'due_at' => 'nullable|date',
        ]);

        // Automatically set the necessary fields
        $validatedQuizData['teacher_id'] = Auth::id();
        $validatedQuizData['user_id'] = Auth::id();
        $validatedQuizData['is_published'] = $request->has('is_published');
        // Generate a unique code (using Str::random is a common, simple approach)
        $validatedQuizData['unique_code'] = \Illuminate\Support\Str::random(8); 
        
        // Start DB Transaction to ensure atomicity
        DB::beginTransaction();

        try {
            // 1. Create the Quiz Header
            $quiz = Quiz::create($validatedQuizData);

            // --- PART 2: Save Questions and Options ---
            if ($request->has('questions') && is_array($request->input('questions'))) {
                
                $requestQuestions = $request->input('questions');
                
                foreach ($requestQuestions as $questionData) {
                    
                    // Use the corrected key: 'question_text'
                    if (empty($questionData['question_text'])) {
                        continue; 
                    }
                    
                    $questionType = $questionData['type'] ?? QuizQuestion::TYPE_MULTIPLE_CHOICE;
                    
                    // 2a. Create the NEW Question record
                    $question = $quiz->questions()->create([
                        'question_text' => $questionData['question_text'],
                        'type' => $questionType, 
                        'points' => $questionData['points'] ?? 1,          
                        'correct_answer' => ($questionType === QuizQuestion::TYPE_SHORT_ANSWER) 
                                            ? ($questionData['correct_answer'] ?? null)
                                            : null,
                        'coding_template' => ($questionType === 'coding')
                                            ? ($questionData['coding_template'] ?? null)
                                            : null,
                        'coding_full_code' => ($questionType === 'coding')
                                            ? ($questionData['coding_full_code'] ?? null)
                                            : null,
                        'coding_language' => ($questionType === 'coding')
                                            ? ($questionData['coding_language'] ?? 'java')
                                            : null,
                        'coding_expected_output' => ($questionType === 'coding')
                                                    ? ($questionData['coding_expected_output'] ?? null)
                                                    : null,
                        'hidden_line_numbers' => ($questionType === 'coding')
                                                    ? ($questionData['hidden_line_numbers'] ?? null)
                                                    : null,
                    ]);

                    // 2b. Handle Options for Multiple Choice/Checkbox Questions
                    if (in_array($questionType, [QuizQuestion::TYPE_MULTIPLE_CHOICE, QuizQuestion::TYPE_CHECKBOX, QuizQuestion::TYPE_TRUE_FALSE])) {
                        
                        $optionsData = $questionData['options'] ?? [];
                        // Handle both 'correct_answer' (radio) and 'correct_answers' (checkbox array)
                        $correctAnswers = $questionData['correct_answers'] ?? $questionData['correct_answer'] ?? [];
                        
                        if (!is_array($correctAnswers)) {
                            $correctAnswers = [$correctAnswers]; 
                        }
                        
                        $sortOrder = 1;
                        foreach ($optionsData as $optionText) {
                            if (empty($optionText)) continue;
                            
                            $isCorrect = in_array($optionText, $correctAnswers);

                            $question->options()->create([
                                'option_text' => $optionText,
                                'is_correct' => $isCorrect,
                                'sort_order' => $sortOrder++,
                            ]);
                        }
                    } 
                }
            } else {
                // You may want to add validation here to ensure at least one question is submitted
                // throw new \Exception('A quiz must contain at least one question.'); 
            }

            DB::commit();

            // --- PART 3: Redirect ---
            return redirect()->route('teacher.quizzes.index')
                ->with('success', 'Quiz "' . $quiz->title . '" created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Re-throw with message to show error back to user (or log)
            throw ValidationException::withMessages(['general' => 'Failed to create quiz. Ensure all question fields are correct. Details: ' . $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified quiz.
     */
    public function edit(Quiz $quiz)
    {
        // FIX: Replaced redundant manual check with policy call
        $this->authorize('update', $quiz);
            
        // Load necessary relationships for the form
        // NOTE: We load 'options' so they are available in the Blade's @json($quiz->questions)
        $quiz->load('questions.options');
        
        return view('quiz.edit', compact('quiz'));
    }


    /**
     * Update the specified quiz in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        // FIX: Policy check removes redundancy
        $this->authorize('update', $quiz);

        // --- PART 1: Quiz Header Validation and Update ---
        $validatedQuizData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'max_attempts' => 'required|integer|min:1',
            'due_at' => 'nullable|date',
            // No need to validate 'is_published' here
        ]);
        
        // FIX: Correctly handle 'is_published' checkbox
        $validatedQuizData['is_published'] = $request->has('is_published');
        
        // Use DB Transaction to ensure data integrity during delete/re-create
        DB::beginTransaction();

        try {
            // Update the Quiz header record
            $quiz->update($validatedQuizData);

            
            // --- PART 2: Delete Old Questions and Options (CRITICAL) ---
            // Efficiently delete all dependents
            
            // 1. Delete all options first
            $quiz->questions->each(function ($question) {
                $question->options()->delete();
            });
            
            // 2. Delete all questions
            $quiz->questions()->delete();

            
            // --- PART 3: Save New/Updated Questions and Options ---
            
            if ($request->has('questions') && is_array($request->input('questions'))) {
                
                $requestQuestions = $request->input('questions');
                
                // Use a standard foreach; the keys are guaranteed to be sequential after the JS fix.
                foreach ($requestQuestions as $questionData) {
                    
                    // FIX: Use the correct input key 'question_text'
                    if (empty($questionData['question_text'])) {
                        continue; 
                    }
                    
                    $questionType = $questionData['type'] ?? QuizQuestion::TYPE_MULTIPLE_CHOICE;
                    
                    // 3a. Create the NEW Question record
                    $question = $quiz->questions()->create([
                        'question_text' => $questionData['question_text'], // FIX: Corrected key
                        'type' => $questionType, 
                        'points' => $questionData['points'] ?? 1,          
                        // FIX: Pulls 'correct_answer' for Short Answer from the request
                        'correct_answer' => ($questionType === QuizQuestion::TYPE_SHORT_ANSWER) 
                                            ? ($questionData['correct_answer'] ?? null)
                                            : null,
                        'coding_template' => ($questionType === 'coding')
                                            ? ($questionData['coding_template'] ?? null)
                                            : null,
                        'coding_full_code' => ($questionType === 'coding')
                                            ? ($questionData['coding_full_code'] ?? null)
                                            : null,
                        'coding_language' => ($questionType === 'coding')
                                            ? ($questionData['coding_language'] ?? 'java')
                                            : null,
                        'coding_expected_output' => ($questionType === 'coding')
                                                    ? ($questionData['coding_expected_output'] ?? null)
                                                    : null,
                        'hidden_line_numbers' => ($questionType === 'coding')
                                                    ? ($questionData['hidden_line_numbers'] ?? null)
                                                    : null,
                    ]);

                    // 3b. Handle Options for Multiple Choice/Checkbox Questions
                    if (in_array($questionType, [QuizQuestion::TYPE_MULTIPLE_CHOICE, QuizQuestion::TYPE_CHECKBOX, QuizQuestion::TYPE_TRUE_FALSE])) {
                        
                        // The 'options' array now contains only the option text strings due to the Blade fix
                        $optionsData = $questionData['options'] ?? [];
                        
                        // Handle both 'correct_answer' (radio) and 'correct_answers' (checkbox array)
                        $correctAnswers = $questionData['correct_answers'] ?? $questionData['correct_answer'] ?? []; 
                        
                        // Ensure it's an array for consistency
                        if (!is_array($correctAnswers)) {
                            $correctAnswers = [$correctAnswers]; 
                        }
                        
                        $sortOrder = 1;
                        foreach ($optionsData as $optionText) {
                            if (empty($optionText)) continue;
                            
                            // Check if the current option text is in the submitted list of correct answers
                            $isCorrect = in_array($optionText, $correctAnswers);

                            $question->options()->create([
                                'option_text' => $optionText,
                                'is_correct' => $isCorrect,
                                'sort_order' => $sortOrder++,
                            ]);
                        }
                    } 
                }
            }

            DB::commit();

            // --- PART 4: Redirect ---
            return redirect()->route('teacher.quizzes.index')
                ->with('success', 'Quiz "' . $quiz->title . '" updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Optional: Log the error and rethrow the exception or return a specific error message
            throw ValidationException::withMessages(['general' => 'Failed to update quiz due to a database error. Details: ' . $e->getMessage()]);
        }
    }
    

    /**
     * Remove the specified quiz from storage.
     * * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Quiz $quiz)
    {
        // 1. Authorization Check (Ensures only the owner can delete, based on QuizPolicy)
        $this->authorize('delete', $quiz); 

        // Get the quiz title for the success message before it's deleted
        $quizTitle = $quiz->title;

        // Use a DB Transaction to ensure data integrity
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // CRITICAL STEP: Manually delete dependents due to complex relationships.
            // This order is crucial: Answers -> Attempts -> Options -> Questions -> Quiz

            // 1a. Delete all Student Answers related to this quiz's attempts
            // This assumes StudentAnswer has a relationship back through QuizAttempt.
            // If your QuizAttempt model has a cascading delete on its answers, you can skip this.
            // Assuming QuizAttempt does NOT cascade delete answers:
            $attemptIds = $quiz->attempts()->pluck('id');
            if ($attemptIds->isNotEmpty()) {
                \App\Models\QuizAnswer::whereIn('attempt_id', $attemptIds)->delete();
            }

            // 1b. Delete all Quiz Attempts related to this quiz
            $quiz->attempts()->delete();
            
            // 1c. Delete all Options related to this quiz's questions
            // This is complex, but safe: find all options belonging to all questions in this quiz
            $questionIds = $quiz->questions()->pluck('id');
            if ($questionIds->isNotEmpty()) {
                \App\Models\QuizOption::whereIn('question_id', $questionIds)->delete();
            }
            
            // 1d. Delete all Questions related to this quiz
            $quiz->questions()->delete();

            // 1e. Delete the Quiz itself (The header record)
            $quiz->delete();

            \Illuminate\Support\Facades\DB::commit();

            // 2. Redirect with success
            return redirect()->route('teacher.quizzes.index')
                ->with('success', "Quiz \"{$quizTitle}\" and all associated data have been permanently deleted.");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            
            // 3. Redirect with error if transaction failed
            return redirect()->route('teacher.quizzes.index')
                ->with('error', 'Failed to delete the quiz due to a system error. Details: ' . $e->getMessage());
        }
    }
    
    /**
     * Display the specified quiz and its questions for viewing/preview (UC0009-01).
     * This method is called by the route 'teacher.quizzes.show'.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\View\View
     */

    public function show(\App\Models\Quiz $quiz)
    {
        // STEP 1: Calls security! If the policy returns false, the 403 error happens here.
        $this->authorize('view', $quiz); 
        
        // STEP 2: If authorized, prepare and load the data.
        $quiz->load('questions.options'); 

        // STEP 3: Return the page.
        return view('quiz.show', compact('quiz'));
    }

    /**
     * Show quiz results for teacher.
     */
    public function showResults(Quiz $quiz)
    {
        $this->authorize('viewResults', $quiz);
        
        // Load attempts with student and answer data
        $attempts = $quiz->attempts()
            ->with(['student', 'answers'])
            ->get();
        
        // Calculate statistics
        $totalAttempts = $attempts->count();
        $totalPoints = $quiz->questions->sum('points');
        
        // Get best score for each student
        $studentBestScores = [];
        $allScores = [];
        
        foreach ($attempts as $attempt) {
            $studentId = $attempt->student_id;
            
            if (!isset($studentBestScores[$studentId])) {
                $studentBestScores[$studentId] = [
                    'student' => $attempt->student,
                    'best_score' => $attempt->score,
                    'percentage' => $totalPoints > 0 ? round(($attempt->score / $totalPoints) * 100) : 0,
                    'latest_submitted_at' => $attempt->submitted_at,
                    'attempt_count' => 1,
                ];
            } else {
                // Update if this attempt has a better score
                if ($attempt->score > $studentBestScores[$studentId]['best_score']) {
                    $studentBestScores[$studentId]['best_score'] = $attempt->score;
                    $studentBestScores[$studentId]['percentage'] = $totalPoints > 0 ? round(($attempt->score / $totalPoints) * 100) : 0;
                }
                // Update to the latest submission date
                if ($attempt->submitted_at && (!$studentBestScores[$studentId]['latest_submitted_at'] || $attempt->submitted_at > $studentBestScores[$studentId]['latest_submitted_at'])) {
                    $studentBestScores[$studentId]['latest_submitted_at'] = $attempt->submitted_at;
                }
                // Increment attempt count
                $studentBestScores[$studentId]['attempt_count']++;
            }
            
            if ($attempt->score) {
                $allScores[] = $attempt->score;
            }
        }
        
        // Calculate average, highest, and lowest scores
        $average = count($allScores) > 0 ? round(array_sum($allScores) / count($allScores)) : 0;
        $highest = count($allScores) > 0 ? max($allScores) : 0;
        $lowest = count($allScores) > 0 ? min($allScores) : 0;
        
        $statistics = [
            'total_attempts' => $totalAttempts,
            'total_students' => count($studentBestScores),
            'average' => $totalPoints > 0 ? round(($average / $totalPoints) * 100) : 0,
            'highest' => $totalPoints > 0 ? round(($highest / $totalPoints) * 100) : 0,
            'lowest' => $totalPoints > 0 ? round(($lowest / $totalPoints) * 100) : 0,
        ];
        
        return view('quiz.result-teacher', compact('quiz', 'attempts', 'studentBestScores', 'statistics', 'totalPoints'));
    }

}