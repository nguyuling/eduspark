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
        cursor: pointer;
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
            route: '/games/cosmic-defender'
        },
        {
            id: 2,
            slug: 'memory-game',
            name: 'Memory Match',
            title: 'Memory Match',
            description: 'Permainan ingatan yang membantu meningkatkan fokus dan ingatan',
            difficulty: 'easy',
            route: '/games/memory-match'
        },
        {
            id: 3,
            slug: 'maze-game',
            name: 'Maze Quest',
            title: 'Maze Quest',
            description: 'Navigasi labirin dan capai pintu keluar dengan cepat',
            difficulty: 'medium',
            route: '/games/maze-game'
        },
        {
            id: 4,
            slug: 'quiz-challenge',
            name: 'Quiz Challenge',
            title: 'Quiz Challenge',
            description: 'Cabaran kuiz cepat dengan soalan pengaturcaraan Python',
            difficulty: 'hard',
            route: '/games/quiz-challenge'
        },
        {
            id: 5,
            slug: 'whack-mole',
            name: 'Whack-a-Mole',
            title: 'Whack-a-Mole',
            description: 'Permainan pantas - tumbuk tikus yang muncul',
            difficulty: 'easy',
            route: '/games/whack-a-mole'
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
            card.className = 'panel game-card';
            card.setAttribute('data-route', game.route);
            card.style.cursor = 'pointer';
            card.innerHTML = `
                <div style="display:flex; flex-direction:column; height:100%; gap:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div style="font-size:48px;">
                            ${getGameEmoji(game.name)}
                        </div>
                        <span class="game-difficulty ${difficultyClass}">
                            ${getDifficultyInMalay(game.difficulty || 'easy')}
                        </span>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:16px; font-weight:700; margin-bottom:8px; color:inherit;">${escapeHtml(game.name)}</div>
                        <div style="font-size:13px; color:var(--muted); line-height:1.5;">${escapeHtml(game.description || 'Permainan edukatif yang menyenungkan')}</div>
                    </div>
                </div>
            `;
            
            // Add click handler after innerHTML is set
            card.addEventListener('click', function() {
                playGame(game.route);
            });
            
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
    
    function getDifficultyInMalay(difficulty) {
        const difficultyMap = {
            'easy': 'Mudah',
            'medium': 'Sederhana',
            'hard': 'Sukar'
        };
        return difficultyMap[difficulty.toLowerCase()] || 'Mudah';
    }
</script>
@endsection
