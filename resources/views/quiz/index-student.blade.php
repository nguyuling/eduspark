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
    </div>

    @if (session('error'))
      <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;font-size:14px;">{{ session('error') }}</div>
    @endif

    <!-- Search and Filter Section -->
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <form method="GET" action="{{ route('student.quizzes.index') }}" style="display:grid; grid-template-columns: 2fr 1fr 1fr auto; gap:12px; align-items:end;">
        
        <!-- Search by Title -->
        <div>
          <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px; color:var(--text);">Cari Kuiz</label>
          <input 
            type="text" 
            name="title" 
            value="{{ request('title') }}"
            placeholder="Cari tajuk kuiz..."
            style="width:100%; padding:10px 14px; border-radius:8px; border:2px solid #d1d5db; background:transparent; color:inherit; font-size:14px; outline:none; transition:border-color 0.2s ease;"
            onfocus="this.style.borderColor='var(--accent)'"
            onblur="this.style.borderColor='#d1d5db'"
          >
        </div>

        <!-- Search by Unique ID -->
        <div>
          <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px; color:var(--text);">Kuiz ID</label>
          <input 
            type="text" 
            name="unique_id" 
            value="{{ request('unique_id') }}"
            placeholder="Cari ID unik..."
            style="width:100%; padding:10px 14px; border-radius:8px; border:2px solid #d1d5db; background:transparent; color:inherit; font-size:14px; outline:none; transition:border-color 0.2s ease;"
            onfocus="this.style.borderColor='var(--accent)'"
            onblur="this.style.borderColor='#d1d5db'"
          >
        </div>

        <!-- Filter by Publish Date -->
        <div>
          <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px; color:var(--text);">Tarikh Diterbitkan</label>
          <select 
            name="publish_date"
            style="width:100%; padding:10px 14px; border-radius:8px; border:2px solid #d1d5db; background:transparent; color:inherit; font-size:14px; outline:none; cursor:pointer; transition:border-color 0.2s ease;"
            onfocus="this.style.borderColor='var(--accent)'"
            onblur="this.style.borderColor='#d1d5db'"
          >
            <option value="">Semua Masa</option>
            <option value="today" {{ request('publish_date') == 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="this_month" {{ request('publish_date') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="3_months" {{ request('publish_date') == '3_months' ? 'selected' : '' }}>3 Bulan Terakhir</option>
            <option value="this_year" {{ request('publish_date') == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
          </select>
        </div>

        <!-- Search Button -->
        <div style="display:flex; gap:8px;">
          <button type="submit" class="btn-cari">
            <i class="bi bi-search"></i> Cari
          </button>
          @if(request()->hasAny(['title', 'unique_id', 'publish_date', 'attempted']))
            <a href="{{ route('student.quizzes.index') }}" class="btn-semula">
              <i class="bi bi-x-lg"></i> Semula
            </a>
          @endif
        </div>
      </form>

      <!-- Attempted Filter -->
      <div style="margin-top:15px; display:flex; align-items:center; gap:6px;">
        <form method="GET" action="{{ route('student.quizzes.index') }}" style="display:flex; align-items:center; gap:6px;">
          <input type="hidden" name="title" value="{{ request('title') }}">
          <input type="hidden" name="unique_id" value="{{ request('unique_id') }}">
          <input type="hidden" name="publish_date" value="{{ request('publish_date') }}">
          <input type="checkbox" id="attempted" name="attempted" value="1" onchange="this.form.submit()" {{ request('attempted') == '1' ? 'checked' : '' }} style="width:18px; height:18px; cursor:pointer;">
          <label for="attempted" style="margin-bottom:0; font-size:13px; cursor:pointer; font-weight:600;">Hanya tunjukkan kuiz yang telah saya cuba</label>
        </form>
      </div>
    </section>

    <!-- Quiz List Table -->
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
        <div style="display:flex; gap:8px; align-items:center;">
          <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Senarai Kuiz</h2>
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px;">
            {{ count($quizzes) }}
          </span>
        </div>
      </div>
      
      <table style="table-layout:fixed; width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="width:5%;">No.</th>
            <th style="width:60%; text-align:left;">Kuiz</th>
            <th style="width:20%; text-align:center;">Status</th>
            <th style="width:15%; text-align:center;">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($quizzes as $index => $quiz)
            @php
              $completedAttempts = $quiz->attempts;
              $attemptsUsed = $completedAttempts->count();
              $isFutureDue = !$quiz->due_at || $quiz->due_at->isFuture();
              $canAttempt = ($attemptsUsed < $quiz->max_attempts) && $isFutureDue;
              $highestAttempt = $completedAttempts->sortByDesc('score')->first();
              $statusBadge = 'New';
              $badgeStyle = 'background:rgba(106,77,247,0.1); color:inherit;';
              if ($highestAttempt) {
                $statusBadge = 'Completed';
                $badgeStyle = 'background:#6A4DF7; color:#fff;';
              } elseif ($quiz->due_at && $quiz->due_at->isPast()) {
                $statusBadge = 'Due';
                $badgeStyle = 'background:rgba(230,57,70,0.15); color:#000;';
              }
            @endphp
            <tr>
              <td style="width:5%; padding:12px; text-align:center; font-weight:600;">{{ $index + 1 }}</td>
              <td style="width:70%; padding:12px;">
                <div style="font-weight:700; margin-bottom:4px;">{{ $quiz->title }}</div>
                <div style="font-size:13px; color:var(--muted); margin-bottom:8px; line-height:1.4;">{{ $quiz->description }}</div>
                <div style="display:flex; gap:6px; flex-wrap:wrap; font-size:11px; align-items:center;">
                  <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px;"><strong>Pencipta:</strong> {{ $quiz->creator->name ?? 'N/A' }}</span>
                  <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px; display:flex; align-items:center; gap:4px;">
                    <strong>ID:</strong> {{ $quiz->unique_code ?? 'N/A' }}
                    <button type="button" onclick="copyToClipboard('{{ $quiz->unique_code }}', this)" style="background:none; border:none; cursor:pointer; padding:0; color:var(--accent); font-weight:600; font-size:12px;" title="Salin ID">📋</button>
                  </span>
                  @if ($quiz->due_at)
                    <span style="background:{{ $quiz->due_at->isPast() ? 'rgba(230,57,70,0.1)' : 'rgba(106,77,247,0.08)' }}; padding:4px 8px; border-radius:4px; color:{{ $quiz->due_at->isPast() ? 'var(--danger)' : 'inherit' }};">
                      <strong>Due on:</strong> {{ $quiz->due_at->format('M d, Y') }}
                    </span>
                  @endif
                </div>
              </td>
              <td style="width:15%; text-align:center; padding:12px;">
                <div style="font-weight:600; font-size:12px; margin-bottom:4px; padding:4px 8px; border-radius:6px; display:inline-block; {{ $badgeStyle }}">{{ $statusBadge }}</div>
                <div style="font-size:12px; margin-top:4px;">
                  <div style="margin-bottom:2px;">{{ $attemptsUsed }}/{{ $quiz->max_attempts }} percubaan</div>
                  @if ($highestAttempt)
                    @php
                      $totalMarks = $quiz->questions->sum('points') ?? 0;
                    @endphp
                    <div style="font-weight:700;">Markah: {{ $highestAttempt->score }}/{{ $totalMarks }}</div>
                  @endif
                </div>
              </td>
              <td style="width:10%; text-align:center; padding:12px;">
                <div style="display:flex; gap:20px; justify-content:center;">
                  @if ($canAttempt)
                    <a href="{{ route('student.quizzes.start', $quiz->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="{{ $attemptsUsed > 0 ? 'Cuba Semula' : 'Mula Kuiz' }}">
                      <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                  @elseif ($highestAttempt)
                    <a href="{{ route('student.quizzes.result', $highestAttempt->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--success); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Lihat Keputusan">
                      <i class="bi bi-bar-chart"></i>
                    </a>
                  @else
                    <span style="color:var(--danger); font-size:12px; font-weight:600;">-</span>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align:center; padding:24px; color:var(--muted);">
                @if (!empty(array_filter(['unique_id' => request('unique_id'), 'title' => request('title'), 'creator_email' => request('creator_email'), 'publish_date' => request('publish_date'), 'attempted' => request('attempted')])))
                  Tiada kuiz sepadan dengan kriteria anda.
                @else
                  Tiada kuiz yang diterbitkan tersedia untuk anda pada masa ini.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <!-- Show More Section -->
      @if ($hasMore)
        <div style="text-align:center; margin-top:20px; padding:20px;">
          <a href="{{ route('student.quizzes.index', array_merge(request()->query(), ['limit' => $nextLimit])) }}" style="color:var(--accent); text-decoration:none; font-size:14px; cursor:pointer;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
            Tunjukkan 10 Kuiz Lagi
          </a>
        </div>
      @endif
    </section>
  </main>
</div>

<script>
function copyToClipboard(text, btn) {
  navigator.clipboard.writeText(text).then(() => {
    const originalText = btn.textContent;
    btn.textContent = '✓ Disalin!';
    setTimeout(() => { btn.textContent = originalText; }, 1500);
  });
}
</script>

@endsection