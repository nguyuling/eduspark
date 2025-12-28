@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(auth()->user()->role === 'teacher')
        {{-- TEACHER VIEW --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">🎮 Manage Games</h1>
                <p class="text-gray-600 dark:text-gray-400">Edit or delete games, and view student performance</p>
            </div>
            <a href="{{ route('teacher.games.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg flex items-center gap-2">
                <span>+</span> Add New Game
            </a>
        </div>

        <!-- Teacher Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-gray-600 dark:text-gray-400 font-semibold text-sm">Total Games</div>
                <div class="text-3xl font-bold mt-2 text-gray-800 dark:text-white">{{ $games->count() }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-gray-600 dark:text-gray-400 font-semibold text-sm">Published</div>
                <div class="text-3xl font-bold mt-2 text-gray-800 dark:text-white">{{ $games->where('is_published', true)->count() }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="text-gray-600 dark:text-gray-400 font-semibold text-sm">Drafts</div>
                <div class="text-3xl font-bold mt-2 text-gray-800 dark:text-white">{{ $games->where('is_published', false)->count() }}</div>
            </div>
        </div>

        <!-- Teacher Game Management Table -->
        @if($games->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Title</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Category</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Difficulty</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($games as $game)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $game->title }}</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($game->description, 50) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $game->category }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded text-xs font-semibold 
                                    {{ $game->difficulty === 'easy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($game->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                       'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                    {{ ucfirst($game->difficulty) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($game->is_published)
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Published</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm space-x-2">
                                <a href="{{ route('games.leaderboard', $game->id) }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 font-medium">📊 Leaderboard</a>
                                <a href="{{ route('teacher.games.edit', $game->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">✏️ Edit</a>
                                <form action="{{ route('games.destroy', $game->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this game?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 font-medium">🗑️ Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
                <p class="text-gray-600 dark:text-gray-400 mb-4">No games created yet</p>
                <a href="{{ route('teacher.games.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                    Create Your First Game
                </a>
            </div>
        @endif

    @else
        {{-- STUDENT VIEW --}}
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-8">🎮 Games</h1>

        @if($games->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($games as $game)
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-40 flex items-center justify-center">
                        <span style="font-size: 3rem;">🎮</span>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">{{ $game->title }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $game->description }}</p>
                        
                        <div class="flex gap-2 mb-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold 
                                {{ $game->difficulty === 'easy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   ($game->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                   'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                {{ ucfirst($game->difficulty) }}
                            </span>
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ $game->category }}</span>
                        </div>

                        <div class="flex gap-2">
                            <a href="#" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center text-sm">
                                ▶️ Play
                            </a>
                            <a href="{{ route('games.leaderboard', $game->id) }}" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center text-sm">
                                📊 Leaderboard
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
                <p class="text-gray-600 dark:text-gray-400">No games available yet</p>
            </div>
        @endif
    @endif
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
                        <button class="play-btn" onclick="playGame('${game.route}')">
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
