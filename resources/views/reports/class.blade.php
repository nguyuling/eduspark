@extends('layouts.app')

@section('page_title','Laporan Kelas')
@section('page_sub','Ringkasan prestasi bagi kelas yang dipilih')

@section('content')
<div class="panel" style="padding:18px;border-radius:12px;">
  <h3 style="margin-top:0;">Laporan Kelas: {{ $selectedClass ?? '—' }}</h3>

  @if($selectedClass)
      <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:12px;">
        
        <div class="card" style="min-width:180px;">
          <div class="label">Bilangan Pelajar</div>
          <div class="value">{{ $classStats['student_count'] ?? 0 }}</div>
        </div>

        <div class="card" style="min-width:180px;">
          <div class="label">Purata Skor</div>
          <div class="value">{{ $classStats['avg_score'] ?? 'N/A' }}</div>
        </div>

        <div class="card" style="min-width:180px;">
          <div class="label">Topik Paling Lemah</div>
          <div class="value">{{ $classStats['weakest_subject'] ?? 'N/A' }}</div>
        </div>

      </div>

      <div style="margin-top:12px;display:flex;gap:12px;flex-wrap:wrap;">
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
