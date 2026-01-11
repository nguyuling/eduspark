@extends('layouts.app')

@section('content')
<style>

    h3 {
        font-weight: 700;
        font-size: 18px;
        text-align: center;
        margin: 0;
    }
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
        gap: 20px; /* Changed from 16px to 20px */
        margin-bottom: 32px;
        width: 100%;
        box-sizing: border-box;
    }
    .game-shelf {
        flex: 0 1 calc(33.333% - 13.333px); /* Adjusted for 20px gap ( (100% - 2*20px) / 3 ) */
        min-width: 0;
        display: flex;
        align-items: stretch;
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
        gap: 20px; /* Increased gap */
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
        width: 60px;  
        height: 60px;
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
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2);
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
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 0 40px;
        row-gap: 20px;
        position: relative;
        width: 100%;
        box-sizing: border-box;
        padding: 0 20px;
    }

    @media (max-width: 1200px) {
        .reward-stats-and-intro-row {
            grid-template-columns: 180px 1fr;
            gap: 0 30px;
        }
    }

    @media (max-width: 900px) {
        .reward-stats-and-intro-row {
            grid-template-columns: 1fr;
            gap: 20px;
            padding: 0;
        }
    }

    /* Grid cell for titles */
    .reward-stats-title {
        grid-column: 1;
        grid-row: 1;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .reward-intro-title {
        grid-column: 2;
        grid-row: 1;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    /* Grid cell for content */
    .reward-stats-content {
        grid-column: 1;
        grid-row: 2;
        display: flex;
        flex-direction: column;
        gap: 20px;
        height: 350px;
    }

    .reward-intro-content {
        grid-column: 2;
        grid-row: 2;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        height: 290px;
        position: relative;
        overflow: visible;
    }

    .reward-stats-col {
        display: contents;
    }

    .reward-intro-col {
        display: contents;
    }

    .reward-intro-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        overflow-x: visible;
        -webkit-overflow-scrolling: touch;
        position: relative;
        width: 100%;
        box-sizing: border-box;
    }

    .reward-intro-card {
        flex-shrink: 0;
        width: 220px;
        border-radius: 16px;
        padding: 16px 16px 42px 16px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: 
            transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1),
            opacity 0.3s,
            margin 0.3s,
            filter 0.3s;
        cursor: pointer;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 280px;
        transform: scale(0.85);
        opacity: 0.6;
        margin: 0 10px;
        z-index: 1;
        filter: brightness(0.9);
    }

    .reward-intro-card:hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2);
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

    .reward-intro-card.game-completed {
        border: 2px solid #14aa1eff;
        box-shadow: 0 8px 24px rgba(90, 247, 85, 0.25);
    }
    .reward-intro-card.speed-demon {
        border: 2px solid #f59e0b;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.25);
    }
    .reward-intro-card.great-player {
        border: 2px solid #ef4444;
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.25);
    }

    /* Highlight the center card */
    .reward-intro-card:nth-child(2) {
        transform: scale(1);
        opacity: 1;
        filter: brightness(1);
    }
    .reward-intro-card:nth-child(2):hover {
        border-color: var(--accent);
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2);
    }

    .reward-intro-card:nth-child(2).game-completed {
        border: 2px solid #14aa1eff;
        box-shadow: 0 8px 24px rgba(90, 247, 85, 0.25);
    }
    .reward-intro-card:nth-child(2).speed-demon {
        border: 2px solid #f59e0b;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.25);
    }
    .reward-intro-card:nth-child(2).great-player {
        border: 2px solid #ef4444;
        box-shadow: 0 8px 24px rgba(239, 68, 68, 0.25);
    }

    .reward-intro-card p {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 12px;
        flex-grow: 1;
    }

    .reward-intro-card .points {
        position: absolute;
        left: 50%;
        bottom: -18px;
        transform: translateX(-50%);
        font-size: 15px;
        font-weight: 700;
        padding: 7px 24px;
        border-radius: 18px;
        color: #ffffff;
        min-width: 120px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        z-index: 2;
        border: none;
        margin: 0;
    }
    .reward-intro-card.game-completed .points {
        background: #20af29ff;
    }
    .reward-intro-card.speed-demon .points {
        background: #f59e0b;
    }
    .reward-intro-card.great-player .points {
        background: #ef4444;
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

    .reward-nav-arrow.disabled {
        color: #b1b1b1ff;
        cursor: not-allowed;
    }

    /* Badge Image Styles */
    .reward-badge-img {
        width: 60px;
        height: 60px;
        margin-left: 2px;
        margin-right: 4px;
    }

    .reward-badge-img.empty {
        opacity: 0.3;
    }

    .intro-badge-img {
        width: 180px;
        height: 180px;
        margin: 0 auto 10px auto;
        display: block;
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
            <div class="reward-stats-title">
                <h3>Statistik Ganjaran</h3>
            </div>
            <div class="reward-intro-title">
                <h3>Jenis Ganjaran</h3>
            </div>
            <div class="reward-stats-content">
                <div class="reward-stat-card">
                    <div class="label">Jumlah Mata Dituntut</div>
                    <div class="value">{{ $totalPoints }}</div>
                </div>
                <div class="reward-stat-card">
                    <div class="label">Jumlah Ganjaran</div>
                    <div class="value">{{ $rewards->count() }}</div>
                </div>
            </div>
            <div class="reward-intro-content">
                <i class="bi bi-chevron-left reward-nav-arrow left" id="prevReward"></i>
                <div class="reward-intro-container">
                    <div class="reward-intro-card game-completed">
                        <img src="{{ asset('badges/game-completed.svg') }}" alt="Game Completed Badge" class="intro-badge-img" />
                        <p>Selesaikan mana-mana permainan.</p>
                        <span class="points">10 Mata</span>
                    </div>
                    <div class="reward-intro-card speed-demon">
                        <img src="{{ asset('badges/speed-demon.svg') }}" alt="Speed Demon Badge" class="intro-badge-img" />
                        <p>Selesaikan permainan dengan cepat.</p>
                        <span class="points">50 Mata</span>
                    </div>
                    <div class="reward-intro-card great-player">
                        <img src="{{ asset('badges/great-player.svg') }}" alt="Pemain Hebat Badge" class="intro-badge-img" />
                        <p>Capai markah tinggi dan kuasai cabaran.</p>
                        <span class="points">25 Mata</span>
                    </div>
                </div>
                <i class="bi bi-chevron-right reward-nav-arrow right" id="nextReward"></i>
            </div>
        </div>

        <!-- Game Shelves -->
        <h3>Koleksi Ganjaran</h3>
        <div style="width: 90%; height: 1px; background-color: #c394f5ff; margin: 20px auto; display: block;"></div>
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
                                    <div class="game-cover-desc">{{ Str::limit($game->description ?? 'Tiada keterangan', 60) }}</div>
                                </div>
                            </div>
                            <div class="rewards-shelf">
                                {{-- Game Completed Reward --}}
                                @php $completedCount = $rewardCounts['Game Completed']; @endphp
                                <div class="reward-slot {{ $completedCount > 0 ? 'filled' : '' }}">
                                    @if($completedCount > 0)
                                        <img src="{{ asset('badges/game-completed.svg') }}" alt="Game Completed" class="reward-badge-img" />
                                        @if($completedCount > 1)
                                            <span class="badge">x {{ $completedCount }}</span>
                                        @endif
                                    @else
                                        <img src="{{ asset('badges/game-completed.svg') }}" alt="Game Completed" class="reward-badge-img empty" />
                                    @endif
                                </div>
                                {{-- Speed Demon Reward --}}
                                @php $speedCount = $rewardCounts['Speed Demon']; @endphp
                                <div class="reward-slot {{ $speedCount > 0 ? 'filled' : '' }}">
                                    @if($speedCount > 0)
                                        <img src="{{ asset('badges/speed-demon.svg') }}" alt="Speed Demon" class="reward-badge-img" />
                                        @if($speedCount > 1)
                                            <span class="badge">x {{ $speedCount }}</span>
                                        @endif
                                    @else
                                        <img src="{{ asset('badges/speed-demon.svg') }}" alt="Speed Demon" class="reward-badge-img empty" />
                                    @endif
                                </div>
                                {{-- Great Player Reward --}}
                                @php $playerCount = $rewardCounts['Great Player']; @endphp
                                <div class="reward-slot {{ $playerCount > 0 ? 'filled' : '' }}">
                                    @if($playerCount > 0)
                                        <img src="{{ asset('badges/great-player.svg') }}" alt="Pemain Hebat" class="reward-badge-img" />
                                        @if($playerCount > 1)
                                            <span class="badge">x {{ $playerCount }}</span>
                                        @endif
                                    @else
                                        <img src="{{ asset('badges/great-player.svg') }}" alt="Pemain Hebat" class="reward-badge-img empty" />
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
        const rewardIntroCol = document.querySelector('.reward-intro-col'); // Parent for arrows

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

            // Dynamically position arrows
            const highlightedCard = cards[1];
            if (highlightedCard) {
                // Use the container for positioning arrows, as it's the closest positioned ancestor for absolute arrows
                const containerRect = container.getBoundingClientRect();
                const highlightedCardRect = highlightedCard.getBoundingClientRect();

                // Calculate position relative to the intro-container's left edge
                const cardLeftInContainer = highlightedCardRect.left - containerRect.left;
                const cardRightInContainer = highlightedCardRect.right - containerRect.left;

                const arrowMargin = 10; // Space between arrow and card
                const arrowWidth = prevBtn.offsetWidth; // Assuming both arrows have same width

                // Position left arrow: its right edge should be 'arrowMargin' pixels from the card's left edge
                // Ensure left arrow doesn't go outside the parent element
                prevBtn.style.left = `${cardLeftInContainer - arrowWidth - arrowMargin}px`;

                // Position right arrow: its left edge should be 'arrowMargin' pixels from the card's right edge
                // Ensure right arrow doesn't go outside the parent element
                nextBtn.style.left = `${cardRightInContainer + arrowMargin}px`;

                // Adjust positioning if arrows appear outside the reward-intro-col boundaries
                const introColRect = rewardIntroCol.getBoundingClientRect();
                if (prevBtn.getBoundingClientRect().left < introColRect.left) {
                    prevBtn.style.left = '0px'; // Align with left edge of reward-intro-col
                }
                if (nextBtn.getBoundingClientRect().right > introColRect.right) {
                    nextBtn.style.left = `${introColRect.width - nextBtn.offsetWidth}px`; // Align with right edge
                }
            }
        };

        const handlePrevClick = () => {
            // Shift right: move last card to beginning
            const lastCard = cards.pop();
            cards.unshift(lastCard);

            // Reorder cards in DOM
            cards.forEach(card => {
                container.appendChild(card);
            });
            updateCardPositions();
        };

        const handleNextClick = () => {
            // Shift left: move first card to end
            const firstCard = cards.shift();
            cards.push(firstCard);

            // Reorder cards in DOM
            cards.forEach(card => {
                container.appendChild(card);
            });
            updateCardPositions();
        };

        prevBtn.addEventListener('click', handlePrevClick);
        nextBtn.addEventListener('click', handleNextClick);

        // Add click handlers to left and right cards
        const updateCardClickHandlers = () => {
            // Remove old listeners by cloning cards
            const leftCard = cards[0];
            const rightCard = cards[2];

            // Left card click shifts right (same as prev button)
            leftCard.style.cursor = 'pointer';
            leftCard.addEventListener('click', handlePrevClick);

            // Right card click shifts left (same as next button)
            rightCard.style.cursor = 'pointer';
            rightCard.addEventListener('click', handleNextClick);
        };

        updateCardClickHandlers();

        // Initial setup
        updateCardPositions();

        // Adjust on window resize
        window.addEventListener('resize', updateCardPositions);
    });
</script>

@endsection
