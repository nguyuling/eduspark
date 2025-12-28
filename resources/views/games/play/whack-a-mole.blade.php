@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-6">
        <a href="{{ route('games.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 mb-4 inline-block">← Back to Games</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="relative">
            <!-- Game Container -->
            <div id="game-container" style="width: 100%; height: 600px; background: #000;">
                @include('games.whack-a-mole')
            </div>

            <!-- Score Overlay (hidden initially, shown on game end) -->
            <div id="score-overlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.9); display: flex; align-items: center; justify-content: center; z-index: 1000;">
                <div class="text-center text-white">
                    <h2 class="text-4xl font-bold mb-4">Game Complete! 🎉</h2>
                    <p class="text-xl mb-2">Your Score: <span id="final-score">0</span> points</p>
                    <p class="text-lg mb-6">Time Taken: <span id="final-time">0</span> seconds</p>
                    
                    <form id="submit-score-form" method="POST" action="{{ route('games.storeResult', $game->id) }}">
                        @csrf
                        <input type="hidden" name="score" id="score-input" value="0">
                        <input type="hidden" name="time_taken" id="time-input" value="0">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg mr-4">
                            ✓ Confirm & View Leaderboard
                        </button>
                    </form>
                    <a href="{{ route('games.index') }}" class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-lg">
                        ← Back to Games
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // This will be populated by the game
    window.gameScore = 0;
    window.gameTime = 0;

    // Function to end the game (called by the game itself)
    window.endGame = function(score, timeInSeconds) {
        window.gameScore = score;
        window.gameTime = timeInSeconds;
        
        document.getElementById('final-score').textContent = score;
        document.getElementById('final-time').textContent = timeInSeconds;
        document.getElementById('score-input').value = score;
        document.getElementById('time-input').value = timeInSeconds;
        
        document.getElementById('score-overlay').style.display = 'flex';
    };
</script>
@endsection
