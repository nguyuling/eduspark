{{-- resources/views/reports/student.blade.php --}}
@extends('layouts.app')

@section('title', 'Student Report')
@section('page_title', ($student->name ?? 'N/A') . ' • Report')
@section('page_sub', 'Individual performance summary')

@section('content')
<div class="panel card" style="margin-top:18px;">
  <div style="margin-bottom:20px;">
    <div style="font-weight:700;font-size:20px;">{{ $student->name ?? 'N/A' }}</div>
    <div style="color:var(--muted);font-size:13px;">ID: {{ $student->id ?? '—' }}</div>
  </div>

  {{-- three-column small cards --}}
  <div class="cards" style="margin-top:18px; grid-template-columns: repeat(3, 1fr); gap:16px; max-width:600px;">
    <div class="card" style="padding:16px;text-align:center;min-height:110px;">
      <div class="label" style="font-size:13px;color:var(--muted);font-weight:700;">Purata</div>
      <div class="value" style="margin-top:10px;">
        <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:6px 12px; font-size:15px;">
          {{ $stats['average_score'] ?? 'N/A' }}
        </span>
      </div>
    </div>

    <div class="card" style="padding:16px;text-align:center;min-height:110px;">
      <div class="label" style="font-size:13px;color:var(--muted);font-weight:700;">Tertinggi</div>
      <div class="value" style="margin-top:10px;">
        <span class="badge-pill" style="background:var(--success); padding:6px 12px; font-size:15px;">
          {{ $stats['highest_score'] ?? 'N/A' }}
        </span>
      </div>
      @if(!empty($stats['highest_subject']))
        <div style="color:var(--muted);font-size:12px;margin-top:6px;">{{ $stats['highest_subject'] }}</div>
      @endif
    </div>

    <div class="card" style="padding:16px;text-align:center;min-height:110px;">
      <div class="label" style="font-size:13px;color:var(--muted);font-weight:700;">Paling Lemah</div>
      <div class="value" style="margin-top:10px;">
        <span class="badge-pill" style="background:var(--danger); padding:6px 12px; font-size:15px;">
          {{ $stats['weakest_score'] ?? 'N/A' }}
        </span>
      </div>
      @if(!empty($stats['weakest_subject']))
        <div style="color:var(--muted);font-size:12px;margin-top:6px;">{{ $stats['weakest_subject'] }}</div>
      @endif
    </div>
  </div>

  <hr style="margin:16px 0;border:none;border-top:1px solid rgba(255,255,255,0.06)">

  <div style="font-weight:700;margin-bottom:8px;">Rekod Percubaan</div>

  @php
    $attempts = $stats['attempts'] ?? [];
  @endphp

  @if(empty($attempts) || count($attempts) === 0)
    <div style="color:var(--muted);padding:8px 0;">Tiada rekod percubaan.</div>
  @else
    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;">
        <thead style="text-align:left;color:var(--muted);font-size:13px;">
          <tr>
            <th style="padding:8px 6px;">Tarikh</th>
            <th style="padding:8px 6px;">Jenis</th>
            <th style="padding:8px 6px;">Topik</th>
            <th style="padding:8px 6px;">Skor</th>
          </tr>
        </thead>
        <tbody>
          @foreach($attempts as $row)
            <tr>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $row['date'] ?? $row->date ?? '-' }}</td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ ucfirst($row['type'] ?? ($row->type ?? '')) }}</td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $row['topic'] ?? $row->topic ?? '-' }}</td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $row['score'] ?? $row->score ?? '' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  <div style="display:flex;gap:10px;margin-top:12px;justify-content:center;">
      @if(!empty($student->id))
        <a href="/reports/student/{{ $student->id }}/export/csv"
           class="btn"
           style="text-decoration:none;padding:8px 14px;border-radius:8px;background:#f5f5f7;color:#111;">CSV</a>

        <a href="/reports/student/{{ $student->id }}/export/pdf"
           class="btn"
           style="text-decoration:none;padding:8px 14px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:white;">PDF</a>
      @else
        <button disabled class="btn" style="padding:8px 14px;border-radius:8px;background:#ddd;color:#999;">CSV</button>
        <button disabled class="btn" style="padding:8px 14px;border-radius:8px;background:#ddd;color:#999;">PDF</button>
      @endif
  </div>
</div>
@endsection
