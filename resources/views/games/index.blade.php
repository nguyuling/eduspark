@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-4">🎮 Games</h1>
    <p>User Role: {{ auth()->user()?->role ?? 'guest' }}</p>
    <p>Games Count: {{ isset($games) ? $games->count() : 'undefined' }}</p>
    
    @if(isset($games) && $games->count() > 0)
        <div class="mt-6">
            <h2 class="text-xl font-bold mb-4">Available Games:</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($games as $game)
                <div class="bg-white dark:bg-gray-800 p-4 rounded border">
                    <h3 class="font-bold">{{ $game->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $game->description }}</p>
                    <p class="text-sm mt-2">
                        <strong>Difficulty:</strong> {{ ucfirst($game->difficulty) }}<br>
                        <strong>Category:</strong> {{ $game->category }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-gray-600 mt-6">No games found or games variable is not set correctly.</p>
    @endif
</div>
@endsection
