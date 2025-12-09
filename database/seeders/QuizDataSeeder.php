<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Quiz;
use App\Models\User;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\DB; // Added DB transaction for safety

class QuizDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use transaction for atomic operation
        DB::transaction(function () {
            // 1. Get the Teacher ID
            $teacher = User::where('role', 'teacher')->first();

            if (!$teacher) {
                $this->command->error('No teacher user found. Skipping quiz seeding.');
                return;
            }

            // 2. Load the structured quiz data from the external file
            $quizzesData = require database_path('seeders/data/quiz_data.php');

            foreach ($quizzesData as $quizData) {
                // Extract questions before creating the Quiz header
                $questionsData = $quizData['questions'];
                unset($quizData['questions']);

                // Generate Unique Code only if published and code is needed
                if ($quizData['is_published'] && !isset($quizData['unique_code'])) {
                     do {
                        $code = Str::random(8); 
                    } while (Quiz::where('unique_code', $code)->exists());
                    $quizData['unique_code'] = $code;
                }
                
                // Create the main Quiz record
                // FIX: Now passing the teacher's ID to BOTH required columns: 'user_id' and 'teacher_id'.
                $quiz = Quiz::create(array_merge($quizData, [
                    'user_id' => $teacher->id,      // Links to the general user who created it
                    'teacher_id' => $teacher->id,   // Satisfies the non-nullable 'teacher_id' column
                ]));

                $questionCount = 0;
                // Loop through and create Questions and Options
                foreach ($questionsData as $questionData) {
                    $optionsData = $questionData['options'] ?? [];
                    $correctText = $questionData['correct_text'] ?? null;
                    
                    // Rename 'text' key to 'question_text' for the Question model
                    if (isset($questionData['text'])) {
                        $questionData['question_text'] = $questionData['text'];
                        unset($questionData['text']);
                    }

                    unset($questionData['options']);
                    unset($questionData['correct_text']);

                    // Create the Question record
                    // Using the relationship helper ensures 'quiz_id' is set correctly.
                    $question = $quiz->questions()->create($questionData);
                    
                    // Create the Options
                    if (!empty($optionsData)) {
                        foreach ($optionsData as $optionData) {
                            // Rename 'text' key to 'option_text' for the Option model
                            if (isset($optionData['text'])) {
                                $optionData['option_text'] = $optionData['text'];
                                unset($optionData['text']);
                            }

                            $question->options()->create($optionData);
                        }
                    }
                    
                    // Handle Short Answer Type
                    if ($question->type === 'short_answer' && $correctText) {
                        $question->options()->create([
                            'option_text' => $correctText,
                            'is_correct' => true, 
                        ]);
                    }
                    $questionCount++;
                }
                
                $this->command->info("Quiz '{$quiz->title}' created with {$questionCount} questions.");
            }
        }); // End DB::transaction

        $this->command->info('Quiz data successfully seeded!');
    }
}