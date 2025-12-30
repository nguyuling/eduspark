@extends('layouts.app')

@section('content')
<style>
    /* Force light backgrounds and dark text for readability */
    .result-container * {
        color: #000 !important;
    }
    .result-container .text-white {
        color: #fff !important;
    }
    .reward-card {
        background: #ffffff !important;
        border: 4px solid #fbbf24 !important;
    }
</style>

<div class="result-container container mx-auto px-6 py-8" style="background: #f3f4f6; min-height: 100vh;">
    <div class="max-w-4xl mx-auto">
        <!-- Success Banner -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 16px; padding: 48px; margin-bottom: 32px; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.3); border: 6px solid #34d399;">
            <div style="font-size: 80px; margin-bottom: 16px; animation: bounce 1s infinite;">🎉</div>
            <h1 style="font-size: 48px; font-weight: 900; margin-bottom: 12px; color: #ffffff !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Permainan Selesai!</h1>
            <p style="font-size: 28px; font-weight: 700; color: #ffffff !important;">Tahniah {{ auth()->user()->name }}!</p>
        </div>

        <!-- Game Summary Card -->
        <div style="background: #ffffff; border-radius: 16px; border: 6px solid #e5e7eb; padding: 48px; margin-bottom: 32px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <h2 style="font-size: 36px; font-weight: 900; color: #000000 !important; margin-bottom: 32px; display: flex; align-items: center;">
                <span style="margin-right: 16px; font-size: 48px;">📊</span> 
                <span style="color: #000000 !important;">{{ $result['game_title'] }} - Ringkasan Permainan</span>
            </h2>
            
            <!-- Main Stats Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 48px;">
                <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 16px; padding: 32px; text-align: center; box-shadow: 0 8px 16px rgba(0,0,0,0.1); border: 6px solid #3b82f6;">
                    <div style="font-size: 64px; margin-bottom: 16px;">🏆</div>
                    <div style="font-size: 16px; color: #1e40af !important; font-weight: 900; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 2px;">Skor Akhir</div>
                    <div style="font-size: 72px; font-weight: 900; color: #1e3a8a !important; line-height: 1;">{{ number_format($result['score']) }}</div>
                    <div style="font-size: 20px; color: #1e40af !important; margin-top: 12px; font-weight: 800;">mata</div>
                </div>
                
                <div style="background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%); border-radius: 16px; padding: 32px; text-align: center; box-shadow: 0 8px 16px rgba(0,0,0,0.1); border: 6px solid #a855f7;">
                    <div style="font-size: 64px; margin-bottom: 16px;">⏱️</div>
                    <div style="font-size: 16px; color: #6b21a8 !important; font-weight: 900; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 2px;">Masa Diambil</div>
                    <div style="font-size: 72px; font-weight: 900; color: #581c87 !important; line-height: 1;">{{ gmdate('i:s', $result['time_taken']) }}</div>
                    <div style="font-size: 20px; color: #6b21a8 !important; margin-top: 12px; font-weight: 800;">minit</div>
                </div>

                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 16px; padding: 32px; text-align: center; box-shadow: 0 8px 16px rgba(0,0,0,0.1); border: 6px solid #f59e0b;">
                    <div style="font-size: 64px; margin-bottom: 16px;">⭐</div>
                    <div style="font-size: 16px; color: #92400e !important; font-weight: 900; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 2px;">Prestasi</div>
                    <div style="font-size: 72px; font-weight: 900; color: #78350f !important; line-height: 1;">
                        @if($result['score'] >= 800) A+
                        @elseif($result['score'] >= 600) A
                        @elseif($result['score'] >= 400) B
                        @elseif($result['score'] >= 200) C
                        @else D
                        @endif
                    </div>
                    <div style="font-size: 20px; color: #92400e !important; margin-top: 12px; font-weight: 800;">Gred</div>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div style="background: #f9fafb; border-radius: 16px; padding: 32px; margin-bottom: 32px; border: 4px solid #d1d5db;">
                <h3 style="font-size: 24px; font-weight: 900; color: #000000 !important; margin-bottom: 24px;">📈 Pecahan Prestasi</h3>
                <div style="margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; font-size: 18px; margin-bottom: 12px;">
                        <span style="color: #000000 !important; font-weight: 900;">Pencapaian Skor</span>
                        <span style="color: #000000 !important; font-weight: 900; font-size: 24px;">{{ min(100, round(($result['score'] / 1000) * 100)) }}%</span>
                    </div>
                    <div style="width: 100%; background: #d1d5db; border-radius: 12px; height: 32px; border: 3px solid #9ca3af; overflow: hidden;">
                        <div style="background: linear-gradient(90deg, #3b82f6, #2563eb); height: 100%; border-radius: 8px; transition: width 0.5s ease; width: {{ min(100, round(($result['score'] / 1000) * 100)) }}%;"></div>
                    </div>
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 18px; margin-bottom: 12px;">
                        <span style="color: #000000 !important; font-weight: 900;">Penarafan Kelajuan</span>
                        <span style="color: #000000 !important; font-weight: 900; font-size: 24px;">
                            @if($result['time_taken'] <= 60) Cemerlang
                            @elseif($result['time_taken'] <= 120) Baik
                            @elseif($result['time_taken'] <= 180) Sederhana
                            @else Perlu Diperbaiki
                            @endif
                        </span>
                    </div>
                    <div style="width: 100%; background: #d1d5db; border-radius: 12px; height: 32px; border: 3px solid #9ca3af; overflow: hidden;">
                        <div style="background: linear-gradient(90deg, #a855f7, #9333ea); height: 100%; border-radius: 8px; transition: width 0.5s ease; width: {{ max(20, 100 - ($result['time_taken'] / 3)) }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rewards Section -->
        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 16px; border: 8px solid #f59e0b; padding: 48px; margin-bottom: 32px; box-shadow: 0 20px 40px rgba(0,0,0,0.3);">
            <h3 style="font-size: 32px; font-weight: 900; color: #78350f !important; margin-bottom: 24px; display: flex; align-items: center;">
                <span style="margin-right: 16px; font-size: 48px;">🎁</span> 
                <span style="color: #78350f !important;">Ganjaran Diperoleh</span>
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div class="reward-card" style="background: #ffffff; border-radius: 16px; padding: 24px; text-align: center; border: 6px solid #f59e0b; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
                    <div style="font-size: 56px; margin-bottom: 12px;">🏅</div>
                    <div style="font-size: 14px; color: #000000 !important; font-weight: 900; margin-bottom: 8px;">Mata XP</div>
                    <div style="font-size: 36px; font-weight: 900; color: #f59e0b !important;">+{{ round($result['score'] / 10) }}</div>
                </div>
                <div class="reward-card" style="background: #ffffff; border-radius: 16px; padding: 24px; text-align: center; border: 6px solid #f59e0b; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
                    <div style="font-size: 56px; margin-bottom: 12px;">💰</div>
                    <div style="font-size: 14px; color: #000000 !important; font-weight: 900; margin-bottom: 8px;">Syiling</div>
                    <div style="font-size: 36px; font-weight: 900; color: #f59e0b !important;">+{{ round($result['score'] / 20) }}</div>
                </div>
                @if($result['score'] >= 500)
                <div class="reward-card" style="background: #ffffff; border-radius: 16px; padding: 24px; text-align: center; border: 6px solid #f59e0b; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
                    <div style="font-size: 56px; margin-bottom: 12px;">⭐</div>
                    <div style="font-size: 14px; color: #000000 !important; font-weight: 900; margin-bottom: 8px;">Pencapaian</div>
                    <div style="font-size: 16px; font-weight: 900; color: #f59e0b !important;">Skor Tinggi!</div>
                </div>
                @endif
                @if($result['time_taken'] <= 60)
                <div class="reward-card" style="background: #ffffff; border-radius: 16px; padding: 24px; text-align: center; border: 6px solid #f59e0b; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
                    <div style="font-size: 56px; margin-bottom: 12px;">⚡</div>
                    <div style="font-size: 14px; color: #000000 !important; font-weight: 900; margin-bottom: 8px;">Bonus Kelajuan</div>
                    <div style="font-size: 16px; font-weight: 900; color: #f59e0b !important;">Pantas Selesai!</div>
                </div>
                @endif
            </div>
            <button onclick="claimRewards()" style="width: 100%; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff !important; font-weight: 900; padding: 24px; border-radius: 16px; font-size: 24px; border: 6px solid #b45309; cursor: pointer; box-shadow: 0 8px 16px rgba(0,0,0,0.3); transition: transform 0.2s;">
                🎁 Tuntut Semua Ganjaran
            </button>
        </div>

        <!-- Action Buttons -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 16px;">
            <a href="{{ route('games.leaderboard', $result['game_id']) }}" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff !important; font-weight: 900; padding: 24px; border-radius: 16px; text-align: center; font-size: 20px; border: 6px solid #1d4ed8; box-shadow: 0 8px 16px rgba(0,0,0,0.3); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                <span style="margin-right: 12px; font-size: 32px;">📊</span> 
                <span style="color: #ffffff !important;">Lihat Papan Pendahulu</span>
            </a>
            <a href="{{ route('games.play', $result['game_id']) }}" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff !important; font-weight: 900; padding: 24px; border-radius: 16px; text-align: center; font-size: 20px; border: 6px solid #047857; box-shadow: 0 8px 16px rgba(0,0,0,0.3); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                <span style="margin-right: 12px; font-size: 32px;">🔄</span> 
                <span style="color: #ffffff !important;">Main Semula</span>
            </a>
        </div>

        <div style="margin-bottom: 32px;">
            <a href="{{ route('games.index') }}" style="display: block; background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: #ffffff !important; font-weight: 900; padding: 20px; border-radius: 16px; text-align: center; font-size: 20px; border: 6px solid #374151; box-shadow: 0 8px 16px rgba(0,0,0,0.3); text-decoration: none;">
                ← Kembali ke Semua Permainan
            </a>
        </div>

        <!-- Tips Card -->
        <div style="background: #dbeafe; border-radius: 16px; border: 6px solid #3b82f6; padding: 32px; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
            <h3 style="font-size: 24px; font-weight: 900; color: #1e3a8a !important; margin-bottom: 20px;">💡 Petua untuk Meningkatkan Prestasi</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="display: flex; align-items: center; margin-bottom: 16px; font-size: 18px;">
                    <span style="color: #10b981 !important; margin-right: 12px; font-size: 24px; font-weight: 900;">✓</span>
                    <span style="color: #1e3a8a !important; font-weight: 700;">Berlatih secara kerap untuk meningkatkan refleks anda</span>
                </li>
                <li style="display: flex; align-items: center; margin-bottom: 16px; font-size: 18px;">
                    <span style="color: #10b981 !important; margin-right: 12px; font-size: 24px; font-weight: 900;">✓</span>
                    <span style="color: #1e3a8a !important; font-weight: 700;">Cuba untuk mengalahkan skor dan masa anda sebelum ini</span>
                </li>
                <li style="display: flex; align-items: center; margin-bottom: 16px; font-size: 18px;">
                    <span style="color: #10b981 !important; margin-right: 12px; font-size: 24px; font-weight: 900;">✓</span>
                    <span style="color: #1e3a8a !important; font-weight: 700;">Selesaikan permainan dengan lebih pantas untuk bonus kelajuan</span>
                </li>
                <li style="display: flex; align-items: center; font-size: 18px;">
                    <span style="color: #10b981 !important; margin-right: 12px; font-size: 24px; font-weight: 900;">✓</span>
                    <span style="color: #1e3a8a !important; font-weight: 700;">Semak papan pendahulu untuk melihat strategi terbaik</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Reward Claimed Notification -->
<div id="rewardNotification" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff; padding: 48px; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); z-index: 9999; text-align: center; border: 8px solid #34d399; min-width: 400px;">
    <div style="font-size: 120px; margin-bottom: 24px; animation: bounce 1s infinite;">🎉</div>
    <h2 style="font-size: 42px; font-weight: 900; margin-bottom: 16px; color: #ffffff !important;">Ganjaran Dituntut!</h2>
    <p style="font-size: 24px; margin-bottom: 32px; color: #ffffff !important; font-weight: 700;">+{{ round($result['score'] / 10) }} XP & +{{ round($result['score'] / 20) }} Syiling</p>
    <button onclick="closeNotification()" style="background: #ffffff; color: #059669 !important; font-weight: 900; padding: 16px 48px; border-radius: 12px; font-size: 20px; border: none; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
        Hebat! 🎊
    </button>
</div>
<div id="rewardOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9998;"></div>

<script>
function claimRewards() {
    document.getElementById('rewardNotification').style.display = 'block';
    document.getElementById('rewardOverlay').style.display = 'block';
}

function closeNotification() {
    document.getElementById('rewardNotification').style.display = 'none';
    document.getElementById('rewardOverlay').style.display = 'none';
}
</script>

<style>
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}
</style>
@endsection
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
