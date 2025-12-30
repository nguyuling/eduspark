@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 py-8">
    <!-- Header with solid background -->
    <div class="mb-8 p-6 bg-white dark:bg-gray-900 rounded-lg shadow-sm">
        <a href="{{ route('games.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 mb-4 text-base font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Games
        </a>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $game->title }} - Leaderboard</h1>
        <p class="text-gray-700 dark:text-gray-300 text-base">Class performance analytics</p>
    </div>

    @if($scores->count() > 0)
        <!-- Student Ranking Banner (Only for students) -->
        @if(auth()->user()->role !== 'teacher' && $highlightedUserIndex !== -1)
        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900 dark:to-orange-900 border-2 border-yellow-400 dark:border-yellow-600 rounded-lg p-6 mb-8 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-yellow-800 dark:text-yellow-200 mb-2 flex items-center">
                        <span class="text-3xl mr-3">🌟</span> Your Ranking
                    </h3>
                    <p class="text-yellow-700 dark:text-yellow-300 text-lg">
                        You are ranked <span class="font-bold text-2xl text-yellow-600 dark:text-yellow-400">{{ $highlightedUserIndex + 1 }}</span> out of <span class="font-bold">{{ $scores->count() }}</span> players
                    </p>
                </div>
                <div class="text-6xl">
                    @if($highlightedUserIndex === 0) 🥇
                    @elseif($highlightedUserIndex === 1) 🥈
                    @elseif($highlightedUserIndex === 2) 🥉
                    @else 🎮
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Statistics Cards - White background for light mode, Dark for dark mode -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-300 dark:border-gray-700 shadow">
                <div class="text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">Total Plays</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $scores->count() }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-300 dark:border-gray-700 shadow">
                <div class="text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">Average Score</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($scores->avg('score'), 0) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-5 border border-gray-300 dark:border-gray-700 shadow">
                <div class="text-gray-700 dark:text-gray-300 text-sm font-semibold mb-2">Unique Players</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $scores->groupBy('user_id')->count() }}</div>
            </div>
        </div>

        <!-- Leaderboard Table - Solid backgrounds with clear text -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-700 shadow-lg overflow-hidden">
            <!-- Table Header with solid background -->
            <div class="px-4 sm:px-6 py-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-300 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Leaderboard Rankings</h2>
            </div>
            
            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider border-r border-gray-300 dark:border-gray-700">RANK</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider border-r border-gray-300 dark:border-gray-700">PLAYER</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider border-r border-gray-300 dark:border-gray-700">SCORE</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider border-r border-gray-300 dark:border-gray-700">TIME TAKEN</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider border-r border-gray-300 dark:border-gray-700">ATTEMPTS</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">COMPLETED</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-300 dark:divide-gray-700">
                        @foreach($scores as $index => $score)
                        <tr class="@if(auth()->user()->role !== 'teacher' && isset($currentUser) && $score->user_id === $currentUser->id) bg-yellow-100 dark:bg-yellow-900 font-bold border-2 border-yellow-400 dark:border-yellow-600 @else bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 @endif transition-colors duration-150">
                            <!-- Rank Column -->
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap border-r border-gray-300 dark:border-gray-700">
                                @if($index === 0)
                                    <div class="flex items-center">
                                        <span class="text-xl mr-2">🥇</span>
                                        <span class="font-bold text-gray-900 dark:text-white">1st</span>
                                    </div>
                                @elseif($index === 1)
                                    <div class="flex items-center">
                                        <span class="text-xl mr-2">🥈</span>
                                        <span class="font-bold text-gray-900 dark:text-white">2nd</span>
                                    </div>
                                @elseif($index === 2)
                                    <div class="flex items-center">
                                        <span class="text-xl mr-2">🥉</span>
                                        <span class="font-bold text-gray-900 dark:text-white">3rd</span>
                                    </div>
                                @else
                                    <span class="font-bold text-gray-800 dark:text-gray-300">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            
                            <!-- Player Column -->
                            <td class="px-4 sm:px-6 py-4 border-r border-gray-300 dark:border-gray-700">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">
                                        {{ $score->user->name }}
                                        @if(auth()->user()->role !== 'teacher' && isset($currentUser) && $score->user_id === $currentUser->id)
                                        <span class="ml-2 text-yellow-600 dark:text-yellow-400 text-lg">👈 You</span>
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-700 dark:text-gray-400 mt-1">
                                        {{ $score->user->class ?? 'No Class' }}
                                    </span>
                                </div>
                            </td>
                            
                            <!-- Score Column -->
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap border-r border-gray-300 dark:border-gray-700">
                                <span class="font-bold text-blue-700 dark:text-blue-400 text-sm sm:text-base">
                                    {{ $score->score }} pts
                                </span>
                            </td>
                            
                            <!-- Time Taken Column -->
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap border-r border-gray-300 dark:border-gray-700">
                                <span class="text-sm text-gray-800 dark:text-gray-300 font-medium">
                                    @if($score->time_taken)
                                        @php
                                            $hours = floor($score->time_taken / 3600);
                                            $minutes = floor(($score->time_taken % 3600) / 60);
                                            $seconds = $score->time_taken % 60;
                                        @endphp
                                        {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </td>
                            
                            <!-- Attempts Column -->
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap border-r border-gray-300 dark:border-gray-700">
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-300">
                                    {{ $scores->where('user_id', $score->user_id)->count() }}
                                </span>
                            </td>
                            
                            <!-- Completed Column -->
                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-800 dark:text-gray-300 font-medium">
                                    {{ $score->completed_at?->format('M d, Y H:i') ?? 'N/A' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Table Footer -->
            <div class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-300 dark:border-gray-700">
                <div class="text-sm text-gray-700 dark:text-gray-400">
                    Showing {{ $scores->count() }} {{ Str::plural('entry', $scores->count()) }}
                </div>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-700 p-8 sm:p-12 text-center shadow">
            <div class="text-4xl text-gray-400 dark:text-gray-600 mb-4">📊</div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">No scores yet</h3>
            <p class="text-gray-700 dark:text-gray-400 mb-6">Be the first to play this game!</p>
            <a href="{{ route('games.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Games
            </a>
        </div>
    @endif
</div>

<style>
    /* FORCE HIGH CONTRAST COLORS */
    
    /* Light Mode */
    .bg-white {
        background-color: #ffffff !important;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb !important;
    }
    
    .bg-gray-100 {
        background-color: #f3f4f6 !important;
    }
    
    /* Dark Mode - Brighter backgrounds */
    .dark .bg-gray-800 {
        background-color: #1f2937 !important;
    }
    
    .dark .bg-gray-900 {
        background-color: #111827 !important;
    }
    
    /* Text Colors - Force High Contrast */
    .text-gray-900 {
        color: #111827 !important;
        font-weight: 600;
    }
    
    .text-gray-800 {
        color: #1f2937 !important;
    }
    
    .text-gray-700 {
        color: #374151 !important;
    }
    
    /* Dark Mode Text - Force Lighter Colors */
    .dark .text-white {
        color: #f9fafb !important;
        font-weight: 600;
    }
    
    .dark .text-gray-200 {
        color: #e5e7eb !important;
    }
    
    .dark .text-gray-300 {
        color: #d1d5db !important;
    }
    
    .dark .text-gray-400 {
        color: #9ca3af !important;
    }
    
    /* Borders - Force Visible Borders */
    .border-gray-300 {
        border-color: #d1d5db !important;
    }
    
    .dark .border-gray-700 {
        border-color: #4b5563 !important;
    }
    
    /* Remove any transparency */
    .dark .bg-opacity-50, .bg-opacity-30, .bg-opacity-20 {
        opacity: 1 !important;
    }
    
    /* Table specific */
    th {
        font-weight: 700 !important;
    }
    
    td {
        font-weight: 500;
    }
    
    /* Ensure no text blending */
    * {
        text-shadow: none !important;
    }
</style>
@endsection