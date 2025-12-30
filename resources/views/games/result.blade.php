@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Success Banner -->
        <div class="bg-gradient-to-r from-green-500 to-emerald-500 dark:from-green-700 dark:to-emerald-700 rounded-lg p-8 mb-8 text-center text-white shadow-lg">
            <div class="text-6xl mb-4 animate-bounce">🎉</div>
            <h1 class="text-4xl font-bold mb-2">Game Completed!</h1>
            <p class="text-xl opacity-90">Congratulations {{ auth()->user()->name }}!</p>
        </div>

        <!-- Game Summary Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8 mb-6 shadow-lg">
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                <span class="mr-3">📊</span> {{ $result['game_title'] }} - Game Summary
            </h2>
            
            <!-- Main Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 rounded-xl p-6 text-center shadow-md border border-blue-200 dark:border-blue-700">
                    <div class="text-blue-600 dark:text-blue-300 text-4xl mb-3">🏆</div>
                    <div class="text-sm text-blue-700 dark:text-blue-300 font-semibold mb-2 uppercase tracking-wide">Final Score</div>
                    <div class="text-5xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($result['score']) }}</div>
                    <div class="text-sm text-blue-600 dark:text-blue-400 mt-2 font-medium">points</div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900 dark:to-purple-800 rounded-xl p-6 text-center shadow-md border border-purple-200 dark:border-purple-700">
                    <div class="text-purple-600 dark:text-purple-300 text-4xl mb-3">⏱️</div>
                    <div class="text-sm text-purple-700 dark:text-purple-300 font-semibold mb-2 uppercase tracking-wide">Time Taken</div>
                    <div class="text-5xl font-bold text-purple-600 dark:text-purple-400">{{ gmdate('i:s', $result['time_taken']) }}</div>
                    <div class="text-sm text-purple-600 dark:text-purple-400 mt-2 font-medium">minutes</div>
                </div>

                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900 dark:to-yellow-800 rounded-xl p-6 text-center shadow-md border border-yellow-200 dark:border-yellow-700">
                    <div class="text-yellow-600 dark:text-yellow-300 text-4xl mb-3">⭐</div>
                    <div class="text-sm text-yellow-700 dark:text-yellow-300 font-semibold mb-2 uppercase tracking-wide">Performance</div>
                    <div class="text-4xl font-bold text-yellow-600 dark:text-yellow-400">
                        @if($result['score'] >= 800) A+
                        @elseif($result['score'] >= 600) A
                        @elseif($result['score'] >= 400) B
                        @elseif($result['score'] >= 200) C
                        @else D
                        @endif
                    </div>
                    <div class="text-sm text-yellow-600 dark:text-yellow-400 mt-2 font-medium">Grade</div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6 border border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">📈 Performance Breakdown</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600 dark:text-gray-300 font-medium">Score Achievement</span>
                            <span class="text-gray-800 dark:text-white font-bold">{{ min(100, round(($result['score'] / 1000) * 100)) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ min(100, round(($result['score'] / 1000) * 100)) }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600 dark:text-gray-300 font-medium">Speed Rating</span>
                            <span class="text-gray-800 dark:text-white font-bold">
                                @if($result['time_taken'] <= 60) Excellent
                                @elseif($result['time_taken'] <= 120) Good
                                @elseif($result['time_taken'] <= 180) Average
                                @else Needs Improvement
                                @endif
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ max(20, 100 - ($result['time_taken'] / 3)) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rewards Section -->
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900 dark:to-orange-900 rounded-lg border border-yellow-200 dark:border-yellow-700 p-6 mb-6 shadow-lg">
            <h3 class="text-xl font-bold text-yellow-800 dark:text-yellow-200 mb-4 flex items-center">
                <span class="mr-2">🎁</span> Rewards Earned
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center border border-yellow-300 dark:border-yellow-700">
                    <div class="text-3xl mb-2">🏅</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">XP Earned</div>
                    <div class="text-lg font-bold text-yellow-600 dark:text-yellow-400">+{{ round($result['score'] / 10) }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center border border-yellow-300 dark:border-yellow-700">
                    <div class="text-3xl mb-2">💰</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Coins</div>
                    <div class="text-lg font-bold text-yellow-600 dark:text-yellow-400">+{{ round($result['score'] / 20) }}</div>
                </div>
                @if($result['score'] >= 500)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center border border-yellow-300 dark:border-yellow-700">
                    <div class="text-3xl mb-2">⭐</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Achievement</div>
                    <div class="text-xs font-bold text-yellow-600 dark:text-yellow-400">High Score!</div>
                </div>
                @endif
                @if($result['time_taken'] <= 60)
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center border border-yellow-300 dark:border-yellow-700">
                    <div class="text-3xl mb-2">⚡</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Speed Bonus</div>
                    <div class="text-xs font-bold text-yellow-600 dark:text-yellow-400">Fast Clear!</div>
                </div>
                @endif
            </div>
            <button class="w-full mt-4 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-lg transition-all transform hover:scale-105 shadow-md">
                🎁 Collect All Rewards
            </button>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <a href="{{ route('games.leaderboard', $result['game_id']) }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-4 px-6 rounded-lg text-center transition-all transform hover:scale-105 shadow-lg flex items-center justify-center">
                <span class="mr-2">📊</span> View Leaderboard & Your Rank
            </a>
            <a href="{{ route('games.play', $result['game_id']) }}" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-4 px-6 rounded-lg text-center transition-all transform hover:scale-105 shadow-lg flex items-center justify-center">
                <span class="mr-2">🔄</span> Play Again
            </a>
        </div>

        <div class="flex gap-4">
            <a href="{{ route('games.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-all">
                ← Back to All Games
            </a>
        </div>

        <!-- Tips Card -->
        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700 p-6 mt-6">
            <h3 class="text-lg font-bold text-blue-800 dark:text-blue-200 mb-3">💡 Tips to Improve</h3>
            <ul class="text-blue-700 dark:text-blue-300 space-y-2 text-sm">
                <li class="flex items-center"><span class="mr-2">✓</span> Practice regularly to improve your reflexes</li>
                <li class="flex items-center"><span class="mr-2">✓</span> Try to beat your previous score and time</li>
                <li class="flex items-center"><span class="mr-2">✓</span> Complete games faster to earn speed bonuses</li>
                <li class="flex items-center"><span class="mr-2">✓</span> Check the leaderboard to see top strategies</li>
            </ul>
        </div>
    </div>
</div>
@endsection
