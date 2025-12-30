<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_id', 
        'score',
        'time_taken',
        'game_stats'
    ];

    // Relationship with game
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    // Relationship with user (for future)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}