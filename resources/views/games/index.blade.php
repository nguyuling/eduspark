@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('success_undo'))
        <div class="mb-6 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-4 rounded-lg flex justify-between items-center">
            <span>{{ session('success_undo') }}</span>
            <form action="{{ route('games.restore', session('undo_game_id')) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-4 rounded text-sm ml-4">
                    ↩️ Undo
                </button>
            </form>
        </div>
    @endif

    @if(auth()->user()->role === 'teacher')
        {{-- TEACHER VIEW --}}
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">🎮 Manage Games</h1>
                <p class="text-gray-600 dark:text-gray-400">Create, edit, and manage educational games for your students</p>
            </div>
            <a href="{{ route('teacher.games.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg inline-flex items-center gap-2">
                <span>+</span> Create Game
            </a>
        </div>

        <!-- Teacher Stats -->
        @if($games->count() > 0)
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
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
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
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($game->description, 50) ?? 'No description' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $game->category ?? '-' }}</td>
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
                        <td class="px-6 py-4 text-sm space-x-3">
                            <a href="{{ route('games.leaderboard', $game->id) }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 font-medium">📊 Leaderboard</a>
                            <a href="{{ route('teacher.games.edit', $game->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">✏️ Edit</a>
                            <button type="button" onclick="showDeleteConfirm({{ $game->id }}, '{{ $game->title }}')" class="text-red-600 hover:text-red-800 dark:text-red-400 font-medium">🗑️ Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <p class="text-gray-600 dark:text-gray-400 mb-6 text-lg">No games created yet</p>
            <a href="{{ route('teacher.games.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg">
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
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $game->description ?? 'No description' }}</p>
                        
                        <div class="flex gap-2 mb-4">
                            <span class="px-2 py-1 rounded text-xs font-semibold 
                                {{ $game->difficulty === 'easy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   ($game->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                   'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                {{ ucfirst($game->difficulty) }}
                            </span>
                            <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ $game->category ?? 'General' }}</span>
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
                <p class="text-gray-600 dark:text-gray-400 text-lg">No games available yet</p>
            </div>
        @endif
    @endif
</div>
@endsection

<script>
function showDeleteConfirm(gameId, gameTitle) {
    const modal = document.createElement('div');
    modal.id = 'deleteModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-sm mx-4">
            <div class="text-center">
                <div class="text-5xl mb-4">⚠️</div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Delete Game?</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-2">"<strong>${gameTitle}</strong>" will be deleted.</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mb-6">Don't worry! You can restore it from the undo notification.</p>
                
                <div class="flex gap-3 justify-center">
                    <button type="button" onclick="closeDeleteModal()" class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white rounded-lg font-semibold hover:bg-gray-400 dark:hover:bg-gray-500">
                        Cancel
                    </button>
                    <form action="/games/${gameId}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold">
                            Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeDeleteModal();
    });
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) modal.remove();
}
</script>
