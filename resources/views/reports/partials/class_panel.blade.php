{{-- resources/views/reports/partials/class_panel.blade.php --}}
@use('Illuminate\Support\Facades\Schema')
@use('Illuminate\Support\Facades\DB')
<div class="card" style="padding:18px;border-radius:12px;">
    <h3 style="margin-top:0;color:#000;">Ringkasan Kelas: {{ $selectedClass }}</h3>

    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:20px;margin-bottom:24px;width:100%;">
        <div class="card" style="padding:32px 24px;border-radius:14px;border:none;box-shadow:0 8px 16px rgba(102,126,234,0.15);background:linear-gradient(135deg,rgba(102,126,234,0.1) 0%,rgba(118,75,162,0.08) 100%);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;min-height:140px;">
            <div class="label" style="font-size:11px;letter-spacing:0.5px;text-transform:uppercase;color:var(--muted);font-weight:700;">Bilangan Pelajar</div>
            <div class="value" style="font-size:32px;font-weight:800;color:var(--accent);">{{ $classStats['student_count'] ?? 0 }}</div>
        </div>

        <div class="card" style="padding:32px 24px;border-radius:14px;border:none;box-shadow:0 8px 16px rgba(76,175,80,0.15);background:linear-gradient(135deg,rgba(76,175,80,0.1) 0%,rgba(102,204,102,0.08) 100%);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;min-height:140px;">
            <div class="label" style="font-size:11px;letter-spacing:0.5px;text-transform:uppercase;color:var(--muted);font-weight:700;">Purata Skor</div>
            <div class="value" style="font-size:32px;font-weight:800;color:#52c41a;">{{ $classStats['avg_score'] ?? 'N/A' }}</div>
        </div>

        <div class="card" style="padding:32px 24px;border-radius:14px;border:none;box-shadow:0 8px 16px rgba(0,150,136,0.15);background:linear-gradient(135deg,rgba(0,150,136,0.1) 0%,rgba(76,175,80,0.08) 100%);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;min-height:140px;">
            <div class="label" style="font-size:11px;letter-spacing:0.5px;text-transform:uppercase;color:var(--muted);font-weight:700;">Jumlah Percubaan</div>
            <div class="value" style="font-size:32px;font-weight:800;color:#009688;">{{ $classStats['total_attempts'] ?? 0 }}</div>
        </div>

        <div class="card" style="padding:32px 24px;border-radius:14px;border:none;box-shadow:0 8px 16px rgba(244,67,54,0.15);background:linear-gradient(135deg,rgba(244,67,54,0.1) 0%,rgba(229,57,53,0.08) 100%);text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;min-height:140px;">
            <div class="label" style="font-size:11px;letter-spacing:0.5px;text-transform:uppercase;color:var(--muted);font-weight:700;">Gred Purata</div>
            <div class="value" style="font-size:32px;font-weight:800;color:#f44336;">{{ $classStats['avg_grade'] ?? 'N/A' }}</div>
        </div>
    </div>

    <hr style="margin:18px 0;border:none;border-top:1px solid rgba(0,0,0,0.06);">

    <strong style="color:#333;display:block;margin-top:18px;margin-bottom:12px;">Senarai Pelajar</strong>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="background:linear-gradient(90deg,#667eea,#764ba2);">
                <th style="padding:16px;text-align:left;font-weight:600;border:none;color:#fff;">Nama</th>
                <th style="padding:16px;text-align:center;font-weight:600;border:none;color:#fff;">Purata Skor</th>
                <th style="padding:16px;text-align:center;font-weight:600;border:none;color:#fff;">Gred</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $s)
                @php
                    // Grade color mapping
                    $gradeColor = '#c41d7f';
                    if (strpos($s->avg_grade, 'A') === 0) {
                        if ($s->avg_grade === 'A+') $gradeColor = '#1e8449';
                        elseif ($s->avg_grade === 'A') $gradeColor = '#27ae60';
                        else $gradeColor = '#52c41a';
                    } elseif (strpos($s->avg_grade, 'B') === 0) {
                        if ($s->avg_grade === 'B+') $gradeColor = '#00a4a6';
                        else $gradeColor = '#13c2c2';
                    } elseif (strpos($s->avg_grade, 'C') === 0) {
                        if ($s->avg_grade === 'C+') $gradeColor = '#faad14';
                        else $gradeColor = '#ffc53d';
                    } elseif ($s->avg_grade === 'D') {
                        $gradeColor = '#ff7a45';
                    } elseif ($s->avg_grade !== 'N/A') {
                        $gradeColor = '#f5222d';
                    }
                @endphp
                <tr style="background:{{ $loop->index % 2 == 0 ? '#f9f9fb' : '#fff' }};border-bottom:1px solid rgba(0,0,0,0.06);">
                    <td style="padding:16px;color:#333;font-size:14px;">{{ $s->name }}</td>
                    <td style="padding:16px;text-align:center;"><span style="background:#667eea;color:#fff;padding:6px 14px;border-radius:6px;font-size:13px;font-weight:700;display:inline-block;">{{ $s->avg_score > 0 ? $s->avg_score . '%' : '0%' }}</span></td>
                    <td style="padding:16px;text-align:center;">
                        @if($s->avg_grade === 'N/A')
                            <span style="background:#d9d9d9;color:#fff;padding:6px 14px;border-radius:6px;font-size:13px;font-weight:700;display:inline-block;">{{ $s->avg_grade }}</span>
                        @else
                            <span style="background:{{ $gradeColor }};color:#fff;padding:6px 14px;border-radius:6px;font-size:13px;font-weight:700;display:inline-block;">{{ $s->avg_grade }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="padding:10px;text-align:center;color:var(--muted);">
                        Tiada rekod pelajar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:14px;display:flex;gap:12px;flex-wrap:wrap;justify-content:flex-end;">
        <a id="download-class-csv-bottom"
           class="btn"
           style="padding:12px 16px;border-radius:8px;background:#f4f4f6;border:1px solid rgba(0,0,0,0.08);text-decoration:none;color:#333;font-size:14px;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s ease;"
           href="#"
           title="Download CSV">
            <i class="bi bi-file-earmark-spreadsheet"></i>CSV
        </a>

        <a id="download-class-pdf-bottom"
           class="btn"
           style="padding:12px 16px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:white;text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:8px;transition:all 0.2s ease;"
           href="#"
           title="Download PDF">
            <i class="bi bi-file-pdf"></i>PDF
        </a>
    </div>
</div>
