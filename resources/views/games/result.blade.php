@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Success Banner -->
        <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-8 mb-8 text-center">
            <div class="text-6xl mb-4">🎉</div>
            <h1 class="text-3xl font-bold text-green-800 dark:text-green-200 mb-2">Excellent Work!</h1>
            <p class="text-green-700 dark:text-green-300">Your score has been recorded in the leaderboard</p>
        </div>

        <!-- Score Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">{{ $result['game_title'] }} - Results</h2>
            
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-6 text-center">
                    <div class="text-sm text-blue-700 dark:text-blue-300 font-semibold mb-2">Score</div>
                    <div class="text-4xl font-bold text-blue-600 dark:text-blue-400">{{ $result['score'] }}</div>
                    <div class="text-sm text-blue-600 dark:text-blue-400 mt-2">points</div>
                </div>
                
                <div class="bg-purple-50 dark:bg-purple-900 rounded-lg p-6 text-center">
                    <div class="text-sm text-purple-700 dark:text-purple-300 font-semibold mb-2">Time Taken</div>
                    <div class="text-4xl font-bold text-purple-600 dark:text-purple-400">{{ gmdate('H:i:s', $result['time_taken']) }}</div>
                    <div class="text-sm text-purple-600 dark:text-purple-400 mt-2">{{ $result['time_taken'] }} seconds</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3">
                <a href="{{ route('games.leaderboard', $result['game_id']) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center">
                    📊 View Leaderboard & See Your Rank
                </a>
                <a href="{{ route('games.index') }}" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center">
                    ← Back to Games
                </a>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700 p-6">
            <h3 class="text-lg font-bold text-blue-800 dark:text-blue-200 mb-3">💡 Tips for Next Time</h3>
            <ul class="text-blue-700 dark:text-blue-300 space-y-2 text-sm">
                <li>✓ Try to beat your previous score</li>
                <li>✓ Complete games faster to climb the leaderboard</li>
                <li>✓ Play different games to earn more points</li>
            </ul>
        </div>
    </div>
</div>
@endsection
