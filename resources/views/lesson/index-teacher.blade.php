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
      <div class="section-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h2 style="margin:0; font-size:18px; font-weight:700;">Bahan Tersedia</h2>
        <div class="card" style="margin:0; min-width:180px; display:flex; justify-content:space-between; align-items:center;">
          <div class="label" style="margin-bottom:0;">Total Bahan</div>
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:8px 12px; border-radius:999px; font-weight:700;">
            {{ \App\Models\Lesson::count() }}
          </span>
        </div>
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
                    <strong>Dibuat:</strong> {{ $lesson->created_at->format('M d, Y') }}
                  </span>
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
                <div class="table-actions">
                  @if ($lesson->file_path)
                    <a href="{{ route('lesson.preview.file', $lesson->id) }}" class="btn btn-secondary">Lihat</a>
                    <a href="{{ route('lesson.download', $lesson->id) }}" download class="btn btn-secondary">Muat Turun</a>
                  @endif
                  <button onclick="editLesson({{ $lesson->id }}, '{{ addslashes($lesson->title) }}', '{{ addslashes($lesson->description ?? '') }}', '{{ $lesson->class_group }}', '{{ $lesson->visibility }}')" class="btn btn-secondary">Kemaskini</button>
                  <button onclick="deleteLesson({{ $lesson->id }}, '{{ addslashes($lesson->title) }}')" class="btn btn-secondary" style="color:var(--danger); border-color:var(--danger);">Padam</button>
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

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:50; align-items:center; justify-content:center;">
  <div style="background:var(--card-dark); border-radius:12px; padding:24px; max-width:500px; width:90%; max-height:80vh; overflow-y:auto;">
    <h2 style="margin-bottom:16px;">Kemaskini Bahan</h2>
    <form id="editLessonForm" enctype="multipart/form-data">
      @csrf
      <input type="hidden" id="editLessonId" name="lesson_id">
      
      <div style="margin-bottom:12px;">
        <label>Tajuk:</label>
        <input type="text" id="editTitle" name="title" required style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--control-border); background:var(--input-bg); color:inherit;">
      </div>

      <div style="margin-bottom:12px;">
        <label>Penerangan:</label>
        <textarea id="editDescription" name="description" rows="3" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--control-border); background:var(--input-bg); color:inherit;"></textarea>
      </div>

      <div style="margin-bottom:12px;">
        <label>Kumpulan Kelas:</label>
        <select id="editClassGroup" name="class_group" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--control-border); background:var(--input-bg); color:inherit;">
          <option value="4A">4A</option>
          <option value="4B">4B</option>
          <option value="4C">4C</option>
          <option value="5A">5A</option>
          <option value="5B">5B</option>
        </select>
      </div>

      <div style="margin-bottom:12px;">
        <label>Keterlihatan:</label>
        <select id="editVisibility" name="visibility" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--control-border); background:var(--input-bg); color:inherit;">
          <option value="class">Kelas Sahaja</option>
          <option value="public">Awam (Semua Pelajar)</option>
        </select>
      </div>

      <div style="margin-bottom:16px;">
        <label>Fail (Tinggalkan kosong untuk tidak mengubah):</label>
        <input type="file" name="file" accept=".pdf,.docx,.pptx,.txt,.jpg,.png" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--control-border); background:var(--input-bg); color:inherit;">
      </div>

      <div style="display:flex; gap:8px;">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" onclick="closeEditModal()" class="btn" style="background:transparent; border:1px solid var(--control-border); color:inherit;">Batal</button>
      </div>
    </form>
  </div>
</div>

@endsection

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function editLesson(id, title, description, classGroup, visibility) {
  document.getElementById('editLessonId').value = id;
  document.getElementById('editTitle').value = title;
  document.getElementById('editDescription').value = description;
  document.getElementById('editClassGroup').value = classGroup;
  document.getElementById('editVisibility').value = visibility;
  document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
  document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editLessonForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const lessonId = document.getElementById('editLessonId').value;
  const formData = new FormData(this);
  
  try {
    const response = await fetch(`/api/lessons/${lessonId}`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken },
      body: formData
    });
    const data = await response.json();
    if (!data.success) throw new Error(data.message || 'Error');
    alert('Bahan dikemaskini!');
    location.reload();
  } catch(err) {
    alert('Ralat: ' + err.message);
  }
});

function deleteLesson(id, title) {
  if (confirm('Adakah anda pasti ingin memadamkan "' + title + '"?')) {
    fetch(`/api/lessons/${id}/delete`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfToken }
    }).then(r => r.json()).then(data => {
      alert(data.message || 'Bahan dipadamkan');
      location.reload();
    }).catch(e => alert('Ralat: ' + e.message));
  }
}

document.addEventListener('click', e => {
  if (e.target.id === 'editModal') closeEditModal();
});
</script>
