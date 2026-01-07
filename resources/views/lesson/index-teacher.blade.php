@extends('layouts.app')

@section('content')

<div class="app">
  <!-- Main -->
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Bahan Pembelajaran</div>
        <div class="sub">Kelola semua bahan pembelajaran</div>
      </div>
      <a href="{{ route('lesson.create') }}" style="display:inline-block; padding:12px 24px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; text-decoration:none; border-radius:8px; font-weight:700; font-size:14px; transition:transform .2s ease, box-shadow .2s ease; box-shadow: 0 4px 12px rgba(106,77,247,0.3); border:none; cursor:pointer; margin-top:15px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)';">
        <i class="bi bi-plus-lg"></i>
        Cipta Bahan
      </a>
    </div>

    @if (session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
      <div style="background:var(--danger);color:#fff;padding:12px 14px;border-radius:var(--card-radius);margin-bottom:20px;margin-left:40px;margin-right:40px;font-size:14px;">{{ session('error') }}</div>
    @endif

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

    <!-- Lessons Panel -->
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <div style="display:flex; gap:8px; align-items:center; margin-bottom:20px;">
        <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Senarai Bahan Pembelajaran</h2>
        <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px;">
          {{ count($lessons ?? []) }}
        </span>
      </div>
      
      <table>
        <thead>
          <tr>
            <th style="width:50%">Bahan</th>
            <th style="width:18%">Fail</th>
            <th style="width:32%">Tindakan</th>
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
                  @if($lesson->uploaded_by === Auth::id())
                    <span class="table-badge" style="background:rgba(106,77,247,0.1); color:var(--accent);">
                      <i class="bi bi-person-check" style="margin-right:4px;"></i><strong>Anda</strong>
                    </span>
                  @endif
                </div>
              </td>
              <td style="width:18%;" class="table-center">
                @if ($lesson->file_path)
                  <div class="table-title">{{ strtoupper(pathinfo($lesson->file_name ?? $lesson->file_path, PATHINFO_EXTENSION)) }}</div>
                  <div class="table-subtitle">{{ $lesson->file_name ?? basename($lesson->file_path) }}</div>
                @else
                  <div class="table-subtitle">Tiada fail</div>
                @endif
              </td>
              <td style="width:32%;" class="table-center">
                <div style="display:flex; gap:20px; justify-content:center; align-items:center;">
                  <!-- VIEW BUTTON (NOW WORKING) -->
                  <a href="{{ route('lesson.show', $lesson->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Lihat Butiran">
                    <i class="bi bi-eye-fill"></i>
                  </a>
                  
                  @if ($lesson->file_path)
                    <a href="{{ route('lesson.download', $lesson->id) }}" download style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Muat Turun">
                      <i class="bi bi-download"></i>
                    </a>
                  @endif
                  
                  <!-- EDIT BUTTON (WITH OWNER CHECK) -->
                  @if($lesson->uploaded_by === Auth::id())
                    <a href="{{ route('lesson.edit', $lesson->id) }}" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--accent); padding:0; font-size:24px; transition:opacity .2s ease; text-decoration:none; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Kemaskini">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                    
                    <!-- DELETE BUTTON (WITH OWNER CHECK) -->
                    <form action="{{ route('lesson.destroy', $lesson->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti ingin memadamkan \'{{ addslashes($lesson->title) }}\'?');" style="display:inline; margin:0;">
                      @csrf
                      @method('DELETE')
                      <button type="submit" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; color:var(--danger); padding:0; font-size:24px; transition:opacity .2s ease; cursor:pointer;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Padam">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  @else
                    <!-- Show locked icons for non-owners -->
                    <span style="display:inline-flex; align-items:center; justify-content:center; color:var(--muted); padding:0; font-size:24px; opacity:0.3; cursor:not-allowed;" title="Hanya pemilik boleh edit">
                      <i class="bi bi-pencil-square"></i>
                    </span>
                    <span style="display:inline-flex; align-items:center; justify-content:center; color:var(--muted); padding:0; font-size:24px; opacity:0.3; cursor:not-allowed;" title="Hanya pemilik boleh padam">
                      <i class="bi bi-trash"></i>
                    </span>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="empty-state" style="text-align:center; padding:24px; color:var(--muted);">
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

@endsection