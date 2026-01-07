{{-- resources/views/reports/partials/student_panel.blade.php --}}
<div class="panel card" style="margin-top:18px;padding:24px;border-radius:14px;box-shadow:0 10px 20px rgba(0,0,0,0.05);display:flex;flex-direction:column;gap:20px;">
  <div style="line-height:1.6;">
    <div style="font-size:20px;font-weight:800;letter-spacing:0.3px;color:#111;">Pelajar</div>
    @if(isset($student->id) && $student->id)
      <div style="font-size:18px;font-weight:700;margin-top:6px;color:#111;">{{ $student->name }}</div>
      <div style="color:var(--muted);font-size:13px;margin-top:2px;">ID: {{ $student->id }}</div>
    @else
      <div style="font-size:18px;font-weight:700;margin-top:6px;color:#111;">N/A</div>
      <div style="color:var(--muted);font-size:13px;margin-top:2px;">ID: —</div>
    @endif
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;width:100%;">
    <!-- Row 1: Purata Skor and Gred side by side -->
    <div class="card" style="padding:32px 24px;border-radius:14px;border:none;box-shadow:0 8px 16px rgba(102,126,234,0.15);background:linear-gradient(135deg,rgba(102,126,234,0.1) 0%,rgba(118,75,162,0.08) 100%);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;min-height:140px;">
      <div class="label" style="font-size:11px;letter-spacing:0.5px;text-transform:uppercase;color:var(--muted);font-weight:700;">Purata Skor</div>
      <div class="value" style="font-size:32px;font-weight:800;color:var(--accent);">{{ str_replace('%', '', $stats['average_score'] ?? 'N/A') }}</div>
    </div>

    <div class="card" style="padding:32px 24px;border-radius:14px;border:none;box-shadow:0 8px 16px rgba(76,175,80,0.15);background:linear-gradient(135deg,rgba(76,175,80,0.1) 0%,rgba(102,204,102,0.08) 100%);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;min-height:140px;">
      <div class="label" style="font-size:11px;letter-spacing:0.5px;text-transform:uppercase;color:var(--muted);font-weight:700;">Gred</div>
      <div class="value" style="font-size:36px;font-weight:800;">
        @php
          $grade = 'N/A';
          $gradeColor = '#d9d9d9';
          
          // Only calculate grade if there are attempts
          if ($stats['average_score'] !== 'N/A' && !empty($stats['attempts'])) {
            $avgScore = floatval(str_replace('%', '', $stats['average_score'] ?? '0'));
            if ($avgScore >= 90) { $grade = 'A+'; $gradeColor = '#1e8449'; }
            elseif ($avgScore >= 80) { $grade = 'A'; $gradeColor = '#27ae60'; }
            elseif ($avgScore >= 70) { $grade = 'A-'; $gradeColor = '#52c41a'; }
            elseif ($avgScore >= 65) { $grade = 'B+'; $gradeColor = '#00a4a6'; }
            elseif ($avgScore >= 60) { $grade = 'B'; $gradeColor = '#13c2c2'; }
            elseif ($avgScore >= 55) { $grade = 'C+'; $gradeColor = '#faad14'; }
            elseif ($avgScore >= 50) { $grade = 'C'; $gradeColor = '#ffc53d'; }
            elseif ($avgScore >= 45) { $grade = 'D'; $gradeColor = '#ff7a45'; }
            elseif ($avgScore >= 40) { $grade = 'E'; $gradeColor = '#f5222d'; }
            else { $grade = 'F'; $gradeColor = '#c41d7f'; }
          }
        @endphp
        <span style="color:{{ $gradeColor }};">{{ $grade }}</span>
      </div>
    </div>

    <!-- Row 2: Topik Terkuat full width -->
    <div class="card" style="grid-column:1 / -1;padding:32px 24px;border-radius:14px;border:none;box-shadow:0 8px 16px rgba(0,150,136,0.15);background:linear-gradient(135deg,rgba(0,150,136,0.1) 0%,rgba(76,175,80,0.08) 100%);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;min-height:100px;">
      <div class="label" style="font-size:11px;letter-spacing:0.5px;text-transform:uppercase;color:var(--muted);font-weight:700;">Topik Terkuat</div>
      <div class="value" style="font-size:20px;font-weight:700;color:#009688;line-height:1.5;">
        {{ $stats['highest_subject'] ?? 'N/A' }}
      </div>
    </div>

    <!-- Row 3: Topik Lemah full width -->
    <div class="card" style="grid-column:1 / -1;padding:32px 24px;border-radius:14px;border:none;box-shadow:0 8px 16px rgba(244,67,54,0.15);background:linear-gradient(135deg,rgba(244,67,54,0.1) 0%,rgba(229,57,53,0.08) 100%);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;min-height:100px;">
      <div class="label" style="font-size:11px;letter-spacing:0.5px;text-transform:uppercase;color:var(--muted);font-weight:700;">Topik Lemah</div>
      <div class="value" style="font-size:20px;font-weight:700;color:#f44336;line-height:1.5;">
        {{ $stats['weakest_subject'] ?? 'N/A' }}
      </div>
    </div>
  </div>

  <hr style="margin:16px 0;border:none;border-top:1px solid rgba(0,0,0,0.08)">

  <div style="font-weight:700;margin-bottom:16px;margin-top:20px;">Rekod Percubaan</div>

  @if(!empty($stats['attempts']) && count($stats['attempts'])>0)
    <div style="width:calc(100% + 48px);margin-left:-24px;margin-right:-24px;overflow-x:auto;">
      <div style="padding-left:24px;padding-right:24px;">
        <table style="width:100%;border-collapse:collapse;background:#fff;min-width:800px;">
          <thead style="text-align:left;color:#ffffff;font-size:14px;background:linear-gradient(90deg,var(--accent),var(--accent-2));font-weight:700;">
            <tr>
              <th style="padding:16px 16px;color:#ffffff;">Tarikh</th>
              <th style="padding:16px 16px;color:#ffffff;">Topik</th>
              <th style="padding:16px 16px;color:#ffffff;">Skor</th>
              <th style="padding:16px 16px;color:#ffffff;">Gred</th>
            </tr>
          </thead>
          <tbody>
            @foreach($stats['attempts'] as $index => $a)
              @php
                $scorePercent = floatval(str_replace(['%', '/', '(', ')'], '', $a['score'] ?? '0'));
                $gradeVal = 'N/A';
                $gradeBg = '#999';
                if ($scorePercent >= 90) { $gradeVal = 'A+'; $gradeBg = '#1e8449'; }
                elseif ($scorePercent >= 80) { $gradeVal = 'A'; $gradeBg = '#27ae60'; }
                elseif ($scorePercent >= 70) { $gradeVal = 'A-'; $gradeBg = '#52c41a'; }
                elseif ($scorePercent >= 65) { $gradeVal = 'B+'; $gradeBg = '#00a4a6'; }
                elseif ($scorePercent >= 60) { $gradeVal = 'B'; $gradeBg = '#13c2c2'; }
                elseif ($scorePercent >= 55) { $gradeVal = 'C+'; $gradeBg = '#faad14'; }
                elseif ($scorePercent >= 50) { $gradeVal = 'C'; $gradeBg = '#ffc53d'; }
                elseif ($scorePercent >= 45) { $gradeVal = 'D'; $gradeBg = '#ff7a45'; }
                elseif ($scorePercent >= 40) { $gradeVal = 'E'; $gradeBg = '#f5222d'; }
                else { $gradeVal = 'F'; $gradeBg = '#c41d7f'; }
              @endphp
              <tr style="background:{{ $index % 2 == 0 ? '#f9f9fb' : '#fff' }};border-bottom:1px solid rgba(0,0,0,0.06);">
                <td style="padding:16px 16px;color:#333;font-size:14px;width:15%;">{{ $a['date'] ?? '' }}</td>
                <td style="padding:16px 16px;color:#333;font-size:14px;">{{ $a['title'] ?? $a['topic'] ?? '' }}</td>
                <td style="padding:16px 16px;font-weight:700;text-align:center;"><span style="background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;padding:6px 14px;border-radius:6px;font-size:13px;display:inline-block;">{{ isset($a['raw_score']) && isset($a['max_points']) ? $a['raw_score'] . '/' . $a['max_points'] : '' }} ({{ $a['score'] ?? '' }})</span></td>
                <td style="padding:16px 16px;font-weight:700;text-align:center;"><span style="background:{{ $gradeBg }};color:#fff;padding:4px 12px;border-radius:6px;font-size:13px;display:inline-block;">{{ $gradeVal }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div style="color:var(--muted);padding:20px;text-align:center;background:#f9f9fb;border-radius:10px;">Tiada rekod percubaan.</div>
  @endif

  <div style="display:flex;justify-content:flex-end;align-items:center;gap:14px;margin-top:24px;flex-wrap:wrap;">
    @if(isset($student->id) && $student->id)
      <a href="{{ route('reports.student.csv', $student->id) }}" data-report="csv" class="btn" style="text-decoration:none;padding:12px 16px;border-radius:10px;background:#f5f5f7;color:#333;font-weight:700;font-size:14px;border:1px solid rgba(0,0,0,0.08);transition:all 0.2s ease;cursor:pointer;display:inline-flex;align-items:center;gap:8px;" title="Download CSV"><i class="bi bi-file-earmark-spreadsheet"></i>CSV</a>
      <a href="{{ route('reports.student.print', $student->id) }}" data-report="pdf" class="btn" style="text-decoration:none;padding:12px 16px;border-radius:10px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;font-weight:700;font-size:14px;box-shadow:0 4px 12px rgba(102,126,234,0.3);transition:all 0.2s ease;cursor:pointer;display:inline-flex;align-items:center;gap:8px;" title="Download PDF"><i class="bi bi-file-pdf"></i>PDF</a>
    @else
      <button class="btn" disabled style="padding:12px 16px;border-radius:10px;background:#eee;color:#999;font-weight:700;font-size:14px;display:inline-flex;align-items:center;gap:8px;"><i class="bi bi-file-earmark-spreadsheet"></i>CSV</button>
      <button class="btn" disabled style="padding:12px 16px;border-radius:10px;background:#eee;color:#999;font-weight:700;font-size:14px;display:inline-flex;align-items:center;gap:8px;"><i class="bi bi-file-pdf"></i>PDF</button>
    @endif
  </div>
</div>
