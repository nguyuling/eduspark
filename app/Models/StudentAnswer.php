<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentAnswer extends Model
{
    protected $fillable = [
    'attempt_id', 
    'question_id', 
    'is_correct', 
    'score_gained', 
    'submitted_text'
];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(Option::class, 'selected_option_id');
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Option::class, 'student_answer_options', 'student_answer_id', 'option_id');
    }
}