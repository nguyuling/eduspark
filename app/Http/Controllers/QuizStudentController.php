<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use App\Models\QuizQuestion; 
use Carbon\Carbon;

class QuizStudentController extends Controller
{
    /**
     * Display a listing of available quizzes with dynamic filtering.
     */
    public function index(Request $request)
    {   
        $studentId = Auth::id();
        $filters = $request->only(keys: ['unique_id', 'creator_email', 'title', 'publish_date', 'attempted']); // Get all filters

        $query = Quiz::where('is_published', true)
                    ->with([
                        // Eager load attempts by the current student
                        'attempts' => function ($query) use ($studentId) {
                            $query->where('student_id', $studentId)
                                  ->whereNotNull('submitted_at'); 
                        }, 
                        'questions', 
                        'creator' // Ensure creator is loaded
                    ]);

        // 1. Filter by Unique ID (Exact Match)
        if (!empty($filters['unique_id'])) {
            $query->where('unique_code', $filters['unique_id']);
        }

        // 2. Filter by Creator Email (Case-insensitive LIKE)
        if (!empty($filters['creator_email'])) {
            $query->whereHas('creator', function (Builder $q) use ($filters) {
                // Using LIKE for partial match, lowercasing both for case-insensitivity
                $q->whereRaw('LOWER(email) LIKE ?', ['%' . strtolower($filters['creator_email']) . '%']);
            });
        }

        // 3. Filter by Title (Case-insensitive LIKE)
        if (!empty($filters['title'])) {
            // Using LIKE for partial match, lowercasing both for case-insensitivity
            $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($filters['title']) . '%']);
        }

        // 4. Filter by Publish Date Range
        if (!empty($filters['publish_date'])) {
            $startDate = null;
            $now = Carbon::now();
            
            switch ($filters['publish_date']) {
                case 'today':
                    $startDate = $now->startOfDay();
                    break;
                case 'this_month':
                    $startDate = $now->startOfMonth();
                    break;
                case '3_months':
                    $startDate = $now->subMonths(3);
                    break;
                case 'this_year':
                    $startDate = $now->startOfYear();
                    break;
            }

            if ($startDate) {
                // We compare the 'created_at' column (assuming this is your publish date)
                $query->where('created_at', '>=', $startDate);
            }
        }
        
        // 5. Role-Specific Filter: Attempted (Student Only)
        if (!empty($filters['attempted'])) {
            $query->whereHas('attempts', function (Builder $q) use ($studentId) {
                $q->where('student_id', $studentId)
                  ->whereNotNull('submitted_at');
            });
        }

        // Execute the filtered query
        $quizzes = $query->orderBy('due_at', 'asc')->get();        
    
        // Pass the quizzes and the filters back to the view
        return view('quiz.index-student', compact('quizzes', 'filters'));
    }

    /**
     * Start a new quiz attempt
     */
    public function start(Quiz $quiz)
    {
        $quiz->load('creator');

        $student = Auth::user();
        
        // 1. Count ONLY SUBMITTED attempts to determine the next attempt number
        $submittedAttempts = $quiz->attempts()
                                ->where('student_id', $student->id)
                                ->whereNotNull('submitted_at')
                                ->count();

        // 2. Check max attempts (based on submitted attempts)
        if ($submittedAttempts >= $quiz->max_attempts) {
            return redirect()->route('student.quizzes.index')->with('error', 'No more attempt for this quiz.');
        }

        // 3. Check deadline (if applicable)
        if ($quiz->due_at && $quiz->due_at->isPast()) {
            return redirect()->route('student.quizzes.index')->with('error', 'This quiz is overdue.');
        }
        
        // 4. Prevent starting a new attempt if an unsubmitted attempt already exists
        $openAttempt = $quiz->attempts()
                            ->where('student_id', $student->id)
                            ->whereNull('submitted_at')
                            ->first();
                            
        if ($openAttempt) {
            $attempt = $openAttempt;
            
            // Load questions/options needed for the attempt view
            $quiz->load('questions.options'); // <--- Ensure this is executed

            // Return view with the existing attempt
            return view('quiz.attempt', compact('quiz', 'attempt')); 
        }

        // 5. Create a new attempt record (if no open attempt exists)
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'attempt_number' => $submittedAttempts + 1, 
            'started_at' => now(),
        ]);

        // Load questions and options
        $quiz->load('questions.options');

        // Return view with the newly created '$attempt' variable
        return view('quiz.attempt', compact('quiz', 'attempt'));
    }

    /**
     * Process and submit the quiz answers
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
                            ->where('student_id', Auth::id())
                            ->whereNull('submitted_at')
                            ->latest()
                            ->firstOrFail();

        $submittedAnswers = $request->input('answers', []);
        $totalScore = 0;

        foreach ($submittedAnswers as $questionId => $studentAnswer) {
            $question = QuizQuestion::with('options')->find($questionId);

            if (!$question) continue;

            $isCorrect = false;
            $scoreGained = 0;
            $submittedText = null; 
            $submittedOptions = [];

            // --- GRADING LOGIC START ---

            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                $correctOptionId = $question->options->where('is_correct', true)->first()->id ?? null;
                
                // Ensure $studentAnswer is treated as a single value for MC/TF
                if ($correctOptionId && (int)$studentAnswer === (int)$correctOptionId) {
                    $isCorrect = true;
                }
                // Store the selected single option ID for sync
                $submittedOptions = [is_array($studentAnswer) ? $studentAnswer[0] : $studentAnswer]; 

            } elseif ($question->type === 'checkbox') {
                // 2. CHECKBOX: Exact match logic
                $submittedOptions = array_map('intval', $studentAnswer); 
                $correctIds = $question->options->where('is_correct', true)->pluck('id')->map('intval')->toArray();

                if (count($submittedOptions) > 0 && 
                    count($submittedOptions) === count($correctIds) && 
                    empty(array_diff($submittedOptions, $correctIds))) 
                {
                    $isCorrect = true;
                }

            } elseif ($question->type === 'short_answer') {
                // Short answer logic remains the same
                $submittedText = $studentAnswer['text'] ?? '';
                $correctText = strtolower(trim($question->options->where('is_correct', true)->first()->option_text ?? ''));

                if (strtolower(trim($submittedText)) === $correctText && $submittedText !== '') {
                    $isCorrect = true;
                }
                $submittedOptions = []; 
            }
            
            // --- SCORING & STORAGE ---
            if ($isCorrect) {
                $scoreGained = $question->points;
            }
            $totalScore += $scoreGained;


            // Use StudentAnswer model for creation
            $answerRecord = QuizAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'score_gained' => $scoreGained,
                'submitted_text' => $submittedText,
            ]);
            
            // Sync selected options (for multiple_choice, checkbox, etc.)
            $answerRecord->options()->sync(array_filter($submittedOptions));
        }

        $attempt->update([
            'score' => $totalScore,
            'submitted_at' => now(),
        ]);
        return redirect()->route('student.quizzes.result', $attempt->id);
    }

    public function quit(QuizAttempt $attempt)
    {
        // 1. Authorization check
        if ($attempt->student_id !== Auth::id() || $attempt->submitted_at) {
            // Prevent unauthorized deletion or deleting a completed attempt
            return redirect()->route('student.quizzes.index')->with('error', 'Cannot quit this attempt.');
        }

        // 2. Delete the attempt (and cascade delete related student_answers)
        $attempt->delete();

        // 3. Redirect back to the quiz list with a success message
        return redirect()->route('student.quizzes.index')->with('success', 'The quiz attempt was successfully abandoned.');
    }

    /**
     * Display the student's own quiz result (UC0012-01).
     */

    public function showResult(QuizAttempt $attempt)
    {
        if ($attempt->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $attempt->load([
            'quiz.questions', 
            'answers.question.options', 
            'answers.selectedOption'
        ]);

        // This view will display: Total Score, Correct answers for wrong questions, Teacher's remark.
        return view('quiz.result-student', compact('attempt'));
    }
}