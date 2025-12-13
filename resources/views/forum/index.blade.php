@extends('layouts.app')

@section('content')
<div style="margin-left: 280px; padding: 40px;">
    <div class="max-w-6xl">
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
            <h1 style="font-size: 32px; font-weight: 700; color: #0b1220; margin: 0;">Forum Diskusi</h1>
            <a href="{{ route('forum.create') }}" style="padding: 12px 24px; background: linear-gradient(90deg, #6A4DF7, #9C7BFF); color: white; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 16px; transition: 0.2s; border: none; cursor: pointer;">
                Cipta Topik
            </a>
        </div>

        @if ($posts->isEmpty())
            <!-- Empty State -->
            <div style="background: white; border-radius: 14px; border: 1px solid rgba(13,18,25,0.06); padding: 48px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <div style="font-size: 48px; margin-bottom: 16px;">💬</div>
                <p style="font-size: 18px; color: #98a0b3; margin: 0;">Belum ada topik diskusi</p>
                <p style="font-size: 14px; color: #98a0b3; margin-top: 8px;">Jadilah yang pertama untuk memulai diskusi</p>
            </div>
        @else
            <!-- Posts List -->
            <div style="display: grid; gap: 16px;">
                @foreach ($posts as $post)
                    <div style="background: white; border-radius: 14px; border: 1px solid rgba(13,18,25,0.06); padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: all 0.2s ease; cursor: pointer;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <!-- Post Content -->
                            <div style="flex: 1;">
                                <h2 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">
                                    <a href="{{ route('forum.show', $post->id) }}" style="color: #0b1220; text-decoration: none; transition: 0.2s;">
                                        {{ $post->title }}
                                    </a>
                                </h2>
                                <p style="color: #6b7280; margin: 12px 0; font-size: 14px; line-height: 1.6;">{{ Str::limit($post->content, 200) }}</p>
                                <div style="display: flex; align-items: center; gap: 16px; font-size: 13px; color: #98a0b3; margin-top: 12px;">
                                    <span>👤 {{ $post->author_name }}</span>
                                    <span>🕐 {{ $post->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            @if (auth()->check() && auth()->id() == $post->user_id)
                                <div style="display: flex; gap: 12px; margin-left: 16px; flex-shrink: 0;">
                                    <a href="{{ route('forum.edit', $post->id) }}" style="color: #6A4DF7; text-decoration: none; font-size: 14px; font-weight: 500; transition: 0.2s; cursor: pointer;">
                                        ✏️ Edit
                                    </a>
                                    <form action="{{ route('forum.destroy', $post->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="color: #E63946; background: none; border: none; text-decoration: none; font-size: 14px; font-weight: 500; transition: 0.2s; cursor: pointer;" onclick="return confirm('Hapus topik ini?')">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<style>
    body.light {
        background: #f5f7ff;
        color: #0b1220;
    }
    
    body.dark {
        background: #071026;
        color: #e6eef8;
    }
    
    a:hover {
        opacity: 0.9;
    }
    
    div[style*="background: white"]:hover {
        box-shadow: 0 6px 25px rgba(0,0,0,0.12) !important;
        transform: translateY(-2px);
    }
</style>
@endsection
