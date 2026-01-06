<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $fillable = [
        'user_id',
        'game_id',
        'reward_type',
        'reward_name',
        'reward_description',
        'points_awarded',
        'badge_icon',
        'is_claimed',
        'claimed_at',
    ];

    protected $casts = [
        'is_claimed' => 'boolean',
        'claimed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who earned this reward
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the game associated with this reward
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
