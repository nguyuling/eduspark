@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Kuiz</div>
        <div class="sub">Cipta dan uruskan kuiz anda</div>
      </div>
      <div class="card" style="margin:0; min-width:120px;">
        <div class="label">Jumlah Kuiz</div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:8px 12px; border-radius:999px;">
            {{ $quizzes->count() }}
          </span>
        </div>
      </div>
    </div>

    @if (session('success'))
      <div style="background:var(--success);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;margin-left:40px;margin-right:40px;font-size:14px;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
      <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;margin-left:40px;margin-right:40px;font-size:14px;">{{ session('error') }}</div>
    @endif

    <!-- Quiz Filter Panel -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
      <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700;">Cari Kuiz</h2>
      <form method="GET" action="{{ route('teacher.quizzes.index') }}" style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; margin-bottom:20px;">
        <div>
          <label>ID Unik</label>
          <input type="text" name="unique_id" value="{{ request('unique_id') ?? '' }}" placeholder="A1b2C3d4" style="height:40px;">
        </div>
        <div>
          <label>Tajuk</label>
          <input type="text" name="title" value="{{ request('title') ?? '' }}" placeholder="Sains Komputer" style="height:40px;">
        </div>
        <div>
          <label>Email Pembuat</label>
          <input type="email" name="creator_email" value="{{ request('creator_email') ?? '' }}" placeholder="guru@email.com" style="height:40px;">
        </div>
        <div>
          <label>Tarikh Diterbitkan</label>
          <select name="publish_date_range" style="height:40px;">
            <option value="">Semua Masa</option>
            <option value="today" @if (request('publish_date_range') === 'today') selected @endif>Hari Ini</option>
            <option value="month" @if (request('publish_date_range') === 'month') selected @endif>Bulan Ini</option>
            <option value="3months" @if (request('publish_date_range') === '3months') selected @endif>3 Bulan Terakhir</option>
            <option value="year" @if (request('publish_date_range') === 'year') selected @endif>Tahun Ini</option>
          </select>
        </div>
      </form>
      <div style="display:flex; gap:10px; align-items:center; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:6px;">
          <input type="checkbox" id="created_by_me" name="scope" value="mine" @if (request('scope') === 'mine') checked @endif style="width:auto;padding:0;">
          <label for="created_by_me" style="margin-bottom:0;font-size:14px;">Hanya tunjukkan kuiz yang saya cipta</label>
        </div>
      </div>
      <div style="display:flex; gap:10px; align-items:center; justify-content:flex-end;">
        <a href="{{ route('teacher.quizzes.index') }}" class="btn-small button-outline">Kosongkan Penapis</a>
        <button type="submit" class="btn-small" onclick="document.querySelector('form').submit();">Gunakan Penapis</button>
      </div>
    </section>

    <!-- Quiz List Table -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="margin:0; font-size:18px; font-weight:700;">Kuiz Tersedia</h2>
        <a href="{{ route('teacher.quizzes.create') }}" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
          Cipta Kuiz
        </a>
      </div>
      
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
              $isMyQuiz = auth()->id() === $quiz->teacher_id;
              $statusBadge = $quiz->is_published ? 'Diterbitkan' : 'Draf';
              $attemptsCount = $quiz->attempts_count ?? 0;
            @endphp
            <tr>
              <td style="width:58%">
                <div style="font-weight:700; margin-bottom:4px;">{{ $quiz->title }}</div>
                <div style="font-size:13px; color:var(--muted); margin-bottom:8px; line-height:1.4;">{{ Str::limit($quiz->description, 100) }}</div>
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
                  <div style="margin-bottom:2px;">{{ $quiz->questions_count ?? 0 }} soalan</div>
                  <div style="font-weight:700;">{{ $attemptsCount }} percubaan</div>
                </div>
              </td>
              <td style="width:22%; text-align:center;">
                <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">
                  @if ($isMyQuiz)
                    <!-- Edit Button (Only for my quizzes) -->
                    <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" style="display:inline-block; padding:10px 14px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:6px; font-weight:600; font-size:12px; transition:transform .2s ease, box-shadow .2s ease;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                      ✏️ Kemaskini
                    </a>
                  @else
                    <!-- View Button (For other teachers' quizzes) -->
                    <a href="{{ route('teacher.quizzes.show', $quiz->id) }}" style="display:inline-block; padding:10px 14px; background:transparent; color:var(--accent); border:2px solid var(--accent); text-decoration:none; border-radius:6px; font-weight:600; font-size:12px; transition:all .2s ease;" onmouseover="this.style.background='rgba(106,77,247,0.1)';" onmouseout="this.style.background='transparent';">
                      👁️ Lihat
                    </a>
                  @endif

                  <!-- Results Button (For all quizzes) -->
                  <a href="{{ route('teacher.quizzes.results', $quiz->id) }}" style="display:inline-block; padding:10px 14px; background:transparent; color:var(--success); border:2px solid var(--success); text-decoration:none; border-radius:6px; font-weight:600; font-size:12px; transition:all .2s ease;" onmouseover="this.style.background='rgba(42,157,143,0.1)';" onmouseout="this.style.background='transparent';">
                    📊 Keputusan
                  </a>

                  <!-- Delete Button (Only for my quizzes) -->
                  @if ($isMyQuiz)
                    <form action="{{ route('teacher.quizzes.destroy', $quiz->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Padam kuiz ini?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" style="display:inline-block; padding:10px 14px; background:transparent; color:var(--danger); border:2px solid var(--danger); text-decoration:none; border-radius:6px; font-weight:600; font-size:12px; transition:all .2s ease; cursor:pointer;" onmouseover="this.style.background='rgba(230,57,70,0.1)';" onmouseout="this.style.background='transparent';">
                        🗑️ Padam
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align:center; padding:24px; color:var(--muted);">
                @if (!empty(array_filter(['unique_id' => request('unique_id'), 'title' => request('title'), 'creator_email' => request('creator_email'), 'publish_date_range' => request('publish_date_range'), 'scope' => request('scope')])))
                  Tiada kuiz sepadan dengan kriteria anda.
                @else
                  Tiada kuiz yang diterbitkan tersedia untuk anda pada masa ini.
                @endif
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <!-- Pagination -->
      @if (method_exists($quizzes, 'links'))
        <div style="margin-top:20px;">
          {{ $quizzes->links() }}
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