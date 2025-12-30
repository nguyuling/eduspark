@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Success Banner -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-lg p-8 mb-8 text-center text-white shadow-2xl border-4 border-green-400">
            <div class="text-6xl mb-4 animate-bounce">🎉</div>
            <h1 class="text-4xl font-bold mb-2 text-white drop-shadow-lg">Permainan Selesai!</h1>
            <p class="text-xl text-white font-semibold">Tahniah {{ auth()->user()->name }}!</p>
        </div>

        <!-- Game Summary Card -->
        <div class="bg-white dark:bg-gray-900 rounded-lg border-4 border-gray-300 dark:border-gray-600 p-8 mb-6 shadow-2xl">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                <span class="mr-3">📊</span> {{ $result['game_title'] }} - Ringkasan Permainan
            </h2>
            
            <!-- Main Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl p-6 text-center shadow-lg border-4 border-blue-300">
                    <div class="text-5xl mb-3">🏆</div>
                    <div class="text-sm text-blue-900 font-bold mb-2 uppercase tracking-wide">Skor Akhir</div>
                    <div class="text-5xl font-black text-blue-900">{{ number_format($result['score']) }}</div>
                    <div class="text-sm text-blue-800 mt-2 font-bold">mata</div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl p-6 text-center shadow-lg border-4 border-purple-300">
                    <div class="text-5xl mb-3">⏱️</div>
                    <div class="text-sm text-purple-900 font-bold mb-2 uppercase tracking-wide">Masa Diambil</div>
                    <div class="text-5xl font-black text-purple-900">{{ gmdate('i:s', $result['time_taken']) }}</div>
                    <div class="text-sm text-purple-800 mt-2 font-bold">minit</div>
                </div>

                <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl p-6 text-center shadow-lg border-4 border-yellow-300">
                    <div class="text-5xl mb-3">⭐</div>
                    <div class="text-sm text-yellow-900 font-bold mb-2 uppercase tracking-wide">Prestasi</div>
                    <div class="text-5xl font-black text-yellow-900">
                        @if($result['score'] >= 800) A+
                        @elseif($result['score'] >= 600) A
                        @elseif($result['score'] >= 400) B
                        @elseif($result['score'] >= 200) C
                        @else D
                        @endif
                    </div>
                    <div class="text-sm text-yellow-800 mt-2 font-bold">Gred</div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-6 mb-6 border-4 border-gray-300 dark:border-gray-600">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">📈 Pecahan Prestasi</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-900 dark:text-white font-bold">Pencapaian Skor</span>
                            <span class="text-gray-900 dark:text-white font-black">{{ min(100, round(($result['score'] / 1000) * 100)) }}%</span>
                        </div>
                        <div class="w-full bg-gray-300 rounded-full h-4 border-2 border-gray-400">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 h-full rounded-full transition-all duration-500" style="width: {{ min(100, round(($result['score'] / 1000) * 100)) }}%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-900 dark:text-white font-bold">Penarafan Kelajuan</span>
                            <span class="text-gray-900 dark:text-white font-black">
                                @if($result['time_taken'] <= 60) Cemerlang
                                @elseif($result['time_taken'] <= 120) Baik
                                @elseif($result['time_taken'] <= 180) Sederhana
                                @else Perlu Diperbaiki
                                @endif
                            </span>
                        </div>
                        <div class="w-full bg-gray-300 rounded-full h-4 border-2 border-gray-400">
                            <div class="bg-gradient-to-r from-purple-600 to-purple-700 h-full rounded-full transition-all duration-500" 
                                 style="width: {{ max(20, 100 - ($result['time_taken'] / 3)) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rewards Section -->
        <div class="bg-gradient-to-r from-yellow-200 to-orange-200 rounded-lg border-4 border-yellow-400 p-6 mb-6 shadow-2xl">
            <h3 class="text-2xl font-black text-yellow-900 mb-4 flex items-center">
                <span class="mr-2">🎁</span> Ganjaran Diperoleh
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 text-center border-4 border-yellow-500 shadow-lg">
                    <div class="text-4xl mb-2">🏅</div>
                    <div class="text-xs text-gray-900 font-bold">Mata XP</div>
                    <div class="text-2xl font-black text-yellow-700">+{{ round($result['score'] / 10) }}</div>
                </div>
                <div class="bg-white rounded-lg p-4 text-center border-4 border-yellow-500 shadow-lg">
                    <div class="text-4xl mb-2">💰</div>
                    <div class="text-xs text-gray-900 font-bold">Syiling</div>
                    <div class="text-2xl font-black text-yellow-700">+{{ round($result['score'] / 20) }}</div>
                </div>
                @if($result['score'] >= 500)
                <div class="bg-white rounded-lg p-4 text-center border-4 border-yellow-500 shadow-lg">
                    <div class="text-4xl mb-2">⭐</div>
                    <div class="text-xs text-gray-900 font-bold">Pencapaian</div>
                    <div class="text-xs font-black text-yellow-700">Skor Tinggi!</div>
                </div>
                @endif
                @if($result['time_taken'] <= 60)
                <div class="bg-white rounded-lg p-4 text-center border-4 border-yellow-500 shadow-lg">
                    <div class="text-4xl mb-2">⚡</div>
                    <div class="text-xs text-gray-900 font-bold">Bonus Kelajuan</div>
                    <div class="text-xs font-black text-yellow-700">Pantas Selesai!</div>
                </div>
                @endif
            </div>
            <a href="{{ route('games.index') }}" class="block w-full mt-4 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-black py-4 px-6 rounded-lg transition-all transform hover:scale-105 shadow-lg text-center text-lg border-4 border-yellow-600">
                🎁 Tuntut Semua Ganjaran
            </a>
        </div>

        <!-- Action Buttons -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <a href="{{ route('games.leaderboard', $result['game_id']) }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-black py-5 px-6 rounded-lg text-center transition-all transform hover:scale-105 shadow-2xl flex items-center justify-center border-4 border-blue-400 text-lg">
                <span class="mr-2 text-2xl">📊</span> Lihat Papan Pendahulu & Kedudukan Anda
            </a>
            <a href="{{ route('games.play', $result['game_id']) }}" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-black py-5 px-6 rounded-lg text-center transition-all transform hover:scale-105 shadow-2xl flex items-center justify-center border-4 border-green-400 text-lg">
                <span class="mr-2 text-2xl">🔄</span> Main Semula
            </a>
        </div>

        <div class="flex gap-4">
            <a href="{{ route('games.index') }}" class="flex-1 bg-gray-700 hover:bg-gray-800 text-white font-black py-4 px-6 rounded-lg text-center transition-all border-4 border-gray-500 shadow-lg text-lg">
                ← Kembali ke Semua Permainan
            </a>
        </div>

        <!-- Tips Card -->
        <div class="bg-blue-100 dark:bg-blue-900 rounded-lg border-4 border-blue-300 dark:border-blue-600 p-6 mt-6 shadow-lg">
            <h3 class="text-xl font-black text-blue-900 dark:text-white mb-3">💡 Petua untuk Meningkatkan Prestasi</h3>
            <ul class="text-blue-900 dark:text-blue-100 space-y-2 font-bold">
                <li class="flex items-center"><span class="mr-2 text-green-600">✓</span> Berlatih secara kerap untuk meningkatkan refleks anda</li>
                <li class="flex items-center"><span class="mr-2 text-green-600">✓</span> Cuba untuk mengalahkan skor dan masa anda sebelum ini</li>
                <li class="flex items-center"><span class="mr-2 text-green-600">✓</span> Selesaikan permainan dengan lebih pantas untuk bonus kelajuan</li>
                <li class="flex items-center"><span class="mr-2 text-green-600">✓</span> Semak papan pendahulu untuk melihat strategi terbaik</li>
            </ul>
        </div>
    </div>
</div>
@endsection
