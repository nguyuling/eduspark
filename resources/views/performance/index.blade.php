@extends('layouts.app')

@section('content')

<main class="main">
  <div class="header">
    <div>
      <div class="title">Prestasi</div>
      <div class="sub">Gambaran pembelajaran peribadi & aktiviti terkini</div>
    </div>
  </div>

  <!-- Performance Cards (2x2 Grid) -->
  <section class="cards performance-cards" style="margin-bottom:20px; margin-top:10px;">
      <div class="card" style="text-align:center;">
        <div class="panel-header" style="justify-content:center;">
            <h3>Purata Skor Kuiz</h3>
        </div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2));">
            {{ $avgQuizScore }}%
          </span>
        </div>
      </div>

      <div class="card" style="text-align:center;">
        <div class="panel-header" style="justify-content:center;">
            <h3>Purata Skor Permainan</h3>
        </div>
        <div class="value">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--yellow),var(--accent));">
            {{ $avgGameScore }}%
          </span>
        </div>
      </div>

      <div class="card" style="text-align:center;">
        <!-- <div class="label">Topik Paling Lemah</div> -->
         <div class="panel-header" style="justify-content:center;">
            <h3>Topik Paling Lemah</h3>
        </div>
        <div class="value">
          <span class="badge-pill" style="background:var(--danger);">
            {{ $weakTopic ?? 'Tiada' }}
          </span>
        </div>
      </div>

      <div class="card" style="text-align:center;">
        <div class="panel-header" style="justify-content:center;">
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
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <div class="panel-header">
        <div>
          <h3>Trend Prestasi</h3>
          <div class="subtitle">Kuiz & permainan terkini</div>
        </div>
      </div>
      <canvas id="trendChart"></canvas>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trendChart');
const gr = ctx.getContext('2d').createLinearGradient(0,0,0,200);
gr.addColorStop(0,'rgba(106,77,247,0.22)');
gr.addColorStop(1,'rgba(156,123,255,0.06)');

const fullLabels = {!! json_encode($labels) !!};
const shortLabels = fullLabels.map(l => {
  if(!l) return '';
  return l.length > 32 ? l.slice(0, 32) + '...' : l;
});

new Chart(ctx,{
  type:'line',
  data:{
    labels: shortLabels,
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
    plugins:{
      legend:{display:false},
      tooltip:{
        callbacks:{
          title:(items)=>{
            const i = items[0]?.dataIndex ?? 0;
            return fullLabels[i] ?? '';
          }
        }
      }
    },
    scales:{y:{beginAtZero:true,max:100}}
  }
});
</script>

@endsection
