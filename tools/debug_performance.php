<?php
// Debug performance graph data
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Find all users with quiz attempts
$users = DB::table('quiz_attempts')->select('student_id')->distinct()->get();
echo "=== Users with quiz attempts ===\n";
foreach($users as $u){
    echo "Student ID: {$u->student_id}\n";
}

echo "\n=== All Quiz Attempts (grouped by student) ===\n";
$attempts = DB::table('quiz_attempts')
    ->orderBy('student_id')
    ->orderBy('quiz_id')
    ->orderBy('attempt_number')
    ->get(['id', 'student_id', 'quiz_id', 'score', 'attempt_number', 'submitted_at']);

$currentUser = null;
foreach($attempts as $a){
    if($currentUser !== $a->student_id){
        $currentUser = $a->student_id;
        echo "\n--- Student {$a->student_id} ---\n";
    }
    echo "  Attempt {$a->id}: Quiz {$a->quiz_id}, Score: {$a->score}/{DB::table('questions')->where('quiz_id', $a->quiz_id)->sum('points')}, Attempt #: {$a->attempt_number}, Submitted: {$a->submitted_at}\n";
}

echo "\n=== Quiz Totals ===\n";
$quizzes = DB::table('quizzes')
    ->join('questions', 'quizzes.id', '=', 'questions.quiz_id')
    ->select('quizzes.id', 'quizzes.title', DB::raw('SUM(questions.points) as total_points'))
    ->groupBy('quizzes.id', 'quizzes.title')
    ->get();

foreach($quizzes as $q){
    echo "Quiz {$q->id}: {$q->title} - Total Points: {$q->total_points}\n";
}
