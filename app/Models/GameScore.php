<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameScore extends Model
{
    use HasFactory;

    protected $table = 'game_scores';

    protected $fillable = [
        'user_id',
        'game_id',
        'score',
        'time_taken',
        'game_stats',
        'completed_at',
    ];

    protected $casts = [
        'game_stats' => 'json',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who played this game
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the game
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
