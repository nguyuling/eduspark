@extends('layouts.app')

@section('page_title','Prestasi')
@section('page_sub','Gambaran pembelajaran peribadi & aktiviti terkini')

@section('content')
<style>
/* minimal page-specific styles to complement the layout */
.cards { display:grid; grid-template-columns:repeat(2,1fr); gap:18px; margin-top:6px; }
@media(max-width:700px){ .cards{grid-template-columns:1fr;} }
.card{border-radius:14px;padding:16px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center}
.badge-pill{border-radius:999px;padding:6px 10px;color:#fff;font-weight:700;font-size:14px}
.panel{border-radius:14px;padding:14px;margin-top:18px}
.panel .chart-header{display:block}
.panel .chart-header .heading{font-weight:700;font-size:16px;display:block}
.panel .chart-header .subtitle{display:block;margin-top:6px;color:var(--muted);font-size:13px}
</style>

<section class="cards">
  <div class="card">
    <div class="label">Purata Skor Kuiz</div>
    <div class="value"><span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2));">{{ $avgQuizScore }}%</span></div>
  </div>

  <div class="card">
    <div class="label">Purata Skor Permainan</div>
    <div class="value"><span class="badge-pill" style="background:linear-gradient(90deg,var(--yellow),var(--accent));">{{ $avgGameScore }}%</span></div>
  </div>

  <div class="card">
    <div class="label">Topik Paling Lemah</div>
    <div class="value"><span class="badge-pill" style="background:var(--danger);">{{ $weakTopic ?? 'Tiada' }}</span></div>
  </div>

  <div class="card">
    <div class="label">Kuiz Diselesaikan</div>
    <div class="value"><span class="badge-pill" style="background:linear-gradient(90deg,#2A9D8F,#4CAF50);">{{ $totalQuizzes ?? 0 }}</span></div>
  </div>
</section>

<section class="panel card">
  <div class="chart-header"><span class="heading">Trend Prestasi</span><span class="subtitle">Kuiz & permainan terkini</span></div>
  <canvas id="trendChart" style="margin-top:14px;max-height:260px;width:100%"></canvas>
</section>

@endsection

@section('scripts')
<script>
const ctx = document.getElementById('trendChart');
if (ctx) {
  const gr = ctx.getContext('2d').createLinearGradient(0,0,0,200);
  gr.addColorStop(0,'rgba(106,77,247,0.22)');
  gr.addColorStop(1,'rgba(156,123,255,0.06)');
  new Chart(ctx,{type:'line',data:{labels:{!! json_encode($labels) !!},datasets:[{label:'Skor',data:{!! json_encode($scores) !!},borderColor:'#6A4DF7',backgroundColor:gr,tension:.38,fill:true,pointRadius:6,pointBackgroundColor:'#fff',pointBorderColor:'#6A4DF7'}]},options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,max:100}}}});
}
</script>
@endsection
