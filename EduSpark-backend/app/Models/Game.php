<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
    public function gameSessions()
    {
        return $this->hasMany(GameSession::class);
    }
    
    public function gameProgress()
    {
        return $this->hasMany(GameProgress::class);
    }
}