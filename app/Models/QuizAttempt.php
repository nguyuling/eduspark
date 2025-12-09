<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'quiz_id', 
        'student_id', 
        'attempt_number', 
        'score', 
        'started_at', 
        'submitted_at', 
        'teacher_remark'
    ];
    
    protected $dates = ['started_at', 'submitted_at'];

    // Define custom casting for attribute types
    protected $casts = [
        'submitted_at' => 'datetime', 
        'started_at' => 'datetime',
        'score' => 'integer', // Ensures score is treated as an integer
    ];


    public function quiz(): BelongsTo
    {
        // Links the attempt to the Quiz model
        return $this->belongsTo(Quiz::class);
    }

    public function student(): BelongsTo
    {
        // Links the attempt to the User model (assuming students are users)
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers(): HasMany
    {
        // Links the attempt to all submitted StudentAnswer records
        return $this->hasMany(StudentAnswer::class, 'attempt_id');
    }
}