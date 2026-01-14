<?php
require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== QUIZ MODULE AUDIT ===\n\n";

// 1. Check questions
$totalQuestions = DB::table('questions')->count();
echo "1. QUESTIONS: $totalQuestions questions imported\n";

// 2. Check options
$totalOptions = DB::table('options')->count();
echo "2. OPTIONS: $totalOptions options imported\n";

if ($totalOptions === 0) {
    echo "   ⚠️  CRITICAL: NO OPTIONS! Multiple choice questions won't display answers\n";
}

// 3. Check question types breakdown
echo "\n3. QUESTION TYPES:\n";
$types = DB::table('questions')
    ->select('type', DB::raw('count(*) as count'))
    ->groupBy('type')
    ->get();

foreach ($types as $t) {
    echo "   - {$t->type}: {$t->count} questions\n";
}

// 4. Check options per question - without having clause
echo "\n4. OPTIONS PER QUESTION:\n";
$multipleChoiceQuestions = DB::table('questions')
    ->whereIn('type', ['multiple_choice', 'checkbox', 'true_false'])
    ->count();

echo "   Total multiple choice/checkbox/true_false: $multipleChoiceQuestions\n";
echo "   Total options in database: $totalOptions\n";

if ($totalOptions === 0 && $multipleChoiceQuestions > 0) {
    echo "   ⚠️  MISSING: {$multipleChoiceQuestions} MC questions need options!\n";
}

// 5. Check quiz attempts
echo "\n5. QUIZ ATTEMPTS:\n";
$attempts = DB::table('quiz_attempts')->count();
echo "   Total attempts: $attempts\n";

// 6. Check student answers
echo "\n6. STUDENT ANSWERS:\n";
$answers = DB::table('student_answers')->count();
echo "   Total answers saved: $answers\n";

// 7. Check controllers exist
echo "\n7. CONTROLLERS:\n";
$controllers = [
    'QuizStudentController' => 'app/Http/Controllers/QuizStudentController.php',
    'QuizAttemptController' => 'app/Http/Controllers/QuizAttemptController.php',
];

foreach ($controllers as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "   ✓ $name exists\n";
    } else {
        echo "   ⚠️  $name MISSING\n";
    }
}

// 8. Check views exist
echo "\n8. VIEWS:\n";
$views = [
    'Quiz List' => 'resources/views/quiz/index-student.blade.php',
    'Quiz Attempt' => 'resources/views/quiz/attempt.blade.php',
    'Quiz Results' => 'resources/views/quiz/results.blade.php',
];

foreach ($views as $name => $path) {
    if (file_exists(__DIR__ . '/' . $path)) {
        echo "   ✓ $name view exists\n";
    } else {
        echo "   ⚠️  $name view MISSING - may not display correctly\n";
    }
}

// 9. Check database tables
echo "\n9. DATABASE TABLES:\n";
$tables = ['questions', 'options', 'quiz_attempts', 'student_answers', 'student_answer_options'];
foreach ($tables as $table) {
    $exists = DB::connection()->getSchemaBuilder()->hasTable($table);
    $count = DB::table($table)->count();
    echo "   - $table: " . ($exists ? "✓ exists" : "✗ MISSING") . " ($count records)\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "SUMMARY:\n";
echo "  Questions: $totalQuestions ✓\n";
echo "  Options: $totalOptions " . ($totalOptions === 0 ? "✗ CRITICAL ISSUE" : "✓") . "\n";
echo "  Attempts: $attempts\n";
echo "  Student Answers: $answers\n";
echo str_repeat("=", 60) . "\n";
