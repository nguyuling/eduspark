@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-6">
        <a href="{{ route('games.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 mb-4 inline-block">← Back to Games</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="relative">
            <!-- Hidden form for auto-submit -->
            <form id="submit-score-form" method="POST" action="{{ route('games.storeResult', $game->id) }}" style="display:none;">
                @csrf
                <input type="hidden" name="score" id="score-input" value="0">
                <input type="hidden" name="time_taken" id="time-input" value="0">
            </form>

            <script>
                // Flag to indicate we're in play wrapper mode
                window.isPlayWrapperMode = true;
                window.submitGameScore = function(score, timeInSeconds) {
                    document.getElementById('score-input').value = score;
                    document.getElementById('time-input').value = timeInSeconds;
                    document.getElementById('submit-score-form').submit();
                };
            </script>

            <!-- Game Container -->
            <div id="game-container" style="width: 100%; height: 600px; background: #000;">
                @include('games.maze-game')
            </div>
        </div>
    </div>
</div>
@endsection
