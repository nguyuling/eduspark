@extends('layouts.app')

@section('content')
<style>
    #score-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
</style>

@include('games.cosmic-defender')

<!-- Score Submission Overlay -->
<div id="score-overlay">
    <div class="text-center text-white px-6">
        <h2 class="text-5xl font-bold mb-6">🎉 Game Complete!</h2>
        <p class="text-2xl mb-3">Your Score: <span id="final-score" class="text-yellow-400 font-bold">0</span> points</p>
        <p class="text-xl mb-8">Time: <span id="final-time" class="text-blue-400 font-bold">0</span> seconds</p>
        
        <form id="submit-score-form" method="POST" action="{{ route('games.storeResult', $game->id) }}">
            @csrf
            <input type="hidden" name="score" id="score-input" value="0">
            <input type="hidden" name="time_taken" id="time-input" value="0">
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-10 rounded-lg mr-4 text-lg transition-all transform hover:scale-105 shadow-lg">
                ✓ View Results & Leaderboard
            </button>
        </form>
    </div>
</div>

<script>
    // Track game start time
    let gameStartTime = Date.now();
    
    // Wait for the page to fully load, then override endGame
    window.addEventListener('load', function() {
        // Store the original endGame if it exists
        const originalEndGame = window.endGame;
        
        // Override endGame function
        window.endGame = function(scoreParam) {
            console.log('Custom endGame called with score:', scoreParam);
            
            // Get the final score from various possible sources
            const finalScore = scoreParam || window.score || 0;
            const timeInSeconds = Math.floor((Date.now() - gameStartTime) / 1000);
            
            console.log('Final score:', finalScore, 'Time:', timeInSeconds);
            
            // Hide the game's built-in game over screen
            const gameOverScreen = document.getElementById('gameOverScreen');
            if (gameOverScreen) {
                gameOverScreen.style.display = 'none';
                console.log('Hid game over screen');
            }
            
            // Hide canvas and header too
            const canvas = document.getElementById('gameCanvas');
            const header = document.getElementById('gameHeader');
            if (canvas) canvas.style.display = 'none';
            if (header) header.style.display = 'none';
            
            // Show our overlay
            document.getElementById('final-score').textContent = finalScore;
            document.getElementById('final-time').textContent = timeInSeconds;
            document.getElementById('score-input').value = finalScore;
            document.getElementById('time-input').value = timeInSeconds;
            document.getElementById('score-overlay').style.display = 'flex';
            
            console.log('Showed score overlay');
        };
        
        console.log('endGame override installed');
    });
    
    // Also track when startGame is called to reset timer
    setTimeout(function() {
        const startBtn = document.getElementById('startBtn');
        if (startBtn) {
            startBtn.addEventListener('click', function() {
                gameStartTime = Date.now();
                console.log('Game started, timer reset');
            });
        }
    }, 100);
</script>
@endsection
