<style>
.sidebar{
  width:240px; border-radius:14px; padding:18px;
  display:flex; flex-direction:column; align-items:center; gap:12px;
  backdrop-filter: blur(8px) saturate(120%);
  box-shadow: none;
  position: fixed;
  left: 28px;
  top: 28px;
  height: calc(100vh - 56px);
  z-index: 100;
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
  transition: all .2s ease;
}
.nav a:hover { color:var(--accent); background:rgba(106,77,247,0.05); }
.nav a.active { color:var(--accent); }
.nav-icon { width:20px; height:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.nav-icon svg { width:100%; height:100%; stroke:currentColor; fill:none; stroke-width:2; }

.profile-section { width:100%; margin-top:40px; padding-top:12px; border-top:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; gap:10px; }
.profile-link { display:flex; align-items:center; gap:10px; padding:10px; border-radius:10px; color:var(--muted); text-decoration:none; font-weight:600; font-size:14px; transition:all .2s ease; width:100%; }
.profile-link:hover { color:var(--accent); background:rgba(106,77,247,0.1); }
.profile-icon { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:currentColor; border-radius:50%; color:#fff; font-weight:700; font-size:11px; flex-shrink:0; }

@media (max-width:920px){ .sidebar{ display:none; } }
</style>

<aside class="sidebar">
  <img src="{{ asset('logo.png') }}" alt="EduSpark logo" class="logo">
  <div class="logo-text" aria-hidden="true" style="font-weight:700;font-size:18px;">
    <span style="color:#1D5DCD;">edu</span><span style="color:#E63946;">Spark</span>
  </div>
  <nav class="nav">
    <a href="{{ route('home') }}" @if(Route::currentRouteName() === 'home') class="active" @endif>
      <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M4 19h16v2H4zm0-6h16v2H4zm0-6h16v2H4zM4 1h16v2H4z"/></svg></span>Lessons
    </a>
    <a href="#">
      <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V4l8 5 8-5v2z"/></svg></span>Forum
    </a>
    <a href="#">
      <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M21 6h-2v9c0 .55-.45 1-1 1h-1c-.55 0-1-.45-1-1V4c0-1.1-.9-2-2-2h-3.5C10.88 2 10 2.88 10 4v3H5c-1.1 0-2 .9-2 2v11h2v2h2v-2h8v2h2v-2h2V9c0-.55-.45-1-1-1zm-4-2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z"/></svg></span>Games
    </a>
    <a href="{{ Auth::user()->role === 'teacher' ? route('teacher.quizzes.index') : route('student.quizzes.index') }}" @if(in_array(Route::currentRouteName(), ['teacher.quizzes.index', 'student.quizzes.index', 'student.quizzes.attempt.start', 'student.quizzes.result'])) class="active" @endif>
      <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg></span>Quiz
    </a>
    <a href="#">
      <span class="nav-icon"><svg viewBox="0 0 24 24"><path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z"/></svg></span>Performance
    </a>
  </nav>
  <div class="profile-section">
    <a href="{{ route('profile.show') }}" class="profile-link">
      <div class="profile-icon">{{ substr(Auth::user()->name, 0, 1) }}</div>
      <div style="flex:1; text-align:left; overflow:hidden;">
        <div style="font-size:13px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ Auth::user()->name }}</div>
        <div style="font-size:11px; color:var(--muted); text-transform:capitalize;">{{ Auth::user()->role }}</div>
      </div>
    </a>
  </div>
</aside>
