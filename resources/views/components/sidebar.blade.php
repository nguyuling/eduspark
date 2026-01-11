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
  border:2px solid rgba(255,255,255,0.03);
}
.logo { width:180px; height:auto; margin:0 auto -20px; }

.span {
    font-weight: 700;
}

.search-box {
  width: 100%;
  margin: 20px 0 0 0;
  position: relative;
  display: flex;
  align-items: center;
}

.search-box input {
  width: 100%;
  padding: 10px 12px 10px 36px;
  border-radius: 12px;
  border: none;
  background: rgb(0, 0, 0);
  color: var(--text, #333);
  font-size: 13px;
  font-family: inherit;
  transition: all .2s ease;
  outline: none;
}

body.light .search-box input {
  background: rgba(255, 255, 255, 0.95);
  border: 2px solid rgba(106, 77, 247, 0.8);
  color: #333;
}

body.dark .search-box input {
  background: rgba(20, 30, 48, 0.9);
  border: 2px solid rgba(106, 77, 247, 0.6);
  color: #e0e0e0;
}

.search-box input:focus {
  border-color: rgba(106, 77, 247, 1);
  background: rgba(255, 255, 255, 0.98);
}

body.dark .search-box input:focus {
  border-color: rgba(106, 77, 247, 0.8);
  background: rgba(20, 30, 48, 0.95);
}

.search-box input::placeholder {
  color: var(--muted);
  opacity: 0.6;
}

.search-box::before {
  content: '';
  display: none;
}

.search-box i {
  position: absolute;
  left: 10px;
  font-size: 14px;
  pointer-events: none;
  color: var(--muted);
  z-index: 2;
}

.search-results {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  margin-top: 4px;
  background: var(--card-bg, rgba(255, 255, 255, 0.95));
  border: 1px solid rgba(106, 77, 247, 0.2);
  border-radius: 12px;
  max-height: 300px;
  overflow-y: auto;
  z-index: 1000;
  display: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

body.light .search-results {
  background: rgba(255, 255, 255, 0.95);
  border-color: rgba(0, 0, 0, 0.1);
}

body.dark .search-results {
  background: rgba(15, 23, 36, 0.95);
  border-color: rgba(255, 255, 255, 0.15);
}

.search-results.active {
  display: block;
}

.search-results a {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 12px !important;
  border-bottom: 1px solid rgba(106, 77, 247, 0.1);
  color: var(--muted) !important;
  text-decoration: none;
  font-weight: 500;
  font-size: 13px !important;
  margin: 0 !important;
  width: 100% !important;
  border-radius: 0 !important;
  background: transparent !important;
  transition: all .2s ease;
  min-width: 0;
}

.search-results a:last-child {
  border-bottom: none;
}

.search-results a:hover {
  background: rgba(106, 77, 247, 0.1) !important;
  color: var(--accent) !important;
}

.search-results a.active {
  background: rgba(106, 77, 247, 0.15) !important;
  color: var(--accent) !important;
  font-weight: 600;
}

.nav { 
  width: 100%; 
  margin-top: 8px; 
  padding-left: 0; 
  position: relative;
  background: transparent;
  border-radius: 12px;
}
.nav a {
  display: flex; 
  align-items: center; 
  gap: 10px; 
  padding: 10px 12px; 
  border-radius: 12px;
  color: var(--muted); 
  text-decoration: none; 
  font-weight: 600;
  margin: 4px 0; 
  position: relative; 
  font-size: 16px;
  transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
  width: 100%;
  overflow: hidden;
  box-sizing: border-box;
}
.nav a:hover { color:var(--accent) !important; background:rgba(106,77,247,0.15) !important; }
.nav a.active { color:var(--accent); }
.nav a i {
  font-size: 16px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 16px;
}

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
.nav-icon { width:20px; height:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0;}
.nav-icon svg { width:100%; height:100%; stroke:currentColor; fill:none; stroke-width:2;}

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

.sidebar-bottom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  gap: 10px;
  margin-top: auto;
}

#helpToggle {
  position: relative;
  z-index: 101 !important;
  visibility: visible !important;
  display: flex !important;
  opacity: 1 !important;
  background: none;
  border: none;
  cursor: pointer;
  background: linear-gradient(135deg, rgba(106, 77, 247, 0.6), rgba(139, 92, 246, 0.6));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-size: 30px;
  flex-shrink: 0;
  padding: 0;
  transition: all .3s ease;
}

#helpToggle:hover {
  transform: scale(1.15);
  opacity: 0.8;
}

.help-modal {
  position: fixed;
  bottom: 0;
  left: 28px;
  width: 240px;
  max-height: 0;
  background: var(--card-bg);
  border: 2px solid rgba(106, 77, 247, 0.3);
  border-bottom: none;
  border-radius: 14px 14px 0 0;
  z-index: 101;
  overflow: hidden;
  transition: max-height .4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.15);
}

body.light .help-modal {
  background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(255,255,255,0.9));
  border-color: rgba(106, 77, 247, 0.2);
}

body.dark .help-modal {
  background: linear-gradient(180deg, rgba(20, 30, 48, 0.95), rgba(15, 23, 36, 0.9));
  border-color: rgba(106, 77, 247, 0.4);
}

.help-modal.active {
  max-height: 400px;
  overflow-y: auto;
}

.help-content {
  padding: 16px;
}

.help-title {
  font-weight: 700;
  font-size: 14px;
  margin-bottom: 12px;
  color: var(--accent);
}

.help-title a {
  color: var(--accent);
  text-decoration: none;
  transition: all .2s ease;
}

.help-title a:hover {
  opacity: 0.8;
  text-decoration: underline;
}

.faq-item {
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(106, 77, 247, 0.1);
}

.faq-item:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.faq-question {
  font-weight: 600;
  font-size: 12px;
  color: var(--muted);
  margin-bottom: 6px;
  cursor: pointer;
  transition: all .2s ease;
}

.faq-question a {
  color: var(--muted);
  text-decoration: none;
  transition: all .2s ease;
  display: block;
}

.faq-question a:hover {
  color: var(--accent);
}

.faq-answer {
  font-size: 11px;
  color: var(--muted);
  line-height: 1.4;
  opacity: 0.85;
}

@media (max-width:920px){ .sidebar{ display:none; } .help-modal { display:none; } }
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

  <!-- Search Box -->
  <div class="search-box">
    <i class="bi bi-search"></i>
    <input 
      type="text" 
      id="searchInput" 
      placeholder="Search..."
      autocomplete="off"
    />
    <div class="search-results" id="searchResults"></div>
  </div>

  <nav class="nav">
    @if($isTeacher)
      <a href="{{ Route::has('reports.index') ? route('reports.index') : url('/reports') }}" class="{{ request()->is('reports*') ? 'active' : '' }}"><i class="bi bi-bar-chart-line-fill"></i> <span>Laporan</span></a>
      <a href="{{ Route::has('lessons.index') ? route('lessons.index') : url('/lessons') }}" class="{{ request()->is('lessons*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text-fill"></i> <span>Bahan</span></a>
      <a href="{{ Route::has('teacher.quizzes.index') ? route('teacher.quizzes.index') : url('/teacher/quizzes') }}" class="{{ request()->is('teacher/quizzes*') ? 'active' : '' }}"><i class="bi bi-question-square-fill"></i> <span>Kuiz</span></a>
      <a href="{{ Route::has('forum.index') ? route('forum.index') : url('/forum') }}" class="{{ request()->is('forum*') ? 'active' : '' }}"><i class="bi bi-chat-square-text-fill"></i> <span>Forum</span></a>
      <a href="{{ url('/games') }}" class="{{ request()->is('games*') ? 'active' : '' }}"><i class="bi bi-controller"></i> <span>Permainan</span></a>
      <a href="{{ route('profile.show') }}" class="{{ Route::current()->getName() === 'profile.show' ? 'active' : '' }}"><i class="bi bi-person-lines-fill"></i> <span>Profil</span></a>

    @else
      <a href="{{ Route::has('performance') ? route('performance') : url('/performance') }}" class="{{ request()->is('performance*') ? 'active' : '' }}"><i class="bi bi-graph-up"></i> <span>Prestasi</span></a>
      <a href="{{ Route::has('lessons.index') ? route('lessons.index') : url('/lessons') }}" class="{{ request()->is('lessons*') ? 'active' : '' }}"><i class="bi bi-file-earmark-text-fill"></i> <span>Bahan</span></a>
      <a href="{{ Route::has('student.quizzes.index') ? route('student.quizzes.index') : url('/quizzes') }}" class="{{ request()->is('quizzes*') ? 'active' : '' }}"><i class="bi bi-question-square-fill"></i> <span>Kuiz</span></a>
      <a href="{{ Route::has('forum.index') ? route('forum.index') : url('/forum') }}" class="{{ request()->is('forum*') ? 'active' : '' }}"><i class="bi bi-chat-square-text-fill"></i> <span>Forum</span></a>
      <a href="{{ url('/games') }}" class="{{ request()->is('games*') ? 'active' : '' }}"><i class="bi bi-controller"></i> <span>Permainan</span></a>
      <a href="{{ route('profile.show') }}" class="{{ Route::current()->getName() === 'profile.show' ? 'active' : '' }}"><i class="bi bi-person-lines-fill"></i> <span>Profil</span></a>

    @endif
  </nav>

  <div class="sidebar-bottom">
    <i id="helpToggle" class="bi bi-info-circle-fill" title="Help & FAQ" style="cursor: pointer;"></i>
    <button id="themeToggle"><div class="toggle-slider"></div></button>
  </div>
</aside>

<!-- Help Modal -->
<div class="help-modal" id="helpModal">
  <div class="help-content">
    <div class="help-title"><a href="{{ Route::has('index') ? route('index') : url('/') }}">Bantuan</a></div>
    
    <div class="faq-item">
      <div class="faq-question"><a href="{{ Route::has('lessons.index') ? route('lessons.index') : url('/lessons') }}">Apa itu Bahan?</a></div>
      <div class="faq-answer">Bahan adalah koleksi materi pembelajaran yang disediakan untuk mendukung proses belajar mengajar.</div>
    </div>

    <div class="faq-item">
      <div class="faq-question"><a href="{{ (Route::has('student.quizzes.index') ? route('student.quizzes.index') : (Route::has('teacher.quizzes.index') ? route('teacher.quizzes.index') : url('/quizzes'))) }}">Bagaimana cara mengerjakan Kuiz?</a></div>
      <div class="faq-answer">Klik menu Kuiz, pilih topik, dan jawab semua soalan. Hasil akan ditampilkan setelah selesai.</div>
    </div>

    <div class="faq-item">
      <div class="faq-question"><a href="{{ Route::has('forum.index') ? route('forum.index') : url('/forum') }}">Bagaimana menggunakan Forum?</a></div>
      <div class="faq-answer">Forum memungkinkan Anda berdiskusi dengan pengguna lain. Klik Forum untuk membuat atau menanggapi pertanyaan.</div>
    </div>

    <div class="faq-item">
      <div class="faq-question"><a href="{{ url('/games') }}">Apa manfaat Permainan?</a></div>
      <div class="faq-answer">Permainan membantu pembelajaran menjadi lebih menyenangkan sambil menguji pengetahuan Anda.</div>
    </div>

    <div class="faq-item">
      <div class="faq-question"><a href="{{ Route::has('performance') ? route('performance') : url('/performance') }}">Bagaimana melihat Prestasi?</a></div>
      <div class="faq-answer">Prestasi menampilkan ringkasan hasil belajar Anda termasuk skor kuiz dan permainan.</div>
    </div>

    <div class="faq-item">
      <div class="faq-question"><a href="{{ route('profile.show') }}">Bagaimana mengubah Profil?</a></div>
      <div class="faq-answer">Klik menu Profil untuk melihat dan mengubah informasi akun Anda.</div>
    </div>
  </div>
</div>

<script>
  (function(){
    // Search functionality with keyword aliases
    const searchInput = document.querySelector('#searchInput');
    const searchResults = document.querySelector('#searchResults');
    const navLinks = Array.from(document.querySelectorAll('.nav a'));
    
    // Keyword mapping for better search results
    const keywordMap = {
        'statistik' : ['laporan', 'prestasi'],
        'skor' : ['laporan', 'prestasi'],
        'purata' : ['laporan', 'prestasi'],
        'gred' : ['laporan', 'prestasi'],
        'topik' : ['laporan', 'prestasi', 'bahan', 'kuiz'],
        'game' : ['permainan'],
        'soalan': ['kuiz'],
        'id': ['kuiz'],
        'kelas': ['laporan', 'bahan'],
        'cikgu': ['bahan', 'kuiz', 'permainan'],
        'guru': ['bahan', 'kuiz', 'permainan'],
        'pelajar': ['laporan', 'prestasi'],
        'materi': ['bahan'],
        'pembelajaran': ['bahan'],
        'ujian': ['kuiz'],
        'test': ['kuiz'],
        'forum': ['forum'],
        'prestasi': ['prestasi'],
        'lapor': ['laporan'],
        'permain': ['permainan'],
        'game': ['permainan'],
        'profil': ['profil'],
        'password': ['profil'],
        'akun': ['profil'],
        'nama': ['profil'],
        'kata alu-aluan': ['profil'],
        'coding': ['kuiz'],
        'koding': ['kuiz'],
        'results': ['kuiz', 'laporan', 'prestasi', 'permainan'],
        'keputusan': ['kuiz', 'laporan', 'prestasi', 'permainan'],
    };
    
    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        
        if (query === '') {
          searchResults.classList.remove('active');
          return;
        }
        
        const filtered = navLinks.filter(link => {
          const text = link.textContent.toLowerCase();
          
          // Direct text match (exact or partial)
          if (text.includes(query)) {
            return true;
          }
          
          // Check keyword aliases
          for (const [keyword, modules] of Object.entries(keywordMap)) {
            if (query.includes(keyword) || keyword.includes(query)) {
              const linkText = text.toLowerCase();
              return modules.some(module => linkText.includes(module));
            }
          }
          
          return false;
        });
        
        if (filtered.length > 0) {
          searchResults.classList.add('active');
          searchResults.innerHTML = filtered.map(link => {
            const moduleName = link.querySelector('span') ? link.querySelector('span').textContent : link.textContent;
            return `
              <a href="${link.href}" class="${link.classList.contains('active') ? 'active' : ''}">
                ${moduleName}
              </a>
            `;
          }).join('');
        } else {
          searchResults.classList.remove('active');
        }
      });
      
      searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim() !== '') {
          searchResults.classList.add('active');
        }
      });
      
      document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-box')) {
          searchResults.classList.remove('active');
        }
      });
    }

    const nav = document.querySelector('.nav');
    if(!nav) return;
    
    // create indicator
    let indicator = nav.querySelector('.nav-indicator');
    if(!indicator){
      indicator = document.createElement('div');
      indicator.className = 'nav-indicator';
      nav.insertBefore(indicator, nav.firstChild);
    }

    const allNavLinks = Array.from(nav.querySelectorAll('a'));
    
    function moveTo(el){
      if(!el) return;
      const top = el.offsetTop;
      const height = el.offsetHeight;
      indicator.style.top = top + 'px';
      indicator.style.height = height + 'px';
      indicator.style.opacity = '1';
    }

    allNavLinks.forEach(link => {
      link.addEventListener('mouseenter', () => moveTo(link));
      link.addEventListener('focus', () => moveTo(link));
      link.addEventListener('click', () => moveTo(link));
    });

    nav.addEventListener('mouseleave', () => {
      const active = nav.querySelector('a.active') || allNavLinks[0];
      if(active) moveTo(active); else indicator.style.opacity = '0';
    });

    // initial position on load
    window.requestAnimationFrame(()=>{
      const active = nav.querySelector('a.active') || allNavLinks[0];
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

    // Help modal toggle
    const helpToggle = document.querySelector('#helpToggle');
    const helpModal = document.querySelector('#helpModal');
    
    if (helpToggle && helpModal) {
      helpToggle.addEventListener('click', () => {
        helpModal.classList.toggle('active');
      });

      document.addEventListener('click', (e) => {
        if (!e.target.closest('#helpToggle') && !e.target.closest('#helpModal')) {
          helpModal.classList.remove('active');
        }
      });
    }
  })();
</script>
