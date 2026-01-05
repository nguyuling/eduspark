@extends('layouts.app')

@section('content')
<style>
    .result-container * {
        color: #000 !important;
    }
    .result-container .text-white {
        color: #fff !important;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
</style>

<div class="result-container container mx-auto px-6 py-8" style="background: #f3f4f6; min-height: 100vh;">
    <div class="max-w-4xl mx-auto">
        <!-- Success Banner -->
        <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 14px; padding: 25px; margin-bottom: 20px; text-align: center; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18); border: 2px solid #34d399;">
            <div style="font-size: 80px; margin-bottom: 16px; animation: bounce 1s infinite;">🎉</div>
            <h1 style="font-size: 48px; font-weight: 900; margin-bottom: 12px; color: #ffffff !important; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Permainan Selesai!</h1>
            <p style="font-size: 28px; font-weight: 700; color: #ffffff !important;">Tahniah {{ auth()->user()->name }}!</p>
        </div>

        <!-- Game Summary Card -->
        <div style="background: #ffffff; border-radius: 14px; border: 2px solid #e5e7eb; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);">
            <h2 style="font-size: 36px; font-weight: 900; color: #000000 !important; margin-bottom: 32px; display: flex; align-items: center;">
                <span style="margin-right: 16px; font-size: 48px;">📊</span> 
                <span style="color: #000000 !important;">{{ $result['game_title'] }} - Ringkasan Permainan</span>
            </h2>
            
            <!-- Main Stats Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 48px;">
                <div style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-radius: 14px; padding: 25px; text-align: center; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18); border: 2px solid #3b82f6;">
                    <div style="font-size: 64px; margin-bottom: 16px;">🏆</div>
                    <div style="font-size: 16px; color: #1e40af !important; font-weight: 900; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 2px;">Skor Akhir</div>
                    <div style="font-size: 72px; font-weight: 900; color: #1e3a8a !important; line-height: 1;">{{ number_format($result['score']) }}</div>
                    <div style="font-size: 20px; color: #1e40af !important; margin-top: 12px; font-weight: 800;">mata</div>
                </div>
                
                <div style="background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%); border-radius: 14px; padding: 25px; text-align: center; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18); border: 2px solid #a855f7;">
                    <div style="font-size: 64px; margin-bottom: 16px;">⏱️</div>
                    <div style="font-size: 16px; color: #6b21a8 !important; font-weight: 900; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 2px;">Masa Diambil</div>
                    <div style="font-size: 72px; font-weight: 900; color: #581c87 !important; line-height: 1;">{{ gmdate('i:s', $result['time_taken']) }}</div>
                    <div style="font-size: 20px; color: #6b21a8 !important; margin-top: 12px; font-weight: 800;">minit</div>
                </div>

                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 14px; padding: 25px; text-align: center; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18); border: 2px solid #f59e0b;">
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
            <div style="background: #f9fafb; border-radius: 14px; padding: 25px; margin-bottom: 20px; border: 2px solid #d1d5db;">
                <h3 style="font-size: 24px; font-weight: 900; color: #000000 !important; margin-bottom: 24px;">📈 Pecahan Prestasi</h3>
                <div style="margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; font-size: 18px; margin-bottom: 12px;">
                        <span style="color: #000000 !important; font-weight: 900;">Pencapaian Skor</span>
                        <span style="color: #000000 !important; font-weight: 900; font-size: 24px;">{{ min(100, round(($result['score'] / 1000) * 100)) }}%</span>
                    </div>
                    <div style="width: 100%; background: #d1d5db; border-radius: 10px; height: 32px; border: 2px solid #9ca3af; overflow: hidden;">
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
                    <div style="width: 100%; background: #d1d5db; border-radius: 10px; height: 32px; border: 2px solid #9ca3af; overflow: hidden;">
                        <div style="background: linear-gradient(90deg, #a855f7, #9333ea); height: 100%; border-radius: 8px; transition: width 0.5s ease; width: {{ max(20, 100 - ($result['time_taken'] / 3)) }}%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rewards Section -->
        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-radius: 14px; border: 2px solid #f59e0b; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);">
            <h3 style="font-size: 32px; font-weight: 900; color: #78350f !important; margin-bottom: 24px; display: flex; align-items: center;">
                <span style="margin-right: 16px; font-size: 48px;">🎁</span> 
                <span style="color: #78350f !important;">Ganjaran Diperoleh</span>
            </h3>

            @if($rewardRecords->count() === 0)
                <p style="color:#78350f; font-weight:700; font-size:14px;">Tiada ganjaran untuk permainan ini.</p>
            @else
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 10px;">
                @foreach($rewardRecords as $reward)
                <div style="background:#fff; border-radius:12px; border:2px solid {{ $reward->is_claimed ? '#22c55e' : '#f59e0b' }}; padding:16px; box-shadow:0 2px 8px rgba(2,6,23,0.12); display:flex; flex-direction:column; gap:8px;">
                    <div style="font-size:32px;">{{ $reward->badge_icon ?? '🎖️' }}</div>
                    <div style="font-size:14px; font-weight:800; color:#111827;">{{ $reward->reward_name }}</div>
                    <div style="font-size:12px; color:#6b7280;">{{ $reward->reward_description }}</div>
                    <div style="font-size:12px; font-weight:700; color:#92400e;">+{{ $reward->points_awarded }} mata</div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span style="font-size:12px; font-weight:700; color: {{ $reward->is_claimed ? '#16a34a' : '#b45309' }};">
                            {{ $reward->is_claimed ? 'Sudah dituntut' : 'Belum dituntut' }}
                        </span>
                        @if(!$reward->is_claimed)
                        <form method="POST" action="{{ route('rewards.claim', $reward->id) }}" style="margin:0;">
                            @csrf
                            <button type="submit" style="padding:8px 12px; background:linear-gradient(90deg,#f59e0b,#d97706); color:#fff; border:none; border-radius:8px; font-weight:700; font-size:12px; cursor:pointer;">
                                Tuntut
                            </button>
                        </form>
                        @else
                        <span style="font-size:12px; color:#16a34a; font-weight:700;">✅</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <a href="{{ route('rewards.index') }}" style="padding:10px 14px; background:#111827; color:#fff; border-radius:8px; font-weight:700; text-decoration:none; font-size:12px;">Lihat semua ganjaran</a>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 20px;">
            <a href="{{ route('games.leaderboard', $result['game_id']) }}" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff !important; font-weight: 900; padding: 25px; border-radius: 14px; text-align: center; font-size: 20px; border: 2px solid #1d4ed8; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                <span style="margin-right: 12px; font-size: 32px;">📊</span> 
                <span style="color: #ffffff !important;">Lihat Papan Pendahulu</span>
            </a>
            <a href="{{ route('games.play', $result['game_id']) }}" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff !important; font-weight: 900; padding: 25px; border-radius: 14px; text-align: center; font-size: 20px; border: 2px solid #047857; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                <span style="margin-right: 12px; font-size: 32px;">🔄</span> 
                <span style="color: #ffffff !important;">Main Semula</span>
            </a>
        </div>

        <div style="margin-bottom: 20px;">
            <a href="{{ route('games.index') }}" style="display: block; background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%); color: #ffffff !important; font-weight: 900; padding: 25px; border-radius: 14px; text-align: center; font-size: 20px; border: 2px solid #374151; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18); text-decoration: none;">
                ← Kembali ke Semua Permainan
            </a>
        </div>

        <!-- Tips Card -->
        <div style="background: #dbeafe; border-radius: 14px; border: 2px solid #3b82f6; padding: 25px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);">
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

@endsection
