<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // Add these to your existing User model
    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }
    
    public function gameProgress()
    {
        return $this->hasMany(GameProgress::class);
    }
    
    public function rewards()
    {
        return $this->belongsToMany(Reward::class, 'user_rewards')
                    ->withTimestamps()
                    ->withPivot('earned_at', 'metadata');
    }
    
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}