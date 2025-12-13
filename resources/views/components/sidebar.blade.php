<style>
.sidebar{
  width:240px; border-radius:14px; padding:18px;
  padding-top:40px;
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
.logo { width:200px; height:auto; margin:0 auto -4px; }

.nav { width:100%; margin-top:14px; padding-top:6px; padding-left:6px; position:relative; }
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
  position:absolute;
  left:6px;
  width:4px;
  background:var(--accent);
  border-radius:2px;
  transition:all .3s cubic-bezier(0.4, 0, 0.2, 1);
  opacity:0;
}
.nav-icon { width:20px; height:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.nav-icon svg { width:100%; height:100%; stroke:currentColor; fill:none; stroke-width:2; }

.profile-icon { display:none; }

#themeToggle { 
  position: relative; 
  z-index: 101 !important;
  visibility: visible !important;
  display: flex !important;
  opacity: 1 !important;
}

/* Theme Toggle Switch Styling */
#themeToggle {
  width: 80px;
  height: 36px;
  background: rgba(106, 77, 247, 0.3);
  border: 1px solid rgba(106, 77, 247, 0.5);
  border-radius: 999px;
  cursor: pointer;
  transition: all .3s ease;
  display: flex !important;
  align-items: center;
  justify-content: space-between;
  padding: 2px 8px;
  position: relative;
  font-size: 16px;
  margin-top: auto !important;
  align-self: flex-end !important;
}

#themeToggle::before {
  content: '☀';
  font-size: 18px;
  z-index: 1;
  transition: opacity .3s ease;
  color: #FDB813;
  text-shadow: 0 0 2px #000, 0 0 4px rgba(0,0,0,0.5);
  filter: drop-shadow(0 0 1px #000);
}

#themeToggle::after {
  content: '☽';
  font-size: 18px;
  z-index: 1;
  transition: opacity .3s ease;
  color: #f5f5f5;
  text-shadow: 0 0 2px #000, 0 0 4px rgba(0,0,0,0.5);
  filter: drop-shadow(0 0 1px #000);
}

/* Sliding indicator circle */
#themeToggle .toggle-slider {
  position: absolute;
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
  border-radius: 50%;
  left: 4px;
  transition: left .4s cubic-bezier(0.34, 1.56, 0.64, 1);
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
  z-index: 0;
}

body.dark #themeToggle .toggle-slider {
  left: calc(100% - 32px);
}

body.light #themeToggle {
  background: rgba(106, 77, 247, 0.3);
  border-color: rgba(106, 77, 247, 0.5);
}

body.dark #themeToggle {
  background: rgba(106, 77, 247, 0.6);
  border-color: rgba(106, 77, 247, 0.8);
}

body.light #themeToggle::after {
  opacity: 0.4;
}

body.dark #themeToggle::before {
  opacity: 0.4;
}

.sidebar {
  visibility: visible !important;
  display: flex !important;
}

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

  <div class="logo-text" aria-hidden="true" style="font-weight:700;font-size:18px;text-align:center;width:100%;">
    @if($isTeacher)
      <span style="color:#1D5DCD;">Gu</span><span style="color:#E63946;">ru</span>
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
      <a href="{{ route('profile.show') }}" class="{{ Route::current()->getName() === 'profile.show' ? 'active' : '' }}">Profil</a>

    @else
      <a href="{{ Route::has('performance.student_view') ? route('performance.student_view') : url('/performance/student') }}" class="{{ request()->is('performance*') ? 'active' : '' }}">Prestasi</a>
      <a href="{{ Route::has('lessons.index') ? route('lessons.index') : url('/lessons') }}" class="{{ request()->is('lessons*') ? 'active' : '' }}">Bahan</a>
      <a href="{{ Route::has('student.quizzes.index') ? route('student.quizzes.index') : url('/quizzes') }}" class="{{ request()->is('quizzes*') ? 'active' : '' }}">Kuiz</a>
      <a href="{{ Route::has('forum.index') ? route('forum.index') : url('/forum') }}" class="{{ request()->is('forum*') ? 'active' : '' }}">Forum</a>
      <a href="{{ Route::has('games.index') ? route('games.index') : url('/games') }}" class="{{ request()->is('games*') ? 'active' : '' }}">Permainan</a>
      <a href="{{ route('profile.show') }}" class="{{ Route::current()->getName() === 'profile.show' ? 'active' : '' }}">Profil</a>

    @endif
  </nav>
  <button id="themeToggle"><div class="toggle-slider"></div></button>
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

    const navLinks = Array.from(nav.querySelectorAll('a'));
    
    function moveTo(el){
      if(!el) return;
      const top = el.offsetTop;
      const height = el.offsetHeight;
      indicator.style.top = top + 'px';
      indicator.style.height = height + 'px';
      indicator.style.opacity = '1';
    }

    navLinks.forEach(link => {
      link.addEventListener('mouseenter', () => moveTo(link));
      link.addEventListener('focus', () => moveTo(link));
      link.addEventListener('click', () => moveTo(link));
    });

    nav.addEventListener('mouseleave', () => {
      const active = nav.querySelector('a.active') || navLinks[0];
      if(active) moveTo(active); else indicator.style.opacity = '0';
    });

    // initial position on load
    window.requestAnimationFrame(()=>{
      const active = nav.querySelector('a.active') || navLinks[0];
      if(active) moveTo(active);
    });

    // Theme toggle functionality
    const themeToggle = document.querySelector('#themeToggle');
    if(themeToggle){
      // Initialize theme on page load
      const isDark = localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
      if(isDark){
        document.body.classList.add('dark');
      } else {
        document.body.classList.remove('dark');
      }

      // Toggle theme on click
      themeToggle.addEventListener('click', () => {
        const isDark = document.body.classList.toggle('dark');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      });
    }
  })();
</script>
