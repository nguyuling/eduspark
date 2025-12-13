@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">{{ $quiz->title }} - Keputusan</div>
        <div class="sub">Melihat keputusan percubaan kuiz</div>
      </div>
      <a href="{{ route('teacher.quizzes.index') }}" style="display:inline-block; padding:12px 24px; background:transparent; color:var(--accent); border:2px solid var(--accent); text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:all .2s ease;" onmouseover="this.style.background='rgba(106,77,247,0.1)';" onmouseout="this.style.background='transparent';">
        ← Kembali ke Kuiz
      </a>
    </div>

    <!-- Results Section -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
      <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; border-bottom:2px solid #d4c5f9; padding-bottom:12px;">Keputusan Percubaan</h2>
      
      @if($attempts->isEmpty())
        <div style="text-align:center; padding:40px; color:var(--muted);">
          <p style="font-size:16px; margin-bottom:12px;">Tiada percubaan kuiz untuk kuiz ini.</p>
        </div>
      @else
        <table>
          <thead>
            <tr>
              <th style="width:25%;">Nama Pelajar</th>
              <th style="width:20%;">Tarikh Selesai</th>
              <th style="width:20%;">Markah</th>
              <th style="width:20%;">Percubaan</th>
              <th style="width:15%;">Tindakan</th>
            </tr>
          </thead>
          <tbody>
            @forelse($attempts as $attempt)
              <tr>
                <td style="width:25%;">
                  <div style="font-weight:700;">{{ $attempt->student->name ?? 'Unknown' }}</div>
                  <div style="font-size:13px; color:var(--muted);">{{ $attempt->student->email ?? 'N/A' }}</div>
                </td>
                <td style="width:20%;">
                  {{ $attempt->submitted_at?->format('d/m/Y H:i') ?? 'Belum selesai' }}
                </td>
                <td style="width:20%;">
                  <span style="background:rgba(106,77,247,0.15); padding:4px 8px; border-radius:4px; color:var(--accent); font-weight:700;">
                    {{ $attempt->score ?? '-' }} / {{ $quiz->questions->sum('points') ?? 0 }}
                  </span>
                </td>
                <td style="width:20%;">
                  {{ $attempt->attempt_number }}
                </td>
                <td style="width:15%;">
                  <a href="#" style="color:var(--accent); text-decoration:none; font-weight:600; font-size:12px;">
                    Lihat
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" style="text-align:center; padding:24px; color:var(--muted);">
                  Tiada data percubaan.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      @endif
    </section>
  </main>
</div>

@endsection
