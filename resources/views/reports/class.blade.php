@extends('layouts.app')

@section('page_title','Laporan Kelas')
@section('page_sub','Ringkasan prestasi bagi kelas yang dipilih')

@section('content')
<div class="panel" style="padding:18px;border-radius:12px;">
  <h3 style="margin-top:0;">Laporan Kelas: {{ $selectedClass ?? '—' }}</h3>

  @if($selectedClass)
      <div style="display:flex;gap:20px;flex-wrap:wrap;margin-bottom:18px;">
        
        <div style="flex:1;min-width:220px;padding:28px 24px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:12px;box-shadow:0 4px 15px rgba(102,126,234,0.3);">
          <div style="color:rgba(255,255,255,0.85);font-size:13px;margin-bottom:12px;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Bilangan Pelajar</div>
          <div style="color:#fff;font-size:32px;font-weight:700;">{{ $classStats['student_count'] ?? 0 }}</div>
        </div>

        <div style="flex:1;min-width:220px;padding:28px 24px;background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);border-radius:12px;box-shadow:0 4px 15px rgba(245,87,108,0.3);">
          <div style="color:rgba(255,255,255,0.85);font-size:13px;margin-bottom:12px;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Purata Skor</div>
          <div style="color:#fff;font-size:32px;font-weight:700;">{{ $classStats['avg_score'] ?? 'N/A' }}</div>
        </div>

        <div style="flex:1;min-width:220px;padding:28px 24px;background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%);border-radius:12px;box-shadow:0 4px 15px rgba(79,172,254,0.3);">
          <div style="color:rgba(255,255,255,0.85);font-size:13px;margin-bottom:12px;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Jumlah Percubaan</div>
          <div style="color:#fff;font-size:32px;font-weight:700;">{{ $classStats['total_attempts'] ?? 0 }}</div>
        </div>

        <div style="flex:1;min-width:220px;padding:28px 24px;background:linear-gradient(135deg,#fa709a 0%,#fee140 100%);border-radius:12px;box-shadow:0 4px 15px rgba(250,112,154,0.3);">
          <div style="color:rgba(255,255,255,0.85);font-size:13px;margin-bottom:12px;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Gred Purata</div>
          <div style="color:#fff;font-size:32px;font-weight:700;">{{ $classStats['avg_grade'] ?? 'N/A' }}</div>
        </div>

      </div>

      <div style="margin-top:12px;display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;">
        <a href="{{ route('reports.class.csv', $selectedClass) }}" class="btn" 
           style="padding:8px 12px;border-radius:8px;background:#f4f4f6;border:1px solid rgba(0,0,0,0.08);">
           Muat Turun CSV
        </a>

        <a href="{{ route('reports.class.pdf', $selectedClass) }}" class="btn"
           style="padding:8px 12px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:white;">
           Muat Turun PDF
        </a>
      </div>

      <h4 style="margin-top:18px;">Senarai Pelajar</h4>

      <table style="width:100%;border-collapse:collapse;margin-top:8px;">
        <thead>
          <tr style="text-align:left;color:var(--muted);">
            <th style="padding:8px;border-bottom:1px solid rgba(0,0,0,0.06);">ID</th>
            <th style="padding:8px;border-bottom:1px solid rgba(0,0,0,0.06);">Nama</th>
          </tr>
        </thead>
        <tbody>
          @foreach($students as $stu)
            <tr>
              <td style="padding:8px;border-bottom:1px solid rgba(0,0,0,0.03)">{{ $stu->id }}</td>
              <td style="padding:8px;border-bottom:1px solid rgba(0,0,0,0.03)">{{ $stu->name }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

  @else
      <div style="color:var(--muted);margin-top:10px;">
        Sila pilih kelas di halaman sebelumnya untuk melihat laporan.
      </div>
  @endif
</div>
@endsection
