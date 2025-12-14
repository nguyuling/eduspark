@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Bahan Pembelajaran</div>
        <div class="sub">Lihat semua bahan pembelajaran</div>
      </div>
    </div>

    <!-- Lessons Panel -->
    <section class="panel" style="margin-bottom:20px; margin-top:20px;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
        <div style="display:flex; gap:8px; align-items:center;">
          <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Bahan Tersedia</h2>
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px;">
            {{ count($lessons ?? []) }}
          </span>
        </div>
        <button type="button" onclick="toggleFilterPanel()" style="background:transparent; border:2px solid var(--accent); color:var(--accent); padding:8px 12px; border-radius:6px; cursor:pointer; font-size:16px; transition:all .2s ease;" onmouseover="this.style.background='rgba(106,77,247,0.1)';" onmouseout="this.style.background='transparent';" title="Tunjukkan/Sembunyikan Penapis">
          <i class="bi bi-funnel"></i>
        </button>
      </div>

      <!-- Filter Panel (Collapsible) -->
      <div id="filter-panel" style="display:{{ !empty(array_filter(['q' => request('q'), 'file_type' => request('file_type'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])) ? 'block' : 'none' }}; margin-bottom:20px; padding-bottom:0;">
        <form method="GET" action="{{ route('lesson.index') }}" id="filter-form">
          <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; margin-bottom:0;">
            <div>
              <label style="font-size:12px;">Cari</label>
              <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tajuk atau penerangan" onchange="autoSubmitForm()" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
            </div>
            <div>
              <label style="font-size:12px;">Jenis Fail</label>
              <select name="file_type" onchange="autoSubmitForm()" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
                <option value="">Semua</option>
                <option value="pdf" @if (isset($filters['file_type']) && $filters['file_type'] == 'pdf') selected @endif>PDF</option>
                <option value="docx" @if (isset($filters['file_type']) && $filters['file_type'] == 'docx') selected @endif>DOCX</option>
                <option value="pptx" @if (isset($filters['file_type']) && $filters['file_type'] == 'pptx') selected @endif>PPTX</option>
                <option value="jpg" @if (isset($filters['file_type']) && $filters['file_type'] == 'jpg') selected @endif>JPG</option>
                <option value="png" @if (isset($filters['file_type']) && $filters['file_type'] == 'png') selected @endif>PNG</option>
              </select>
            </div>
            <div>
              <label style="font-size:12px;">Dari</label>
              <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" onchange="autoSubmitForm()" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
            </div>
            <div style="display:flex; flex-direction:column;">
              <label style="font-size:12px;">Hingga</label>
              <div style="display:flex; gap:8px; align-items:flex-start; height:40px;">
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" onchange="autoSubmitForm()" style="height:100%; flex:1; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
                <a href="{{ route('lesson.index') }}" onclick="keepFilterPanelOpen(); return true;" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:8px; cursor:pointer; font-size:24px; transition:all .2s ease; text-decoration:none; white-space:nowrap; height:40px; width:40px;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Ulang Penapis">
                  <i class="bi bi-arrow-repeat"></i>
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
      
      <table>
        <thead>
          <tr>
            <th style="width:60%">Bahan</th>
            <th style="width:20%">Fail</th>
            <th style="width:20%">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($lessons ?? [] as $lesson)
            <tr>
              <td style="width:60%">
                <div class="table-title">{{ $lesson->title }}</div>
                <div class="table-subtitle">{{ $lesson->description ?: 'Tiada penerangan' }}</div>
                <div class="table-meta">
                  <span class="table-badge"><strong>Kelas:</strong> {{ $lesson->class_group ?? 'N/A' }}</span>
                  <span class="table-badge">
                    <strong>Dibuat:</strong> {{ $lesson->created_at->format('M d, Y') }}
                  </span>
                </div>
              </td>
              <td style="width:20%; text-align:center;">
                @if ($lesson->file_path)
                  <div class="table-title">{{ strtoupper(pathinfo($lesson->file_name ?? $lesson->file_path, PATHINFO_EXTENSION)) }}</div>
                  <div class="table-subtitle">{{ $lesson->file_name ?? basename($lesson->file_path) }}</div>
                @else
                  <div class="table-subtitle">Tiada fail</div>
                @endif
              </td>
              <td style="width:20%; text-align:center;">
                <div style="display:flex; gap:20px; justify-content:center;">
                  @if ($lesson->file_path)
                    <a href="{{ route('lesson.preview-file', $lesson->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Lihat">
                      <i class="bi bi-eye-fill"></i>
                    </a>
                    <a href="{{ route('lesson.download', $lesson->id) }}" download style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Muat Turun">
                      <i class="bi bi-download"></i>
                    </a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align:center; padding:24px; color:var(--muted);">
                @if (!empty(array_filter(['q' => request('q'), 'file_type' => request('file_type'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])))
                  Tiada bahan sepadan dengan kriteria anda.
                @else
                  Tiada bahan ditemui.
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
