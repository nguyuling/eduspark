<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\QuizQuestion; 
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Quiz extends Model
{
    use HasFactory;

    // 1. COMBINE: Use a single, comprehensive list for $fillable
    protected $fillable = [
        'user_id',
        'teacher_id', 
        'title', 
        'description', 
        'max_attempts', 
        'due_at', 
        'is_published',
        'unique_code',
    ];

    // 2. CASTING: Use only the modern $casts property for date and boolean attributes
    protected $casts = [
        'due_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    /**
     * Boot method to automatically generate unique code if not provided
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->unique_code)) {
                // Generate unique 8-character code
                do {
                    $code = Str::random(8);
                } while (static::where('unique_code', $code)->exists());
                
                $model->unique_code = $code;
            }
        });
    }
    
    public function creator(): BelongsTo
    {
        // Assumes the foreign key is 'teacher_id' on the 'quizzes' table
        // and links to the 'id' of the User model.
        return $this->belongsTo(User::class, 'teacher_id');
    }

    protected function totalMarks(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->questions()->sum('points'),
        );
    }

    // --- Relationships ---
    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id', 'id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopeFilter($query, array $filters)
    {
        // Filter by Unique ID
        $query->when($filters['unique_id'] ?? null, function ($query, $id) {
            $query->where('unique_code', 'like', '%' . $id . '%');
        });

        // Filter by Title Keyword
        $query->when($filters['title'] ?? null, function ($query, $title) {
            $query->where('title', 'like', '%' . $title . '%');
        });

        // Filter by Status (Published/Draft)
        $query->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'published') {
                $query->where('is_published', true);
            } elseif ($status === 'draft') {
                $query->where('is_published', false);
            }
        });
        
        // Filter by Due Date Before
        $query->when($filters['due_before'] ?? null, function ($query, $date) {
            $query->where('due_at', '<=', $date);
        });

        // Filter by Scope (Mine/All)
        $query->when($filters['scope'] === 'mine' && Auth::check(), function ($query) {
            $query->where('creator_id', Auth::id());
        });
        
        return $query;
    }
}