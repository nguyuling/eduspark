<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\User;
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
        // Skip if quizzes already exist (from import or previous seeding)
        if (Quiz::count() > 0) {
            echo "Quizzes already seeded. Skipping.\n";
            return;
        }

        $quizzes_data = include database_path('seeders/QuizQuestionSeeder.php');
        
        // Get a teacher user (role = 'teacher')
        $teacher = User::where('role', 'teacher')->first();
        $teacher_id = $teacher ? $teacher->id : null;
        
        if (!$teacher_id) {
            echo "No teacher found. Skipping quiz seeding.\n";
            return;
        }

        foreach ($quizzes_data as $quiz_data) {
            $questions = $quiz_data['questions'] ?? [];
            $quiz_teacher_id = $quiz_data['teacher_id'] ?? $teacher_id;
            unset($quiz_data['questions']);

            // Create quiz with unique code, using the teacher ID from data or fallback
            $quiz = Quiz::create([
                'user_id' => $quiz_teacher_id,
                'title' => $quiz_data['title'],
                'description' => $quiz_data['description'],
                'teacher_id' => $quiz_teacher_id,
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
                    'teacher_id' => $quiz_teacher_id,
                    'question_text' => $question_data['text'],
                    'type' => $question_data['type'],
                    'points' => $question_data['points'],
                    'coding_template' => $coding_template,
                    'coding_language' => $coding_language,
                    'coding_expected_output' => $coding_expected_output,
                    'correct_answer' => ($question_data['type'] === 'short_answer') ? $correct_text : null,
                ]);

                // Create options
                foreach ($options as $option) {
                    QuizOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'] ?? false,
                    ]);
                }
            }
        }

        echo "Questions seeded successfully!\n";
    }
}
