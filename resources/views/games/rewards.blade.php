@extends('layouts.app')

@section('content')
<style>
    .reward-stat-card {
        background: linear-gradient(135deg, #8b5cf6 0%, #6A4DF7 100%);
        border: none;
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }

    .reward-stat-card:hover {
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
        color: #ffffff;
        margin-bottom: 6px;
    }

    .reward-stat-card .value {
        font-size: 36px;
        font-weight: 700;
        color: #ffffff;
    }

    /* Game Shelf Styles */
    .game-shelf {
        background: #fff;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        padding: 15px;
        margin-bottom: 24px;
        display: flex;
        gap: 20px;
        align-items: stretch;
        box-shadow: 0 2px 8px rgba(2, 6, 23, 0.12);
        transition: all 0.3s ease;
    }

    .game-shelf:hover {
        box-shadow: 0 8px 20px rgba(2, 6, 23, 0.15);
    }

    .game-shelf.empty {
        opacity: 0.6;
        background: #f9fafb;
    }

    .game-info {
        min-width: 180px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-right: 2px solid #e5e7eb;
        padding-right: 20px;
    }

    .game-info h3 {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 8px 0;
    }

    .game-info p {
        font-size: 13px;
        color: #6b7280;
        margin: 0 0 6px 0;
        line-height: 1.4;
    }

    .rewards-shelf {
        display: flex;
        gap: 16px;
        flex: 1;
        align-items: center;
        overflow-x: auto;
        padding-right: 10px;
    }

    .rewards-shelf::-webkit-scrollbar {
        height: 4px;
    }

    .rewards-shelf::-webkit-scrollbar-track {
        background: #e5e7eb;
        border-radius: 4px;
    }

    .rewards-shelf::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    .reward-slot {
        position: relative;
        flex-shrink: 0;
        width: 100px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        border: 2px dashed #e5e7eb;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .reward-slot:hover {
        border-color: #6A4DF7;
        background: #f5f3ff;
    }

    .reward-slot.filled {
        background: none;
        border: none;
    }

    .reward-slot .icon {
        font-size: 48px;
    }

    .reward-slot .badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 12px;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(2, 6, 23, 0.15);
    }

    .reward-slot.empty-state {
        border-style: solid;
        color: #d1d5db;
    }

    .empty-shelf {
        background: #f9fafb;
        border: 2px dashed #e5e7eb;
        border-radius: 14px;
        padding: 40px 20px;
        text-align: center;
        color: #9ca3af;
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

    /* Reward Intro Cards */
    .reward-stats-and-intro-row {
        display: flex;
        justify-content: center;
        align-items: stretch;
        gap: 40px;
        max-width: 1200px;
        margin: 0px auto 60px auto;
    }

    .reward-stats-col {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 220px;
        max-width: 260px;
        flex: 0 0 240px;
        gap: 20px;
    }

    .reward-intro-col {
        flex: 1 1 0;
        min-width: 0;
        position: relative;
        display: flex;
        align-items: center;
    }

    .reward-intro-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0;
        padding: 40px 0;
        overflow-x: visible;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 0;
        position: relative;
        width: 100%;
    }

    .reward-intro-card {
        flex-shrink: 0;
        width: 220px;
        background: #ffffff;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: 
            transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
            opacity 0.3s,
            margin 0.3s;
        cursor: pointer;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 220px;
        transform: scale(0.7);
        opacity: 0.5;
        margin: 0 -30px; /* squeeze side cards */
        z-index: 1;
    }

    .reward-intro-card.highlighted {
        transform: scale(1.25);
        opacity: 1;
        margin: 0 60px;
        z-index: 2;
    }

    /* Remove nth-child based styles and use type-based classes instead */
    .reward-intro-card.game-completed.highlighted {
        border-color: #20af29ff;
        box-shadow: 0 8px 24px rgba(90, 247, 85, 0.25);
    }
    .reward-intro-card.speed-demon.highlighted {
        border-color: #f59e0b;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.25);
    }
    .reward-intro-card.great-player.highlighted {
        border-color: #ef4444;
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.25);
    }

    .reward-intro-card .points {
        font-size: 16px;
        font-weight: 700;
        padding: 8px 15px;
        border-radius: 8px;
        display: inline-block;
    }
    .reward-intro-card.game-completed .points {
        background: #20af29ff;
        color: #ffffff;
    }
    .reward-intro-card.speed-demon .points {
        background: #f59e0b;
        color: #ffffff;
    }
    .reward-intro-card.great-player .points {
        background: #ef4444;
        color: #ffffff;
    }

    /* Navigation arrows */
    .reward-nav-arrow {
        font-size: 40px;
        color: #9ca3af;
        cursor: pointer;
        transition: color 0.2s ease, transform 0.2s ease;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
    }

    .reward-nav-arrow:hover:not(.disabled) {
        color: #6A4DF7;
        transform: translateY(-50%) scale(1.1);
    }

    .reward-nav-arrow.left { left: 170px; }
    .reward-nav-arrow.right { right: 170px; }

    .reward-nav-arrow.disabled {
        color: #e5e7eb;
        cursor: not-allowed;
    }

    .reward-intro-card .card-icon {
        font-size: 60px;
        margin-bottom: 15px;
        line-height: 1;
    }

    /* Icon color follows card type */
    .reward-intro-card.game-completed .card-icon {
        color: #20af29ff;
    }
    .reward-intro-card.speed-demon .card-icon {
        color: #f59e0b;
    }
    .reward-intro-card.great-player .card-icon {
        color: #ef4444;
    }

    .reward-intro-card h4 {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 10px 0;
    }

    .reward-intro-card p {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 15px;
        flex-grow: 1;
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

        <div class="reward-stats-and-intro-row">
            <div class="reward-stats-col">
                <div class="reward-stat-card">
                    <div class="label">Jumlah Mata Dituntut</div>
                    <div class="value">{{ $totalPoints }}</div>
                </div>
                <div class="reward-stat-card">
                    <div class="label">Jumlah Ganjaran</div>
                    <div class="value">{{ $rewards->count() }}</div>
                </div>
            </div>
            <div class="reward-intro-col">
                <i class="bi bi-chevron-left reward-nav-arrow left" id="prevReward"></i>
                <div class="reward-intro-container">
                    <div class="reward-intro-card game-completed">
                        <i class="bi bi-controller card-icon icon-controller"></i>
                        <h4>Game Completed</h4>
                        <p>Complete any game to earn this badge.</p>
                        <span class="points">10 Points</span>
                    </div>
                    <div class="reward-intro-card speed-demon">
                        <i class="bi bi-lightning card-icon icon-lightning"></i>
                        <h4>Speed Demon</h4>
                        <p>Finish games quickly to prove your speed.</p>
                        <span class="points">50 Points</span>
                    </div>
                    <div class="reward-intro-card great-player">
                        <i class="bi bi-star card-icon icon-star"></i>
                        <h4>Great Player</h4>
                        <p>Achieve high scores and master challenges.</p>
                        <span class="points">25 Points</span>
                    </div>
                </div>
                <i class="bi bi-chevron-right reward-nav-arrow right" id="nextReward"></i>
            </div>
        </div>

        <!-- Game Shelves -->
        @if($rewards->isEmpty())
            <div class="empty-shelf">
                <p>Tiada ganjaran lagi. Main permainan untuk dapatkan ganjaran!</p>
            </div>
        @else
            @php
                // Group rewards by game
                $rewardsByGame = $rewards->groupBy('game_id');
            @endphp

            @foreach($rewardsByGame as $gameId => $gameRewards)
                @php
                    $game = $gameRewards->first()->game;
                    // Count rewards by name
                    $rewardCounts = $gameRewards->groupBy('reward_name')->map->count();
                @endphp
                
                <div class="game-shelf">
                    <div class="game-info">
                        <h3>{{ $game->title ?? 'Unknown Game' }}</h3>
                        <p>{{ Str::limit($game->description ?? 'No description', 60) }}</p>
                    </div>
                    
                    <div class="rewards-shelf">
                        <!-- Game Completed Reward -->
                        @php $completedCount = $rewardCounts['Game Completed'] ?? 0; @endphp
                        <div class="reward-slot filled">
                            <div style="text-align: center;">
                                <i class="bi bi-controller icon" style="color: #20af29ff;"></i>
                                @if($completedCount > 1)
                                    <div class="badge">x{{ $completedCount }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Speed Demon Reward -->
                        @php $speedCount = $rewardCounts['Speed Demon'] ?? 0; @endphp
                        <div class="reward-slot {{ $speedCount > 0 ? 'filled' : '' }}">
                            @if($speedCount > 0)
                                <div style="text-align: center;">
                                    <i class="bi bi-lightning icon" style="color: #f59e0b;"></i>
                                    @if($speedCount > 1)
                                        <div class="badge">x{{ $speedCount }}</div>
                                    @endif
                                </div>
                            @else
                                <i class="bi bi-lightning empty-state"></i>
                            @endif
                        </div>

                        <!-- Great Player Reward -->
                        @php $playerCount = $rewardCounts['Great Player'] ?? 0; @endphp
                        <div class="reward-slot {{ $playerCount > 0 ? 'filled' : '' }}">
                            @if($playerCount > 0)
                                <div style="text-align: center;">
                                    <i class="bi bi-star icon" style="color: #ef4444;"></i>
                                    @if($playerCount > 1)
                                        <div class="badge">x{{ $playerCount }}</div>
                                    @endif
                                </div>
                            @else
                                <i class="bi bi-star empty-state"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const container = document.querySelector('.reward-intro-container');
        let cards = Array.from(document.querySelectorAll('.reward-intro-card'));
        const prevBtn = document.getElementById('prevReward');
        const nextBtn = document.getElementById('nextReward');

        const updateCardPositions = () => {
            // Always highlight the middle card (index 1)
            cards.forEach((card, index) => {
                card.classList.remove('highlighted');
                if (index === 1) {
                    card.classList.add('highlighted');
                }
            });

            // Scroll to show all three cards with middle one centered
            const cardWidth = cards[0].offsetWidth;
            const gap = 20;
            const scrollLeft = cards[0].offsetWidth + gap - (container.offsetWidth / 2) + (cardWidth / 2);

            container.scrollTo({
                left: scrollLeft,
                behavior: 'smooth'
            });
        };

        prevBtn.addEventListener('click', () => {
            // Shift right: move last card to beginning
            const lastCard = cards.pop();
            cards.unshift(lastCard);

            // Reorder cards in DOM
            cards.forEach(card => {
                container.appendChild(card);
            });
            updateCardPositions();
        });

        nextBtn.addEventListener('click', () => {
            // Shift left: move first card to end
            const firstCard = cards.shift();
            cards.push(firstCard);

            // Reorder cards in DOM
            cards.forEach(card => {
                container.appendChild(card);
            });
            updateCardPositions();
        });

        // Initial setup
        updateCardPositions();

        // Adjust on window resize
        window.addEventListener('resize', updateCardPositions);
    });
</script>

@endsection
