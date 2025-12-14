@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
        <div>
            <div class="title">Forum</div>
            <div class="sub">Perbincangan dan pertanyaan komuniti</div>
        </div>
        <a href="{{ route('forum.create') }}" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer; margin-top:15px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
            <i class="bi bi-plus-lg"></i>
            Cipta Post
        </a>
    </div>

    <div>
        {{-- Search Form --}}
        <section class="panel" style="margin-bottom:20px;">
            <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700;">Cari Post</h2>
            <form method="GET" action="{{ route('forum.index') }}" class="flex items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search posts..." 
                       class="flex-1">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('forum.index') }}" class="btn-secondary">
                        Clear
                    </a>
                @endif
            </form>
        </section>

        {{-- Posts List --}}
        <div class="space-y-4">
            @forelse ($posts as $post)
                <div class="card" style="position:relative;">
                    {{-- ACTION BUTTONS - Only show for post creator --}}
                    @if(Auth::check() && Auth::id() === $post->author_id)
                    <div style="display:flex; gap:12px; position:absolute; top:20px; right:20px;">
                        <a href="{{ route('forum.edit', $post->id) }}" 
                           style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" 
                           onmouseover="this.style.opacity='0.7';" 
                           onmouseout="this.style.opacity='1';" 
                           title="Kemaskini">
                            <i class="bi bi-pencil-square"></i>
                        </a>

                        <form action="{{ route('forum.destroy', $post->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    style="display:inline-flex; align-items:center; justify-content:center; background:transparent; box-shadow: none; border:none; color:var(--danger); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" 
                                    onmouseover="this.style.opacity='0.7';" 
                                    onmouseout="this.style.opacity='1';" 
                                    title="Padam"
                                    onclick="return confirm('Anda pasti ingin memadam post ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif

                    {{-- Post Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            @php
                                $userName = $post->user->name ?? $post->author_name;
                                $firstLetter = strtoupper(substr($userName, 0, 1));
                                $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B88B', '#52C41A'];
                                $colorIndex = abs(crc32($userName)) % count($colors);
                                $bgColor = $colors[$colorIndex];
                            @endphp
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg" style="background-color: {{ $bgColor }};">
                                {{ $firstLetter }}
                            </div>
                            <div>
                                <p class="font-semibold">{{ $userName }}</p>
                                <p class="text-xs opacity-60">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Post Title & Content --}}
                    <div class="mb-4" style="margin-left:16px;">
                        <h2 class="text-xl font-bold mb-2">
                            <a href="{{ route('forum.show', $post->id) }}" class="link-primary hover:opacity-80 transition">
                                {{ $post->title }}
                            </a>
                        </h2>
                        <p class="opacity-80">{{ Str::limit($post->content, 200) }}</p>
                    </div>

                </div>
            @empty
                <p class="text-center py-12 opacity-60">No posts found.</p>
            @endforelse
        </div>
    </div>

    <script>
        function toggleReplyForm(postId) {
            const form = document.getElementById('reply-form-' + postId);
            form.classList.toggle('hidden');
        }
    </script>

    </main>
</div>
@endsection
