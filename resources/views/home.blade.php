@extends('layouts.app')

@section('content')

<style>
/* ---------- Theme variables (kept from lessons UI) ---------- */
:root{
  --bg-light:#f5f7ff;
  --bg-dark:#071026;
  --card-light:rgba(255,255,255,0.9);
  --card-dark:#0f1724;
  --accent:#6A4DF7;
  --accent-2:#9C7BFF;
  --muted:#98a0b3;
  --success:#2A9D8F;
  --danger:#E63946;
  --yellow:#F4C430;
  --glass: rgba(255,255,255,0.04);
  --input-bg: rgba(255,255,255,0.02);
  --control-border: rgba(255,255,255,0.08);
  --radius: 10px;
  --card-radius: 14px;
  --focus-glow: 0 6px 20px rgba(106,77,247,0.12);
  --shadow-soft: 0 6px 20px rgba(2,6,23,0.45);
  font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

body.light { background:var(--bg-light); color:#0b1220; }
body.dark  { background:var(--bg-dark); color:#e6eef8; }

.app { display:flex; min-height:80vh; gap:28px; padding:28px; font-family: Inter, system-ui, sans-serif; }

.sidebar{
  width:240px; border-radius:var(--card-radius); padding:18px;
  display:flex; flex-direction:column; align-items:center; gap:12px;
  backdrop-filter: blur(8px) saturate(120%);
  box-shadow: none;
}
body.light .sidebar{
  background: linear-gradient(180deg, rgba(255,255,255,0.75), rgba(255,255,255,0.68));
  border:1px solid rgba(13,18,25,0.05);
}
body.dark .sidebar{
  background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
  border:1px solid rgba(255,255,255,0.03);
}
.logo { width:110px; height:auto; margin-bottom:6px; }

.nav { width:100%; margin-top:10px; padding-top:6px; }
.nav a {
  display:flex; align-items:center; gap:10px;
  padding:10px 12px; border-radius:10px;
  color:var(--muted); text-decoration:none; font-weight:600;
  margin:6px 0; position:relative; font-size:14px;
}
.nav a.active { color:var(--accent); }

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

table { width:100%; border-collapse:collapse; font-size:14px; margin-top:1rem; border: 2px solid #d4c5f9; border-radius: 8px; overflow: hidden; }
thead th { text-align:center; font-weight:700; color:var(--muted); font-size:13px; padding:12px 10px; border-bottom: 2px solid #d4c5f9; background: rgba(212, 197, 249, 0.05); }
tbody td { padding:12px 10px; border-bottom: 1px solid #e5e1f2; vertical-align: middle; background: transparent; border-right: 1px solid #e5e1f2; }
tbody td:last-child { border-right: none; }
tbody tr:last-child td { border-bottom: none; }

tbody tr:hover td { background: rgba(212, 197, 249, 0.08); }
.file-meta { display:flex; flex-direction:column; gap:6px; }
.actions { display:flex; gap:8px; align-items:center; }

@media (max-width:920px){ .sidebar{ display:none; } .app{ padding:14px; } thead th:nth-child(5), td:nth-child(5) { min-width: 160px; } }

.modal-backdrop { position:fixed; inset:0; display:none; align-items:center; justify-content:center; background: rgba(0,0,0,0.5); z-index:50; padding:20px; }
.modal { width:100%; max-width:980px; height:82vh; background: var(--card-dark); border-radius:12px; overflow:hidden; display:flex; flex-direction:column; box-shadow: var(--shadow-soft); border: 1px solid rgba(255,255,255,0.03); }
.modal header { padding:12px 16px; display:flex; justify-content:space-between; align-items:center; border-bottom: 1px solid rgba(255,255,255,0.03); background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); }
.modal header .info { color:var(--muted); font-size:13px; }
.modal .content { flex:1; background: #fff; display:flex; align-items:stretch; justify-content:stretch; }
.modal iframe { width:100%; height:100%; border:0; }
.progress-bar { height:6px; background:rgba(2,6,23,0.06); border-radius:6px; overflow:hidden; margin-top:6px; }
.progress-bar > span { display:block; height:100%; width:0%; background: linear-gradient(90deg,var(--accent),var(--accent-2)); transition: width .15s ease; }
@keyframes fadeInUp { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:none;} }
@keyframes fadeInDown { from{opacity:0; transform:translateY(-10px);} to{opacity:1; transform:none;} }
.hidden { display:none; }
.center { display:flex; align-items:center; justify-content:center; }
</style>

<div class="app">
  <!-- Sidebar -->
  <aside class="sidebar">
    <img src="{{ asset('logo.png') }}" alt="EduSpark logo" class="logo">
    <div class="logo-text" aria-hidden="true" style="font-weight:700;font-size:18px;">
      <span style="color:#1D5DCD;">edu</span><span style="color:#E63946;">Spark</span>
    </div>
    <nav class="nav">
      <a href="{{ route('home') }}" class="active">Lessons</a>
      <a href="#">Forum</a>
      <a href="#">Games</a>
      <a href="{{ Auth::user()->role === 'teacher' ? route('teacher.quizzes.index') : route('student.quizzes.index') }}">Quiz</a>
      <a href="#">Performance</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main" style="flex:1;">
    <div class="header" style="display:flex;justify-content:space-between;align-items:center; margin-bottom:20px;">
      <div>
        <div class="title" style="font-weight:700;font-size:20px;">Lessons</div>
        <div class="sub" style="color:var(--muted);font-size:13px;">Manage lesson materials</div>
      </div>
      <button id="themeToggle" style="background:none;border:0;color:inherit;font-weight:600;cursor:pointer;">🌙</button>
    </div>

    <!-- Cards (optional stats placeholders) -->
    <section class="cards">
      <div class="card">
        <div class="label">Total Lessons</div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:8px 12px; border-radius:999px;">
            {{ \App\Models\Lesson::count() }}
          </span>
        </div>
      </div>
    </section>

    <!-- Lesson List Table -->
    <section class="panel">
      <h2 style="margin:0 0 12px 0; font-size:18px;">Search Lesson</h2>

      <!-- Search & Filters (Sprint 3) -->
      <div style="display:flex; gap:12px; margin-bottom:12px; align-items:end;">
        <div style="flex:1.5;">
          <label class="small-muted">Search (title, description, subject)</label>
          <input type="text" id="searchInput" placeholder="Enter keyword...">
        </div>

        <div style="flex:1;">
          <label class="small-muted">File type</label>
          <select id="fileTypeFilter">
            <option value="">All</option>
            <option value="pdf">PDF</option>
            <option value="docx">DOCX</option>
            <option value="pptx">PPTX</option>
            <option value="jpg">JPG</option>
            <option value="png">PNG</option>
            <option value="txt">TXT</option>
          </select>
        </div>

        <div style="flex:1;">
          <label class="small-muted">From</label>
          <input type="date" id="dateFrom">
        </div>

        <div style="flex:1;">
          <label class="small-muted">To</label>
          <input type="date" id="dateTo">
        </div>

        <div style="width:110px;">
          <button id="filterBtn" class="btn-small">Filter</button>
        </div>
      </div>

      <table>
          <thead>
              <tr>
                  <th style="width:48px">#</th>
                  <th>Title</th>
                  <th>Description</th>
                  <th style="width:180px">File</th>
                  <th style="width:120px">Class Group</th>

                  <th style="width:220px">Actions</th>
              </tr>
          </thead>
          <tbody id="lessonTableBody"></tbody>
      </table>

      <div id="noResults" style="display:none; margin-top:12px; color:var(--muted);">No results found.</div>
    </section>

    <!-- Lesson Upload Panel -->
    <section class="panel">
      <h2 style="margin:0 0 10px 0; font-size:18px;">Add New Lesson</h2>
      <form id="createLessonForm" enctype="multipart/form-data">
        @csrf
        <div style="margin-bottom:12px;">
            <label>Title:</label>
            <input type="text" name="title" required placeholder="e.g. Introduction to Algorithms">
        </div>

        <div style="margin-bottom:12px;">
            <label>Description:</label>
            <textarea name="description" rows="3" placeholder="Short description (optional)"></textarea>
        </div>

<div class="mt-3">
    <label for="class_group" class="block text-sm font-medium text-gray-700">Class Group</label>
    <select name="class_group" id="class_group" class="mt-1 w-full border border-gray-300 rounded-md p-2">
        <option value="4A">4A</option>
        <option value="4B">4B</option>
        <option value="4C">4C</option>
        <option value="5A">5A</option>
        <option value="5B">5B</option>
    </select>
</div>

<div class="mt-3">
    <label for="visibility" class="block text-sm font-medium text-gray-700">Visibility</label>
    <select name="visibility" id="visibility" class="mt-1 w-full border border-gray-300 rounded-md p-2">
        <option value="class">Class Only</option>
        <option value="public">Public (All Students)</option>
    </select>
</div>

        <div style="margin-bottom:12px;">
            <label>Upload File:</label>
            <input type="file" name="file" accept=".pdf,.docx,.pptx,.txt,.jpg,.png">
        </div>

        <div style="display:flex; gap:10px; align-items:center;">
          <button type="submit" style="min-width:120px;">Upload Lesson</button>
          <div class="small-muted" style="font-size:13px;">Max file size: 10MB</div>
        </div>
      </form>
  </main>
</div>


<!-- Modal Viewer (Option A) -->
<div id="modalBackdrop" class="modal-backdrop" role="dialog" aria-hidden="true">
  <div class="modal" role="document">
    <header>
      <div>
        <div id="modalTitle" style="font-weight:700;color:inherit;">Preview</div>
        <div id="modalInfo" class="info" style="margin-top:6px;color:var(--muted);font-size:13px;"></div>
      </div>
      <div style="display:flex; gap:8px; align-items:center;">
        <button id="modalDownloadBtn" class="button-outline btn-small" style="padding:8px 10px;">Download</button>
        <button id="modalCloseBtn" class="button-outline btn-small" style="padding:8px 10px;">✕</button>
      </div>
    </header>
    <div class="content" style="background:#fff;">
      <iframe id="previewFrame" src="" frameborder="0"></iframe>
    </div>
  </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const body=document.body, toggle=document.getElementById('themeToggle');
function applyTheme(mode){
  if(mode==='light'){body.classList.replace('dark','light');toggle.textContent='☀️';}
  else{body.classList.replace('light','dark');toggle.textContent='🌙';}
}
const saved=localStorage.getItem('theme')||'dark'; applyTheme(saved);
toggle.addEventListener('click',()=>{const next=body.classList.contains('dark')?'light':'dark'; applyTheme(next); localStorage.setItem('theme',next);});

function buildQuery(params) {
    return Object.keys(params).filter(k => params[k]).map(k => `${encodeURIComponent(k)}=${encodeURIComponent(params[k])}`).join('&');
}

async function loadLessons() {
    const q = document.getElementById('searchInput').value.trim();
    const file_type = document.getElementById('fileTypeFilter').value;
    const date_from = document.getElementById('dateFrom').value;
    const date_to = document.getElementById('dateTo').value;

    const qs = buildQuery({ q, file_type, date_from, date_to });
    const url = '/api/lessons' + (qs ? ('?' + qs) : '');

    const response = await fetch(url);
    if (!response.ok) {
        alert('Failed to load lessons.');
        return;
    }
    const lessons = await response.json();
    const tableBody = document.getElementById('lessonTableBody');
    tableBody.innerHTML = '';

    if (!lessons || lessons.length === 0) {
        document.getElementById('noResults').style.display = 'block';
        return;
    } else {
        document.getElementById('noResults').style.display = 'none';
    }

    lessons.forEach((lesson, index) => {
    const fileLabel = lesson.file_path ? `${lesson.file_name}` : 'No file';
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${index + 1}</td>
        <td><input type="text" id="title-${lesson.id}" value="${escapeHtml(lesson.title)}"></td>
        <td><textarea id="desc-${lesson.id}">${escapeHtml(lesson.description || '')}</textarea></td>
        <td>
            ${lesson.file_path ? `<div class="file-meta">
                <div style="font-weight:700">${escapeHtml(fileLabel)}</div>
                <div class="small-muted" style="font-size:12px">${lesson.file_ext ? lesson.file_ext.toUpperCase() : ''} • ${lesson.created_at ? new Date(lesson.created_at).toLocaleString() : ''}</div>
            </div>` : 'No file'}
            <input type="file" id="file-${lesson.id}" accept=".pdf,.docx,.pptx,.txt,.jpg,.png" style="margin-top:6px;">
        </td>
        <td class="actions">
            <button onclick="updateLesson(${lesson.id})" class="btn-small">Edit</button>
            <button onclick="deleteLesson(${lesson.id})" class="btn-small danger">Delete</button>
            ${lesson.file_path ? `<button onclick="viewLesson(${lesson.id})" class="btn-small button-outline" style="margin-left:6px;">View</button>
            <button onclick="downloadLesson(${lesson.id}, this)" class="btn-small button-outline" style="margin-left:6px;">Download</button>` : ''}
        </td>
    `;
    tableBody.appendChild(row);
});

}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/[&<>"'\/]/g, function (s) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;' };
        return map[s];
    });
}

document.getElementById('createLessonForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    try {
        const response = await fetch('/api/lessons', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        });
        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Unknown error');
        alert('Lesson created successfully!');
        this.reset();
        loadLessons();
    } catch(err){
        console.error(err);
        alert('Unexpected error: ' + err.message);
    }
});

async function updateLesson(id) {
    const formData = new FormData();
    formData.append('title', document.getElementById(`title-${id}`).value);
    formData.append('description', document.getElementById(`desc-${id}`).value);
    formData.append('_method', 'PUT');

    const fileInput = document.getElementById(`file-${id}`);
    if (fileInput && fileInput.files[0]) {
        formData.append('file', fileInput.files[0]);
    }

    try {
        const response = await fetch(`/api/lessons/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        });
        const data = await response.json();
        if (!data.success) throw new Error(data.message || 'Unknown error');
        alert(data.message || 'Lesson updated successfully!');
        loadLessons();
    } catch(err){
        console.error(err);
        alert('Unexpected error: ' + err.message);
    }
}

async function deleteLesson(id) {
    if (!confirm('Are you sure you want to delete this lesson?')) return;
    const formData = new FormData();
    try {
        const response = await fetch(`/api/lessons/${id}/delete`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        });
        const data = await response.json();
        alert(data.message || 'Lesson deleted');
        loadLessons();
    } catch(err){
        console.error(err);
        alert('Unexpected error: ' + err.message);
    }
}

document.getElementById('filterBtn').addEventListener('click', () => loadLessons());
document.getElementById('searchInput').addEventListener('keydown', (e) => { if (e.key === 'Enter') loadLessons(); });

const modalBackdrop = document.getElementById('modalBackdrop');
const previewFrame = document.getElementById('previewFrame');
const modalTitle = document.getElementById('modalTitle');
const modalInfo = document.getElementById('modalInfo');
const modalCloseBtn = document.getElementById('modalCloseBtn');
const modalDownloadBtn = document.getElementById('modalDownloadBtn');

modalCloseBtn.addEventListener('click', closeModal);
modalBackdrop.addEventListener('click', (e) => { if (e.target === modalBackdrop) closeModal(); });

function openModal() {
    modalBackdrop.style.display = 'flex';
    modalBackdrop.setAttribute('aria-hidden', 'false');
}
function closeModal() {
    previewFrame.src = '';
    modalBackdrop.style.display = 'none';
    modalBackdrop.setAttribute('aria-hidden', 'true');
    modalTitle.textContent = 'Preview';
    modalInfo.textContent = '';
}

function isPreviewable(ext, mime) {
    const previewableExts = ['pdf', 'jpg', 'jpeg', 'png', 'txt'];
    if (!ext) return false;
    ext = ext.toLowerCase();
    if (previewableExts.includes(ext)) return true;
    if (mime && mime.startsWith('image/')) return true;
    if (mime === 'application/pdf') return true;
    return false;
}

async function viewLesson(id) {
    try {
        const res = await fetch(`/api/lessons/${id}/preview`);
        const data = await res.json();
        if (!res.ok || !data.success) {
            alert(data.message || 'Cannot preview this file.');
            return;
        }

        const { url, mime, file_name, file_ext, lesson } = data;

        modalTitle.textContent = lesson.title || file_name || 'Preview';
        modalInfo.textContent = `${file_name} • ${mime || (file_ext?file_ext.toUpperCase():'')}`;

        modalDownloadBtn.onclick = () => downloadLesson(id, modalDownloadBtn);

        if (isPreviewable(file_ext, mime)) {
            previewFrame.src = url;
        } else {
            previewFrame.src = 'about:blank';
            if (confirm('This file cannot be previewed in the browser. Would you like to download it instead?')) {
                downloadLesson(id, modalDownloadBtn);
                return;
            } else {
                return;
            }
        }

        openModal();
    } catch (err) {
        console.error(err);
        alert('Error while trying to preview file.');
    }
}

function downloadLesson(id, button) {
    button = button || null;
    const originalText = button ? button.textContent : null;
    if (button) { button.textContent = 'Downloading...'; button.disabled = true; }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `/lessons/download/${id}`, true);
    xhr.responseType = 'blob';
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

    let progressBarWrapper = null;
    let progressBar = null;
    if (button) {
        progressBarWrapper = document.createElement('div');
        progressBarWrapper.style.width = '100%';
        progressBarWrapper.style.maxWidth = '220px';
        progressBarWrapper.style.marginTop = '6px';
        const wrapperInner = document.createElement('div');
        wrapperInner.className = 'progress-bar';
        progressBar = document.createElement('span');
        progressBar.style.width = '0%';
        wrapperInner.appendChild(progressBar);
        progressBarWrapper.appendChild(wrapperInner);
        button.parentNode.insertBefore(progressBarWrapper, button.nextSibling);
    }

    xhr.onprogress = function(e) {
        if (!e.lengthComputable) return;
        const percent = Math.round((e.loaded / e.total) * 100);
        if (progressBar) progressBar.style.width = percent + '%';
    };

    xhr.onload = function() {
        if (xhr.status === 200) {
            const disposition = xhr.getResponseHeader('Content-Disposition') || '';
            let filename = 'download';
            const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
            if (matches != null && matches[1]) {
                filename = matches[1].replace(/['"]/g, '');
            } else {
            }

            (async () => {
                try {
                    if (!filename || filename === 'download') {
                        const p = await fetch(`/api/lessons/${id}/preview`);
                        if (p.ok) {
                            const pd = await p.json();
                            if (pd && pd.file_name) filename = pd.file_name;
                        }
                    }
                } catch(e) { }

                const blob = xhr.response;
                const link = document.createElement('a');
                const url = window.URL.createObjectURL(blob);
                link.href = url;
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);

                if (button) {
                    button.textContent = 'Downloaded';
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.disabled = false;
                        if (progressBarWrapper) progressBarWrapper.remove();
                    }, 1200);
                } else {
                    alert('Download complete');
                }
            })();
        } else {
            alert('Download failed (server error).');
            if (button) { button.textContent = originalText; button.disabled = false; if (progressBarWrapper) progressBarWrapper.remove(); }
        }
    };

    xhr.onerror = function() {
        alert('Download failed (network error).');
        if (button) { button.textContent = originalText; button.disabled = false; if (progressBarWrapper) progressBarWrapper.remove(); }
    };

    xhr.send();
}

loadLessons();

</script>

@endsection
