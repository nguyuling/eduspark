<style>
.sidebar{
  width:240px; border-radius:14px; padding:18px;
  display:flex; flex-direction:column; align-items:flex-start; gap:12px;
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
.logo { width:110px; height:auto; margin:0 auto 6px; }

.nav { width:100%; margin-top:10px; padding-top:6px; padding-left:6px; position:relative; }
.nav a {
  display:block; padding:12px 16px; padding-left:28px; border-radius:12px;
  color:var(--muted); text-decoration:none; font-weight:600;
  margin:8px 0; position:relative; font-size:16px;
  transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
  width:calc(100% - 32px);
  overflow: hidden;
}
.nav a:hover { color:var(--accent) !important; background:rgba(106,77,247,0.15) !important; }
.nav a.active { color:var(--accent); }

/* floating indicator pill */
.nav-indicator{
  display:none;
}
.nav-icon { width:20px; height:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.nav-icon svg { width:100%; height:100%; stroke:currentColor; fill:none; stroke-width:2; }

.profile-section { width:100%; margin-top:auto; padding-top:12px; border-top:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; gap:10px; }
.profile-link { display:flex; align-items:center; gap:10px; padding:10px; border-radius:10px; color:var(--muted); text-decoration:none; font-weight:600; font-size:14px; transition:all .2s ease; width:100%; }
.profile-link:hover { color:var(--accent); background:rgba(106,77,247,0.1); }
.profile-icon { width:20px; height:20px; display:flex; align-items:center; justify-content:center; background:currentColor; border-radius:50%; color:#fff; font-weight:700; font-size:11px; flex-shrink:0; }

@media (max-width:920px){ .sidebar{ display:none; } }
</style>

<aside class="sidebar">
  <img src="{{ asset('logo.png') }}" alt="EduSpark logo" class="logo">
  @php
    $isTeacher = auth()->check() && (auth()->user()->role ?? null) === 'teacher';
    if (! $isTeacher && request()->is('reports*')) {
      $isTeacher = true;
    }
  @endphp

  <div class="logo-text" aria-hidden="true" style="font-weight:700;font-size:18px;">
    @if($isTeacher)
      <span style="color:#1D5DCD;">Pen</span><span style="color:#E63946;">gajar</span>
    @else
      <span style="color:#1D5DCD;">Pel</span><span style="color:#E63946;">ajar</span>
    @endif
  </div>
  <nav class="nav">
    @if($isTeacher)
      <a href="{{ Route::has('reports.index') ? route('reports.index') : url('/reports') }}" class="{{ request()->is('reports*') ? 'active' : '' }}">Laporan</a>
      <a href="{{ Route::has('lessons.index') ? route('lessons.index') : url('/lessons') }}" class="{{ request()->is('lessons*') ? 'active' : '' }}">Bahan</a>
      <a href="{{ Route::has('teacher.quizzes.index') ? route('teacher.quizzes.index') : url('/teacher/quizzes') }}" class="{{ request()->is('teacher/quizzes*') ? 'active' : '' }}">Kuiz</a>
      <a href="{{ Route::has('forum.index') ? route('forum.index') : url('/forum') }}" class="{{ request()->is('forum*') ? 'active' : '' }}">Forum</a>
      <a href="{{ Route::has('games.index') ? route('games.index') : url('/games') }}" class="{{ request()->is('games*') ? 'active' : '' }}">Permainan</a>
    @else
      <a href="{{ Route::has('performance.student_view') ? route('performance.student_view') : url('/performance/student') }}" class="{{ request()->is('performance*') ? 'active' : '' }}">Prestasi</a>
      <a href="{{ Route::has('lessons.index') ? route('lessons.index') : url('/lessons') }}" class="{{ request()->is('lessons*') ? 'active' : '' }}">Bahan</a>
      <a href="{{ Route::has('student.quizzes.index') ? route('student.quizzes.index') : url('/quizzes') }}" class="{{ request()->is('quizzes*') ? 'active' : '' }}">Kuiz</a>
      <a href="{{ Route::has('forum.index') ? route('forum.index') : url('/forum') }}" class="{{ request()->is('forum*') ? 'active' : '' }}">Forum</a>
      <a href="{{ Route::has('games.index') ? route('games.index') : url('/games') }}" class="{{ request()->is('games*') ? 'active' : '' }}">Permainan</a>
    @endif
  </nav>
  <div class="profile-section">
    @auth
      <a href="{{ route('profile.show') }}" class="profile-link">
        <div class="profile-icon">{{ substr(Auth::user()->name, 0, 1) }}</div>
        <div style="flex:1; text-align:left; overflow:hidden;">
          <div style="font-size:13px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ Auth::user()->name }}</div>
          <div style="font-size:11px; color:var(--muted); text-transform:capitalize;">{{ Auth::user()->role }}</div>
        </div>
      </a>
    @else
      <a href="{{ url('/login') }}" class="profile-link" style="justify-content:center;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;">
        Log in
      </a>
    @endauth

    <button id="themeToggle" style="background:none;border:0;color:inherit;font-size:18px;cursor:pointer;padding:8px;border-radius:8px;transition:all .2s ease;">☀️</button>
  </div>
</aside>
<script>
  (function(){
    const nav = document.querySelector('.nav');
    if(!nav) return;
    // create indicator
    let indicator = nav.querySelector('.nav-indicator');
    if(!indicator){
      indicator = document.createElement('div');
      indicator.className = 'nav-indicator';
      nav.insertBefore(indicator, nav.firstChild);
    }

    const links = Array.from(nav.querySelectorAll('a'));
    function moveTo(el){
      if(!el) return;
      const top = el.offsetTop;
      const height = el.offsetHeight;
      indicator.style.top = top + 'px';
      indicator.style.height = height + 'px';
      indicator.style.opacity = '1';
    }

    links.forEach(link => {
      link.addEventListener('mouseenter', () => moveTo(link));
      link.addEventListener('focus', () => moveTo(link));
      link.addEventListener('click', () => moveTo(link));
    });

    nav.addEventListener('mouseleave', () => {
      const active = nav.querySelector('a.active') || links[0];
      if(active) moveTo(active); else indicator.style.opacity = '0';
    });

    // initial position on load
    window.requestAnimationFrame(()=>{
      const active = nav.querySelector('a.active') || links[0];
      if(active) moveTo(active);
    });
  })();
</script>
