@extends('layouts.app')

@section('content')
<div class="app">
  <main class="main">
    <!-- Header -->
    <div class="header">
      <div>
        <div class="title">Papan Mata: {{ $game->title }}</div>
        <div class="sub">Analitik prestasi kelas</div>
      </div>
      <a href="{{ route('games.index') }}" class="btn-kembali">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
    </div>

    <!-- Filter Section -->
    @if(isset($classes) && $classes->count() > 0)
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <form method="GET" action="{{ route('games.leaderboard', $game->id) }}" style="display:grid; grid-template-columns: 1fr auto; gap:12px; align-items:end;">
        <div>
          <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px; color:var(--text);">Tapis Mengikut Kelas</label>
          <select name="class" style="width:100%; padding:10px 14px; border-radius:8px; border:2px solid #d1d5db; background:transparent; color:inherit; font-size:14px; outline:none; transition:border-color 0.2s ease; cursor:pointer;"
            onfocus="this.style.borderColor='var(--accent)'"
            onblur="this.style.borderColor='#d1d5db'">
            <option value="">Semua Kelas</option>
            @foreach($classes as $class)
              <option value="{{ $class }}" {{ ($classFilter ?? '') === $class ? 'selected' : '' }}>{{ $class }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
          <button type="submit" class="btn-cari">
            <i class="bi bi-search"></i> Tapis
          </button>
          @if(!empty($classFilter))
            <a href="{{ route('games.leaderboard', $game->id) }}" class="btn-semula">
              <i class="bi bi-x-lg"></i> Semula
            </a>
          @endif
        </div>
      </form>
    </section>
    @endif

    @if($scores->count() > 0)
      <!-- Student Ranking Banner -->
      @if(auth()->user()->role !== 'teacher' && $highlightedUserIndex !== -1)
      <section class="panel" style="margin-bottom:20px; background:linear-gradient(90deg, rgba(42,157,143,0.08), rgba(42,157,143,0.04)); border-color: var(--success);">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <div>
            <h3 style="font-size:16px; font-weight:700; margin-bottom:8px; display:flex; align-items:center; gap:8px;">
              <span style="font-size:24px;">⭐</span> Kedudukan Anda
            </h3>
            <p style="font-size:14px; color:var(--muted);">
              Anda berada di kedudukan <span style="font-weight:700; font-size:18px; color:var(--success);">{{ $highlightedUserIndex + 1 }}</span> daripada <span style="font-weight:700;">{{ $scores->count() }}</span> pemain
            </p>
          </div>
          <div style="font-size:48px;">
            @if($highlightedUserIndex === 0) 🥇
            @elseif($highlightedUserIndex === 1) 🥈
            @elseif($highlightedUserIndex === 2) 🥉
            @else 🎮
            @endif
          </div>
        </div>
      </section>
      @endif

      <!-- Statistics Cards -->
      <div class="cards" style="margin-bottom:20px;">
        <div class="card">
          <div class="label">Jumlah Permainan</div>
          <div class="value">{{ $scores->count() }}</div>
        </div>
        <div class="card">
          <div class="label">Skor Purata</div>
          <div class="value">{{ number_format($scores->avg('score'), 0) }}</div>
        </div>
        <div class="card">
          <div class="label">Pemain Unik</div>
          <div class="value">{{ $scores->groupBy('user_id')->count() }}</div>
        </div>
      </div>

      <!-- Leaderboard Table -->
      <section class="panel" style="margin-bottom:20px; margin-top:10px;">
        <div style="margin-bottom:20px;">
          <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Senarai Papan Mata</h2>
        </div>
        
        <table>
          <thead>
            <tr>
              <th style="width:10%; text-align:center;">Kedudukan</th>
              <th style="width:35%;">Pemain</th>
              <th style="width:15%; text-align:center;">Skor</th>
              <th style="width:15%; text-align:center;">Masa Diambil</th>
              <th style="width:12%; text-align:center;">Percubaan</th>
              <th style="width:13%; text-align:center;">Selesai</th>
            </tr>
          </thead>
          <tbody>
            @foreach($scores as $index => $score)
            <tr style="@if(auth()->user()->role !== 'teacher' && isset($currentUser) && $score->user_id === $currentUser->id) background: rgba(106,77,247,0.08); font-weight:600; @endif">
              <td style="text-align:center; font-weight:700;">
                @if($index === 0)
                  <span style="font-size:20px; margin-right:6px;">🥇</span> 1
                @elseif($index === 1)
                  <span style="font-size:20px; margin-right:6px;">🥈</span> 2
                @elseif($index === 2)
                  <span style="font-size:20px; margin-right:6px;">🥉</span> 3
                @else
                  {{ $index + 1 }}
                @endif
              </td>
              
              <td>
                <div style="font-weight:700; margin-bottom:4px;">{{ $score->user->name }}
                  @if(auth()->user()->role !== 'teacher' && isset($currentUser) && $score->user_id === $currentUser->id)
                  <span style="margin-left:6px; color:var(--accent); font-size:12px;">👈 Anda</span>
                  @endif
                </div>
                <div style="font-size:12px; color:var(--muted); margin-top:4px;">{{ $score->user->class ?? 'Tiada Kelas' }}</div>
              </td>
              
              <td style="text-align:center; font-weight:700; color:var(--accent);">
                {{ $score->score }} pts
              </td>
              
              <td style="text-align:center; font-size:14px;">
                @if($score->time_taken)
                  @php
                    $hours = floor($score->time_taken / 3600);
                    $minutes = floor(($score->time_taken % 3600) / 60);
                    $seconds = $score->time_taken % 60;
                  @endphp
                  {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                @else
                  N/A
                @endif
              </td>
              
              <td style="text-align:center; font-weight:700;">
                {{ $scores->where('user_id', $score->user_id)->count() }}
              </td>
              
              <td style="text-align:center; font-size:13px;">
                {{ $score->completed_at?->format('M d, Y H:i') ?? 'N/A' }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </section>
    @else
      <!-- Empty State -->
      <section class="panel" style="text-align:center; padding:40px; margin-bottom:20px; margin-top:10px;">
        <div style="font-size:48px; margin-bottom:16px;">📊</div>
        <h3 style="font-size:16px; font-weight:700; margin-bottom:8px;">Tiada skor lagi</h3>
        <p style="font-size:14px; color:var(--muted); margin-bottom:20px;">Jadilah yang pertama memainkan permainan ini!</p>
        <a href="{{ route('games.index') }}" class="btn-kembali">
          <i class="bi bi-arrow-left"></i> Kembali ke Permainan
        </a>
      </section>
    @endif
  </main>
</div>
@endsection