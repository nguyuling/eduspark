<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizzes_data = include database_path('seeders/QuizQuestionSeeder.php');

        foreach ($quizzes_data as $quiz_data) {
            $questions = $quiz_data['questions'] ?? [];
            unset($quiz_data['questions']);

            // Create quiz with unique code
            $quiz = Quiz::create([
                'user_id' => $quiz_data['teacher_id'],
                'title' => $quiz_data['title'],
                'description' => $quiz_data['description'],
                'teacher_id' => $quiz_data['teacher_id'],
                'max_attempts' => $quiz_data['max_attempts'],
                'due_at' => $quiz_data['due_at'],
                'is_published' => $quiz_data['is_published'],
                'unique_code' => Str::random(8),
            ]);

            // Create questions
            foreach ($questions as $question_data) {
                $options = $question_data['options'] ?? [];
                unset($question_data['options']);
                $correct_text = $question_data['correct_text'] ?? null;
                unset($question_data['correct_text']);
                
                // Extract coding-specific fields
                $coding_template = $question_data['coding_template'] ?? null;
                unset($question_data['coding_template']);
                $coding_language = $question_data['coding_language'] ?? 'java';
                unset($question_data['coding_language']);
                $coding_expected_output = $question_data['coding_expected_output'] ?? null;
                unset($question_data['coding_expected_output']);

                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_text' => $question_data['text'],
                    'type' => $question_data['type'],
                    'points' => $question_data['points'],
                    'coding_template' => $coding_template,
                    'coding_language' => $coding_language,
                    'coding_expected_output' => $coding_expected_output,
                ]);

                // Create options
                foreach ($options as $option) {
                    QuizOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'] ?? false,
                    ]);
                }

                // Handle short_answer correct text
                if ($correct_text && $question->type === 'short_answer') {
                    QuizOption::create([
                        'question_id' => $question->id,
                        'option_text' => $correct_text,
                        'is_correct' => true,
                    ]);
                }
            }
        }

        echo "Questions seeded successfully!\n";
    }
}
