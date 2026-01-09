@extends('layouts.app')

@section('content')
<style>
    .result-card {
        border-radius: 16px;
        padding: 24px;
        border: 2px solid #e5e7eb;
        box-shadow: 0 2px 12px rgba(2, 6, 23, 0.12);
        background: linear-gradient(135deg, #f5f0ffff 0%, #ffffffff 100%);
        transition: transform 0.12s ease, box-shadow 0.12s ease;
        text-align: center;
        cursor: pointer;
    }
    .result-card:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2);
    }
    .result-card .icon {
        font-size: 60px;
        margin-bottom: 10px;
        color: #7c3aed;
        text-shadow: 0 2px 8px rgba(44,0,80,0.18);
    }
    .result-card .label {
        font-size: 24px;
        font-weight: 800;
        color: #7c3aed;
        margin-bottom: 6px;
    }
    .result-card .value {
        font-size: 36px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 8px;
    }
    .result-card .unit {
        font-size: 18px;
        font-weight: 600;
        color: #6b7280;
        margin-top: 4px;
    }

    .panel {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(2, 6, 23, 0.10);
        margin-bottom: 32px;
        padding: 30px;
    }

    .panel-header h3 {
        font-size: 20px;
        font-weight: 800;
        color: #000000;
        margin-bottom: 6px;
    }

    .performance-metric-header {
        display: flex;
        justify-content: space-between;
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 10px;
        color: #7c3aed;
    }
    .performance-metric-label {
        color: #000000;
    }
    .performance-metric-value {
        font-size: 24px;
        font-weight: 800;
        color: #7c3aed;
    }
    .progress-bar {
        width: 100%;
        height: 24px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        background: #f3f4f6;
        margin-top: 4px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 8px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Reward Card Styles (from rewards.blade.php) */
    .reward-card-item {
        background: linear-gradient(135deg, #f5f0ffff 0%, #ffffffff 100%);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(2, 6, 23, 0.12);
        border: 2px solid #e5e7eb;
        align-items: center;
        text-align: center;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
        cursor: pointer;
    }
    .result-card:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2);
    }

    .reward-card-item:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2);
    }
    .reward-card-item.game-completed {
        background: linear-gradient(135deg, #ebfee8ff 0%, #ffffffff 100%);
    }
    .reward-card-item.speed-demon {
        background: linear-gradient(135deg, #f8f8deff 0%, #ffffffff 100%);
    }
    .reward-card-item.great-player {
        background: linear-gradient(135deg, #fff0f0ff 0%, #ffffffff 100%);
    }
    .reward-card-item .icon {
        font-size: 60px;
    }
    .reward-card-item .name {
        font-size: 24px;
        font-weight: 800;
        color: #111827;
        margin: 12px 0px;
    }
    .reward-card-item .description {
        font-size: 18px;
        font-weight: 600;
        color: #6b7280;
    }
    .reward-card-item .points {
        font-size: 15px;
        font-weight: 800;
        background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
        color: #fff;
        padding: 4px 12px;
        border-radius: 8px;
        margin: 8px 0px;
        display: inline-block;
    }
    .reward-card-item .claimed {
        font-size: 14px;
        font-weight: 600;
        color: #16a34a;
    }
    .reward-card-item .not-claimed {
        font-size: 13px;
        font-weight: 600;
        color: #b45309;
        margin-top: 8px;
    }
    .reward-card-item .claim-btn {
        padding: 8px 16px;
        background: linear-gradient(90deg, #f59e0b, #d97706);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 8px;
        box-shadow: 0 2px 8px rgba(2, 6, 23, 0.12);
    }
    .reward-card-item .claim-btn:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2);
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
        box-shadow: 0 2px 12px rgba(2, 6, 23, 0.12);
        color: #ffffff !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
        border: none;
    }
    .result-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(2, 6, 23, 0.18);
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
        margin-bottom: 8px;
        font-size: 18px;
    }
    .tips-list li:last-child {
        margin-bottom: 0;
    }
    .tips-list .checkmark {
        color: #7c3aed !important;
        margin-right: 15px;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
    }
    .tips-list .text {
        font-weight: 600;
        font-size: 18px;
        color: #6e6e6eff;
    }

    /* Ensure both cards and rewards use the same gap */
    .cards,
    .reward-cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 32px;
        margin-bottom: 20px;
    }
</style>

<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title" style="font-size: 32px; font-weight: 800; color: #000000;">Permainan Selesai</div>
                <div class="sub" style="font-size: 16px; color: #6b7280;">Tahniah {{ auth()->user()->name }}!</div>
            </div>
            <a href="{{ route('games.index') }}" class="btn-kembali" style="font-size: 14px; color: #7c3aed; font-weight: 700;">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>
        </div>

        <div style="margin: 0 auto;">

            <!-- Game Summary Card -->
            <div class="panel">
                <div class="panel-header">
                    <h3>Ringkasan Permainan</h3>
                </div>
                <div class="cards" style="margin-bottom: 32px;">
                    <div class="result-card">
                        <div class="icon">🏆</div>
                        <div class="label">Skor Akhir</div>
                        <div class="value">{{ number_format($result['score']) }}</div>
                        <div class="unit">Mata</div>
                    </div>
                    <div class="result-card">
                        <div class="icon">⏱️</div>
                        <div class="label">Masa Diambil</div>
                        <div class="value">{{ gmdate('i:s', $result['time_taken']) }}</div>
                        <div class="unit">Minit</div>
                    </div>
                    <div class="result-card">
                        <div class="icon">⭐</div>
                        <div class="label">Prestasi</div>
                        <div class="value">
                            @if($result['score'] >= 800) A+
                            @elseif($result['score'] >= 600) A
                            @elseif($result['score'] >= 400) B
                            @elseif($result['score'] >= 200) C
                            @else D
                            @endif
                        </div>
                        <div class="unit">Gred</div>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                    <div class="performance-metric">
                        <div class="performance-metric-header">
                            <span class="performance-metric-label">
                                Pencapaian Skor: 
                                <span class="performance-metric-value">
                                    {{ max(1, min(10, round($result['score'] / 100))) }}/10
                                </span>
                            </span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill" style="background: linear-gradient(90deg, #7c3aed, #a78bfa); width: {{ min(100, ($result['score'] / 1000) * 100) }}%;"></div>
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
                            <div class="progress-bar-fill" style="background: linear-gradient(90deg, #f59e0b, #a78bfa); width: {{ 
                                $result['time_taken'] <= 60 ? 100 : 
                                ($result['time_taken'] <= 120 ? 75 : 
                                ($result['time_taken'] <= 180 ? 50 : 25))
                            }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rewards Section -->
            <div class="panel">
                <div class="panel-header">
                    <h3>Ganjaran Diperoleh</h3>
                </div>
                @if($rewardRecords->count() === 0)
                    <p style="color:#78350f; font-weight:600; font-size:14px;">Tiada ganjaran untuk permainan ini.</p>
                @else
                <div class="reward-cards-grid">
                    @foreach($rewardRecords as $reward)
                    @php
                        $rewardTypeClass = '';
                        if (strtolower($reward->reward_name) === 'game completed') $rewardTypeClass = 'game-completed';
                        elseif (strtolower($reward->reward_name) === 'speed demon') $rewardTypeClass = 'speed-demon';
                        elseif (strtolower($reward->reward_name) === 'great player') $rewardTypeClass = 'great-player';
                    @endphp
                    <div class="reward-card-item {{ $rewardTypeClass }}">
                        <div class="icon">
                            @if($rewardTypeClass === 'game-completed')
                                <i class="bi bi-check-circle icon" style="color:#20af29ff;"></i>
                            @elseif($rewardTypeClass === 'speed-demon')
                                <i class="bi bi-lightning icon" style="color:#f59e0b;"></i>
                            @elseif($rewardTypeClass === 'great-player')
                                <i class="bi bi-controller icon" style="color:#ef4444;"></i>
                            @else
                                {{ $reward->badge_icon ?? '🎖️' }}
                            @endif
                        </div>
                        <div class="name">{{ $reward->reward_name }}</div>
                        <div class="description">{{ $reward->reward_description }}</div>
                        <div class="points">+{{ $reward->points_awarded }} mata</div>
                        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; margin-top:8px; width:100%;">
                            <span class="{{ $reward->is_claimed ? 'claimed' : 'not-claimed' }}">
                                {{ $reward->is_claimed ? 'Sudah dituntut' : 'Belum dituntut' }}
                            </span>
                            @if(!$reward->is_claimed)
                            <form method="POST" action="{{ route('rewards.claim', $reward->id) }}" style="margin:0;">
                                @csrf
                                <button type="submit" class="claim-btn">
                                    Tuntut
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            
            <!-- Tips Card -->
            <div class="panel">
                <div class="panel-header">
                    <h3>Petua untuk Meningkatkan Prestasi</h3>
                </div>
                <ul class="tips-list">
                    <li>
                        <span class="checkmark">✓</span>
                        <span class="text">Berlatih secara kerap untuk meningkatkan refleks anda</span>
                    </li>
                    <li>
                        <span class="checkmark">✓</span>
                        <span class="text">Cuba untuk mengalahkan skor dan masa anda sebelum ini</span>
                    </li>
                    <li>
                        <span class="checkmark">✓</span>
                        <span class="text">Selesaikan permainan dengan lebih pantas untuk bonus kelajuan</span>
                    </li>
                    <li>
                        <span class="checkmark">✓</span>
                        <span class="text">Semak papan pendahulu untuk melihat strategi terbaik</span>
                    </li>
                </ul>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: center; gap: 16px; margin-bottom: 20px;">
                <a href="{{ route('games.leaderboard', $result['game_id']) }}" class="result-btn" style="background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);">
                    <i class="bi bi-bar-chart-fill icon" style="margin-right:4px;"></i>
                    <span style="margin-left:4px;">Lihat Papan Pendahulu</span>
                </a>
                <a href="{{ route('rewards.index') }}" class="result-btn" style="background: linear-gradient(135deg, #f59e0b 0%, #a78bfa 100%);">
                    <i class="bi bi-gift icon" style="margin-right:4px;"></i>
                    <span style="margin-left:4px;">Lihat Ganjaran Anda</span>
                </a>
            </div>
        </div>
    </main>
</div>

@endsection