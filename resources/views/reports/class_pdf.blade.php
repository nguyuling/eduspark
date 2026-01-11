<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'LAPORAN PRESTASI KELAS' }}</title>
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
    <strong>Class:</strong> {{ $class ?? 'N/A' }}<br>
    <strong>Purata Skor Kelas:</strong> {{ $classAverage ?? 'N/A' }}<br>
    <small>Dicipta pada: {{ date('Y-m-d H:i') }}</small>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:60px;">Kedudukan</th>
        <th style="width:80px;">student_id</th>
        <th>nama</th>
        <th style="width:100px;">Purata Skor</th>
        <th style="width:60px;">Gred</th>
      </tr>
    </thead>
    <tbody>
      @if(!empty($students))
        @foreach($students as $index => $s)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $s['id'] }}</td>
            <td>{{ $s['name'] }}</td>
            <td>{{ isset($s['avg_score']) ? $s['avg_score'] . '%' : 'N/A' }}</td>
            <td style="text-align:center;font-weight:700;">{{ $s['grade'] ?? 'N/A' }}</td>
          </tr>
        @endforeach
      @else
        <tr>
          <td colspan="5" style="text-align:center;color:#666;">Tiada rekod pelajar.</td>
        </tr>
      @endif
    </tbody>
  </table>
</body>
</html>
