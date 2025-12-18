<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Quiz;
use App\Models\QuizQuestion;

$quiz = Quiz::with('questions.options')->first();

if ($quiz) {
    echo "Quiz: {$quiz->title}\n";
    echo "Questions: " . $quiz->questions->count() . "\n\n";
    
    foreach ($quiz->questions as $q) {
        echo "Q{$q->id}: {$q->question_text}\n";
        echo "  Options count: " . $q->options->count() . "\n";
        
        if ($q->options->count() === 0) {
            echo "  ⚠️ NO OPTIONS FOUND!\n";
            // Check if options exist at all for this question
            $directCheck = \App\Models\QuizOption::where('question_id', $q->id)->get();
            echo "  Direct DB query: " . $directCheck->count() . " options\n";
        } else {
            echo "  Options: ";
            echo $q->options->pluck('option_text')->implode(', ') . "\n";
        }
        echo "\n";
    }
} else {
    echo "No quiz found\n";
}
