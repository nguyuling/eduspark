@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Kuiz</div>
        <div class="sub">Ambil kuiz dan jejak kemajuan anda</div>
      </div>
      <div style="display:flex;gap:12px;align-items:center;">
        <div class="card" style="margin:0; min-width:120px;">
          <div class="label">Jumlah Kuiz</div>
          <div class="value">
            <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:8px 12px; border-radius:999px;">
              {{ $quizzes->count() }}
            </span>
          </div>
        </div>
      </div>
    </div>

    @if (session('error'))
      <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;font-size:14px;">{{ session('error') }}</div>
    @endif

    <!-- Quiz Filter Panel -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
      <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700;">Cari Kuiz</h2>
      <form method="GET" action="{{ route('student.quizzes.index') }}" style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; margin-bottom:20px;">
        <div>
          <label>ID Unik</label>
          <input type="text" name="unique_id" value="{{ $filters['unique_id'] ?? '' }}" placeholder="A1b2C3d4" style="height:40px;">
        </div>
        <div>
          <label>Tajuk</label>
          <input type="text" name="title" value="{{ $filters['title'] ?? '' }}" placeholder="Sains Komputer" style="height:40px;">
        </div>
        <div>
          <label>Email Pembuat</label>
          <input type="email" name="creator_email" value="{{ $filters['creator_email'] ?? '' }}" placeholder="teacher@email.com" style="height:40px;">
        </div>
        <div>
          <label>Tarikh Diterbitkan</label>
          <select name="publish_date" style="height:40px;">
            <option value="">Semua Masa</option>
            <option value="today" @if (isset($filters['publish_date']) && $filters['publish_date'] == 'today') selected @endif>Hari Ini</option>
            <option value="this_month" @if (isset($filters['publish_date']) && $filters['publish_date'] == 'this_month') selected @endif>Bulan Ini</option>
            <option value="3_months" @if (isset($filters['publish_date']) && $filters['publish_date'] == '3_months') selected @endif>3 Bulan Terakhir</option>
            <option value="this_year" @if (isset($filters['publish_date']) && $filters['publish_date'] == 'this_year') selected @endif>Tahun Ini</option>
          </select>
        </div>
      </form>
      <div style="display:flex; gap:10px; align-items:center; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:6px;">
          <input type="checkbox" id="attempted" name="attempted" value="1" @if (isset($filters['attempted']) && $filters['attempted'] == '1') checked @endif style="width:auto;padding:0;">
          <label for="attempted" style="margin-bottom:0;font-size:14px;">Hanya tunjukkan kuiz yang telah saya cuba</label>
        </div>
      </div>
      <div style="display:flex; gap:10px; align-items:center; justify-content:flex-end;">
        <a href="{{ route('student.quizzes.index') }}" class="btn-small button-outline">Kosongkan Penapis</a>
        <button type="submit" class="btn-small" onclick="document.querySelector('form').submit();">Gunakan Penapis</button>
      </div>
    </section>

    <!-- Quiz List Table -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
      <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700;">Kuiz Tersedia</h2>
      
      <table>
        <thead>
          <tr>
            <th style="width:58%">Kuiz</th>
            <th style="width:20%">Status</th>
            <th style="width:22%">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($quizzes as $quiz)
            @php
              $completedAttempts = $quiz->attempts;
              $attemptsUsed = $completedAttempts->count();
              $isFutureDue = !$quiz->due_at || $quiz->due_at->isFuture();
              $canAttempt = ($attemptsUsed < $quiz->max_attempts) && $isFutureDue;
              $latestAttempt = $completedAttempts->sortByDesc('submitted_at')->first();
              $statusBadge = 'New';
              if ($latestAttempt) $statusBadge = 'Completed';
              elseif ($quiz->due_at && $quiz->due_at->isPast()) $statusBadge = 'Due';
            @endphp
            <tr>
              <td style="width:58%">
                <div style="font-weight:700; margin-bottom:4px;">{{ $quiz->title }}</div>
                <div style="font-size:13px; color:var(--muted); margin-bottom:8px; line-height:1.4;">{{ $quiz->description }}</div>
                <div style="display:flex; gap:6px; flex-wrap:wrap; font-size:11px; align-items:center;">
                  <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px;"><strong>Pembuat:</strong> {{ $quiz->creator->name ?? 'N/A' }}</span>
                  <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                    <strong>ID:</strong> {{ $quiz->unique_code ?? 'N/A' }}
                    <button type="button" onclick="copyToClipboard('{{ $quiz->unique_code }}', this)" style="background:none; border:none; cursor:pointer; padding:0; color:var(--accent); font-weight:600; font-size:12px;" title="Salin ID">📋</button>
                  </span>
                  @if ($quiz->due_at)
                    <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px; color:{{ $quiz->due_at->isPast() ? 'var(--danger)' : 'var(--success)' }};">
                      <strong>Tarikh Akhir:</strong> {{ $quiz->due_at->format('M d, Y') }}
                    </span>
                  @endif
                </div>
              </td>
              <td style="width:20%; text-align:center;">
                <div style="font-weight:600; font-size:12px; margin-bottom:4px; padding:4px 8px; background:rgba(106,77,247,0.1); border-radius:6px; display:inline-block;">{{ $statusBadge }}</div>
                <div style="font-size:12px; margin-top:4px;">
                  <div style="margin-bottom:2px;">{{ $attemptsUsed }}/{{ $quiz->max_attempts }} percubaan</div>
                  @if ($latestAttempt)
                    @php
                      $totalMarks = $quiz->questions->sum('points') ?? 0;
                    @endphp
                    <div style="font-weight:700;">Markah: {{ $latestAttempt->score }}/{{ $totalMarks }}</div>
                  @endif
                </div>
              </td>
              <td style="width:22%; text-align:center;">
                @if ($canAttempt)
                  <a href="{{ route('student.quizzes.attempt.start', $quiz->id) }}" style="display:inline-block; padding:10px 18px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
                    {{ $attemptsUsed > 0 ? 'Cuba Semula' : 'Mula Kuiz' }}
                  </a>
                @elseif ($latestAttempt)
                  <a href="{{ route('student.quizzes.result', $latestAttempt->id) }}" style="display:inline-block; padding:10px 18px; background:transparent; color:var(--accent); border:2px solid var(--accent); text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:all .2s ease;">
                    Lihat Keputusan
                  </a>
                @else
                  <span style="color:var(--danger); font-size:12px; font-weight:600;">Tidak Tersedia</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align:center; padding:24px; color:var(--muted);">
                @if (!empty(array_filter($filters)))
                  Tiada kuiz sepadan dengan kriteria anda.
                @else
                  Tiada kuiz yang diterbitkan tersedia untuk anda pada masa ini.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </section>
  </main>
</div>

<script>
const body=document.body;

function copyToClipboard(text, btn) {
  navigator.clipboard.writeText(text).then(() => {
    const originalText = btn.textContent;
    btn.textContent = '✓ Copied!';
    setTimeout(() => { btn.textContent = originalText; }, 1500);
  });
}
</script>

@endsection