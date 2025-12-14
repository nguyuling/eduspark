@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
            <div>
                <div class="title">{{ $post->title }}</div>
                <div class="sub">Lihat post dan balasan dari komuniti</div>
            </div>
            <a href="{{ route('forum.index') }}" 
               style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer; margin-top:15px;" 
               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" 
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
                <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
            </a>
        </div>

        <div class="panel-spaced" style="margin-left:40; margin-right:40; margin-top:0;">
            {{-- POST CONTENT SECTION --}}
            <section class="panel" style="margin-left:0; margin-right:0; margin-top:0; margin-bottom:20px; padding:20px;">
                {{-- AUTHOR INFO --}}
                <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px; padding-bottom:16px; border-bottom:2px solid rgba(0,0,0,0.05);">
                    <img src="{{ $post->user->avatar ?? $post->author_avatar }}" alt="{{ $post->user->name ?? $post->author_name }}" style="width:52px; height:52px; border-radius:50%; object-fit:cover; border:2px solid var(--accent);">
                    <div style="flex:1;">
                        <p style="font-weight:700; margin-bottom:4px; font-size:15px;">{{ $post->user->name ?? $post->author_name }}</p>
                        <p style="font-size:12px; color:var(--muted);">{{ $post->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                {{-- POST CONTENT --}}
                <div style="color:inherit; line-height:1.8; white-space:pre-wrap; font-size:15px; background:rgba(106,77,247,0.02); border-radius:8px; border-left:4px solid var(--accent); padding:12px 18px; margin-top:16px;">
                    {{ $post->content }}
                </div>
            </section>

            {{-- REPLIES SECTION --}}
            <section class="panel">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <h2 style="margin:0; font-size:18px; font-weight:700;">Balasan ({{ $post->replies->count() }})</h2>
                    <button type="button" onclick="toggleReplyForm()" 
                            style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" 
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4);'" 
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3);'">
                        <i class="bi bi-reply" style="margin-right:6px;"></i>Balas
                    </button>
                </div>

                {{-- REPLY FORM (HIDDEN BY DEFAULT) --}}
                <div id="reply-form-section" style="display:none; margin-bottom:20px; padding:16px; background:rgba(106,77,247,0.05); border-radius:8px; border:2px solid rgba(106,77,247,0.1);">
                    <form action="{{ route('forum.reply', $post->id) }}" method="POST">
                        @csrf

                        <div style="margin-bottom:12px;">
                            <textarea name="reply"
                                      style="width:100%; padding:12px 14px; border:2px solid #d1d5db; background:transparent; color:inherit; box-sizing:border-box; border-radius:8px; transition:border-color .2s ease, background .2s ease; font-size:14px; font-family:inherit; resize:vertical;"
                                      rows="3"
                                      placeholder="Tulis balasan anda..."></textarea>
                            @error('reply')
                                <p style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4);'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3);'">
                            <i class="bi bi-send" style="margin-right:6px;"></i>Hantar
                        </button>
                    </form>
                </div>

                {{-- REPLIES LIST --}}
                <div style="margin-bottom:12px;">
                    @forelse ($post->replies as $reply)
                        <div style="border-left:4px solid var(--accent); background:rgba(106,77,247,0.03); padding:14px; border-radius:6px; margin-bottom:12px;">
                            {{-- REPLY HEADER --}}
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                                <img src="{{ $reply->author_avatar }}" alt="{{ $reply->author_name }}" style="width:32px; height:32px; border-radius:50%; object-fit:cover;">
                                <div>
                                    <p style="font-weight:600; font-size:14px; margin:0;">{{ $reply->author_name }}</p>
                                    <p style="font-size:11px; color:var(--muted); margin:2px 0 0 0;">{{ $reply->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>

                            {{-- REPLY CONTENT --}}
                            <p style="color:inherit; line-height:1.5; white-space:pre-wrap; font-size:13px; margin:0;">{{ $reply->content }}</p>
                        </div>
                    @empty
                        <p style="text-align:center; color:var(--muted); padding:24px; font-size:14px;">Tiada balasan lagi. Jadilah yang pertama!</p>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
</div>

<script>
    function toggleReplyForm() {
        const replyForm = document.getElementById('reply-form-section');
        if (replyForm.style.display === 'none') {
            replyForm.style.display = 'block';
        } else {
            replyForm.style.display = 'none';
        }
    }
</script>

@endsection
