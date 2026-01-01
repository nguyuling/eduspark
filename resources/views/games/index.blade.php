@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Permainan</div>
        <div class="sub">Mainkan permainan edukatif untuk meningkatkan prestasi anda</div>
      </div>
      @if(auth()->user()->role === 'teacher')
        <a href="{{ route('teacher.games.create') }}" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer; margin-top:15px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
          <i class="bi bi-plus-lg"></i>
          Cipta Permainan
        </a>
      @endif
    </div>

    @if (session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
      <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;margin-left:40px;margin-right:40px;font-size:14px;">{{ session('error') }}</div>
    @endif
    @if(auth()->user()->role === 'teacher')
      {{-- TEACHER VIEW --}}
      <!-- Games Stats -->
      @if($games->count() > 0)
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-bottom:20px; margin-top:10px;">
          <div class="panel" style="display:flex; flex-direction:column; gap:8px;">
            <div style="font-size:12px; color:var(--muted); font-weight:600;">Jumlah Permainan</div>
            <div style="font-size:28px; font-weight:700; color:var(--accent);">{{ $games->count() }}</div>
          </div>
          <div class="panel" style="display:flex; flex-direction:column; gap:8px;">
            <div style="font-size:12px; color:var(--muted); font-weight:600;">Diterbitkan</div>
            <div style="font-size:28px; font-weight:700; color:#22c55e;">{{ $games->where('is_published', true)->count() }}</div>
          </div>
          <div class="panel" style="display:flex; flex-direction:column; gap:8px;">
            <div style="font-size:12px; color:var(--muted); font-weight:600;">Draf</div>
            <div style="font-size:28px; font-weight:700; color:#f97316;">{{ $games->where('is_published', false)->count() }}</div>
          </div>
        </div>
      @endif

      {{-- TEACHER VIEW --}}
      <!-- Games Management Section -->
      <section class="panel" style="margin-bottom:20px; margin-top:10px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
          <div style="display:flex; gap:8px; align-items:center;">
            <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Senarai Permainan</h2>
            <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px;">
              {{ count($games) }}
            </span>
          </div>
        </div>
        
        <table style="table-layout:fixed; width:100%; border-collapse:collapse;">
          <thead>
            <tr>
              <th style="width:5%;">No.</th>
              <th style="width:55%; text-align:left;">Permainan</th>
              <th style="width:20%; text-align:center;">Kesukaran</th>
              <th style="width:20%; text-align:center;">Tindakan</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($games as $index => $game)
              <tr>
                <td style="width:5%; padding:12px; text-align:center; font-weight:600;">{{ $index + 1 }}</td>
                <td style="width:55%; padding:12px;">
                  <div style="font-weight:700; margin-bottom:4px;">{{ $game->title }}</div>
                  <div style="font-size:13px; color:var(--muted); margin-bottom:8px; line-height:1.4;">{{ $game->description ?? 'Tiada penerangan' }}</div>
                  <div style="display:flex; gap:6px; flex-wrap:wrap; font-size:11px; align-items:center;">
                    @if(!$game->is_published)
                      <span style="background:rgba(230,57,70,0.1); padding:4px 8px; border-radius:4px; color:var(--danger); font-weight:600;">Draf</span>
                    @endif
                    <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px;"><strong>Kategori:</strong> 
                      @php
                        $categoryMap = [
                            'Action' => 'Aksi',
                            'Casual' => 'Santai',
                            'Puzzled' => 'Teka-teki',
                            'Education' => 'Pendidikan',
                            'Others' => 'Lain-lain'
                        ];
                        echo $categoryMap[$game->category] ?? $game->category ?? 'N/A';
                      @endphp
                    </span>
                  </div>
                </td>
                <td style="width:20%; text-align:center; padding:12px;">
                  <div style="font-weight:600; font-size:12px; padding:4px 8px; border-radius:6px; display:inline-block; 
                    {{ $game->difficulty === 'easy' ? 'background:rgba(74,222,128,0.2); color:#22c55e;' : 
                       ($game->difficulty === 'medium' ? 'background:rgba(251,146,60,0.2); color:#f97316;' : 
                       'background:rgba(239,68,68,0.2); color:#ef4444;') }}">
                    {{ ucfirst($game->difficulty ?? 'easy') }}
                  </div>
                </td>
                <td style="width:20%; text-align:center; padding:12px;">
                  <div style="display:flex; gap:20px; justify-content:center;">
                    <a href="{{ route('teacher.games.edit', $game->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Kemaskini">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="{{ route('games.leaderboard', $game->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:#22c55e; padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Papan Keputusan">
                      <i class="bi bi-bar-chart"></i>
                    </a>
                    <button type="button" onclick="showDeleteConfirm({{ $game->id }}, '{{ $game->title }}')" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; box-shadow:none; color:var(--danger); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Buang">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align:center; padding:24px; color:var(--muted);">
                  Tiada permainan yang dibuat lagi.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </section>

    @else
      {{-- STUDENT VIEW --}}
      <!-- Games List -->
      @if($games->count() > 0)
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:24px;">
          @foreach($games as $game)
            <div class="panel game-card" onclick="playGame('{{ route('games.play', $game->id) }}')" style="cursor:pointer; display:flex; flex-direction:column; height:100%; gap:16px;">
              <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="font-size:48px;">
                  🎮
                </div>
                <span class="game-difficulty" style="display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;
                  {{ $game->difficulty === 'easy' ? 'background:rgba(74,222,128,0.2); color:#22c55e;' : 
                     ($game->difficulty === 'medium' ? 'background:rgba(251,146,60,0.2); color:#f97316;' : 
                     'background:rgba(239,68,68,0.2); color:#ef4444;') }}">
                  {{ $game->difficulty === 'easy' ? 'Mudah' : ($game->difficulty === 'medium' ? 'Sederhana' : 'Sukar') }}
                </span>
              </div>
              <div style="flex:1;">
                <div style="font-size:16px; font-weight:700; margin-bottom:8px; color:inherit;">{{ $game->title }}</div>
                <div style="font-size:13px; color:var(--muted); line-height:1.5;">{{ $game->description ?? 'Permainan edukatif yang menyenangkan' }}</div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div style="text-align:center; padding:40px; color:var(--muted);">
          <p>Belum ada permainan tersedia</p>
        </div>
      @endif
    @endif
  </main>
</div>

@endsection

<script>
    function playGame(route) {
        // Redirect to game page
        window.location.href = route;
    }
    
    function getGameEmoji(gameName) {
        const emojiMap = {
            'cosmic': '🚀',
            'whack': '🔨',
            'memory': '🧠',
            'maze': '🗺️',
            'puzzle': '🧩',
            'quiz': '❓',
            'snake': '🐍',
            'flappy': '🐦'
        };
        
        for (const [key, emoji] of Object.entries(emojiMap)) {
            if (gameName.toLowerCase().includes(key)) {
                return emoji;
            }
        }
        return '🎮';
    }
    
    function getDifficultyInMalay(difficulty) {
        const difficultyMap = {
            'easy': 'Mudah',
            'medium': 'Sederhana',
            'hard': 'Sukar'
        };
        return difficultyMap[difficulty.toLowerCase()] || 'Mudah';
    }

    function showDeleteConfirm(gameId, gameTitle) {
        if (confirm(`Adakah anda pasti ingin menghapus permainan ini? Tindakan ini tidak boleh dibatalkan!`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/games/${gameId}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
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