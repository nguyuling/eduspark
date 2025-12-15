@extends('layouts.app')

@section('content')
<div class="app">
    <main class="main">
        <div class="header">
        <div>
            <div class="title">Cipta Post Baru</div>
            <div class="sub">Bagikan pertanyaan atau diskusi anda dengan komuniti</div>
        </div>
        <a href="{{ route('forum.index') }}" class="btn-kembali" style="display:inline-block !important; margin-top:15px; padding:12px 24px !important; background:transparent !important; color:#6A4DF7 !important; border:2px solid #6A4DF7 !important; text-decoration:none !important; border-radius:8px !important; font-weight:700 !important; font-size:14px !important; transition:all 0.2s ease !important; cursor:pointer !important; line-height:1 !important; height:auto !important;" onmouseover="this.style.background='rgba(106,77,247,0.1)'" onmouseout="this.style.background='transparent'">
            <i class="bi bi-arrow-left" style="margin-right:6px;"></i>Kembali
        </a>
    </div>

    <div>
        <section class="panel" style="margin-bottom:20px; margin-top:10px;">
            <form action="{{ route('forum.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- Title Field --}}
                <div>
                    <label style="display:block; font-size:14px; font-weight:600; margin-bottom:8px; color:inherit;">Tajuk Post</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Masukkan tajuk post anda..." 
                           class="form-input-height" style="width:100%; height:44px; padding:11px 14px; border:2px solid #d1d5db; background:transparent; color:inherit; box-sizing:border-box; border-radius:8px; transition:border-color .2s ease, background .2s ease; font-size:14px;" required>
                    @error('title')
                        <p style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Content Field --}}
                <div>
                    <label style="display:block; font-size:14px; font-weight:600; margin-bottom:8px; color:inherit;">Kandungan</label>
                    <textarea name="content" placeholder="Tulis kandungan post anda..." rows="8"
                              style="width:100%; padding:11px 14px; border:2px solid #d1d5db; background:transparent; color:inherit; box-sizing:border-box; border-radius:8px; transition:border-color .2s ease, background .2s ease; font-size:14px; font-family:inherit; resize:vertical;" required></textarea>
                    @error('content')
                        <p style="color:var(--danger); font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-center gap-3 pt-4">
                    <button type="submit" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
                        <i class="bi bi-check-lg"></i>
                        Post
                    </button>
                </div>
            </form>
        </section>
    </main>
</div>
@endsection
