@extends('layouts.app')

@section('content')
<!-- Hidden form for auto-submit -->
<form id="submit-score-form" method="POST" action="{{ route('games.storeResult', $game->id) }}" style="display:none;">
    @csrf
    <input type="hidden" name="score" id="score-input" value="0">
    <input type="hidden" name="time_taken" id="time-input" value="0">
</form>

<script>
    // Flag to indicate we're in play wrapper mode
    window.isPlayWrapperMode = true;
    window.submitGameScore = function(scoreParam, timeParam) {
        const finalScore = scoreParam || window.score || 0;
        const timeInSeconds = timeParam || 0;
        
        document.getElementById('score-input').value = finalScore;
        document.getElementById('time-input').value = timeInSeconds;
        document.getElementById('submit-score-form').submit();
    };
</script>

@include('games.cosmic-defender')
@endsection
