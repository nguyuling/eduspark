<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>EduSpark • Create Lesson</title>

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Font & Tailwind (from your app.css) -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/app.css') }}" rel="stylesheet">

<style>
/* ---------- Theme variables (kept from original) ---------- */
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

.app { display:flex; min-height:100vh; gap:28px; padding:28px; font-family: Inter, system-ui, sans-serif; }

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

.panel {
  border-radius:var(--card-radius); padding:14px; animation: fadeInUp .4s ease; margin-bottom:20px;
  background: transparent;
  border: 1px solid var(--control-border);
  backdrop-filter: blur(6px);
  box-shadow: 0 2px 12px rgba(2,6,23,0.18);
}
body.light .panel { background: rgba(255,255,255,0.96); }
body.dark .panel  { background:#0f1724; }

input[type="text"],
input[type="date"],
textarea,
select,
input[type="file"] {
  width:100%;
  padding:11px 14px;
  border-radius:8px;
  border:1px solid var(--control-border);
  background: var(--input-bg);
  color: inherit;
  font-size:14px;
  outline: none;
  transition: box-shadow .12s ease, border-color .12s ease, transform .06s ease;
  resize: vertical;
  box-sizing: border-box;
}

textarea { min-height:84px; line-height:1.45; }

input[type="file"] {
  padding:8px 12px;
  border-radius:8px;
}

input[type="text"]:focus,
textarea:focus,
select:focus,
input[type="date"]:focus,
input[type="file"]:focus {
  box-shadow: var(--focus-glow);
  border-color: var(--accent);
  transform: translateY(-1px);
}

::placeholder { color: rgba(255,255,255,0.45); }

label { font-size:13px; color:var(--muted); font-weight:600; display:block; margin-bottom:6px; }

.small-muted { color:var(--muted); font-size:13px; }

button {
  cursor:pointer;
  padding:8px 12px;
  border-radius:10px;
  border:none;
  background: linear-gradient(90deg,var(--accent),var(--accent-2));
  color:#fff;
  font-weight:700;
  font-size:14px;
  transition: transform .08s ease, box-shadow .12s ease, opacity .12s ease;
  box-shadow: 0 6px 18px rgba(8,12,32,0.25);
}

button[style*="background:transparent"],
.button-outline {
  background: transparent !important;
  color: inherit;
  border: 1px solid rgba(255,255,255,0.06);
  box-shadow: none;
}

button.danger {
  background: var(--danger);
  box-shadow: none;
}

button:hover { transform: translateY(-3px); opacity:0.98; }
button:active { transform: translateY(-1px); }

.btn-small { padding:6px 10px; font-size:13px; border-radius:8px; }

@keyframes fadeInUp { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:none;} }

.hidden { display:none; }
</style>
</head>

<body class="dark">
<div class="app">
  <!-- Sidebar -->
  <aside class="sidebar">
    <img src="{{ asset('logo.png') }}" alt="EduSpark logo" class="logo">
    <div class="logo-text" aria-hidden="true" style="font-weight:700;font-size:18px;">
      <span style="color:#1D5DCD;">edu</span><span style="color:#E63946;">Spark</span>
    </div>
    <nav class="nav">
      <a href="{{ route('lesson.index') }}" class="active">Lessons</a>
      <a href="#">Materials</a>
      <a href="#">Assessments</a>
      <a href="#">Forum</a>
      <a href="#">Games</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main" style="flex:1;">
    <div class="header" style="display:flex;justify-content:space-between;align-items:center; margin-bottom:20px;">
      <div>
        <div class="title" style="font-weight:700;font-size:20px;">Create Lesson</div>
        <div class="sub" style="color:var(--muted);font-size:13px;">Add a new lesson material for your class</div>
      </div>
      <button id="themeToggle" style="background:none;border:0;color:inherit;font-weight:600;cursor:pointer;">🌙</button>
    </div>

    <!-- Add New Lesson Panel -->
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

        <div style="margin-bottom:12px;">
            <label for="class_group" class="block text-sm font-medium">Class Group</label>
            <select name="class_group" id="class_group">
                <option value="4A">4A</option>
                <option value="4B">4B</option>
                <option value="4C">4C</option>
                <option value="5A">5A</option>
                <option value="5B">5B</option>
            </select>
        </div>

        <div style="margin-bottom:12px;">
            <label for="visibility" class="block text-sm font-medium">Visibility</label>
            <select name="visibility" id="visibility">
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
    </section>
  </main>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Theme toggle
const body=document.body, toggle=document.getElementById('themeToggle');
function applyTheme(mode){
  if(mode==='light'){body.classList.replace('dark','light');toggle.textContent='☀️';}
  else{body.classList.replace('light','dark');toggle.textContent='🌙';}
}
const saved=localStorage.getItem('theme')||'dark'; applyTheme(saved);
toggle.addEventListener('click',()=>{const next=body.classList.contains('dark')?'light':'dark'; applyTheme(next); localStorage.setItem('theme',next);});

// Create lesson
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
        window.location.href = '{{ route("lesson.index") }}';
    } catch(err){
        console.error(err);
        alert('Unexpected error: ' + err.message);
    }
});
</script>
</body>
</html>
