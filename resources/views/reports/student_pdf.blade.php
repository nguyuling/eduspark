<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'LAPORAN PRESTASI PELAJAR' }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:12px; color:#222; }
    h1 { text-align:center; margin-bottom:10px; }
    .meta { margin-bottom:12px; }
    table { width:100%; border-collapse:collapse; margin-top:8px; }
    th, td { padding:8px; border:1px solid #ddd; text-align:left; }
    th { background:#f3f3f3; font-weight:700; }
  </style>
</head>
<body>
  <h1>{{ $title }}</h1>

  <div class="meta">
    <strong>Name:</strong> {{ $studentName ?? 'N/A' }}<br>
    <strong>Class:</strong> {{ $studentClass ?? 'N/A' }}<br>
    <small>Dicipta pada: {{ date('Y-m-d H:i') }}</small>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:120px;">Tarikh</th>
        <th style="width:100px;">Jenis</th>
        <th>Topik</th>
        <th style="width:100px;">Skor</th>
        <th style="width:60px;">Gred</th>
      </tr>
    </thead>
    <tbody>
      @if(!empty($attempts))
        @foreach($attempts as $a)
          @php
            $percent = isset($a['percentage']) ? floatval(str_replace('%', '', $a['percentage'])) : 0;
            $grade = 'N/A';
            if ($percent >= 90) $grade = 'A+';
            elseif ($percent >= 80) $grade = 'A';
            elseif ($percent >= 70) $grade = 'A-';
            elseif ($percent >= 65) $grade = 'B+';
            elseif ($percent >= 60) $grade = 'B';
            elseif ($percent >= 55) $grade = 'C+';
            elseif ($percent >= 50) $grade = 'C';
            elseif ($percent >= 45) $grade = 'D';
            elseif ($percent >= 40) $grade = 'E';
            else $grade = 'F';
          @endphp
          <tr>
            <td>{{ $a['date'] }}</td>
            <td>{{ $a['type'] }}</td>
            <td>{{ $a['topic'] }}</td>
            <td>{{ isset($a['raw_score']) && isset($a['max_points']) ? $a['raw_score'] . '/' . $a['max_points'] : '' }} ({{ isset($a['percentage']) ? $a['percentage'] . '%' : '' }})</td>
            <td style="text-align:center;font-weight:700;">{{ $grade }}</td>
          </tr>
        @endforeach
      @else
        <tr>
          <td colspan="5" style="text-align:center;color:#666;">Tiada rekod percubaan.</td>
        </tr>
      @endif
    </tbody>
  </table>
</body>
</html>
