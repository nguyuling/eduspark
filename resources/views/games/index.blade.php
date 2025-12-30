@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 py-8">
    <!-- Success Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 dark:bg-green-900 border-2 border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-4 rounded-lg font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('success_undo'))
        <div class="mb-6 bg-green-100 dark:bg-green-900 border-2 border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-4 rounded-lg flex justify-between items-center">
            <span class="font-medium">{{ session('success_undo') }}</span>
            <form action="{{ route('games.restore', session('undo_game_id')) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="bg-green-700 hover:bg-green-800 text-white font-bold py-1 px-4 rounded text-sm ml-4">
                    ↩️ Undo
                </button>
            </form>
        </div>
    @endif

    @if(auth()->user()->role === 'teacher')
        {{-- TEACHER VIEW --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 bg-white dark:bg-gray-800 p-6 rounded-lg border-2 border-gray-300 dark:border-gray-700">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">🎮 Manage Games</h1>
                <p class="text-gray-700 dark:text-gray-300 text-base">Create, edit, and manage educational games for your students</p>
            </div>
            <a href="{{ route('teacher.games.create') }}" class="bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-6 rounded-lg inline-flex items-center gap-2 whitespace-nowrap">
                <span class="text-lg">+</span> Create New Game
            </a>
        </div>

        <!-- Teacher Stats -->
        @if($games->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border-2 border-gray-300 dark:border-gray-700 shadow">
                <div class="text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Total Games</div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $games->count() }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border-2 border-gray-300 dark:border-gray-700 shadow">
                <div class="text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Published</div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $games->where('is_published', true)->count() }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border-2 border-gray-300 dark:border-gray-700 shadow">
                <div class="text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Drafts</div>
                <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $games->where('is_published', false)->count() }}</div>
            </div>
        </div>

        <!-- Teacher Game Management Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-300 dark:border-gray-700 overflow-hidden shadow-lg">
            <div class="px-4 sm:px-6 py-4 bg-gray-100 dark:bg-gray-900 border-b-2 border-gray-300 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Games List</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800 border-b-2 border-gray-300 dark:border-gray-700">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-gray-200 uppercase tracking-wider border-r-2 border-gray-300 dark:border-gray-700">Title</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-gray-200 uppercase tracking-wider border-r-2 border-gray-300 dark:border-gray-700">Category</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-gray-200 uppercase tracking-wider border-r-2 border-gray-300 dark:border-gray-700">Difficulty</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-gray-200 uppercase tracking-wider border-r-2 border-gray-300 dark:border-gray-700">Status</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-sm font-bold text-gray-900 dark:text-gray-200 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y-2 divide-gray-300 dark:divide-gray-700">
                        @foreach($games as $game)
                        <tr class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                            <!-- Title Column -->
                            <td class="px-4 sm:px-6 py-4 border-r-2 border-gray-300 dark:border-gray-700">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-900 dark:text-white text-sm sm:text-base">{{ $game->title }}</span>
                                    <span class="text-xs text-gray-700 dark:text-gray-400 mt-1">{{ Str::limit($game->description, 60) ?? 'No description' }}</span>
                                </div>
                            </td>
                            
                            <!-- Category Column -->
                            <td class="px-4 sm:px-6 py-4 border-r-2 border-gray-300 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-300">
                                    {{ $game->category ?? '-' }}
                                </span>
                            </td>
                            
                            <!-- Difficulty Column -->
                            <td class="px-4 sm:px-6 py-4 border-r-2 border-gray-300 dark:border-gray-700">
                                <span class="px-3 py-1 rounded-full text-xs font-bold 
                                    {{ $game->difficulty === 'easy' ? 'bg-green-200 text-green-900 dark:bg-green-900 dark:text-green-200' : 
                                       ($game->difficulty === 'medium' ? 'bg-yellow-200 text-yellow-900 dark:bg-yellow-900 dark:text-yellow-200' : 
                                       'bg-red-200 text-red-900 dark:bg-red-900 dark:text-red-200') }}">
                                    {{ ucfirst($game->difficulty) }}
                                </span>
                            </td>
                            
                            <!-- Status Column -->
                            <td class="px-4 sm:px-6 py-4 border-r-2 border-gray-300 dark:border-gray-700">
                                @if($game->is_published)
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-200 text-blue-900 dark:bg-blue-900 dark:text-blue-200">Published</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-900 dark:bg-gray-700 dark:text-gray-200">Draft</span>
                                @endif
                            </td>
                            
                            <!-- Actions Column -->
                            <td class="px-4 sm:px-6 py-4">
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('games.leaderboard', $game->id) }}" class="text-purple-700 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 font-bold text-sm flex items-center gap-1">
                                        📊 Leaderboard
                                    </a>
                                    <a href="{{ route('teacher.games.edit', $game->id) }}" class="text-blue-700 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-bold text-sm flex items-center gap-1">
                                        ✏️ Edit
                                    </a>
                                    <button type="button" onclick="showDeleteConfirm({{ $game->id }}, '{{ $game->title }}')" class="text-red-700 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 font-bold text-sm flex items-center gap-1">
                                        🗑️ Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-300 dark:border-gray-700 p-8 sm:p-12 text-center shadow">
            <div class="text-4xl text-gray-400 dark:text-gray-600 mb-4">🎮</div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">No games created yet</h3>
            <p class="text-gray-700 dark:text-gray-400 mb-6 text-base">Start by creating your first educational game!</p>
            <a href="{{ route('teacher.games.create') }}" class="inline-block bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-8 rounded-lg text-base transition-colors duration-200">
                Create Your First Game
            </a>
        </div>
        @endif

    @else
        {{-- STUDENT VIEW --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border-2 border-gray-300 dark:border-gray-700 mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-2">🎮 Available Games</h1>
            <p class="text-gray-700 dark:text-gray-300 text-base">Play educational games to learn and earn rewards!</p>
        </div>

        @if($games->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($games as $game)
                <div class="bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-300 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-600 h-40 flex items-center justify-center">
                        <span class="text-5xl">🎮</span>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">{{ $game->title }}</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-400 mb-4">{{ Str::limit($game->description, 80) ?? 'No description' }}</p>
                        
                        <div class="flex flex-wrap gap-2 mb-5">
                            <span class="px-3 py-1 rounded-full text-xs font-bold 
                                {{ $game->difficulty === 'easy' ? 'bg-green-200 text-green-900 dark:bg-green-900 dark:text-green-200' : 
                                   ($game->difficulty === 'medium' ? 'bg-yellow-200 text-yellow-900 dark:bg-yellow-900 dark:text-yellow-200' : 
                                   'bg-red-200 text-red-900 dark:bg-red-900 dark:text-red-200') }}">
                                {{ ucfirst($game->difficulty) }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-200 text-blue-900 dark:bg-blue-900 dark:text-blue-200">
                                {{ $game->category ?? 'General' }}
                            </span>
                        </div>

                        <div class="flex">
                            <a href="{{ route('games.play', $game->id) }}" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded text-center text-base">
                                ▶️ Play Now
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <!-- Empty State for Students -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border-2 border-gray-300 dark:border-gray-700 p-8 sm:p-12 text-center shadow">
                <div class="text-4xl text-gray-400 dark:text-gray-600 mb-4">🎮</div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">No games available</h3>
                <p class="text-gray-700 dark:text-gray-400 text-base">Check back later for new educational games!</p>
            </div>
        @endif
    @endif
</div>
@endsection

<script>
function showDeleteConfirm(gameId, gameTitle) {
    const modal = document.createElement('div');
    modal.id = 'deleteModal';
    modal.className = 'fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 p-4';
    modal.innerHTML = `
        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md w-full border-4 border-gray-300 dark:border-gray-700">
            <div class="text-center">
                <div class="text-5xl mb-4 text-yellow-600 dark:text-yellow-400">⚠️</div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Delete Game?</h3>
                <p class="text-gray-700 dark:text-gray-300 mb-2">Are you sure you want to delete:</p>
                <p class="font-bold text-lg text-gray-900 dark:text-white mb-4">"${gameTitle}"</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Note: You can restore it from the undo notification if needed.</p>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <button type="button" onclick="closeDeleteModal()" class="px-6 py-3 bg-gray-300 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg font-bold hover:bg-gray-400 dark:hover:bg-gray-600 transition-colors duration-200">
                        Cancel
                    </button>
                    <form action="/games/${gameId}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-red-700 hover:bg-red-800 text-white rounded-lg font-bold transition-colors duration-200">
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
    
    // Prevent scrolling when modal is open
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    if (modal) modal.remove();
    document.body.style.overflow = 'auto';
}
</script>

<style>
    /* Force High Contrast */
    .text-gray-900 {
        color: #111827 !important;
        font-weight: 600;
    }
    
    .text-gray-700 {
        color: #374151 !important;
    }
    
    .text-gray-600 {
        color: #4b5563 !important;
    }
    
    .dark .text-white {
        color: #f9fafb !important;
    }
    
    .dark .text-gray-300 {
        color: #d1d5db !important;
    }
    
    .dark .text-gray-400 {
        color: #9ca3af !important;
    }
    
    /* Borders */
    .border-2 {
        border-width: 2px !important;
    }
    
    .border-gray-300 {
        border-color: #d1d5db !important;
    }
    
    .dark .border-gray-700 {
        border-color: #4b5563 !important;
    }
    
    /* Backgrounds */
    .bg-white {
        background-color: #ffffff !important;
    }
    
    .bg-gray-100 {
        background-color: #f3f4f6 !important;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb !important;
    }
    
    .dark .bg-gray-800 {
        background-color: #1f2937 !important;
    }
    
    .dark .bg-gray-900 {
        background-color: #111827 !important;
    }
    
    /* Make all text bolder */
    th, td, p, span:not(.text-xs) {
        font-weight: 500 !important;
    }
    
    /* Remove any transparency */
    .bg-opacity-50, .bg-opacity-70 {
        opacity: 1 !important;
    }
    
    /* Ensure button text is visible */
    .bg-blue-700 {
        background-color: #1d4ed8 !important;
    }
    
    .bg-red-700 {
        background-color: #b91c1c !important;
    }
    
    .bg-green-700 {
        background-color: #15803d !important;
    }
</style>