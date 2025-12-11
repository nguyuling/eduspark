{{-- resources/views/reports/partials/student_panel.blade.php --}}
<div class="panel card" style="margin-top:18px;">
  <div style="display:flex;justify-content:space-between;align-items:center;">
    <div>
      <strong style="font-size:18px;">Pelajar</strong>
      <div style="color:var(--muted);font-size:13px">Prestasi pelajar terpilih</div>
    </div>

    <div style="text-align:right;">
      @if(isset($student->id) && $student->id)
        <div style="font-weight:700">{{ $student->name }}</div>
        <div style="color:var(--muted);font-size:13px">ID: {{ $student->id }}</div>
      @else
        <div style="font-weight:700">N/A</div>
        <div style="color:var(--muted);font-size:13px">ID: —</div>
      @endif
    </div>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-top:12px;">
    <div class="card" style="padding:10px;text-align:center;">
      <div class="label">Average</div>
      <div class="value">
        <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:5px 8px; font-size:13px;">
          {{ $stats['average_score'] ?? 'N/A' }}
        </span>
      </div>
    </div>

    <div class="card" style="padding:10px;text-align:center;">
      <div class="label">Highest</div>
      <div class="value">
        <span class="badge-pill" style="background:var(--success); padding:5px 8px; font-size:13px;">
          {{ $stats['highest_score'] ?? 'N/A' }}
        </span>
      </div>
      <div style="color:var(--muted);font-size:12px;margin-top:6px">
        {{ $stats['highest_subject'] ?? '' }}
      </div>
    </div>

    <div class="card" style="padding:10px;text-align:center;">
      <div class="label">Weakest</div>
      <div class="value">
        <span class="badge-pill" style="background:var(--danger); padding:5px 8px; font-size:13px;">
          {{ $stats['weakest_score'] ?? 'N/A' }}
        </span>
      </div>
      <div style="color:var(--muted);font-size:12px;margin-top:6px">
        {{ $stats['weakest_subject'] ?? '' }}
      </div>
    </div>
  </div>

  <hr style="margin:14px 0;border:none;border-top:1px solid rgba(0,0,0,0.06)">

  <div style="font-weight:700;margin-bottom:8px;">Attempts</div>

  @if(!empty($stats['attempts']) && count($stats['attempts'])>0)
    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;">
        <thead style="text-align:left;color:var(--muted);font-size:13px;">
          <tr>
            <th style="padding:8px 6px;">Date</th>
            <th style="padding:8px 6px;">Type</th>
            <th style="padding:8px 6px;">Topic</th>
            <th style="padding:8px 6px;">Score</th>
          </tr>
        </thead>
        <tbody>
          @foreach($stats['attempts'] as $a)
            <tr>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $a['date'] ?? '' }}</td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $a['type'] ?? '' }}</td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $a['topic'] ?? '' }}</td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $a['score'] ?? '' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div style="color:var(--muted);padding:8px 0;">Tiada rekod percubaan.</div>
  @endif

  <div style="display:flex;gap:10px;margin-top:12px;">
    @if(isset($student->id) && $student->id)
      <a href="{{ route('reports.student.csv', $student->id) }}" data-report="csv" class="btn" style="text-decoration:none;padding:8px 14px;border-radius:8px;background:#f5f5f7;color:#111;">CSV</a>
      <a href="{{ route('reports.student.print', $student->id) }}" data-report="pdf" class="btn" style="text-decoration:none;padding:8px 14px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;">PDF</a>
    @else
      <button class="btn" disabled style="padding:8px 14px;border-radius:8px;background:#eee;color:#999;">CSV</button>
      <button class="btn" disabled style="padding:8px 14px;border-radius:8px;background:#eee;color:#999;">PDF</button>
    @endif
  </div>
</div>
