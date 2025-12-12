<!doctype html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>@yield('title', (View::hasSection('page_title') ? trim(View::getSection('page_title')) : 'Pengajar'))</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
:root {
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
}

/* Reset */
* { box-sizing:border-box; transition: all .18s ease; }
html, body { height:100%; margin:0; font-family:'Inter', system-ui, sans-serif; }

/* Themes */
body.light { background:var(--bg-light); color:#0b1220; }
body.dark  { background:var(--bg-dark); color:#e6eef8; }

/* Layout */
.app { display:flex; height:100vh; gap:28px; padding:28px; overflow:hidden; }

/* Sidebar */
.sidebar {
  width:240px; border-radius:16px; padding:18px;
  display:flex; flex-direction:column; align-items:center; gap:12px;
  backdrop-filter:blur(8px) saturate(120%);
  /* sidebar is scrollable if content overflows */
  height: calc(100vh - 56px);
  max-height: calc(100vh - 56px);
  overflow-y: auto;
}
body.light .sidebar {
  background:linear-gradient(180deg,rgba(255,255,255,0.70),rgba(255,255,255,0.65));
  border:1px solid rgba(13,18,25,0.05);
}
body.dark .sidebar {
  background:linear-gradient(180deg,rgba(255,255,255,0.02),rgba(255,255,255,0.01));
  border:1px solid rgba(255,255,255,0.03);
}
.logo { width:110px; margin-bottom:6px; }

/* Brand */
.brand { font-weight:700; font-size:18px; }

/* Sidebar nav */
.nav { width:100%; margin-top:10px; text-align:center; }
.nav a {
  display:block; padding:10px 12px; border-radius:12px;
  color:var(--muted); text-decoration:none; font-weight:600; margin:4px 0;
  position:relative;
}
.nav a::before {
  content:''; position:absolute; left:8px; width:4px; top:6px; bottom:6px;
  background:var(--accent); border-radius:12px; transform:scaleY(0);
  transition:transform .18s ease;
}
.nav a:hover::before { transform:scaleY(1); }
.nav a.active {
  color:var(--accent);
  background:linear-gradient(90deg,rgba(106,77,247,0.08),rgba(156,123,255,0.02));
  font-weight:700;
}

/* Panel */
.panel {
  border-radius:14px; padding:14px;
  animation: fadeInUp .45s ease;
}
body.light .panel { background:rgba(255,255,255,0.95); }
body.dark .panel  { background:#0f1724; }

/* Cards */
.cards { display:grid; gap:16px; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); }
.card {
  border-radius:14px; padding:16px; text-align:center;
  transition: transform .18s ease, box-shadow .18s ease;
}
body.light .card { background:var(--card-light); border:1px solid rgba(0,0,0,0.06); }
body.dark  .card { background:var(--card-dark); border:1px solid rgba(255,255,255,0.03); }
.card:hover { transform:translateY(-8px) scale(1.02); box-shadow:0 20px 40px rgba(0,0,0,0.2); }

/* Card text */
.card .label { font-size:13px; color:var(--muted); font-weight:700; }
.card .value { font-size:22px; font-weight:700; margin-top:6px; }
.badge-pill { border-radius:999px; padding:8px 12px; font-weight:700; color:white; }

/* Animations */
@keyframes fadeInUp { from{opacity:0; transform:translateY(20px);} to{opacity:1; transform:none;} }

/* Header layout */
.title { font-size:22px; font-weight:800; margin:0; }
.sub { color:var(--muted); font-size:13px; }

/* Main area scroll handling + Responsive */
main { flex:1; overflow-y: auto; overflow-x: hidden; }

@media(max-width:920px){
  .sidebar { display:none; }
  .app { padding:14px; }
}
</style>
</head>

<body class="light">
<div class="app">

  {{-- Sidebar --}}
  <aside class="sidebar">
    <img src="{{ asset('logo.png') }}" alt="Logo" class="logo">
    @php
      $isTeacher = auth()->check() && (auth()->user()->role ?? null) === 'teacher';
      // Fallback: if visiting reports routes, show teacher nav even if role missing
      if (! $isTeacher && request()->is('reports*')) {
          $isTeacher = true;
      }
    @endphp

    <div class="brand">
      @if($isTeacher)
        <span style="color:#1D5DCD;">Pen</span><span style="color:#E63946;">gajar</span>
      @else
        <span style="color:#1D5DCD;">Pel</span><span style="color:#E63946;">ajar</span>
      @endif
    </div>

    <nav class="nav">
      @if($isTeacher)
        {{-- Teacher Navigation --}}
        <a href="{{ Route::has('reports.index') ? route('reports.index') : url('/reports') }}"
           class="{{ request()->is('reports*') ? 'active' : '' }}">
           Laporan
        </a>
        <a href="{{ Route::has('materials.index') ? route('materials.index') : url('/materials') }}"
           class="{{ request()->is('materials*') ? 'active' : '' }}">
           Bahan
        </a>
        <a href="{{ Route::has('assessments.index') ? route('assessments.index') : url('/assessments') }}"
           class="{{ request()->is('assessments*') ? 'active' : '' }}">
           Penilaian
        </a>
        <a href="{{ Route::has('forum.index') ? route('forum.index') : url('/forum') }}"
           class="{{ request()->is('forum*') ? 'active' : '' }}">
           Forum
        </a>
        <a href="{{ Route::has('games.index') ? route('games.index') : url('/games') }}"
           class="{{ request()->is('games*') ? 'active' : '' }}">
           Permainan
        </a>
      @else
        {{-- Student Navigation --}}
        <a href="{{ Route::has('performance.index') ? route('performance.index') : url('/performance') }}"
          class="{{ request()->is('performance*') ? 'active' : '' }}">
          Prestasi
        </a>
        <a href="{{ Route::has('materials.index') ? route('materials.index') : url('/materials') }}"
           class="{{ request()->is('materials*') ? 'active' : '' }}">
           Bahan
        </a>
        <a href="{{ Route::has('assessments.index') ? route('assessments.index') : url('/assessments') }}"
           class="{{ request()->is('assessments*') ? 'active' : '' }}">
           Penilaian
        </a>
        <a href="{{ Route::has('forum.index') ? route('forum.index') : url('/forum') }}"
           class="{{ request()->is('forum*') ? 'active' : '' }}">
           Forum
        </a>
        <a href="{{ Route::has('games.index') ? route('games.index') : url('/games') }}"
           class="{{ request()->is('games*') ? 'active' : '' }}">
           Permainan
        </a>
      @endif
    </nav>

    {{-- User button pinned to bottom of sidebar --}}
    <div style="margin-top:auto;width:100%;padding-top:12px;border-top:1px solid rgba(255,255,255,0.1);">
      <div style="display:flex;align-items:center;gap:8px;padding:12px 8px;width:100%;">
        @auth
          <a href="{{ url('/profile') }}" style="display:flex;align-items:center;text-decoration:none;color:inherit;flex:1;gap:8px;padding:8px;border-radius:12px;transition:all .2s ease;min-width:0;" onmouseover="this.style.background='rgba(106,77,247,0.08)'" onmouseout="this.style.background='transparent'">
            <img src="{{ auth()->user()->avatar ?? asset('avatar.png') }}" alt="avatar" style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
            <div style="text-align:left;flex:1;min-width:0;">
              <div style="font-weight:700;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</div>
              <div style="font-size:11px;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->email }}</div>
            </div>
          </a>
        @else
          <a href="{{ url('/login') }}" style="display:block;flex:1;padding:10px 12px;text-align:center;background:linear-gradient(90deg,var(--accent),var(--accent-2));border-radius:12px;color:#fff;text-decoration:none;font-weight:700;font-size:14px;transition:all .2s ease;box-shadow:0 4px 12px rgba(106,77,247,0.3);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 16px rgba(106,77,247,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(106,77,247,0.3)'">Log in</a>
        @endauth
        <button id="themeToggle" style="background:none;border:0;color:inherit;font-size:18px;cursor:pointer;padding:8px;border-radius:8px;transition:all .2s ease;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">☀️</button>
      </div>
    </div>
  </aside>

  {{-- Main --}}
  <main style="flex:1;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
      <div>
        <h1 class="title">@yield('page_title','Pengajar')</h1>
        <div class="sub">@yield('page_sub','')</div>
      </div>
    </div>

    @yield('content')
  </main>
</div>

<script>
(function(){
  const toggle = document.getElementById('themeToggle');
  const saved = localStorage.getItem('theme') || 'light';
  document.body.classList.add(saved);
  function applyTheme(mode){
    document.body.classList.remove('light','dark');
    document.body.classList.add(mode);
    toggle.textContent = mode === 'light' ? '☀️' : '🌙';
  }
  applyTheme(saved);
  toggle.addEventListener('click', () => {
    const next = document.body.classList.contains('dark') ? 'light' : 'dark';
    localStorage.setItem('theme', next);
    applyTheme(next);
  });
})();
</script>

@yield('scripts')
</body>
</html>
