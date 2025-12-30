{{-- resources/views/reports/partials/student_panel.blade.php --}}
<div class="panel card" style="margin-top:18px;padding:18px;border-radius:14px;box-shadow:0 10px 20px rgba(0,0,0,0.05);display:flex;flex-direction:column;gap:14px;">
  <div style="line-height:1.4;">
    @if(isset($student->id) && $student->id)
      <div style="font-size:18px;font-weight:800;letter-spacing:0.3px;">{{ $student->name }}</div>
      <div style="color:var(--muted);font-size:13px;margin-top:4px;">ID: {{ $student->id }}</div>
    @else
      <div style="font-size:18px;font-weight:800;letter-spacing:0.3px;">N/A</div>
      <div style="color:var(--muted);font-size:13px;margin-top:4px;">ID: —</div>
    @endif
  </div>

  <div style="display:flex;gap:12px;width:100%;">
    <div class="card" style="flex:1;padding:12px 14px;border-radius:12px;border:1px solid rgba(0,0,0,0.05);box-shadow:0 6px 12px rgba(0,0,0,0.03);text-align:center;transition:all 0.3s ease;cursor:pointer;" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 6px 12px rgba(0,0,0,0.03)'; this.style.transform='translateY(0)'">
      <div class="label" style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:var(--muted);font-weight:700;">Purata</div>
      <div class="value">
        <span class="badge-pill" style="display:inline-block;margin-top:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:6px 12px; font-size:13px;border-radius:999px;color:#fff;">
          {{ $stats['average_score'] ?? 'N/A' }}
        </span>
      </div>
    </div>

    <div class="card" style="flex:1;padding:12px 14px;border-radius:12px;border:1px solid rgba(0,0,0,0.05);box-shadow:0 6px 12px rgba(0,0,0,0.03);text-align:center;transition:all 0.3s ease;cursor:pointer;" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 6px 12px rgba(0,0,0,0.03)'; this.style.transform='translateY(0)'">
      <div class="label" style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:var(--muted);font-weight:700;">Tertinggi</div>
      <div class="value">
        <span class="badge-pill" style="display:inline-block;margin-top:8px;background:var(--success); padding:6px 12px; font-size:13px;border-radius:999px;color:#fff;">
          {{ $stats['highest_score'] ?? 'N/A' }}
        </span>
      </div>
      <div style="color:var(--muted);font-size:12px;margin-top:6px">
        {{ $stats['highest_subject'] ?? '' }}
      </div>
    </div>

    <div class="card" style="flex:1;padding:12px 14px;border-radius:12px;border:1px solid rgba(0,0,0,0.05);box-shadow:0 6px 12px rgba(0,0,0,0.03);text-align:center;transition:all 0.3s ease;cursor:pointer;" onmouseover="this.style.boxShadow='0 8px 16px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 6px 12px rgba(0,0,0,0.03)'; this.style.transform='translateY(0)'">
      <div class="label" style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:var(--muted);font-weight:700;">Terlemah</div>
      <div class="value">
        <span class="badge-pill" style="display:inline-block;margin-top:8px;background:var(--danger); padding:6px 12px; font-size:13px;border-radius:999px;color:#fff;">
          {{ $stats['weakest_score'] ?? 'N/A' }}
        </span>
      </div>
      <div style="color:var(--muted);font-size:12px;margin-top:6px">
        {{ $stats['weakest_subject'] ?? '' }}
      </div>
    </div>
  </div>

  <hr style="margin:16px 0;border:none;border-top:1px solid rgba(0,0,0,0.08)">

  <div style="font-weight:700">Percubaan</div>

  @if(!empty($stats['attempts']) && count($stats['attempts'])>0)
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

    <div style="display:flex;justify-content:center;gap:12px;margin-top:14px;flex-wrap:wrap;">
      @if(isset($student->id) && $student->id)
        <a href="{{ route('reports.student.csv', $student->id) }}" data-report="csv" class="btn" style="text-decoration:none;padding:9px 16px;border-radius:10px;background:#f5f5f7;color:#111;font-weight:700;min-width:60px;text-align:center;">CSV</a>
        <a href="{{ route('reports.student.print', $student->id) }}" data-report="pdf" class="btn" style="text-decoration:none;padding:9px 16px;border-radius:10px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;font-weight:700;min-width:60px;text-align:center;">PDF</a>
      @else
        <button class="btn" disabled style="padding:9px 16px;border-radius:10px;background:#eee;color:#999;font-weight:700;min-width:60px;text-align:center;">CSV</button>
        <button class="btn" disabled style="padding:9px 16px;border-radius:10px;background:#eee;color:#999;font-weight:700;min-width:60px;text-align:center;">PDF</button>
      @endif
    </div>
  @else
    <div style="color:var(--muted)">Tiada rekod percubaan.</div>
  @endif
</div>
