<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "=== DEBUGGING MAX_SCORE ISSUE ===\n\n";
    
    // Check questions with points
    echo "1. Questions with points > 0:\n";
    $questionsWithPoints = DB::table('questions')->where('points', '>', 0)->count();
    echo "   Count: $questionsWithPoints\n";
    
    echo "\n2. Questions with points = 0:\n";
    $questionsZeroPoints = DB::table('questions')->where('points', '=', 0)->count();
    echo "   Count: $questionsZeroPoints\n";
    
    echo "\n3. Sample questions from imported quizzes:\n";
    $importedQuizzes = [1, 2, 3, 4, 5]; // These are typically the imported ones
    foreach ($importedQuizzes as $qid) {
        $points = DB::table('questions')
            ->where('quiz_id', $qid)
            ->sum('points');
        $count = DB::table('questions')
            ->where('quiz_id', $qid)
            ->count();
        echo "   Quiz $qid: $count questions, total points: $points\n";
    }
    
    echo "\n4. Quiz attempts and their scores:\n";
    $attempts = DB::table('quiz_attempts')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get(['id', 'quiz_id', 'score']);
    
    foreach ($attempts as $att) {
        echo "   Attempt {$att->id}: Quiz {$att->quiz_id}, Score: {$att->score}\n";
    }
    
    echo "\n5. Max score per quiz from attempts:\n";
    $maxScores = DB::table('quiz_attempts')
        ->groupBy('quiz_id')
        ->selectRaw('quiz_id, MAX(score) as max_score')
        ->get();
    
    foreach ($maxScores as $row) {
        echo "   Quiz {$row->quiz_id}: max score = {$row->max_score}\n";
    }
    
    echo "\n6. Check rawScores data being sent to view:\n";
    $studentId = 2;
    $allQuizAttempts = DB::table('quiz_attempts as qa')
        ->where('qa.student_id', $studentId)
        ->whereNotNull('qa.submitted_at')
        ->select('qa.quiz_id', 'qa.score')
        ->orderBy('qa.id', 'desc')
        ->limit(6)
        ->get();
    
    echo "   Found " . count($allQuizAttempts) . " attempts\n";
    foreach ($allQuizAttempts as $att) {
        echo "   Quiz {$att->quiz_id}: {$att->score}\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
?>
