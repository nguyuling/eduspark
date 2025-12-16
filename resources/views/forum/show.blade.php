@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header" style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px;">
            <div style="flex:1; min-width:0;">
                <div class="title">{{ $post->title }}</div>
                <div class="sub">Lihat post dan balasan dari komuniti</div>
            </div>
            <a href="{{ route('forum.index') }}" class="btn-kembali" style="display:inline-block !important; margin-top:15px; padding:12px 24px !important; background:transparent !important; color:#6A4DF7 !important; border:2px solid #6A4DF7 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important; white-space:nowrap; flex-shrink:0;" onmouseover="this.style.background='rgba(106,77,247,0.1)'" onmouseout="this.style.background='transparent'">
                <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
            </a>
        </div>

        <div style="margin-top:0;">
            {{-- POST CONTENT SECTION --}}
            <section class="panel" style="margin-bottom:20px; margin-top:10px;">
                {{-- REPLY BUTTON (TOP RIGHT) --}}
                <button type="button" onclick="toggleReplyForm()" 
                        style="position:absolute; top:20px; right:20px; display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" 
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4);'" 
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3);'">
                    <i class="bi bi-reply" style="margin-right:6px;"></i>Balas
                </button>

                {{-- AUTHOR INFO --}}
                <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px; padding-bottom:16px; border-bottom:2px solid rgba(0,0,0,0.05);">
                    @php
                        $userName = $post->user->name ?? $post->author_name;
                        $firstLetter = strtoupper(substr($userName, 0, 1));
                        $colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B88B', '#52C41A'];
                        $colorIndex = abs(crc32($userName)) % count($colors);
                        $bgColor = $colors[$colorIndex];
                    @endphp
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg" style="background-color: {{ $bgColor }}; width:52px; height:52px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:20px; color:white;">
                        {{ $firstLetter }}
                    </div>
                    <div style="flex:1;">
                        <p style="font-weight:700; margin-bottom:4px; font-size:15px;">{{ $userName }}</p>
                        <p style="font-size:12px; color:var(--muted);">{{ $post->created_at->format('M d, Y') }}</p>
                    </div>
                </div>

                {{-- POST CONTENT --}}
                <div style="color:inherit; line-height:1.8; white-space:pre-wrap; font-size:15px; background:rgba(106,77,247,0.02); border-radius:8px; border-left:4px solid var(--accent); padding:12px 18px; margin-top:16px;">
                    {{ $post->content }}
                </div>
            </section>

            {{-- REPLY FORM (HIDDEN BY DEFAULT) --}}
            <div id="reply-form-section" style="display:none; margin-bottom:20px; padding:16px; background:rgba(106,77,247,0.05); border-radius:8px; border:2px solid rgba(106,77,247,0.1);">
                <form action="{{ route('forum.reply', $post->id) }}" method="POST" onsubmit="hideReplyForm();">
                    @csrf

                    <div style="margin-bottom:12px;">
                        <textarea name="content"
                                  style="width:100%; padding:12px 14px; border:2px solid #d1d5db; background:transparent; color:inherit; box-sizing:border-box; border-radius:8px; transition:border-color .2s ease, background .2s ease; font-size:14px; font-family:inherit; resize:vertical;"
                                  rows="3"
                                  placeholder="Tulis balasan anda..."></textarea>
                        @error('content')
                            <p style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div style="text-align:center;">
                        <button type="submit" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4);'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3);'">
                            <i class="bi bi-send" style="margin-right:6px;"></i>Hantar
                        </button>
                    </div>
                </form>
            </div>

            {{-- REPLIES SECTION HEADER --}}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px; margin-bottom:20px;">
                <h2 style="margin:0; font-size:18px; font-weight:700; white-space:nowrap;">Balasan ({{ $post->replies->count() }})</h2>
            </div>

            {{-- REPLIES LIST --}}
            <div style="margin-bottom:20px;">
                @forelse ($post->replies as $reply)
                    <section class="panel" style="margin-top:20px; margin-bottom:16px; padding:16px;">
                        {{-- REPLY HEADER --}}
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px; padding-bottom:12px; border-bottom:2px solid rgba(0,0,0,0.05);">
                            @php
                                $replyUserName = $reply->author_name;
                                $replyFirstLetter = strtoupper(substr($replyUserName, 0, 1));
                                $replyColorIndex = abs(crc32($replyUserName)) % count($colors);
                                $replyBgColor = $colors[$replyColorIndex];
                            @endphp
                            <div style="background-color: {{ $replyBgColor }}; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:16px; color:white;">
                                {{ $replyFirstLetter }}
                            </div>
                            <div>
                                <p style="font-weight:600; font-size:14px; margin:0;">{{ $replyUserName }}</p>
                                <p style="font-size:11px; color:var(--muted); margin:2px 0 0 0;">{{ $reply->created_at->format('M d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>

                        {{-- REPLY CONTENT --}}
                        <p style="color:inherit; line-height:1.6; white-space:pre-wrap; font-size:14px; margin:0;">{{ $reply->reply_content }}</p>
                    </section>
                @empty
                    <p style="text-align:center; color:var(--muted); padding:24px; font-size:14px;">Tiada balasan lagi. Jadilah yang pertama!</p>
                @endforelse
            </div>
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

    function hideReplyForm() {
        // Hide after a slight delay to allow form submission
        setTimeout(() => {
            document.getElementById('reply-form-section').style.display = 'none';
        }, 100);
    }
</script>

@endsection
