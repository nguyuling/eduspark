{{-- resources/views/reports/partials/student_panel.blade.php --}}
<div class="panel card" style="margin-top:18px;padding:18px;border-radius:14px;box-shadow:0 10px 20px rgba(0,0,0,0.05);display:flex;flex-direction:column;gap:14px;">
  <div style="line-height:1.4;">
    <div style="font-size:18px;font-weight:800;letter-spacing:0.3px;">Pelajar</div>
    @if(isset($student->id) && $student->id)
      <div style="font-size:17px;font-weight:700;margin-top:4px;">{{ $student->name }}</div>
      <div style="color:var(--muted);font-size:13px;margin-top:1px;">ID: {{ $student->id }}</div>
    @else
      <div style="font-size:17px;font-weight:700;margin-top:4px;">N/A</div>
      <div style="color:var(--muted);font-size:13px;margin-top:1px;">ID: —</div>
    @endif
  </div>

  <div style="display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
    <div class="card" style="min-width:250px;padding:20px 18px;border-radius:12px;border:1px solid rgba(0,0,0,0.05);box-shadow:0 6px 12px rgba(0,0,0,0.03);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;">
      <div class="label" style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:var(--muted);font-weight:700;">Purata Skor</div>
      <div class="value">
        <span class="badge-pill" style="display:inline-block;background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:6px 12px; font-size:13px;border-radius:999px;color:#fff;">
          {{ $stats['average_score'] ?? 'N/A' }}
        </span>
      </div>
    </div>

    <div class="card" style="min-width:250px;padding:20px 18px;border-radius:12px;border:1px solid rgba(0,0,0,0.05);box-shadow:0 6px 12px rgba(0,0,0,0.03);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;">
      <div class="label" style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:var(--muted);font-weight:700;">Topik Terkuat</div>
      <div class="value">
        <span class="badge-pill" style="display:inline-block;background:var(--success); padding:6px 12px; font-size:12px;border-radius:999px;color:#fff;max-width:200px;word-wrap:break-word;line-height:1.3;">
          {{ $stats['highest_subject'] ?? 'N/A' }}
        </span>
      </div>
    </div>

    <div class="card" style="min-width:250px;padding:20px 18px;border-radius:12px;border:1px solid rgba(0,0,0,0.05);box-shadow:0 6px 12px rgba(0,0,0,0.03);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;">
      <div class="label" style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:var(--muted);font-weight:700;">Topik Lemah</div>
      <div class="value">
        <span class="badge-pill" style="display:inline-block;background:var(--danger); padding:6px 12px; font-size:12px;border-radius:999px;color:#fff;max-width:200px;word-wrap:break-word;line-height:1.3;">
          {{ $stats['weakest_subject'] ?? 'N/A' }}
        </span>
      </div>
    </div>
  </div>

  <hr style="margin:16px 0;border:none;border-top:1px solid rgba(0,0,0,0.08)">

  <div style="font-weight:700;margin-bottom:10px;">Rekod Percubaan</div>

  @if(!empty($stats['attempts']) && count($stats['attempts'])>0)
    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;">
        <thead style="text-align:left;color:var(--muted);font-size:13px;">
          <tr>
            <th style="padding:8px 6px;">Tarikh</th>
            <th style="padding:8px 6px;">Topik</th>
            <th style="padding:8px 6px;">Skor</th>
          </tr>
        </thead>
        <tbody>
          @foreach($stats['attempts'] as $a)
            <tr>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $a['date'] ?? '' }}</td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $a['title'] ?? $a['topic'] ?? '' }}</td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);">{{ $a['score'] ?? '' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div style="color:var(--muted);padding:8px 0;">Tiada rekod percubaan.</div>
  @endif

  <div style="display:flex;justify-content:center;gap:12px;margin-top:14px;flex-wrap:wrap;">
    @if(isset($student->id) && $student->id)
      <a href="{{ route('reports.student.csv', $student->id) }}" data-report="csv" class="btn" style="text-decoration:none;padding:9px 16px;border-radius:10px;background:#f5f5f7;color:#111;font-weight:700;">CSV</a>
      <a href="{{ route('reports.student.print', $student->id) }}" data-report="pdf" class="btn" style="text-decoration:none;padding:9px 16px;border-radius:10px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;font-weight:700;">PDF</a>
    @else
      <button class="btn" disabled style="padding:9px 16px;border-radius:10px;background:#eee;color:#999;font-weight:700;">CSV</button>
      <button class="btn" disabled style="padding:9px 16px;border-radius:10px;background:#eee;color:#999;font-weight:700;">PDF</button>
    @endif
  </div>
</div>
