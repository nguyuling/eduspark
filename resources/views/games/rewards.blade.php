@extends('layouts.app')

@section('content')
<style>
    .reward-stat-card {
        background: linear-gradient(135deg, #f5f0ffff 0%, #ffffffff 100%);
        border: 2px solid #7c3aed;
        border-radius: 16px;
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
        font-size: 16px;
        font-weight: 700;
        color: #000000;
        margin-bottom: 6px;
    }

    .reward-stat-card .value {
        font-size: 36px;
        font-weight: 700;
        color: #7c3aed;
    }

    /* Game Shelf Styles */
    .game-shelf {
        display: flex;
        align-items: stretch;
        gap: 0;
        padding: 0;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 32px;
        background: #fff;
        border: none;
        box-shadow: 0 2px 8px rgba(2, 6, 23, 0.12);
    }

    .game-cover-cell {
        width: 220px;
        min-width: 300px;
        max-width: 300px;
        background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: flex-start;
        padding: 0;
        overflow: hidden;
    }
    .game-cover-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.25;
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 1;
        filter: grayscale(1) brightness(1.2) hue-rotate(-30deg) saturate(0.6);
        pointer-events: none;
    }
    .game-cover-overlay {
        position: relative;
        z-index: 2;
        padding: 24px 20px;
        width: 100%;
    }
    .game-cover-title {
        font-size: 20px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 8px;
        text-shadow: 0 2px 8px rgba(44,0,80,0.18);
        line-height: 1.2;
    }
    .game-cover-desc {
        font-size: 13px;
        color: #ffffff;
        text-shadow: 0 1px 4px rgba(44,0,80,0.12);
        margin-bottom: 0;
        line-height: 1.4;
    }

    .rewards-shelf {
        display: flex;
        gap: 16px;
        align-items: center;
        padding: 32px 24px;
        background: #fff;
        flex: 1;
        min-width: 0;
        overflow-x: auto;
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
        background: linear-gradient(135deg, #8674e2ff 0%, #6A4DF7 100%);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 12px;
        border: 2px solid #ccccccff;
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
        padding: 20px 10px;
        overflow-x: visible;
        -webkit-overflow-scrolling: touch;
        margin-bottom: 40px;
        position: relative;
        width: 100%;
    }

    .reward-intro-card {
        flex-shrink: 0;
        width: 300px;
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
        border-color: #14aa1eff;
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
        color: #6f6f6fff;
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

    .reward-nav-arrow.left { left: 200px; }
    .reward-nav-arrow.right { right: 200px; }

    .reward-nav-arrow.disabled {
        color: #b1b1b1ff;
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
        margin: 0 0 20px 0;
    }

    .reward-intro-card p {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 15px;
        flex-grow: 1.;
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
                        <i class="bi bi-check-circle card-icon icon-check-circle"></i>
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
                        <i class="bi bi-controller card-icon icon-controller"></i>
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
                    $rewardCounts = $gameRewards->groupBy('reward_name')->map->count();
                    // Assign a unique image per game based on $gameId or $game->title
                    $defaultImages = [
                        1 => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80', // Example Game 1
                        2 => 'https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=400&q=80', // Example Game 2
                        3 => 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80', // Example Game 3
                        2 => 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=400&q=80', // New Quiz Challenge image
                    ];
                    $gameImage = $game->image_url 
                        ?? ($defaultImages[$gameId] ?? 'https://static.wixstatic.com/media/4700cf_6e10db7253d348f0a8f8e695e8815597~mv2.jpg');
                @endphp

                <div class="game-shelf">
                    <div class="game-cover-cell">
                        <img src="{{ $gameImage }}" alt="Game Cover" class="game-cover-img" />
                        <div class="game-cover-overlay">
                            <div class="game-cover-title">{{ $game->title ?? 'Unknown Game' }}</div>
                            <div class="game-cover-desc">{{ Str::limit($game->description ?? 'No description', 60) }}</div>
                        </div>
                    </div>
                    <div class="rewards-shelf">
                        <!-- Game Completed Reward -->
                        @php $completedCount = $rewardCounts['Game Completed'] ?? 0; @endphp
                        <div class="reward-slot filled">
                            <div style="text-align: center;">
                                <i class="bi bi-check-circle icon" style="color: #20af29ff;"></i>
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
                                    <i class="bi bi-controller icon" style="color: #ef4444;"></i>
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
