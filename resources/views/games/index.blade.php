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
</style>

<script>
    // Placeholder games data - in production this should come from the API
    const sampleGames = [
        {
            id: 1,
            name: 'Cosmic Defender',
            title: 'Cosmic Defender',
            description: 'Pertahanan luar angkasa yang menarik dengan soalan matematik',
            difficulty: 'medium'
        },
        {
            id: 2,
            name: 'Memory Match',
            title: 'Memory Match',
            description: 'Permainan ingatan yang membantu meningkatkan fokus dan ingatan',
            difficulty: 'easy'
        },
        {
            id: 3,
            name: 'Maze Quest',
            title: 'Maze Quest',
            description: 'Navigasi labirin sambil menjawab soalan pembelajaran',
            difficulty: 'medium'
        },
        {
            id: 4,
            name: 'Quiz Challenge',
            title: 'Quiz Challenge',
            description: 'Cabaran kuiz cepat dengan pelbagai topik',
            difficulty: 'hard'
        },
        {
            id: 5,
            name: 'Whack-a-Mole',
            title: 'Whack-a-Mole',
            description: 'Permainan pantas dengan soalan pendidikan',
            difficulty: 'easy'
        }
    ];

    document.addEventListener('DOMContentLoaded', async function() {
        const container = document.getElementById('gamesContainer');
        
        try {
            // Try to fetch from API first
            const response = await fetch('/api/games');
            const games = await response.json();
            
            if (games && games.length > 0) {
                renderGames(games);
                return;
            }
        } catch (error) {
            console.log('API not available, using sample games');
        }
        
        // Fall back to sample games
        renderGames(sampleGames);
    });

    function renderGames(games) {
        const container = document.getElementById('gamesContainer');
        container.innerHTML = '';
        
        if (!games || games.length === 0) {
            container.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: var(--muted);"><p>Belum ada permainan tersedia</p></div>';
            return;
        }
        
        games.forEach(game => {
            const gameName = game.name || game.title || 'Unknown Game';
            const difficultyClass = `difficulty-${(game.difficulty || 'easy').toLowerCase()}`;
            const card = document.createElement('div');
            card.className = 'game-card';
            card.innerHTML = `
                <div class="game-card-image">
                    ${getGameEmoji(gameName)}
                </div>
                <div class="game-card-content">
                    <div class="game-card-title">${escapeHtml(gameName)}</div>
                    <div class="game-card-description">${escapeHtml(game.description || 'Permainan edukatif yang menyenangkan')}</div>
                    <div class="game-card-footer">
                        <span class="game-difficulty ${difficultyClass}">
                            ${capitalizeFirst(game.difficulty || 'Easy')}
                        </span>
                    </div>
                </div>
            `;
            
            card.addEventListener('click', function() {
                // Link to game details or launch
                if (game.url) {
                    window.location.href = game.url;
                } else if (game.game_file) {
                    window.location.href = `/game/${game.id}`;
                } else {
                    alert('Permainan ini belum siap untuk dimainkan');
                }
            });
            
            container.appendChild(card);
        });
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
