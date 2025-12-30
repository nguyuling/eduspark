<?php
require __DIR__ . '/../bootstrap/app.php';

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;

// Get all quizzes
$quizzes = Quiz::all();

echo "=== QUIZ CORRECT ANSWERS ===\n\n";

foreach ($quizzes as $quiz) {
    echo "QUIZ: {$quiz->title}\n";
    echo str_repeat("=", 80) . "\n";
    
    $questions = QuizQuestion::where('quiz_id', $quiz->id)->get();
    
    foreach ($questions as $index => $question) {
        echo "\nQ" . ($index + 1) . ": {$question->question_text}\n";
        echo "Type: {$question->type}\n";
        
        if ($question->type === 'true_false') {
            echo "Options: True / False\n";
            $correctOption = QuizOption::where('question_id', $question->id)->where('is_correct', true)->first();
            if ($correctOption) {
                echo "✓ CORRECT ANSWER: {$correctOption->option_text}\n";
            }
        } elseif ($question->type === 'multiple_choice' || $question->type === 'checkbox') {
            $options = QuizOption::where('question_id', $question->id)->orderBy('sort_order')->get();
            echo "Options:\n";
            foreach ($options as $opt) {
                $marker = $opt->is_correct ? "✓ CORRECT" : "✗";
                echo "  " . ($opt->is_correct ? ">" : " ") . " {$opt->option_text} {$marker}\n";
            }
        } elseif ($question->type === 'short_answer') {
            $correctOption = QuizOption::where('question_id', $question->id)->where('is_correct', true)->first();
            if ($correctOption) {
                echo "✓ CORRECT ANSWER: {$correctOption->option_text}\n";
            }
        } elseif ($question->type === 'coding') {
            echo "Expected Output:\n{$question->coding_expected_output}\n";
        }
    }
    
    echo "\n" . str_repeat("-", 80) . "\n\n";
}

echo "\n=== END OF QUIZ ANSWERS ===\n";
