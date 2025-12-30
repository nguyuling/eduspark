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
    let gameStartTime = Date.now();
    let overrideInstalled = false;
    
    // Aggressively poll and override endGame as soon as it exists
    const installOverride = setInterval(function() {
        if (typeof window.endGame === 'function' && !overrideInstalled) {
            console.log('Found endGame function, installing override...');
            
            // Store original
            const originalEndGame = window.endGame;
            
            // Override it
            window.endGame = function(scoreParam) {
                console.log('CUSTOM endGame triggered! Score:', scoreParam);
                
                const finalScore = scoreParam || window.score || 0;
                const timeInSeconds = Math.floor((Date.now() - gameStartTime) / 1000);
                
                // FORCE hide everything from the game
                setTimeout(function() {
                    const gameOverScreen = document.getElementById('gameOverScreen');
                    const canvas = document.getElementById('gameCanvas');
                    const header = document.getElementById('gameHeader');
                    const startScreen = document.getElementById('startScreen');
                    
                    if (gameOverScreen) gameOverScreen.style.display = 'none !important';
                    if (canvas) canvas.style.display = 'none !important';
                    if (header) header.style.display = 'none !important';
                    if (startScreen) startScreen.style.display = 'none !important';
                    
                    // Update our overlay
                    document.getElementById('final-score').textContent = finalScore;
                    document.getElementById('final-time').textContent = timeInSeconds;
                    document.getElementById('score-input').value = finalScore;
                    document.getElementById('time-input').value = timeInSeconds;
                    document.getElementById('score-overlay').style.display = 'flex';
                    
                    console.log('Score overlay should now be visible!');
                }, 10);
                
                return false; // Don't let original run
            };
            
            overrideInstalled = true;
            clearInterval(installOverride);
            console.log('Override successfully installed!');
        }
    }, 50); // Check every 50ms
    
    // Track game start
    setTimeout(function() {
        const startBtn = document.getElementById('startBtn');
        if (startBtn) {
            startBtn.addEventListener('click', function() {
                gameStartTime = Date.now();
                console.log('Timer reset - game started');
            });
        }
    }, 500);
    
    // Safety timeout - stop checking after 10 seconds
    setTimeout(function() {
        clearInterval(installOverride);
        if (!overrideInstalled) {
            console.error('Failed to install endGame override after 10 seconds');
        }
    }, 10000);
</script>
@endsection
