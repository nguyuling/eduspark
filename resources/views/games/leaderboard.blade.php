@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="mb-8">
        <a href="{{ route('games.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 mb-4 inline-block">← Back to Games</a>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">📊 {{ $game->title }} - Leaderboard</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ auth()->user()->role === 'teacher' ? 'Class performance analytics' : 'Top performers in this game' }}</p>
    </div>

    @if($scores->count() > 0)
        @if(auth()->user()->role === 'teacher')
            <!-- Teacher Analytics View -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-600 dark:text-gray-400 font-semibold text-sm">Total Plays</div>
                    <div class="text-3xl font-bold mt-2 text-gray-800 dark:text-white">{{ $scores->count() }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-600 dark:text-gray-400 font-semibold text-sm">Average Score</div>
                    <div class="text-3xl font-bold mt-2 text-gray-800 dark:text-white">{{ number_format($scores->avg('score'), 0) }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                    <div class="text-gray-600 dark:text-gray-400 font-semibold text-sm">Unique Players</div>
                    <div class="text-3xl font-bold mt-2 text-gray-800 dark:text-white">{{ $scores->groupBy('user_id')->count() }}</div>
                </div>
            </div>
        @else
            <!-- Student View - Show their ranking -->
            @if($highlightedUserIndex !== -1)
            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-8">
                <h3 class="font-bold text-blue-800 dark:text-blue-200 mb-2">🌟 Your Ranking</h3>
                <p class="text-blue-700 dark:text-blue-300">
                    You are ranked <span class="font-bold text-lg">{{ $highlightedUserIndex + 1 }}</span> out of {{ $scores->count() }} players
                </p>
            </div>
            @endif
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Rank</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Player</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Score</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Time Taken</th>
                        @if(auth()->user()->role === 'teacher')
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Attempts</th>
                        @else
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Class</th>
                        @endif
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Completed</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($scores as $index => $score)
                    <tr class="@if(auth()->user()->role !== 'teacher' && $score->user_id === $currentUser->id) bg-yellow-50 dark:bg-yellow-900 font-bold @else hover:bg-gray-50 dark:hover:bg-gray-700 @endif">
                        <td class="px-6 py-4">
                            <span class="font-bold text-lg">
                                @if($index === 0)
                                    🥇 1st
                                @elseif($index === 1)
                                    🥈 2nd
                                @elseif($index === 2)
                                    🥉 3rd
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 dark:text-white">
                                    {{ $score->user->name }}
                                    @if(auth()->user()->role !== 'teacher' && $score->user_id === $currentUser->id)
                                    <span class="ml-2 text-sm">👈 You</span>
                                    @endif
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $score->user->email }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-lg text-blue-600 dark:text-blue-400">{{ $score->score }} pts</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $score->time_taken ? gmdate('H:i:s', $score->time_taken) : 'N/A' }}
                        </td>
                        @if(auth()->user()->role === 'teacher')
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $scores->where('user_id', $score->user_id)->count() }}
                            </td>
                        @else
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 text-xs font-semibold">
                                    {{ $score->user->email }}
                                </span>
                            </td>
                        @endif
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $score->completed_at?->format('M d, Y H:i') ?? 'N/A' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <p class="text-gray-600 dark:text-gray-400 mb-4">No scores yet. Be the first to play!</p>
            <a href="{{ route('games.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                Back to Games
            </a>
        </div>
    @endif
</div>
@endsection
