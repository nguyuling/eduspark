@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
    <div class="header">
      <div>
        <div class="title">Forum</div>
        <div class="sub">Bincang topik berkenaan Sains Komputer di sini</div>
      </div>
      <a href="{{ route('forum.create') }}" class="btn btn-primary">
        Cipta Topik
      </a>
    </div>

        @if ($posts->isEmpty())
            <!-- Empty State -->
            <section class="panel panel-spaced" style="margin-top: 60px;">
              <div class="empty-state">
                <div style="font-size: 48px; margin-bottom: 16px;">💬</div>
                <p style="font-size: 18px; color: #98a0b3; margin: 0;">Belum ada topik diskusi</p>
                <p style="font-size: 14px; color: #98a0b3; margin-top: 8px;">Jadilah yang pertama untuk memulai diskusi</p>
              </div>
            </section>
        @else
            <!-- Posts List -->
            <section class="panel panel-spaced" style="margin-top: 60px;">
                @foreach ($posts as $post)
                    <div class="post-card">
                        <div class="post-header">
                            <!-- Post Content -->
                            <div class="post-content">
                                <h2 class="post-title">
                                    <a href="{{ route('forum.show', $post->id) }}">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                <p class="post-excerpt">{{ Str::limit($post->content, 200) }}</p>
                                <div class="post-meta">
                                    <span>👤 {{ $post->author_name }}</span>
                                    <span>🕐 {{ $post->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            @if (auth()->check() && auth()->id() == $post->user_id)
                                <div class="post-actions">
                                    <a href="{{ route('forum.edit', $post->id) }}" class="post-action-link">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('forum.destroy', $post->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="post-action-delete" onclick="return confirm('Hapus topik ini?')">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </section>
        @endif
    </div>
</div>

@endsection
