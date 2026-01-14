<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;

$quizzes = DB::table('quizzes')->limit(7)->get(['id', 'title']);

echo "=== CHECKING FULL MARKS FOR QUIZZES ===\n\n";

foreach ($quizzes as $q) {
    $points = DB::table('questions')->where('quiz_id', $q->id)->sum('points');
    $maxAttempt = DB::table('quiz_attempts')->where('quiz_id', $q->id)->max('score');
    $countQuestions = DB::table('questions')->where('quiz_id', $q->id)->count();
    
    echo "Quiz {$q->id}: {$q->title}\n";
    echo "  Questions: $countQuestions\n";
    echo "  Question points sum: $points\n";
    echo "  Max attempt score: $maxAttempt\n";
    echo "\n";
}
?>
