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
        border-color: var(--accent);
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
    .shelves-row {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 32px;
        width: 100%;
        box-sizing: border-box;
    }
    .game-shelf {
        flex: 0 1 calc(33.333% - 11px);
        min-width: 0;
        display: flex;
        align-items: stretch;
        gap: 0;
        padding: 0;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        border: 2px solid #7c3aed;
        box-shadow: 0 8px 24px rgba(2, 6, 23, 0.12);
        margin-bottom: 0;
        box-sizing: border-box;
    }

    .game-shelf:hover{
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2);
    }
        
    .game-cover-cell {
        width: 200px;
        /* min-width: 180px;
        max-width: 250px; */
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
        padding: 18px 14px;
        width: 100%;
    }
    .game-cover-title {
        font-size: 36px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 8px;
        text-shadow: 0 2px 8px rgba(44,0,80,0.18);
        line-height: 1.2;
    }
    .game-cover-desc {
        font-size: 18px;
        color: #ffffff;
        text-shadow: 0 1px 4px rgba(44,0,80,0.12);
        margin-bottom: 0;
        line-height: 1.4;
    }

    .rewards-shelf {
        display: flex;
        flex-direction: column;
        gap: 12px; /* reduced gap */
        align-items: flex-start;
        justify-content: center;
        padding: 10px 12px;
        background: linear-gradient(135deg, #f5f0ffff 0%, #ffffffff 100%);
        flex: 1;
        min-width: 0;
        overflow-x: auto;
    }

    .reward-slot {
        position: relative;
        flex-shrink: 0;
        width: 60px;   /* reduced width */
        height: 60px;  /* reduced height */
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        background: #f9fafb;
        border: 2px dashed #e5e7eb;
        border-radius: 10px;
        transition: all 0.3s ease;
        padding-left: 0;
        padding-right: 0;
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
        font-size: 36px; /* reduced icon size */
        margin-left: 10px;
        margin-right: 4px;
        display: block;
    }
    .reward-slot .badge {
        position: relative;
        margin-left: 2px;
        background: none;
        color: #7c3aed;
        font-size: 20px; /* reduced badge size */
        font-weight: 700;
        padding: 0;
        border: none;
        box-shadow: none;
        display: inline-block;
        vertical-align: middle;
        text-align: center;
        /* Ensure no line break inside badge */
        white-space: nowrap;
    }
    .reward-slot.empty-state {
        border-style: solid;
        color: #d1d5db;
        background: #f3f4f6;
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
        justify-content: flex-start;
        align-items: stretch;
        gap: 20px;
        position: relative;
        width: 100%;
        box-sizing: border-box;
        flex-wrap: wrap;
    }
    @media (max-width: 900px) {
        .reward-stats-and-intro-row {
            flex-direction: column;
            gap: 40px;
        }
    }

    /* Separator line above shelves */
    .shelves-top-separator {
        width: calc(100% - 60px);
        height: 1px;
        background: #c3c3c3;
        margin: 48px 0 48px 60px;
        box-sizing: border-box;
    }

    .reward-stats-col {
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 220px;
        max-width: 260px;
        flex: 0 0 auto;
        gap: 20px;
        flex-shrink: 0;
    }

    .reward-intro-col {
        flex: 1 1 auto;
        min-width: 280px;
        position: relative;
        display: flex;
        align-items: center;
        box-sizing: border-box;
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
        box-sizing: border-box;
    }

    .reward-intro-card {
        flex-shrink: 0;
        width: 260px;
        border-radius: 16px;
        padding: 22px;
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
        height: 200px;
        transform: scale(0.7);
        opacity: 0.5;
        margin: 0 -28px;
        z-index: 1;
    }

    /* Gradient backgrounds for each reward card */
    .reward-intro-card.game-completed {
        background: linear-gradient(135deg, #f2fff0ff 0%, #ffffffff 100%);
    }
    .reward-intro-card.speed-demon {
        background: linear-gradient(135deg, #fffff0ff 0%, #ffffffff 100%);
    }
    .reward-intro-card.great-player {
        background: linear-gradient(135deg, #fff0f0ff 0%, #ffffffff 100%);
    }

    .reward-intro-card.highlighted {
        transform: scale(1.1);
        opacity: 1;
        margin: 0 30px;
        z-index: 2;
    }

    /* Remove nth-child based styles and use type-based classes instead */
    .reward-intro-card.game-completed.highlighted {
        border: 2px solid #14aa1eff;
        box-shadow: 0 8px 24px rgba(90, 247, 85, 0.25);
    }
    .reward-intro-card.speed-demon.highlighted {
        border: 2px solid #f59e0b;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.25);
    }
    .reward-intro-card.great-player.highlighted {
        border: 2px solid #ef4444;
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.25);
    }

    .reward-intro-card .card-icon {
        font-size: 56px;
        margin-bottom: 14px;
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
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 14px 0;
    }

    .reward-intro-card p {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 12px;
        flex-grow: 1.;
    }

    .reward-intro-card .points {
        font-size: 15px;
        font-weight: 700;
        padding: 7px 0;
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
</style>

<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Ganjaran Saya</div>
                <div class="sub">Lihat semua ganjaran yang anda peroleh daripada permainan.</div>
            </div>
            <!-- Change the Kembali button to go back to the games index page -->
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

        <!-- Separator line above shelves -->
        <div class="shelves-top-separator"></div>

        <!-- Game Shelves -->
        @if($rewards->isEmpty())
            <div class="empty-shelf">
                <p>Tiada ganjaran lagi. Main permainan untuk dapatkan ganjaran!</p>
            </div>
        @else
            @php
                // Group rewards by game
                $rewardsByGame = $rewards->groupBy('game_id');
                // Fix: avoid using $gameId as both key and parameter
                $gameShelves = $rewardsByGame->map(function($gameRewards, $gid) {
                    return ['gameRewards' => $gameRewards, 'gameId' => $gid];
                })->values();
            @endphp

            @for($i = 0; $i < $gameShelves->count(); $i += 3)
                <div class="shelves-row">
                    @for($j = $i; $j < min($i+3, $gameShelves->count()); $j++)
                        @php
                            $gameRewards = $gameShelves[$j]['gameRewards'];
                            $gameId = $gameShelves[$j]['gameId'];
                            $game = $gameRewards->first()->game;
                            // Fix: Count actual rewards, not just unique names
                            $rewardCounts = [
                                'Game Completed' => $gameRewards->where('reward_name', 'Game Completed')->count(),
                                'Speed Demon' => $gameRewards->where('reward_name', 'Speed Demon')->count(),
                                'Great Player' => $gameRewards->where('reward_name', 'Great Player')->count(),
                            ];
                            $defaultImages = [
                                1 => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80',
                                2 => 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=400&q=80',
                                3 => 'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80',
                            ];
                            $gameImage = $game->image_url ?? ($defaultImages[$gameId] ?? 'https://static.wixstatic.com/media/4700cf_6e10db7253d348f0a8f8e695e8815597~mv2.jpg');
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
                                {{-- Game Completed Reward --}}
                                @php $completedCount = $rewardCounts['Game Completed']; @endphp
                                <div class="reward-slot {{ $completedCount > 0 ? 'filled' : '' }}">
                                    @if($completedCount > 0)
                                        <i class="bi bi-check-circle icon" style="color: #20af29ff;"></i>
                                        @if($completedCount > 1)
                                            <span class="badge">x {{ $completedCount }}</span>
                                        @endif
                                    @else
                                        <i class="bi bi-check-circle empty-state"></i>
                                    @endif
                                </div>
                                {{-- Speed Demon Reward --}}
                                @php $speedCount = $rewardCounts['Speed Demon']; @endphp
                                <div class="reward-slot {{ $speedCount > 0 ? 'filled' : '' }}">
                                    @if($speedCount > 0)
                                        <i class="bi bi-lightning icon" style="color: #f59e0b;"></i>
                                        @if($speedCount > 1)
                                            <span class="badge">x {{ $speedCount }}</span>
                                        @endif
                                    @else
                                        <i class="bi bi-lightning empty-state"></i>
                                    @endif
                                </div>
                                {{-- Great Player Reward --}}
                                @php $playerCount = $rewardCounts['Great Player']; @endphp
                                <div class="reward-slot {{ $playerCount > 0 ? 'filled' : '' }}">
                                    @if($playerCount > 0)
                                        <i class="bi bi-controller icon" style="color: #ef4444;"></i>
                                        @if($playerCount > 1)
                                            <span class="badge">x {{ $playerCount }}</span>
                                        @endif
                                    @else
                                        <i class="bi bi-controller empty-state"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            @endfor
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
