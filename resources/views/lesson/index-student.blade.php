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

    <!-- Filter Panel -->
    <section class="panel" style="margin-bottom:20px; padding-bottom:20px;">
        <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; line-height:1;">Cari Bahan</h2>
        <form method="GET" action="{{ route('lesson.index') }}" id="filter-form">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; margin-bottom:15px;">
            <div>
                <label style="font-size:12px;">Tajuk atau Penerangan</label>
                <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari bahan..." onchange="autoSubmitForm()" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
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
                <label style="font-size:12px;">Dari Tarikh</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" onchange="autoSubmitForm()" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
            </div>
            <div style="display:flex; flex-direction:column;">
                <label style="font-size:12px;">Hingga Tarikh</label>
                <div style="display:flex; gap:8px; align-items:flex-start; height:40px;">
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" onchange="autoSubmitForm()" style="height:100%; flex:1; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;">
                <a href="{{ route('lesson.index') }}" onclick="keepFilterPanelOpen(); return true;" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:8px; cursor:pointer; font-size:24px; transition:all .2s ease; text-decoration:none; white-space:nowrap; height:40px; width:40px;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Ulang Penapis">
                    <i class="bi bi-arrow-repeat"></i>
                </a>
                </div>
            </div>
            </div>
        </form>
    </section>

    <!-- Lessons Panel -->
    <section class="panel" style="margin-bottom:20px; padding-bottom:20px;">
        <div style="display:flex; gap:8px; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Senarai Bahan</h2>
            <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px;">
            {{ count($lessons ?? []) }}
            </span>
        </div>
        
        <table>
        <thead>
            <tr>
            <th style="width:50%">Bahan</th>
            <th style="width:25%">Fail</th>
            <th style="width:25%">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lessons ?? [] as $lesson)
            <tr>
                <td style="width:50%">
                <div class="table-title">{{ $lesson->title }}</div>
                <div class="table-subtitle">{{ $lesson->description ?: 'Tiada penerangan' }}</div>
                <div class="table-meta">
                    <span class="table-badge"><strong>Kelas:</strong> {{ $lesson->class_group ?? 'N/A' }}</span>
                    <span class="table-badge">
                    <strong>Dicipta:</strong> {{ $lesson->created_at->format('M d, Y') }}
                    </span>
                </div>
                </td>
                <td style="width:25%; text-align:center;">
                @if ($lesson->file_path)
                    <div class="table-title">{{ strtoupper(pathinfo($lesson->file_name ?? $lesson->file_path, PATHINFO_EXTENSION)) }}</div>
                    <div class="table-subtitle">{{ $lesson->file_name ?? basename($lesson->file_path) }}</div>
                @else
                    <div class="table-subtitle">Tiada fail</div>
                @endif
                </td>
                <td style="width:25%; text-align:center;">
                <div style="display:flex; gap:20px; justify-content:center;">
                    @if ($lesson->file_path)
                    <a href="{{ route('lesson.preview', $lesson->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Lihat Butiran">
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

        <!-- Show More Section -->
        @if ($hasMore ?? false)
        <div style="text-align:center; margin-top:20px; padding:20px;">
            <a href="{{ route('lesson.index', array_merge(request()->query(), ['limit' => $nextLimit])) }}" style="color:var(--accent); text-decoration:none; font-size:14px; cursor:pointer;" onmouseover="this.style.textDecoration='underline';" onmouseout="this.style.textDecoration='none';">
            Tunjukkan 10 Bahan Lagi
            </a>
        </div>
        @endif
    </section>
  </main>
</div>

<script>
function keepFilterPanelOpen() {
  sessionStorage.setItem('keepFilterOpen', 'true');
}

function autoSubmitForm() {
  document.getElementById('filter-form').submit();
}

// Keep filter panel open if flag is set
document.addEventListener('DOMContentLoaded', function() {
  if (sessionStorage.getItem('keepFilterOpen') === 'true') {
    sessionStorage.removeItem('keepFilterOpen');
  }
});
</script>

@endsection
