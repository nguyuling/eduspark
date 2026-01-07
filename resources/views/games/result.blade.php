@extends('layouts.app')

@section('content')
<style>

    .result-card {
        border-radius: 14px;
        padding: 15px;
        animation: fadeInUp 0.4s ease;
        border: 1px solid;
        box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-align: center;
        cursor: pointer;
    }

    .result-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(2, 6, 23, 0.28);
    }

    .result-card .icon {
        font-size: 40px;
        margin-bottom: 12px;
    }

    .result-card .label {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }

    .result-card .value {
        font-size: 36px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 12px;
    }

    .result-card .unit {
        font-size: 14px;
        font-weight: 600;
        margin-top: 12px;
    }

    /* Performance Metrics */
    .performance-metric {
        margin-bottom: 24px;
    }

    .performance-metric-header {
        display: flex;
        justify-content: space-between;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .performance-metric-label {
        color: #000000 !important;
    }

    .performance-metric-value {
        font-size: 16px;
        font-weight: 400;
    }

    .progress-bar {
        width: 100%;
        height: 32px;
        border-radius: 15px;
        border: 1px solid #9ca3af;
        overflow: hidden;
        background: #d1d5db;
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 8px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Reward Card */
    .reward-card-item {
        background: #dcfce7;
        border-radius: 12px;
        border: 1px solid;
        padding: 15px;
        max-width: 300px;
        box-shadow: 0 2px 8px rgba(2, 6, 23, 0.12);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .reward-card-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(2, 6, 23, 0.18);
    }

    .reward-card-item .icon {
        font-size: 32px;
    }

    .reward-card-item .name {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }

    .reward-card-item .description {
        font-size: 12px;
        color: #6b7280;
    }

    .reward-card-item .points {
        font-size: 12px;
        font-weight: 700;
    }

    /* Action Buttons */
    .result-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);
        color: #ffffff !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .result-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(2, 6, 23, 0.28);
    }

    .result-btn:active {
        transform: translateY(-2px);
    }

    .result-btn .icon {
        margin-right: 4px;
        font-size: 20px;
    }

    /* Tips Card */
    .tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .tips-list li {
        display: flex;
        align-items: flex-start;
        margin-bottom: 12px;
        font-size: 13px;
    }

    .tips-list li:last-child {
        margin-bottom: 0;
    }

    .tips-list .checkmark {
        color: #10b981 !important;
        margin-right: 12px;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }

    .tips-list .text {
        font-weight: 600;
    }
</style>

<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Permainan Selesai</div>
                <div class="sub">Tahniah {{ auth()->user()->name }}!</div>
            </div>
            <a href="{{ route('games.index') }}" class="btn-kembali">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>
        </div>

        <div style="margin: 0 auto;">

            <!-- Game Summary Card -->
            <div class="panel" style="padding: 30px;">
                <div class="panel-header">
                    <h3>Ringkasan Permainan</h3>
                </div>
                
                <!-- Main Stats Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; margin-bottom: 32px;">
                    <div class="result-card" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-color: #3b82f6;">
                        <div class="icon">🏆</div>
                        <div class="label" style="color: #1e40af !important;">Skor Akhir</div>
                        <div class="value" style="color: #1e3a8a !important;">{{ number_format($result['score']) }}</div>
                        <div class="unit" style="color: #1e40af !important;">Mata</div>
                    </div>
                    
                    <div class="result-card" style="background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%); border-color: #a855f7;">
                        <div class="icon">⏱️</div>
                        <div class="label" style="color: #6b21a8 !important;">Masa Diambil</div>
                        <div class="value" style="color: #581c87 !important;">{{ gmdate('i:s', $result['time_taken']) }}</div>
                        <div class="unit" style="color: #6b21a8 !important;">Minit</div>
                    </div>

                    <div class="result-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-color: #f59e0b;">
                        <div class="icon">⭐</div>
                        <div class="label" style="color: #92400e !important;">Prestasi</div>
                        <div class="value" style="color: #78350f !important;">
                            @if($result['score'] >= 800) A+
                            @elseif($result['score'] >= 600) A
                            @elseif($result['score'] >= 400) B
                            @elseif($result['score'] >= 200) C
                            @else D
                            @endif
                        </div>
                        <div class="unit" style="color: #92400e !important;">Gred</div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <div class="performance-metric">
                        <div class="performance-metric-header">
                            <span class="performance-metric-label">Pencapaian Skor: <span class="performance-metric-value">{{ round($result['score'] / 100) }}/10</span></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill" style="background: linear-gradient(90deg, #3b82f6, #2563eb); width: {{ min(100, ($result['score'] / 1000) * 100) }}%;"></div>
                        </div>
                    </div>
                    
                    <div class="performance-metric">
                        <div class="performance-metric-header">
                            <span class="performance-metric-label">Penarafan Kelajuan: <span class="performance-metric-value">
                                @if($result['time_taken'] <= 60) Cemerlang
                                @elseif($result['time_taken'] <= 120) Bagus
                                @elseif($result['time_taken'] <= 180) Memuaskan
                                @else Usaha Lagi
                                @endif
                            </span></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill" style="background: linear-gradient(90deg, #a855f7, #9333ea); width: {{ 
                                $result['time_taken'] <= 60 ? 100 : 
                                ($result['time_taken'] <= 120 ? 75 : 
                                ($result['time_taken'] <= 180 ? 50 : 25))
                            }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rewards Section -->
            <div class="panel" style="padding: 30px;">
                <div class="panel-header">
                    <h3>Ganjaran Diperoleh</h3>
                </div>

                @if($rewardRecords->count() === 0)
                    <p style="color:#78350f; font-weight:600; font-size:14px;">Tiada ganjaran untuk permainan ini.</p>
                @else
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, auto)); gap: 12px; margin-bottom: 16px; justify-content: start;">
                    @foreach($rewardRecords as $reward)
                    <div class="reward-card-item" style="border-color: {{ $reward->is_claimed ? '#22c55e' : '#f59e0b' }};">
                        <div class="icon">{{ $reward->badge_icon ?? '🎖️' }}</div>
                        <div class="name">{{ $reward->reward_name }}</div>
                        <div class="description">{{ $reward->reward_description }}</div>
                        <div class="points" style="color:#92400e;">+{{ $reward->points_awarded }} mata</div>
                        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; margin-top:8px; width:100%;">
                            <span style="font-size:12px; font-weight:600; color: {{ $reward->is_claimed ? '#16a34a' : '#b45309' }};">
                                {{ $reward->is_claimed ? 'Sudah dituntut' : 'Belum dituntut' }}
                            </span>
                            @if(!$reward->is_claimed)
                            <form method="POST" action="{{ route('rewards.claim', $reward->id) }}" style="margin:0;">
                                @csrf
                                <button type="submit" style="padding:6px 12px; background:linear-gradient(90deg,#f59e0b,#d97706); color:#fff; border:none; border-radius:6px; font-weight:600; font-size:11px; cursor:pointer; transition: all 0.2s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    Tuntut
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                <div style="display:flex; justify-content:flex-end;">
                    <a href="{{ route('rewards.index') }}" style="padding:10px 16px; background:#111827; color:#fff; border-radius:8px; font-weight:600; text-decoration:none; font-size:12px; transition: all 0.2s ease; display: inline-block;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">Lihat semua ganjaran</a>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: center; gap: 16px; margin-bottom: 20px;">
                <a href="{{ route('games.leaderboard', $result['game_id']) }}" class="result-btn" style="background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%); border-color: #7e22ce;">
                    <i class="bi bi-bar-chart-fill icon"></i>
                    <span>Lihat Papan Pendahulu</span>
                </a>
                <a href="{{ route('games.play', $result['game_id']) }}" class="result-btn" style="background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%); border-color: #6b7280;">
                    <i class="bi bi-arrow-repeat icon"></i>
                    <span>Main Semula</span>
                </a>
            </div>

            <!-- Tips Card -->
            <div class="panel" style="padding: 30px;">
                <div class="panel-header">
                    <h3>Petua untuk Meningkatkan Prestasi</h3>
                </div>
                <ul class="tips-list">
                    <li>
                        <span class="checkmark">✓</span>
                        <span class="text" style="color: #1e3a8a !important;">Berlatih secara kerap untuk meningkatkan refleks anda</span>
                    </li>
                    <li>
                        <span class="checkmark">✓</span>
                        <span class="text" style="color: #1e3a8a !important;">Cuba untuk mengalahkan skor dan masa anda sebelum ini</span>
                    </li>
                    <li>
                        <span class="checkmark">✓</span>
                        <span class="text" style="color: #1e3a8a !important;">Selesaikan permainan dengan lebih pantas untuk bonus kelajuan</span>
                    </li>
                    <li>
                        <span class="checkmark">✓</span>
                        <span class="text" style="color: #1e3a8a !important;">Semak papan pendahulu untuk melihat strategi terbaik</span>
                    </li>
                </ul>
            </div>
        </div>
    </main>
</div>

@endsection