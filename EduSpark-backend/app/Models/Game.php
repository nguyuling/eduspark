<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug', 
        'description',
        'topic',
        'game_type',
        'difficulty',
        'game_data'
    ];

    // Relationship with game scores
    public function scores()
    {
        return $this->hasMany(GameScore::class);
    }
}