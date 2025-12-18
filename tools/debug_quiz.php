<?php
require __DIR__ . '/../vendor/autoload.php';

// Load Laravel
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Quiz;
use App\Models\Student;

// Check database quizzes
echo "=== QUIZZES IN DATABASE ===\n";
$quizzes = Quiz::all();
echo "Total quizzes: " . $quizzes->count() . "\n";
foreach ($quizzes as $quiz) {
    echo "Quiz {$quiz->id}: '{$quiz->title}' | published=" . ($quiz->is_published ? 'YES' : 'NO') . " | user_id={$quiz->user_id} | teacher_id={$quiz->teacher_id}\n";
}

echo "\n=== USERS AND STUDENTS ===\n";
$students = User::where('role', 'student')->get();
echo "Total student users: " . $students->count() . "\n";

foreach ($students->take(5) as $user) {
    $student = Student::where('user_id', $user->id)->first();
    if ($student) {
        echo "User {$user->id} ({$user->name}): classroom_id={$student->classroom_id}\n";
    } else {
        echo "User {$user->id} ({$user->name}): NO STUDENT RECORD\n";
    }
}

echo "\n=== CHECKING QUIZ ASSIGNMENT LOGIC ===\n";
echo "The issue: Quizzes are published but they are NOT assigned to any classroom or student.\n";
echo "The quiz table has 'user_id' and 'teacher_id' fields.\n";
echo "user_id likely means the teacher who created it (or should be assigned to students).\n";
echo "There's NO relationship table (quiz_classroom or quiz_student) to assign quizzes to students.\n";
