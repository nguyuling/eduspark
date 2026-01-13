<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>LAPORAN STATISTIK PRESTASI</title>
  <style>
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; color:#222; }
    h1 { text-align:center; margin-bottom:10px; font-size:18px; }
    h2 { font-size:14px; margin-top:15px; margin-bottom:10px; border-bottom:2px solid #333; padding-bottom:5px; }
    .meta { margin-bottom:12px; padding:8px; background:#f9f9f9; border-left:4px solid #007bff; }
    .stats-grid { display:table; width:100%; margin:15px 0; }
    .stat-card { display:table-cell; width:23%; padding:10px; margin-right:2%; border:1px solid #ddd; background:#f5f5f5; vertical-align:top; }
    .stat-value { font-size:18px; font-weight:bold; color:#007bff; }
    .stat-label { font-size:11px; color:#666; margin-top:3px; }
    table { width:100%; border-collapse:collapse; margin-top:8px; }
    th, td { padding:8px; border:1px solid #ddd; text-align:left; font-size:11px; }
    th { background:#f3f3f3; font-weight:700; }
    .text-center { text-align:center; }
    .text-right { text-align:right; }
    .no-data { text-align:center; color:#999; padding:20px; }
  </style>
</head>
<body>
  <h1>LAPORAN STATISTIK PRESTASI</h1>

  <div class="meta">
    <strong>Kelas:</strong> {{ $class ?? 'Semua Kelas' }}<br>
    <strong>Tempoh:</strong> {{ $range ?? 'Tidak dinyatakan' }}<br>
    <small>Dijana: {{ $generatedAt ?? now()->format('Y-m-d H:i:s') }}</small>
  </div>

  @php
    // Define grade function globally for use throughout the template
    // Must match the UI grading scale from ReportController
    $getGrade = function($score) {
      if ($score >= 90) return 'A+';
      if ($score >= 80) return 'A';
      if ($score >= 70) return 'A-';
      if ($score >= 65) return 'B+';
      if ($score >= 60) return 'B';
      if ($score >= 55) return 'C+';
      if ($score >= 50) return 'C';
      if ($score >= 45) return 'D';
      if ($score >= 40) return 'E';
      return 'F';
    };
  @endphp

  <h2>Ringkasan Statistik</h2>
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-value">{{ $statsData['avgScore'] ?? 'N/A' }}</div>
      <div class="stat-label">Purata Skor (%)</div>
    </div>
    <div class="stat-card">
      <div class="stat-value">{{ $statsData['totalAttempts'] ?? 'N/A' }}</div>
      <div class="stat-label">Jumlah Percubaan</div>
    </div>
    <div class="stat-card">
      <div class="stat-value">{{ $statsData['activeStudents'] ?? 'N/A' }}</div>
      <div class="stat-label">Pelajar Aktif</div>
    </div>
    <div class="stat-card">
      <div class="stat-value">{{ $getGrade($statsData['avgScore'] ?? 0) }}</div>
      <div class="stat-label">Gred Purata</div>
    </div>
  </div>

  <h2>Prestasi Mengikut Topik</h2>
  @php
    $topicLabels = $statsData['topicData']['labels'] ?? [];
    $topicScores = $statsData['topicData']['scores'] ?? [];
    $hasTopicData = !empty($topicLabels) && !empty($topicScores) && count($topicLabels) > 0;
  @endphp
  @if($hasTopicData)
    <table>
      <thead>
        <tr>
          <th>Topik</th>
          <th style="width:100px;" class="text-right">Purata Skor (%)</th>
          <th style="width:80px;" class="text-center">Gred</th>
        </tr>
      </thead>
      <tbody>
        @foreach($topicLabels as $index => $label)
          <tr>
            <td>{{ $label ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($topicScores[$index] ?? 0, 2) }}%</td>
            <td class="text-center"><strong>{{ $getGrade($topicScores[$index] ?? 0) }}</strong></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <div class="no-data">Tiada data topik tersedia.</div>
  @endif

  <h2>Tren Prestasi</h2>
  @php
    $trendDates = $statsData['trendData']['dates'] ?? [];
    $trendScores = $statsData['trendData']['scores'] ?? [];
    $hasTrendData = !empty($trendDates) && !empty($trendScores) && count($trendDates) > 0;
  @endphp
  @if($hasTrendData)
    <table>
      <thead>
        <tr>
          <th>Tarikh</th>
          <th style="width:100px;" class="text-right">Purata Skor (%)</th>
          <th style="width:80px;" class="text-center">Gred</th>
        </tr>
      </thead>
      <tbody>
        @foreach($trendDates as $index => $date)
          <tr>
            <td>{{ $date ?? 'N/A' }}</td>
            <td class="text-right">{{ number_format($trendScores[$index] ?? 0, 2) }}%</td>
            <td class="text-center"><strong>{{ $getGrade($trendScores[$index] ?? 0) }}</strong></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <div class="no-data">Tiada data tren tersedia.</div>
  @endif

  <div style="margin-top:30px; padding-top:15px; border-top:1px solid #ddd; font-size:10px; color:#999; text-align:center;">
    <p>Laporan ini dihasilkan secara automatik. Untuk maklumat lebih lanjut, sila hubungi pentadbir sistem.</p>
  </div>
</body>
</html>
