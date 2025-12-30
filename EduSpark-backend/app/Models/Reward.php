<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'icon',
        'description',
        'points_required',
        'game_id',
        'level_required',
        'is_active'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_rewards')
                    ->withPivot('earned_at', 'claimed', 'claimed_at')
                    ->withTimestamps();
    }
}