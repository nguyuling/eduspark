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

    <!-- Search and Filter Section -->
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <form method="GET" action="{{ route('lesson.index') }}" style="display:grid; grid-template-columns: 2fr 1fr 1fr auto; gap:12px; align-items:end;">
          
          <!-- Search by Title -->
          <div>
            <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px; color:var(--text);">Cari Bahan</label>
            <input 
              type="text" 
              name="q" 
              value="{{ $filters['q'] ?? '' }}"
              placeholder="Cari tajuk bahan..."
              style="width:100%; padding:10px 14px; border-radius:8px; border:2px solid #d1d5db; background:transparent; color:inherit; font-size:14px; outline:none; transition:border-color 0.2s ease;"
              onfocus="this.style.borderColor='var(--accent)'"
              onblur="this.style.borderColor='#d1d5db'"
            >
          </div>

          <!-- Filter by File Type -->
          <div>
            <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px; color:var(--text);">Jenis Fail</label>
            <select 
              name="file_type"
              style="width:100%; padding:10px 14px; border-radius:8px; border:2px solid #d1d5db; background:transparent; color:inherit; font-size:14px; outline:none; cursor:pointer; transition:border-color 0.2s ease;"
              onfocus="this.style.borderColor='var(--accent)'"
              onblur="this.style.borderColor='#d1d5db'"
            >
              <option value="">Semua</option>
              <option value="pdf" @if (isset($filters['file_type']) && $filters['file_type'] == 'pdf') selected @endif>PDF</option>
              <option value="docx" @if (isset($filters['file_type']) && $filters['file_type'] == 'docx') selected @endif>DOCX</option>
              <option value="pptx" @if (isset($filters['file_type']) && $filters['file_type'] == 'pptx') selected @endif>PPTX</option>
              <option value="jpg" @if (isset($filters['file_type']) && $filters['file_type'] == 'jpg') selected @endif>JPG</option>
              <option value="png" @if (isset($filters['file_type']) && $filters['file_type'] == 'png') selected @endif>PNG</option>
            </select>
          </div>

          <!-- Filter by Date Range -->
          <div>
            <label style="display:block; font-weight:600; font-size:13px; margin-bottom:6px; color:var(--text);">Tarikh Cipta</label>
            <input 
              type="date" 
              name="date_from" 
              value="{{ $filters['date_from'] ?? '' }}"
              style="width:100%; padding:10px 14px; border-radius:8px; border:2px solid #d1d5db; background:transparent; color:inherit; font-size:14px; outline:none; transition:border-color 0.2s ease;"
              onfocus="this.style.borderColor='var(--accent)'"
              onblur="this.style.borderColor='#d1d5db'"
            >
          </div>

          <!-- Search Button -->
          <div style="display:flex; gap:8px;">
            <button type="submit" class="btn-cari">
              <i class="bi bi-search"></i> Cari
            </button>
            @if(request()->hasAny(['q', 'file_type', 'date_from', 'date_to']))
              <a href="{{ route('lesson.index') }}" class="btn-semula">
                <i class="bi bi-x-lg"></i> Semula
              </a>
            @endif
          </div>
        </form>
      </section>

    <section class="panel" style="margin-bottom:20px;">
      <div style="margin-bottom:20px;">
        <span class="badge-pill">Jumlah: {{ $lessons->count() }}</span>
      </div>
      <table>
        <thead>
          <tr>
            <th style="width:5%">No.</th>
            <th style="width:55%">Bahan</th>
            <th style="width:20%">Fail</th>
            <th style="width:20%">Tindakan</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($lessons ?? [] as $index => $lesson)
            <tr>
              <td style="width:5%; text-align:center; font-weight:600;">{{ $index + 1 }}</td>
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
                    <a href="{{ route('lesson.show', $lesson->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Lihat Butiran">
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
              <td colspan="4" style="text-align:center; padding:24px; color:var(--muted);">
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

@endsection
