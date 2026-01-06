<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizOption extends Model
{
    protected $table = 'options';
    
    protected $fillable = ['question_id', 'option_text', 'is_correct', 'sort_order'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    // An Option belongs to one Question
    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id', 'id');
    }
}