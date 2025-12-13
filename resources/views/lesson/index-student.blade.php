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
      <div class="header-content">
        <div class="card">
          <div class="label">Total Bahan</div>
          <div class="value">
            <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:8px 12px; border-radius:999px;">
              {{ \App\Models\Lesson::count() }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Lesson Panel -->
    <section class="panel panel-spaced" style="margin-top: 20px;">
      <h2 class="section-heading">Cari Bahan</h2>
      <form method="GET" action="{{ route('lesson.index') }}" class="filter-form">
        <div class="form-row">
          <label>Cari</label>
          <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tajuk atau penerangan" class="form-input-height">
        </div>
        <div class="form-row">
          <label>Jenis Fail</label>
          <select name="file_type" class="form-input-height">
            <option value="">Semua</option>
            <option value="pdf" @if (isset($filters['file_type']) && $filters['file_type'] == 'pdf') selected @endif>PDF</option>
            <option value="docx" @if (isset($filters['file_type']) && $filters['file_type'] == 'docx') selected @endif>DOCX</option>
            <option value="pptx" @if (isset($filters['file_type']) && $filters['file_type'] == 'pptx') selected @endif>PPTX</option>
            <option value="jpg" @if (isset($filters['file_type']) && $filters['file_type'] == 'jpg') selected @endif>JPG</option>
            <option value="png" @if (isset($filters['file_type']) && $filters['file_type'] == 'png') selected @endif>PNG</option>
          </select>
        </div>
        <div class="form-row">
          <label>Dari</label>
          <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-input-height">
        </div>
        <div class="form-row">
          <label>Hingga</label>
          <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-input-height">
        </div>
      </form>
      <div class="filter-actions">
        <a href="{{ route('lesson.index') }}" class="btn-small button-outline">Kosongkan Penapis</a>
        <button onclick="document.querySelector('.panel form').submit();" class="btn-small">Gunakan Penapis</button>
      </div>
    </section>

    <!-- Lessons Available Panel -->
    <section class="panel panel-spaced">
      <div class="section-header">
        <h2>Bahan Tersedia</h2>
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
              <td style="width:20%;" class="table-center">
                @if ($lesson->file_path)
                  <div class="table-title">{{ strtoupper(pathinfo($lesson->file_name ?? $lesson->file_path, PATHINFO_EXTENSION)) }}</div>
                  <div class="table-subtitle">{{ $lesson->file_name ?? basename($lesson->file_path) }}</div>
                @else
                  <div class="table-subtitle">Tiada fail</div>
                @endif
              </td>
              <td style="width:20%;" class="table-center">
                <div class="table-actions">
                  @if ($lesson->file_path)
                    <a href="{{ route('lesson.preview.file', $lesson->id) }}" class="btn btn-secondary">Lihat</a>
                    <a href="{{ route('lesson.download', $lesson->id) }}" download class="btn btn-secondary">Muat Turun</a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="empty-state">Tiada bahan ditemui.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </section>
  </main>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Theme toggle is now in sidebar
});
</script>
