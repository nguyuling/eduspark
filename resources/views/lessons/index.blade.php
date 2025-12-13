@extends('layouts.app')

@section('content')

<style>
/* Theme variables defined in layouts/app.blade.php */
.app { display:flex; min-height:80vh; gap:28px; padding:28px; font-family: Inter, system-ui, sans-serif; margin-left:268px; }

.cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:16px; margin-bottom:20px; }
.card { border-radius:var(--card-radius); padding:14px 16px; display:flex; flex-direction:column; align-items:flex-start; justify-content:center; text-align:left; transition: transform .12s ease, box-shadow .12s ease; background: transparent; }
body.light .card { background:var(--card-light); border:1px solid rgba(11,18,32,0.04); }
body.dark .card  { background:var(--card-dark); border:1px solid rgba(255,255,255,0.03); }
.card .label { font-size:13px; color:var(--muted); font-weight:600; }
.card .value { font-weight:700; font-size:20px; margin-top:6px; }

.panel { border-radius:var(--card-radius); padding:20px; animation: fadeInUp .4s ease; margin-bottom:20px; background: transparent; border: 2px solid #d4c5f9; backdrop-filter: blur(6px); box-shadow: 0 2px 12px rgba(2,6,23,0.18); transition: border-color .2s ease; }
body.light .panel { background: rgba(255,255,255,0.96); }
body.dark .panel  { background:#0f1724; }

.panel:hover { border-color: var(--accent); }

input[type="text"], input[type="date"], textarea, select, input[type="file"] { width:100%; padding:11px 14px; border-radius:8px; border:1px solid var(--control-border); background: var(--input-bg); color: inherit; font-size:14px; outline: none; transition: box-shadow .12s ease, border-color .12s ease, transform .06s ease; resize: vertical; box-sizing: border-box; }

/* Search & Filter / Form input styling - gray borders */
.panel form input[type="text"],
.panel form input[type="date"],
.panel form textarea,
.panel form select,
.panel form input[type="file"] {
  border: 2px solid #d1d5db !important;
  background: transparent !important;
  color: inherit;
  padding: 11px 14px !important;
  box-sizing: border-box;
  border-radius: 8px;
  transition: border-color .2s ease, background .2s ease;
  width: 100% !important;
}

.panel form input[type="text"]:hover,
.panel form input[type="date"]:hover,
.panel form textarea:hover,
.panel form select:hover,
.panel form input[type="file"]:hover {
  border-color: #9ca3af !important;
  background: rgba(200, 200, 200, 0.08) !important;
}

.panel form input[type="text"]:focus,
.panel form input[type="date"]:focus,
.panel form textarea:focus,
.panel form select:focus,
.panel form input[type="file"]:focus {
  border-color: #9ca3af !important;
  background: rgba(200, 200, 200, 0.08) !important;
  outline: none;
}

textarea { min-height:84px; line-height:1.45; }

input[type="file"] { padding:8px 12px; border-radius:8px; }

input[type="text"]:focus, textarea:focus, select:focus, input[type="date"]:focus, input[type="file"]:focus { box-shadow: var(--focus-glow); border-color: var(--accent); transform: translateY(-1px); }

::placeholder { color: rgba(255,255,255,0.45); }
label { font-size:13px; color:var(--muted); font-weight:600; display:block; margin-bottom:6px; }
.small-muted { color:var(--muted); font-size:13px; }

button { cursor:pointer; padding:8px 12px; border-radius:10px; border:none; background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; font-weight:700; font-size:14px; transition: transform .08s ease, box-shadow .12s ease, opacity .12s ease; box-shadow: 0 6px 18px rgba(8,12,32,0.25); }
button[style*="background:transparent"], .button-outline { background: transparent !important; color: inherit; border: 1px solid rgba(255,255,255,0.06); box-shadow: none; }
button.danger { background: var(--danger); box-shadow: none; }
button:hover { transform: translateY(-3px); opacity:0.98; }
button:active { transform: translateY(-1px); }
.btn-small { padding:6px 10px; font-size:13px; border-radius:8px; }

.btn { cursor:pointer; padding:8px 12px; border-radius:10px; border:none; font-weight:700; font-size:14px; transition: transform .08s ease, box-shadow .12s ease, opacity .12s ease; display:inline-flex; align-items:center; gap:6px; text-decoration:none; }
.btn-primary { background: linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; box-shadow: 0 6px 18px rgba(8,12,32,0.25); }
.btn-primary:hover { transform: translateY(-3px); opacity:0.98; }
table { width:100%; border-collapse:collapse; font-size:14px; margin-top:1rem; border: 2px solid #d4c5f9; border-radius: 8px; overflow: hidden; }
thead th { text-align:center; font-weight:700; color:var(--muted); font-size:13px; padding:12px 10px; border-bottom: 2px solid #d4c5f9; background: rgba(212, 197, 249, 0.05); }
tbody td { padding:12px 10px; border-bottom: 1px solid #e5e1f2; vertical-align: middle; background: transparent; border-right: 1px solid #e5e1f2; }
tbody td:last-child { border-right: none; }
tbody tr:last-child td { border-bottom: none; }

tbody tr:hover td { background: rgba(212, 197, 249, 0.08); }
.file-meta { display:flex; flex-direction:column; gap:6px; }
.actions { display:flex; gap:8px; align-items:center; }

@media (max-width:920px){ .sidebar{ display:none; } .app{ padding:14px; } thead th:nth-child(5), td:nth-child(5) { min-width: 160px; } }

@keyframes fadeInUp { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:none;} }
@keyframes fadeInDown { from{opacity:0; transform:translateY(-10px);} to{opacity:1; transform:none;} }
.hidden { display:none; }
.center { display:flex; align-items:center; justify-content:center; }
</style>

<div class="app">
  <!-- Main -->
  <main class="main" style="flex:1;">
    <div class="header" style="display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:20px; margin-top:40px; margin-left:40px; margin-right:40px;">
      <div>
        <div class="title" style="font-weight:700;font-size:28px;">Bahan Pembelajaran</div>
        <div class="sub" style="color:var(--muted);font-size:13px;">Lihat semua bahan pembelajaran</div>
      </div>
      <div style="display:flex;gap:12px;align-items:center;">
        <div class="card" style="margin:0; min-width:120px;">
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
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
      <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700;">Cari Bahan</h2>
      <form method="GET" action="{{ route('lessons.index') }}" style="display:grid; grid-template-columns:1fr 1fr 1fr 1fr; gap:12px; margin-bottom:20px;">
        <div>
          <label>Cari</label>
          <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tajuk atau penerangan" style="height:40px;">
        </div>
        <div>
          <label>Jenis Fail</label>
          <select name="file_type" style="height:40px;">
            <option value="">Semua</option>
            <option value="pdf" @if (isset($filters['file_type']) && $filters['file_type'] == 'pdf') selected @endif>PDF</option>
            <option value="docx" @if (isset($filters['file_type']) && $filters['file_type'] == 'docx') selected @endif>DOCX</option>
            <option value="pptx" @if (isset($filters['file_type']) && $filters['file_type'] == 'pptx') selected @endif>PPTX</option>
            <option value="jpg" @if (isset($filters['file_type']) && $filters['file_type'] == 'jpg') selected @endif>JPG</option>
            <option value="png" @if (isset($filters['file_type']) && $filters['file_type'] == 'png') selected @endif>PNG</option>
          </select>
        </div>
        <div>
          <label>Dari</label>
          <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" style="height:40px;">
        </div>
        <div>
          <label>Hingga</label>
          <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" style="height:40px;">
        </div>
      </form>
      <div style="display:flex; gap:10px; align-items:center; justify-content:flex-end; margin-top:20px;">
        <a href="{{ route('lessons.index') }}" class="btn-small button-outline">Kosongkan Penapis</a>
        <button onclick="document.querySelector('.panel form').submit();" class="btn-small">Gunakan Penapis</button>
      </div>
    </section>

    <!-- Lessons Available Panel -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h2 style="margin:0 0 10px 0; font-size:18px; font-weight:700;">Bahan Tersedia</h2>
        <a href="{{ route('lessons.create') }}" class="btn btn-primary" style="padding:10px 18px; border-radius:10px; border:none; font-weight:700; font-size:14px; transition: transform .08s ease, box-shadow .12s ease, opacity .12s ease; display:inline-flex; align-items:center; gap:6px; text-decoration:none;">Cipta Bahan</a>
      </div>
      
      <table style="margin-top:20px;">
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
                <div style="font-weight:700; margin-bottom:4px;">{{ $lesson->title }}</div>
                <div style="font-size:13px; color:var(--muted); margin-bottom:8px; line-height:1.4;">{{ $lesson->description ?: 'Tiada penerangan' }}</div>
                <div style="display:flex; gap:6px; flex-wrap:wrap; font-size:11px; align-items:center;">
                  <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px;"><strong>Kelas:</strong> {{ $lesson->class_group ?? 'N/A' }}</span>
                  <span style="background:rgba(106,77,247,0.08); padding:4px 8px; border-radius:4px;">
                    <strong>Dibuat:</strong> {{ $lesson->created_at->format('M d, Y') }}
                  </span>
                </div>
              </td>
              <td style="width:20%; text-align:center;">
                @if ($lesson->file_path)
                  <div style="font-weight:700; font-size:12px; margin-bottom:4px;">{{ strtoupper(pathinfo($lesson->file_name ?? $lesson->file_path, PATHINFO_EXTENSION)) }}</div>
                  <div style="font-size:11px; color:var(--muted);">{{ $lesson->file_name ?? basename($lesson->file_path) }}</div>
                @else
                  <div style="color:var(--muted); font-size:12px;">Tiada fail</div>
                @endif
              </td>
              <td style="width:20%; text-align:center;">
                <div style="display:flex; gap:6px; justify-content:center; flex-wrap:wrap;">
                  @if ($lesson->file_path)
                    <a href="{{ route('lesson.preview.file', $lesson->id) }}" style="background: transparent; color: inherit; border: 1px solid rgba(255,255,255,0.06); box-shadow: none; padding:12px 20px; border-radius:10px; font-weight:700; font-size:15px; text-decoration:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition: transform .08s ease, opacity .12s ease;">Lihat</a>
                    <a href="{{ route('lesson.download', $lesson->id) }}" download style="background: transparent; color: inherit; border: 1px solid rgba(255,255,255,0.06); box-shadow: none; padding:12px 20px; border-radius:10px; font-weight:700; font-size:15px; text-decoration:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition: transform .08s ease, opacity .12s ease;">Muat Turun</a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" style="text-align:center; padding:20px; color:var(--muted);">Tiada bahan ditemui.</td>
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
