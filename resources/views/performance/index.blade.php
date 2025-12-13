@extends('layouts.app')

@section('content')

<style>
.app { display:flex; min-height:80vh; gap:28px; padding:28px; font-family: Inter, system-ui, sans-serif; margin-left:268px; }

/* Cards → FIXED 2x2 layout */
.cards {
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* ALWAYS 2 columns */
  gap: 16px;
  margin-bottom:20px;
}

@media (max-width: 700px) {
  .cards {
    grid-template-columns: 1fr; /* stack on mobile */
  }
}

.card {
  border-radius:var(--card-radius); 
  padding:14px 16px; 
  display:flex; 
  flex-direction:column;
  align-items:flex-start; 
  justify-content:center; 
  text-align:left;
  transition: border-color .2s ease, transform .12s ease, box-shadow .12s ease;
  background: transparent;
  border: 2px solid #d4c5f9;
  backdrop-filter: blur(6px);
}
body.light .card { background: rgba(255,255,255,0.96); }
body.dark .card  { background:#0f1724; }
.card:hover { border-color: var(--accent); }
.card .label { font-size:13px; color:var(--muted); font-weight:600; }
.card .value { font-weight:700; font-size:20px; margin-top:6px; }
.badge-pill {
  border-radius:999px; padding:8px 12px;
  color:white; font-weight:700; display:inline-block; font-size:16px;
}

/* Chart panel header */
.panel { border-radius:var(--card-radius); padding:20px; animation: fadeInUp .4s ease; margin-bottom:20px; background: transparent; border: 2px solid #d4c5f9; backdrop-filter: blur(6px); box-shadow: 0 2px 12px rgba(2,6,23,0.18); transition: border-color .2s ease; }
body.light .panel { background: rgba(255,255,255,0.96); }
body.dark .panel  { background:#0f1724; }

.panel:hover { border-color: var(--accent); }

.panel .chart-header { display:block; }
.panel .chart-header .heading { font-weight:700; font-size:16px; display:block; }
.panel .chart-header .subtitle { display:block; margin-top:6px; color:var(--muted); font-size:13px; }

@keyframes fadeInUp { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:none;} }

.main-header { display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:20px; margin-left:40px; margin-right:40px; margin-top:40px; }
.main-header .title { font-weight:700;font-size:28px; }
.main-header .sub { color:var(--muted);font-size:13px; margin-top:6px; }
</style>

<div class="app">
  <main class="main" style="flex:1;">
    <div class="main-header">
      <div>
        <div class="title">Prestasi</div>
        <div class="sub">Gambaran pembelajaran peribadi & aktiviti terkini</div>
      </div>
    </div>

    <!-- Cards (4 cards in 2x2 grid) -->
    <section class="cards" style="margin-left:40px; margin-right:40px;">

      <div class="card">
        <div class="label">Purata Skor Kuiz</div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2));">
            {{ $avgQuizScore }}%
          </span>
        </div>
      </div>

      <div class="card">
        <div class="label">Purata Skor Permainan</div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--yellow),var(--accent));">
            {{ $avgGameScore }}%
          </span>
        </div>
      </div>

      <div class="card">
        <div class="label">Topik Paling Lemah</div>
        <div class="value">
          <span class="badge-pill" style="background:var(--danger);">
            {{ $weakTopic ?? 'Tiada' }}
          </span>
        </div>
      </div>

      <div class="card">
        <div class="label">Kuiz Diselesaikan</div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,#2A9D8F,#4CAF50);">
            {{ $totalQuizzes ?? 0 }}
          </span>
        </div>
      </div>

    </section>

    <!-- Chart -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-bottom:20px;">
      <div class="chart-header">
        <span class="heading">Trend Prestasi</span>
        <span class="subtitle">Kuiz & permainan terkini</span>
      </div>

      <canvas id="trendChart" style="margin-top:14px;max-height:260px;"></canvas>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trendChart');
const gr = ctx.getContext('2d').createLinearGradient(0,0,0,200);
gr.addColorStop(0,'rgba(106,77,247,0.22)');
gr.addColorStop(1,'rgba(156,123,255,0.06)');

new Chart(ctx,{
  type:'line',
  data:{
    labels:{!! json_encode($labels) !!},
    datasets:[{
      label:'Skor',
      data:{!! json_encode($scores) !!},
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

@endsection
