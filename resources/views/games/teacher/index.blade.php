@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">🎮 Manage Games</h1>
            <p class="text-gray-600 dark:text-gray-400">Create, edit, and manage educational games</p>
        </div>
        <a href="{{ route('teacher.games.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg flex items-center gap-2">
            <span>+</span> Add New Game
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-100 px-4 py-4 rounded-lg flex justify-between items-center shadow-md">
            <div>
                <span class="font-semibold">✓ {{ session('success') }}</span>
                @if(session('undo_available') && session('undo_game_id'))
                    <p class="text-sm mt-1">You can undo this action below.</p>
                @endif
            </div>
            @if(session('undo_available') && session('undo_game_id'))
                <form action="{{ route('teacher.games.restore', session('undo_game_id')) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg text-sm ml-4 transition whitespace-nowrap">
                        ↶ Undo Delete
                    </button>
                </form>
            @endif
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="text-gray-600 dark:text-gray-400 font-semibold text-sm">Total Games</div>
            <div class="text-3xl font-bold mt-2 text-gray-800 dark:text-white">{{ $games->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="text-gray-600 dark:text-gray-400 font-semibold text-sm">Published Games</div>
            <div class="text-3xl font-bold mt-2 text-gray-800 dark:text-white">{{ $games->where('is_published', true)->count() }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="text-gray-600 dark:text-gray-400 font-semibold text-sm">Draft Games</div>
            <div class="text-3xl font-bold mt-2 text-gray-800 dark:text-white">{{ $games->where('is_published', false)->count() }}</div>
        </div>
    </div>

    <!-- Games Table -->
    @if($games->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Game Title</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Category</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Difficulty</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Created</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($games as $game)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $game->title }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $game->category ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                {{ $game->difficulty === 'easy' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   ($game->difficulty === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                   'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200') }}">
                                {{ ucfirst($game->difficulty) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($game->is_published)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Published</span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $game->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex gap-2">
                                <a href="{{ route('teacher.games.edit', $game->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">Edit</a>
                                <button type="button" onclick="showDeleteModal({{ $game->id }}, '{{ addslashes($game->title) }}')" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">Delete</button>
                            </div>
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
</div>
@endsection

<script>
let deleteModalData = null;

function showDeleteModal(gameId, gameTitle) {
    deleteModalData = { gameId, gameTitle };
    const modal = document.getElementById('deleteModal');
    document.getElementById('gameTitle').textContent = gameTitle;
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteModalData = null;
}

function confirmDelete() {
    if (!deleteModalData) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/teacher/games/${deleteModalData.gameId}`;
    form.innerHTML = `
        @csrf
        @method('DELETE')
    `;
    document.body.appendChild(form);
    form.submit();
    closeDeleteModal();
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeDeleteModal();
    }
});

// Close on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-sm w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center mb-2">Delete Game</h3>
            <p class="text-gray-600 dark:text-gray-400 text-center mb-4">
                Are you sure you want to delete <strong id="gameTitle"></strong>?
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500 text-center mb-6">
                <span class="text-green-600 dark:text-green-400 font-semibold">✓ Don't worry!</span> You can undo this action from the success message.
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 font-medium transition">
                    Cancel
                </button>
                <button type="button" onclick="confirmDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
