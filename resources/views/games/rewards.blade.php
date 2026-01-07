@extends('layouts.app')

@section('content')
<style>
    .reward-stat-card {
        background: linear-gradient(135deg, #f0f4ff 0%, #e6eef8 100%);
        border: 2px solid #c7d2f0;
        border-radius: 14px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);
        transition: border-color 0.2s ease, transform 0.12s ease, box-shadow 0.12s ease;
    }

    .reward-stat-card:hover {
        border-color: #6A4DF7;
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2);
    }

    .reward-stat-card:active {
        transform: translateY(0);
        box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);
    }

    .reward-stat-card .label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6b7280;
        margin-bottom: 12px;
    }

    .reward-stat-card .value {
        font-size: 36px;
        font-weight: 700;
        color: #1e3a8a;
    }

    .reward-item-card {
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(2, 6, 23, 0.12);
        transition: all 0.3s ease;
    }

    .reward-item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(2, 6, 23, 0.18);
    }

    .reward-item-card .icon {
        font-size: 32px;
    }

    .reward-item-card .name {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
    }

    .reward-item-card .description {
        font-size: 12px;
        color: #6b7280;
    }

    .reward-item-card .points {
        font-size: 12px;
        font-weight: 700;
        color: #d97706;
    }

    .reward-item-card .game-title {
        font-size: 12px;
        color: #9ca3af;
        font-style: italic;
    }

    .reward-item-card .status-claim {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid #e5e7eb;
    }

    .reward-item-card .status {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
    }

    .reward-item-card .claim-btn {
        padding: 6px 12px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .reward-item-card .claim-btn:hover {
        transform: scale(1.05);
    }

    .claim-checkmark {
        font-size: 14px;
        color: #10b981;
        font-weight: 700;
    }

    .empty-state {
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 48px 24px;
        text-align: center;
    }

    .empty-state p {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        color: #6A4DF7;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .back-link:hover {
        color: #7c3aed;
    }

    .back-link i {
        margin-right: 4px;
    }
</style>

<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Ganjaran Saya</div>
                <div class="sub">Lihat semua ganjaran yang anda peroleh daripada permainan.</div>
            </div>
            <a href="{{ route('games.index') }}" class="btn-kembali">
                <i class="bi bi-arrow-left"></i>Kembali
            </a>
        </div>

        <!-- Stats Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 32px;">
            <div class="reward-stat-card" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-color: #3b82f6;">
                <div class="label" style="color: #1e40af;">Jumlah Mata Dituntut</div>
                <div class="value" style="color: #1e3a8a;">{{ $totalPoints }}</div>
            </div>
            <div class="reward-stat-card" style="background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%); border-color: #a855f7;">
                <div class="label" style="color: #6b21a8;">Ganjaran Belum Dituntut</div>
                <div class="value" style="color: #581c87;">{{ $unclaimedCount }}</div>
            </div>
            <div class="reward-stat-card" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border-color: #f59e0b;">
                <div class="label" style="color: #92400e;">Jumlah Ganjaran</div>
                <div class="value" style="color: #78350f;">{{ $rewards->count() }}</div>
            </div>
        </div>

        <!-- Rewards Grid -->
        @if($rewards->isEmpty())
            <div class="empty-state">
                <p>Tiada ganjaran lagi. Main permainan untuk dapatkan ganjaran!</p>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px;">
                @foreach($rewards as $reward)
                    <div class="reward-item-card">
                        <div class="icon">{{ $reward->badge_icon ?? '🎖️' }}</div>
                        <div class="name">{{ $reward->reward_name }}</div>
                        <div class="description">{{ $reward->reward_description }}</div>
                        <div class="points">+{{ $reward->points_awarded }} mata</div>
                        <div class="game-title">Permainan: {{ $reward->game->title ?? 'N/A' }}</div>
                        <div class="status-claim">
                            <span class="status">{{ $reward->is_claimed ? 'Sudah dituntut' : 'Belum dituntut' }}</span>
                            @if(!$reward->is_claimed)
                                <form method="POST" action="{{ route('rewards.claim', $reward->id) }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="claim-btn">Tuntut</button>
                                </form>
                            @else
                                <span class="claim-checkmark">✓</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</div>

@endsection
