{{-- resources/views/reports/partials/class_panel.blade.php --}}
<div class="card" style="padding:18px;border-radius:12px;">
    <h3 style="margin-top:0;">Ringkasan Kelas: {{ $selectedClass }}</h3>

    <div style="display:flex;gap:12px;margin-top:12px;">
        <div style="background:white;padding:16px;border-radius:10px;flex:1;">
            <strong>Bilangan Pelajar</strong>
            <div style="margin-top:6px;color:var(--muted);">
                {{ $classStats['student_count'] ?? 0 }}
            </div>
        </div>

        <div style="background:white;padding:16px;border-radius:10px;flex:1;">
            <strong>Purata Skor Kelas</strong>
            <div style="margin-top:6px;color:var(--muted);">
                {{ $classStats['avg_score'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    <hr style="margin:18px 0;">

    <strong>Senarai Pelajar</strong>
    <table style="width:100%;margin-top:10px;border-collapse:collapse;">
        <thead>
            <tr style="color:var(--muted);">
                <th style="padding:8px;text-align:left;">ID</th>
                <th style="padding:8px;text-align:left;">Nama</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $s)
                <tr>
                    <td style="padding:6px;border-top:1px solid #eee;">{{ $s->id }}</td>
                    <td style="padding:6px;border-top:1px solid #eee;">{{ $s->name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="padding:10px;text-align:center;color:var(--muted);">
                        Tiada rekod pelajar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:14px;display:flex;gap:8px;">
        <a id="download-class-csv-bottom"
           class="btn btn-outline"
           style="padding:8px 14px;border-radius:8px;text-decoration:none;"
           href="#">
            CSV
        </a>

        <a id="download-class-pdf-bottom"
           class="btn btn-outline"
           style="padding:8px 14px;border-radius:8px;text-decoration:none;"
           href="#">
            PDF
        </a>
    </div>
</div>
