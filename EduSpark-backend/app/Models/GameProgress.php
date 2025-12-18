<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id',
        'level',
        'score',
        'highest_score',
        'time_spent',
        'attempts',
        'stars',
        'completed',
        'last_played_at',
        'progress_data'
    ];

    protected $casts = [
        'progress_data' => 'array',
        'completed' => 'boolean',
        'last_played_at' => 'datetime'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Calculate accuracy percentage
    public function getAccuracyAttribute()
    {
        if ($this->attempts == 0) return 0;
        return ($this->score / ($this->attempts * 100)) * 100;
    }
}