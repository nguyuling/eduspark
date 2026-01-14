<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For quizzes that don't have points data, calculate max points from quiz_attempts
        // This ensures old imported data works with the performance dashboard
        
        // Get all quizzes and their current max points
        $quizzes = DB::table('quizzes')->get();
        
        foreach ($quizzes as $quiz) {
            // Sum of points from questions for this quiz
            $questionPointsSum = DB::table('questions')
                ->where('quiz_id', $quiz->id)
                ->sum('points');
            
            // If questions have no points, try to infer from quiz_attempts
            if ($questionPointsSum <= 0) {
                // Get the max score from any quiz_attempt for this quiz
                $maxAttemptScore = DB::table('quiz_attempts')
                    ->where('quiz_id', $quiz->id)
                    ->max('score');
                
                if ($maxAttemptScore && $maxAttemptScore > 0) {
                    // If we found attempts with scores, set question points
                    // Divide the max score by number of questions to get average points per question
                    $questionCount = DB::table('questions')
                        ->where('quiz_id', $quiz->id)
                        ->count();
                    
                    if ($questionCount > 0) {
                        // Set all questions to equal points that sum to max_attempt_score
                        $pointsPerQuestion = intval(ceil($maxAttemptScore / $questionCount));
                        
                        DB::table('questions')
                            ->where('quiz_id', $quiz->id)
                            ->update(['points' => $pointsPerQuestion]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a data fix migration, rollback is not necessary
    }
};
