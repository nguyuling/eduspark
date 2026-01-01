@extends('layouts.app')

@section('content')

<div class="app">
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
        <div class="value" style="margin-top:-10px;">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); font-size:14px; padding:6px 12px;">
            {{ $avgQuizScore }}%
          </span>
        </div>
      </div>

      <div class="card" style="text-align:center;">
        <div class="panel-header" style="justify-content:center;">
            <h3>Permainan Dimainkan</h3>
        </div>
        <div class="value" style="margin-top:-10px;">
          <span class="badge-pill" style="background:linear-gradient(90deg,var(--yellow),var(--accent)); font-size:14px; padding:6px 12px;">
            {{ $totalGames ?? 0 }}
          </span>
        </div>
      </div>

      <div class="card" style="text-align:center;">
         <div class="panel-header" style="justify-content:center;">
            <h3>Topik Paling Lemah</h3>
        </div>
        <div class="value" style="margin-top:-10px;">
          <span class="badge-pill" style="background:var(--danger); font-size:14px; padding:6px 12px;">
            {{ $weakTopic ?? 'Tiada' }}
          </span>
        </div>
      </div>

      <div class="card" style="text-align:center;">
        <div class="panel-header" style="justify-content:center;">
            <h3>Kuiz Diselesaikan</h3>
        </div>
        <div class="value" style="margin-top:-10px;">
          <span class="badge-pill" style="background:linear-gradient(90deg,#2A9D8F,#4CAF50); font-size:14px; padding:6px 12px;">
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
        </div>
      </div>
      <canvas id="trendChart"></canvas>
    </section>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trendChart');
const gr = ctx.getContext('2d').createLinearGradient(0,0,0,200);
gr.addColorStop(0,'rgba(106,77,247,0.22)');
gr.addColorStop(1,'rgba(156,123,255,0.06)');

const labels = {!! json_encode($labels) !!};
const labelsFull = {!! json_encode($labelsFull ?? $labels) !!};
const scores = {!! json_encode($scores) !!};
const rawScores = {!! json_encode($rawScores ?? []) !!};

new Chart(ctx,{
  type:'line',
  data:{
    labels: labels,
    datasets:[{
      label:'Skor',
      data: scores,
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
            const idx = items[0]?.dataIndex ?? 0;
            return labelsFull[idx] ?? labels[idx] ?? '';
          },
          label:(ctx)=> {
            const idx = ctx.dataIndex;
            const percent = ctx.parsed.y;
            const raw = rawScores[idx];
            if (raw && raw.raw !== null && raw.max !== null && raw.max > 0) {
              return [`Skor: ${percent}%`, `(${raw.raw}/${raw.max})`];
            } else if (raw && raw.raw !== null) {
              return `Skor: ${raw.raw}`;
            }
            return `Skor: ${percent}%`;
          }
        }
      }
    },
    scales:{
      y:{beginAtZero:true,max:100,suggestedMax:100},
      x:{ticks:{callback:(value)=>{
        const label = labels[value] ?? '';
        return label;
      }}}
    }
  }
});
</script>

@endsection
