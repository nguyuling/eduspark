@extends('layouts.app')

@section('content')

<style>
.app { margin-left: 268px; padding: 28px; font-family: Inter, system-ui, sans-serif; }
.main { flex: 1; }

.cards {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
}

.card {
  border-radius: var(--card-radius);
  padding: 20px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: center;
  text-align: left;
  animation: fadeInUp .4s ease;
  background: transparent;
  border: 2px solid #d4c5f9;
  backdrop-filter: blur(6px);
  box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);
  transition: border-color .2s ease, transform .12s ease, box-shadow .12s ease;
}
body.light .card { background: rgba(255, 255, 255, 0.96); }
body.dark .card { background: #0f1724; }

.card:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2); }

.card .label { font-size: 12px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
.card .value { font-weight: 700; font-size: 24px; margin-top: 6px; }
.badge-pill {
  border-radius: 999px;
  padding: 8px 12px;
  color: white;
  font-weight: 700;
  display: inline-block;
  font-size: 18px;
}

.panel {
  border-radius: var(--card-radius);
  padding: 20px;
  animation: fadeInUp .4s ease;
  background: transparent;
  border: 2px solid #d4c5f9;
  backdrop-filter: blur(6px);
  box-shadow: 0 2px 12px rgba(2, 6, 23, 0.18);
  transition: border-color .2s ease, transform .12s ease, box-shadow .12s ease;
}
body.light .panel { background: rgba(255, 255, 255, 0.96); }
body.dark .panel { background: #0f1724; }

.panel:hover { border-color: var(--accent); transform: translateY(-2px); box-shadow: 0 4px 20px rgba(106, 77, 247, 0.2); }

.panel-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 0; border-bottom: none; }
.panel-header h3 { font-weight: 700; font-size: 18px; margin: 0; }
.panel-header .subtitle { display: block; margin-top: 6px; color: var(--muted); font-size: 13px; }

@keyframes fadeInUp { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:none;} }

@media (max-width: 920px) {
  .app { margin-left: 0; }
  .cards { grid-template-columns: 1fr; }
}
</style>

<div class="app">
  <main class="main" style="flex:1;">
    <div class="header" style="display:flex;justify-content:space-between;align-items:flex-start; margin-bottom:40px; margin-top:40px; margin-left:40px; margin-right:40px;">
      <div>
        <div class="title" style="font-weight:700;font-size:28px;">Prestasi</div>
        <div class="sub" style="color:var(--muted);font-size:13px;">Gambaran pembelajaran peribadi & aktiviti terkini</div>
      </div>
    </div>

    <!-- Performance Cards (2x2 Grid) -->
    <section class="cards" style="margin-left:40px; margin-right:40px; margin-top:20px; margin-bottom:20px;">
      <div class="card">
        <div class="panel-header" style="margin:0 0 20px 0;">
            <h3>Purata Skor Kuiz</h3>
        </div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2));">
            {{ $avgQuizScore }}%
          </span>
        </div>
      </div>

      <div class="card">
        <div class="panel-header" style="margin:0 0 20px 0;">
            <h3>Purata Skor Permainan</h3>
        </div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--yellow),var(--accent));">
            {{ $avgGameScore }}%
          </span>
        </div>
      </div>

      <div class="card">
        <!-- <div class="label">Topik Paling Lemah</div> -->
         <div class="panel-header" style="margin:0 0 20px 0;">
            <h3>Topik Paling Lemah</h3>
        </div>
        <div class="value">
          <span class="badge-pill" style="background:var(--danger);">
            {{ $weakTopic ?? 'Tiada' }}
          </span>
        </div>
      </div>

      <div class="card">
        <div class="panel-header" style="margin:0 0 20px 0;">
            <h3>Kuiz Diselesaikan</h3>
        </div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,#2A9D8F,#4CAF50);">
            {{ $totalQuizzes ?? 0 }}
          </span>
        </div>
      </div>
    </section>

    <!-- Chart Panel -->
    <section class="panel" style="margin-left:40px; margin-right:40px; margin-top:20px; margin-bottom:20px;">
      <div class="panel-header" style="margin:0 0 20px 0;">
        <div>
          <h3 style="margin:0; font-weight:700; font-size:18px;">Trend Prestasi</h3>
          <div class="subtitle">Kuiz & permainan terkini</div>
        </div>
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
