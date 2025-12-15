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
    <small>Dicipta pada: {{ date('Y-m-d H:i') }}</small>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:120px;">student_id</th>
        <th>name</th>
      </tr>
    </thead>
    <tbody>
      @if(!empty($students))
        @foreach($students as $s)
          <tr>
            <td>{{ $s['id'] }}</td>
            <td>{{ $s['name'] }}</td>
          </tr>
        @endforeach
      @else
        <tr>
          <td colspan="2" style="text-align:center;color:#666;">Tiada rekod pelajar.</td>
        </tr>
      @endif
    </tbody>
  </table>
</body>
</html>
