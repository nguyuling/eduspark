@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">Permainan</div>
                <div class="sub">Mainkan permainan edukatif untuk meningkatkan prestasi anda</div>
            </div>
        </div>

        <div class="games-container" id="gamesContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; margin-top: 20px;">
            <!-- Games will be loaded here via JavaScript -->
            <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--muted);">
                <p>Sedang memuatkan permainan...</p>
            </div>
        </div>
    </main>
</div>

<style>
    .game-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
    }

    .game-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(106, 77, 247, 0.2);
        border-color: var(--accent);
    }

    .game-card-image {
        width: 100%;
        height: 160px;
        background: linear-gradient(135deg, rgba(106,77,247,0.1), rgba(230,57,70,0.1));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
    }

    .game-card-content {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .game-card-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text);
    }

    .game-card-description {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 12px;
        flex: 1;
    }

    .game-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid var(--border);
    }

    .game-difficulty {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .difficulty-easy {
        background: rgba(74, 222, 128, 0.2);
        color: #22c55e;
    }

    .difficulty-medium {
        background: rgba(251, 146, 60, 0.2);
        color: #f97316;
    }

    .difficulty-hard {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
    }

    .play-btn {
        padding: 6px 14px;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .play-btn:hover {
        opacity: 0.9;
        transform: scale(1.05);
    }
</style>

<script>
    // Game database with actual game implementations
    const gameDatabase = [
        {
            id: 1,
            slug: 'cosmic-defender',
            name: 'Cosmic Defender',
            title: 'Cosmic Defender',
            description: 'Pertahanan luar angkasa yang menarik dengan aksi penembakan',
            difficulty: 'medium',
            route: '{{ route("games.cosmic") }}'
        },
        {
            id: 2,
            slug: 'memory-game',
            name: 'Memory Match',
            title: 'Memory Match',
            description: 'Permainan ingatan yang membantu meningkatkan fokus dan ingatan',
            difficulty: 'easy',
            route: '{{ route("games.memory") }}'
        },
        {
            id: 3,
            slug: 'maze-game',
            name: 'Maze Quest',
            title: 'Maze Quest',
            description: 'Navigasi labirin dan capai pintu keluar dengan cepat',
            difficulty: 'medium',
            route: '{{ route("games.maze") }}'
        },
        {
            id: 4,
            slug: 'quiz-challenge',
            name: 'Quiz Challenge',
            title: 'Quiz Challenge',
            description: 'Cabaran kuiz cepat dengan soalan pengaturcaraan Python',
            difficulty: 'hard',
            route: '{{ route("games.quiz") }}'
        },
        {
            id: 5,
            slug: 'whack-mole',
            name: 'Whack-a-Mole',
            title: 'Whack-a-Mole',
            description: 'Permainan pantas - tumbuk tikus yang muncul',
            difficulty: 'easy',
            route: '{{ route("games.whack") }}'
        }
    ];

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('gamesContainer');
        container.innerHTML = '';
        
        if (!gameDatabase || gameDatabase.length === 0) {
            container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--muted);"><p>Belum ada permainan tersedia</p></div>';
            return;
        }
        
        gameDatabase.forEach(game => {
            const difficultyClass = `difficulty-${(game.difficulty || 'easy').toLowerCase()}`;
            const card = document.createElement('div');
            card.className = 'game-card';
            card.innerHTML = `
                <div class="game-card-image">
                    ${getGameEmoji(game.name)}
                </div>
                <div class="game-card-content">
                    <div class="game-card-title">${escapeHtml(game.name)}</div>
                    <div class="game-card-description">${escapeHtml(game.description || 'Permainan edukatif yang menyenungkan')}</div>
                    <div class="game-card-footer">
                        <span class="game-difficulty ${difficultyClass}">
                            ${capitalizeFirst(game.difficulty || 'Easy')}
                        </span>
                        <button class="play-btn" onclick="playGame('${game.reactRoute}')">
                            Main ▶
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(card);
        });
    });

    function playGame(route) {
        // Redirect to game page
        window.location.href = route;
    }
    
    function getGameEmoji(gameName) {
        const emojiMap = {
            'cosmic': '🚀',
            'whack': '🔨',
            'memory': '🧠',
            'maze': '🗺️',
            'puzzle': '🧩',
            'quiz': '❓',
            'snake': '🐍',
            'flappy': '🐦'
        };
        
        for (const [key, emoji] of Object.entries(emojiMap)) {
            if (gameName.toLowerCase().includes(key)) {
                return emoji;
            }
        }
        return '🎮';
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
</script>
@endsection
