<!doctype html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">

<title><?php echo $__env->yieldContent('title', (View::hasSection('page_title') ? trim(View::getSection('page_title')) : 'Pengajar')); ?></title>

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
.app { display:flex; min-height:100vh; gap:28px; padding:28px; }

/* Sidebar */
.sidebar {
  width:240px; border-radius:16px; padding:18px;
  display:flex; flex-direction:column; align-items:center; gap:12px;
  backdrop-filter:blur(8px) saturate(120%);
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

/* Responsive */
@media(max-width:920px){
  .sidebar { display:none; }
  .app { padding:14px; }
}
</style>
</head>

<body class="light">
<div class="app">

  
  <aside class="sidebar">
    <img src="<?php echo e(asset('logo.png')); ?>" alt="Pengajar logo" class="logo">
    <div class="brand">
      <span style="color:#1D5DCD;">Pen</span><span style="color:#E63946;">gajar</span>
    </div>

    <nav class="nav">
      <a href="<?php echo e(Route::has('reports.index') ? route('reports.index') : url('/reports')); ?>"
         class="<?php echo e(request()->is('reports*') ? 'active' : ''); ?>">
         Laporan
      </a>

      <a href="<?php echo e(Route::has('materials.index') ? route('materials.index') : url('/materials')); ?>"
         class="<?php echo e(request()->is('materials*') ? 'active' : ''); ?>">
         Bahan
      </a>

      <a href="<?php echo e(Route::has('assessments.index') ? route('assessments.index') : url('/assessments')); ?>"
         class="<?php echo e(request()->is('assessments*') ? 'active' : ''); ?>">
         Penilaian
      </a>

      <a href="<?php echo e(Route::has('forum.index') ? route('forum.index') : url('/forum')); ?>"
         class="<?php echo e(request()->is('forum*') ? 'active' : ''); ?>">
         Forum
      </a>

      <a href="<?php echo e(Route::has('games.index') ? route('games.index') : url('/games')); ?>"
         class="<?php echo e(request()->is('games*') ? 'active' : ''); ?>">
         Permainan
      </a>
    </nav>
  </aside>

  
  <main style="flex:1;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
      <div>
        <h1 class="title"><?php echo $__env->yieldContent('page_title','Pengajar'); ?></h1>
        <div class="sub"><?php echo $__env->yieldContent('page_sub',''); ?></div>
      </div>

      <button id="themeToggle" style="background:none;border:0;color:inherit;font-size:18px;cursor:pointer;">
        ☀️
      </button>
    </div>

    <?php echo $__env->yieldContent('content'); ?>
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

<?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\php\myapp\resources\views/layouts/app.blade.php ENDPATH**/ ?>