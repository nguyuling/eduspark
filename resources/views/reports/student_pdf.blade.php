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
        <th style="width:140px;">Tarikh</th>
        <th style="width:110px;">Jenis</th>
        <th>Topik</th>
        <th style="width:80px;">Skor</th>
      </tr>
    </thead>
    <tbody>
      @if(!empty($attempts))
        @foreach($attempts as $a)
          <tr>
            <td>{{ $a['date'] }}</td>
            <td>{{ $a['type'] }}</td>
            <td>{{ $a['topic'] }}</td>
            <td>{{ $a['score'] }}</td>
          </tr>
        @endforeach
      @else
        <tr>
          <td colspan="4" style="text-align:center;color:#666;">Tiada rekod percubaan.</td>
        </tr>
      @endif
    </tbody>
  </table>
</body>
</html>
