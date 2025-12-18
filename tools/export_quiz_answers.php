<?php
// Export a concise answer key for all quizzes
// Usage: php tools/export_quiz_answers.php

use Illuminate\Database\Capsule\Manager as DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function line($s = ''){ echo $s . PHP_EOL; }

// Models
class Quiz extends Illuminate\Database\Eloquent\Model {
    protected $table = 'quizzes';
    public function questions(){
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }
}
class QuizQuestion extends Illuminate\Database\Eloquent\Model {
    protected $table = 'questions';
    public function options(){
        return $this->hasMany(QuizOption::class, 'question_id');
    }
}
class QuizOption extends Illuminate\Database\Eloquent\Model {
    protected $table = 'options';
}

$quizzes = Quiz::with(['questions' => function($q){
    $q->orderBy('id');
}, 'questions.options' => function($q){
    $q->orderBy('id');
}])->orderBy('id')->get();

if($quizzes->isEmpty()){
    line('No quizzes found.');
    exit(0);
}

foreach($quizzes as $quiz){
    line("=== Quiz: {$quiz->title} (ID: {$quiz->id}) ===");
    foreach($quiz->questions as $idx => $question){
        $num = $idx + 1;
        $type = $question->type ?? 'multiple_choice';
        line("Q{$num}. {$question->question} [{$type}]");
        if(in_array($type, ['multiple_choice','true_false'])){
            // single correct option
            $correct = $question->options->firstWhere('is_correct', 1);
            if($correct){
                line("- Answer: " . ($correct->option_text ?? $correct->text ?? '[missing text]'));
            } else {
                line("- Answer: [no correct option flagged]");
            }
        } elseif($type === 'checkbox'){
            // multiple correct options
            $correctOpts = $question->options->where('is_correct', 1);
            if($correctOpts->isEmpty()){
                line("- Answers: [no correct options flagged]");
            } else {
                $list = $correctOpts->map(function($o){ return $o->option_text ?? $o->text ?? '[missing text]'; })->implode(', ');
                line("- Answers: {$list}");
            }
        } elseif($type === 'short_answer'){
            // use option marked correct or expected_answer field if exists
            $correct = $question->options->firstWhere('is_correct', 1);
            $expected = $question->expected_answer ?? null;
            if($expected){
                line("- Answer: {$expected}");
            } elseif($correct){
                line("- Answer: " . ($correct->option_text ?? $correct->text ?? '[missing text]'));
            } else {
                line("- Answer: [free text; no canonical answer stored]");
            }
        } else {
            // fallback
            $correct = $question->options->firstWhere('is_correct', 1);
            if($correct){
                line("- Answer: " . ($correct->option_text ?? $correct->text ?? '[missing text]'));
            } else {
                line("- Answer: [unknown type]");
            }
        }
    }
    line();
}

line('✓ Export complete.');
