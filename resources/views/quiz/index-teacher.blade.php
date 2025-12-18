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
      <a href="{{ route('teacher.quizzes.create') }}" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer; margin-top:15px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
        <i class="bi bi-plus-lg"></i>
        Cipta Kuiz
      </a>
    </div>

    @if (session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
      <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;margin-left:40px;margin-right:40px;font-size:14px;">{{ session('error') }}</div>
    @endif

    <!-- Quiz List Table -->
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
        <div style="display:flex; gap:8px; align-items:center;">
          <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Senarai Kuiz</h2>
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px;">
            {{ count($quizzes) }}
          </span>
        </div>
        <button type="button" onclick="toggleFilterPanel()" style="background:transparent; border:2px solid var(--accent); color:var(--accent); padding:8px 12px; border-radius:6px; cursor:pointer; font-size:16px; transition:all .2s ease;" onmouseover="this.style.background='rgba(106,77,247,0.1)';" onmouseout="this.style.background='transparent';" title="Tunjukkan/Sembunyikan Penapis">
          <i class="bi bi-funnel"></i>
        </button>
      </div>
      <!-- Filter Panel (Collapsible) -->
      <div id="filter-panel" style="display:{{ !empty(array_filter(['unique_id' => request('unique_id'), 'title' => request('title'), 'creator_email' => request('creator_email'), 'publish_date_range' => request('publish_date_range'), 'scope' => request('scope')])) ? 'block' : 'none' }}; margin-bottom:20px; padding-bottom:20px;">
        <form method="GET" action="{{ route('teacher.quizzes.index') }}" id="filter-form">
          <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; margin-bottom:15px;">
            <div>
              <label style="font-size:12px;">ID Unik</label>
              <input type="text" name="unique_id" value="{{ request('unique_id') ?? '' }}" placeholder="A1b2C3d4" onchange="autoSubmitForm()" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
            </div>
            <div>
              <label style="font-size:12px;">Tajuk</label>
              <input type="text" name="title" value="{{ request('title') ?? '' }}" placeholder="Sains Komputer" onchange="autoSubmitForm()" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
            </div>
            <div>
              <label style="font-size:12px;">Email Pencipta</label>
              <input type="email" name="creator_email" value="{{ request('creator_email') ?? '' }}" placeholder="guru@email.com" onchange="autoSubmitForm()" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
            </div>
            <div style="display:flex; flex-direction:column;">
              <label style="font-size:12px;">Tarikh Diterbitkan</label>
              <div style="display:flex; gap:8px; align-items:flex-start; height:40px;">
                <select name="publish_date_range" onchange="autoSubmitForm()" style="height:100%; flex:1; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
                  <option value="">Semua Masa</option>
                  <option value="today" @if (request('publish_date_range') === 'today') selected @endif>Hari Ini</option>
                  <option value="month" @if (request('publish_date_range') === 'month') selected @endif>Bulan Ini</option>
                  <option value="3months" @if (request('publish_date_range') === '3months') selected @endif>3 Bulan Terakhir</option>
                  <option value="year" @if (request('publish_date_range') === 'year') selected @endif>Tahun Ini</option>
                </select>
                <a href="{{ route('teacher.quizzes.index') }}" onclick="keepFilterPanelOpen(); return true;" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:8px; cursor:pointer; font-size:24px; transition:all .2s ease; text-decoration:none; white-space:nowrap; height:40px; width:40px;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Ulang Penapis">
                  <i class="bi bi-arrow-repeat"></i>
                </a>
              </div>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:6px;">
            <input type="checkbox" id="created_by_me" name="scope" value="mine" onchange="autoSubmitForm()" @if (request('scope') === 'mine') checked @endif style="width:18px; height:18px; cursor:pointer;">
            <label for="created_by_me" style="margin-bottom:0; font-size:12px; cursor:pointer;">Hanya tunjukkan kuiz yang saya cipta</label>
          </div>
        </form>
      </div>      
      <table>
        <thead>
          <tr>
            <th style="width:5%">No.</th>
            <th style="width:60%">Kuiz</th>
            <th style="width:15%">Bil. Soalan</th>
            <th style="width:20%">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($quizzes as $index => $quiz)
            @php
              $statusBadge = $quiz->is_published ? 'Diterbitkan' : 'Draf';
              $isMyQuiz = auth()->id() === $quiz->teacher_id;
              $attemptsCount = $quiz->attempts_count ?? 0;
            @endphp
            <tr>
              <td style="width:5%; text-align:center; font-weight:600;">{{ $index + 1 }}</td>
              <td style="width:53%">
                <div style="font-weight:700; margin-bottom:4px;">{{ $quiz->title }}</div>
                <div style="font-size:13px; color:var(--muted); margin-bottom:8px; line-height:1.4;">{{ Str::limit($quiz->description, 100) }}</div>
                <div style="display:flex; gap:6px; flex-wrap:wrap; font-size:11px; align-items:center;">
                  @if (!$quiz->is_published)
                    <span style="background:rgba(230,57,70,0.1); padding:4px 8px; border-radius:4px; color:var(--danger); font-weight:600;">Draf</span>
                  @endif
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
              <td style="width:10%; text-align:center;">
                <div style="font-size:12px;">
                  <div style="margin-bottom:2px;">{{ $quiz->questions_count ?? 0 }} soalan</div>
                </div>
              </td>
              <td style="width:12%; text-align:center;">
                <div style="display:flex; gap:20px; justify-content:center; flex-wrap:nowrap;">
                  @if ($isMyQuiz)
                    <!-- Edit Button (Only for my quizzes) -->
                    <a href="{{ route('teacher.quizzes.edit', $quiz->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Kemaskini">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                  @else
                    <!-- View Button (For other teachers' quizzes) -->
                    <a href="{{ route('teacher.quizzes.show', $quiz->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Lihat">
                      <i class="bi bi-eye-fill"></i>
                    </a>
                  @endif

                  @if ($isMyQuiz)
                    <!-- Delete Button (Only for my quizzes) -->
                    <button type="button" onclick="deleteQuiz({{ $quiz->id }})" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; box-shadow:none; color:var(--danger); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Buang">
                      <i class="bi bi-trash"></i>
                    </button>
                  @endif

                  <!-- Results Button (For all quizzes) -->
                  <a href="{{ route('teacher.quizzes.results', $quiz->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--success); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Keputusan">
                    <i class="bi bi-bar-chart"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" style="text-align:center; padding:24px; color:var(--muted);">
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

      <!-- Show More Section -->
      @if ($hasMore)
        <div style="text-align:center; margin-top:20px; padding:20px;">
          <a href="{{ route('teacher.quizzes.index', array_merge(request()->query(), ['limit' => $nextLimit])) }}" style="color:var(--accent); text-decoration:none; font-size:14px; cursor:pointer;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
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

function deleteQuiz(quizId) {
  const confirmed = confirm('Adakah anda pasti ingin menghapus kuiz ini? Tindakan ini tidak boleh dibatalkan!');
  if (confirmed) {
    const deleteForm = document.createElement('form');
    deleteForm.method = 'POST';
    deleteForm.action = '/teacher/quizzes/' + quizId;
    deleteForm.innerHTML = '@csrf @method("DELETE")';
    document.body.appendChild(deleteForm);
    deleteForm.submit();
  }
}

function toggleFilterPanel() {
  const filterPanel = document.getElementById('filter-panel');
  if (filterPanel.style.display === 'none') {
    filterPanel.style.display = 'block';
  } else {
    filterPanel.style.display = 'none';
  }
}

function keepFilterPanelOpen() {
  sessionStorage.setItem('keepFilterOpen', 'true');
}

function autoSubmitForm() {
  document.getElementById('filter-form').submit();
}

// Keep filter panel open if flag is set
document.addEventListener('DOMContentLoaded', function() {
  if (sessionStorage.getItem('keepFilterOpen') === 'true') {
    const filterPanel = document.getElementById('filter-panel');
    filterPanel.style.display = 'block';
    sessionStorage.removeItem('keepFilterOpen');
  }
});
</script>

@endsection