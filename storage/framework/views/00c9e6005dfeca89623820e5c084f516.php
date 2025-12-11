<!doctype html>
<html lang="ms">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pelajar • Prestasi</title>

<!-- Font & Chart.js -->
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
* { box-sizing:border-box; transition: all .2s ease; }
html, body {
  height:100%; margin:0;
  font-family:'Inter', system-ui, sans-serif;
  overflow-x:hidden;
}

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
.logo { width:110px; height:auto; margin-bottom:6px; animation: fadeInDown 1s ease; }

/* Sidebar nav */
.nav { width:100%; margin-top:10px; }
.nav a {
  display:flex; align-items:center; gap:10px;
  padding:10px 12px; border-radius:12px;
  color:var(--muted); text-decoration:none; font-weight:600;
  margin:4px 0; position:relative;
}
.nav a::before {
  content:''; position:absolute; left:0; width:4px; height:100%;
  background:var(--accent); border-radius:12px; transform:scaleY(0);
  transition:transform .2s ease;
}
.nav a:hover::before { transform:scaleY(1); }
.nav a.active {
  background:linear-gradient(90deg,rgba(106,77,247,0.16),rgba(156,123,255,0.08));
  color:var(--accent);
  box-shadow:0 8px 24px rgba(106,77,247,0.08);
  transform:translateY(-2px);
}

/* Hover animation */
.card:hover {
  transform:translateY(-8px) scale(1.02);
  box-shadow:0 20px 40px rgba(8,12,20,0.2);
}

/* Cards → FIXED 2x2 layout */
.cards {
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* ALWAYS 2 columns */
  gap: 18px;
  margin-top: 18px;
}

@media (max-width: 700px) {
  .cards {
    grid-template-columns: 1fr; /* stack on mobile */
  }
}

.card {
  border-radius:14px; 
  padding:16px; 
  display:flex; 
  flex-direction:column;
  align-items:center; 
  justify-content:center; 
  text-align:center;
  transition: transform .18s ease, box-shadow .18s ease;
}
body.light .card { background:var(--card-light); border:1px solid rgba(11,18,32,0.04); }
body.dark .card  { background:var(--card-dark); border:1px solid rgba(255,255,255,0.03); }
.card .label { font-size:13px; color:var(--muted); font-weight:600; }
.card .value { font-weight:700; font-size:22px; margin-top:6px; }
.badge-pill {
  border-radius:999px; padding:8px 12px;
  color:white; font-weight:700; display:inline-block; font-size:16px;
}

/* Page header */
.header { margin-bottom:6px; }
.title { font-weight:700;font-size:20px; }
.sub { color:var(--muted);font-size:13px; margin-top:6px; }

/* Chart panel header */
.panel .chart-header { display:block; }
.panel .chart-header .heading { font-weight:700; font-size:16px; display:block; }
.panel .chart-header .subtitle { display:block; margin-top:6px; color:var(--muted); font-size:13px; }

.panel {
  border-radius:14px; padding:14px; animation: fadeInUp .5s ease; margin-top:20px;
}
body.light .panel { background:rgba(255,255,255,0.95); }
body.dark .panel  { background:#0f1724; }

@keyframes fadeInUp { from{opacity:0; transform:translateY(20px);} to{opacity:1; transform:none;} }
@keyframes fadeInDown { from{opacity:0; transform:translateY(-20px);} to{opacity:1; transform:none;} }

@media(max-width:920px){
  .sidebar{display:none;}
  .app{padding:14px;}
}
</style>
</head>

<body class="dark">
<div class="app">

  <!-- Sidebar -->
  <aside class="sidebar">
    <img src="<?php echo e(asset('logo.png')); ?>" alt="Pelajar logo" class="logo">
    <div class="logo-text" aria-hidden="true" style="font-weight:700;font-size:18px;">
      <span style="color:#1D5DCD;">Pel</span><span style="color:#E63946;">ajar</span>
    </div>
    <nav class="nav">
      <a href="#" class="active">Papan Pemuka</a>
      <a href="#">Bahan</a>
      <a href="#">Penilaian</a>
      <a href="#">Forum</a>
      <a href="#">Permainan</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main" style="flex:1;">
    <div class="header" style="display:flex;justify-content:space-between;align-items:center;">
      <div>
        <div class="title">Prestasi</div>
        <div class="sub">Gambaran pembelajaran peribadi & aktiviti terkini</div>
      </div>
      <button id="themeToggle" style="background:none;border:0;color:inherit;font-weight:600;cursor:pointer;">🌙</button>
    </div>

    <!-- Cards (4 cards in 2x2 grid) -->
    <section class="cards">

      <div class="card">
        <div class="label">Purata Skor Kuiz</div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2));">
            <?php echo e($avgQuizScore); ?>%
          </span>
        </div>
      </div>

      <div class="card">
        <div class="label">Purata Skor Permainan</div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--yellow),var(--accent));">
            <?php echo e($avgGameScore); ?>%
          </span>
        </div>
      </div>

      <div class="card">
        <div class="label">Topik Paling Lemah</div>
        <div class="value">
          <span class="badge-pill" style="background:var(--danger);">
            <?php echo e($weakTopic ?? 'Tiada'); ?>

          </span>
        </div>
      </div>

      <div class="card">
        <div class="label">Kuiz Diselesaikan</div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,#2A9D8F,#4CAF50);">
            <?php echo e($totalQuizzes ?? 0); ?>

          </span>
        </div>
      </div>

    </section>

    <!-- Chart -->
    <section class="panel card">
      <div class="chart-header">
        <span class="heading">Trend Prestasi</span>
        <span class="subtitle">Kuiz & permainan terkini</span>
      </div>

      <canvas id="trendChart" style="margin-top:14px;max-height:260px;"></canvas>
    </section>
  </main>
</div>

<!-- JS -->
<script>
const body=document.body, toggle=document.getElementById('themeToggle');
function applyTheme(mode){
  if(mode==='light'){body.classList.replace('dark','light');toggle.textContent='☀️';}
  else{body.classList.replace('light','dark');toggle.textContent='🌙';}
}
applyTheme(localStorage.getItem('theme')||'dark');
toggle.addEventListener('click',()=>{
  const next=body.classList.contains('dark')?'light':'dark';
  applyTheme(next); localStorage.setItem('theme',next);
});

const ctx = document.getElementById('trendChart');
const gr = ctx.getContext('2d').createLinearGradient(0,0,0,200);
gr.addColorStop(0,'rgba(106,77,247,0.22)');
gr.addColorStop(1,'rgba(156,123,255,0.06)');

new Chart(ctx,{
  type:'line',
  data:{
    labels:<?php echo json_encode($labels); ?>,
    datasets:[{
      label:'Skor',
      data:<?php echo json_encode($scores); ?>,
      borderColor:'#6A4DF7',
      backgroundColor:gr,
      tension:.38,
      fill:true,
      pointRadius:6,
      pointBackgroundColor:'#fff',
      pointBorderColor:'#6A4DF7'
    }]
  },
  options:{
    plugins:{legend:{display:false}},
    scales:{y:{beginAtZero:true,max:100}}
  }
});
</script>

</body>
</html>
<?php /**PATH C:\xampp\php\myapp\resources\views/performance/index.blade.php ENDPATH**/ ?>