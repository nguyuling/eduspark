<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizQuestion extends Model
{
    protected $table = 'questions';
    
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_SHORT_ANSWER = 'short_answer';
    public const TYPE_TRUE_FALSE = 'true_false';
    public const TYPE_CHECKBOX = 'checkbox';
    protected $fillable = ['quiz_id', 'question_text', 'points', 'type'];
    // A Question belongs to one Quiz
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    // A Question has many Options
    public function options(): HasMany
    {
        return $this->hasMany(QuizOption::class, 'question_id', 'id');
    }
}