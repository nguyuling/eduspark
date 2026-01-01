@extends('layouts.app')

@section('page_title','Papan Laporan')
@section('page_sub','')

@section('content')

<div class="app">
  <main class="main">
    <div class="header">
      <div>
        <div class="title">Laporan</div>
        <div class="sub">Prestasi pelajar dan kelas</div>
      </div>
    </div>

{{-- ===================== STATISTICS ===================== --}}
<div class="panel" style="padding:18px;border-radius:12px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="margin:0;font-weight:700;">Statistik Kelas</h3>
        <div style="display:flex;gap:8px;">
            <button id="export-stats-btn" class="btn" style="padding:6px 12px;border-radius:8px;background:var(--success);color:#fff;border:0;font-weight:600;font-size:13px;cursor:pointer;">
                Export
            </button>
        </div>
    </div>

    <div style="display:flex;gap:8px;align-items:center;margin-bottom:16px;flex-wrap:wrap;">
        <label style="font-weight:700;color:var(--muted);font-size:13px;">Filter Kelas:</label>
        <select id="stats-class-select" class="select" style="padding:8px 12px;border-radius:8px;min-width:160px;border:2px solid #ddd;background:#f9f9f9;">
            <option value="">-- Sila Pilih --</option>
            <option value="semua">Semua Kelas</option>
            @foreach($classes as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>

        <label style="font-weight:700;color:var(--muted);font-size:13px;margin-left:16px;">Jangka Masa:</label>
        <select id="stats-date-range" class="select" style="padding:8px 12px;border-radius:8px;min-width:140px;border:2px solid #ddd;background:#f9f9f9;">
            <option value="">-- Sila Pilih --</option>
            <option value="week">Minggu Ini</option>
            <option value="month">Bulan Ini</option>
            <option value="quarter">Suku Tahun</option>
            <option value="all">Semua Masa</option>
        </select>

        <button id="refresh-stats-btn" class="btn" style="padding:8px 12px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;border:0;font-weight:700;margin-left:auto;">
            Muat Semula
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px;">
        <div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:8px;padding:16px;color:#fff;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:8px;">Purata Skor</div>
            <div id="stat-avg-score" style="font-size:28px;font-weight:700;">0</div>
        </div>
        <div style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);border-radius:8px;padding:16px;color:#fff;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:8px;">Jumlah Percubaan</div>
            <div id="stat-total-attempts" style="font-size:28px;font-weight:700;">0</div>
        </div>
        <div style="background:linear-gradient(135deg,#4facfe 0%,#00f2fe 100%);border-radius:8px;padding:16px;color:#fff;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:8px;">Pelajar Aktif</div>
            <div id="stat-active-students" style="font-size:28px;font-weight:700;">0</div>
        </div>
        <div style="background:linear-gradient(135deg,#43e97b 0%,#38f9d7 100%);border-radius:8px;padding:16px;color:#fff;">
            <div style="font-size:13px;opacity:0.9;margin-bottom:8px;">Kadar Kejayaan</div>
            <div id="stat-success-rate" style="font-size:28px;font-weight:700;">0%</div>
        </div>
    </div>

    {{-- Debug info --}}
    <div id="debug-info" style="background:#f0f0f0;padding:12px;border-radius:8px;margin-bottom:16px;font-size:12px;color:#666;display:none;">
        Status: <span id="debug-status">Menunggu...</span>
    </div>

    {{-- Charts Container --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div style="border:1px solid rgba(0,0,0,0.1);border-radius:8px;padding:16px;">
            <div style="font-weight:700;margin-bottom:12px;font-size:14px;">Prestasi Mengikut Topik</div>
            <canvas id="topicChart" style="max-height:250px;"></canvas>
        </div>
        <div style="border:1px solid rgba(0,0,0,0.1);border-radius:8px;padding:16px;">
            <div style="font-weight:700;margin-bottom:12px;font-size:14px;">Trend Prestasi</div>
            <canvas id="trendChart" style="max-height:250px;"></canvas>
        </div>
    </div>

    {{-- Performance Table --}}
    <div id="stats-table-container" style="border:1px solid rgba(0,0,0,0.1);border-radius:8px;padding:16px;display:none;">
        <div style="font-weight:700;margin-bottom:12px;font-size:14px;">Perbandingan Prestasi Kelas</div>
        <div style="overflow:auto;">
            <table id="stats-table" style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead style="text-align:left;color:var(--muted);background:rgba(0,0,0,0.02);">
                    <tr>
                        <th style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.1);">Kelas</th>
                        <th style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.1);">Purata Skor</th>
                        <th style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.1);">Tertinggi</th>
                        <th style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.1);">Terendah</th>
                        <th style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.1);">Kuiz Tertinggi</th>
                        <th style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.1);">Kuiz Terendah</th>
                    </tr>
                </thead>
                <tbody id="stats-tbody">
                    <tr>
                        <td colspan="5" style="padding:20px;text-align:center;color:var(--muted);">Memuatkan data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===================== STUDENT REPORT ===================== --}}
<div class="panel" style="padding:18px;border-radius:12px;">
    <h3 style="margin-top:0;font-weight:700;">Laporan Pelajar</h3>

    <div style="display:flex;gap:0;align-items:center;margin-bottom:12px;flex-wrap:wrap;">
        <label style="font-weight:700;color:var(--muted);margin-right:8px;">Kelas</label>
        <select id="class-select" class="select" style="padding:8px 12px;border-radius:8px;min-width:160px;border:2px solid #ddd;background:#f9f9f9;margin-right:20px;">
            <option value="">{{ $classPlaceholder ?? '-- pilih kelas --' }}</option>
            @foreach($classes as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>

        <label style="font-weight:700;color:var(--muted);margin-right:8px;">Pelajar</label>
        <select id="student-select" class="select" style="padding:8px 12px;border-radius:8px;min-width:280px;border:2px solid #ddd;background:#f9f9f9;margin-right:20px;">
            <option value="">{{ $studentPlaceholder ?? '-- pilih pelajar --' }}</option>
        </select>

        <button id="open-student" class="btn"
            style="padding:8px 12px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;border:0;font-weight:700;margin-top:8px;">
            Buka
        </button>
    </div>

    <div id="student-panel-wrap" style="display:none;">
    </div>


</div>


{{-- ===================== CLASS REPORT ===================== --}}
<div class="panel" style="padding:18px;border-radius:12px;margin-top:16px;">
    <h3 style="margin-top:0;font-weight:700;">Laporan Kelas</h3>

    <div style="display:flex;gap:8px;align-items:center;margin-bottom:12px;flex-wrap:wrap;">
        <label style="font-weight:700;color:var(--muted);">Pilih kelas</label>

        <select id="class-report-select" class="select" style="padding:8px 12px;border-radius:8px;min-width:160px;border:2px solid #ddd;background:#f9f9f9;">
            <option value="">{{ $classPlaceholder ?? '-- pilih kelas --' }}</option>
            @foreach($classes as $c)
                <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>

        <button id="open-class" class="btn"
            style="padding:8px 12px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;border:0;font-weight:700;">
            Buka Kelas
        </button>
    </div>

    <div id="class-panel-wrap" style="color:var(--muted);">
        Pilih kelas untuk melihat ringkasan kelas.
    </div>
</div>

@endsection


@section('scripts')
<script>
function fillStudentSelect(selectEl, items) {
    // robustly replace options, then trigger change and a small focus/blur to force UI refresh
    try {
        console.debug('fillStudentSelect called, items:', items);
    } catch (e) {}
    selectEl.innerHTML = '<option value="">{{ $studentPlaceholder ?? "-- pilih pelajar --" }}</option>';
    if (Array.isArray(items) && items.length > 0) {
        items.forEach(it => {
            const opt = document.createElement('option');
            opt.value = it.id;
            opt.textContent = it.name;
            selectEl.appendChild(opt);
        });
    }
    // ensure value reset and trigger change for any listeners/plugins
    try {
        // make sure option text is visible (in case CSS set color transparent)
        selectEl.style.color = '';
        selectEl.value = '';
        selectEl.dispatchEvent(new Event('change', { bubbles: true }));

        // If common plugins are present, try to refresh/reinit them so the visual widget updates
        if (window.jQuery) {
            try {
                const $sel = window.jQuery(selectEl);
                if ($sel && $sel.length) {
                    if ($.fn.selectpicker && typeof $sel.selectpicker === 'function') {
                        try { $sel.selectpicker('refresh'); } catch (e) {}
                    }
                    if ($.fn.select2 && typeof $sel.select2 === 'function') {
                        try { $sel.trigger('change.select2'); } catch (e) {}
                    }
                }
            } catch (e) {}
        }

        // force a visual refresh for some UI libraries
        selectEl.focus();
        selectEl.blur();
    } catch (e) {
        // ignore
    }
}

const classSelect = document.getElementById('class-select');
const studentSelect = document.getElementById('student-select');
const openStudentBtn = document.getElementById('open-student');

classSelect && classSelect.addEventListener('change', async function() {
    const cls = this.value;
    if (!cls) { fillStudentSelect(studentSelect, []); return; }
    try {
        const res = await fetch(`/reports/students-by-class/${encodeURIComponent(cls)}`);
        const data = await res.json();
        fillStudentSelect(studentSelect, data);
    } catch (err) {
        console.error(err);
        alert('Tidak dapat memuatkan senarai pelajar.');
    }
});

// NEW: load student panel in-place via AJAX (no redirect)
openStudentBtn.addEventListener('click', async () => {
    const id = studentSelect.value;
    if (!id) return alert('Sila pilih pelajar.');

    try {
        const res = await fetch(`/reports/student/${encodeURIComponent(id)}`, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!res.ok) {
            throw new Error(`HTTP ${res.status}: ${res.statusText}`);
        }

        const contentType = res.headers.get('content-type');
        const panelWrap = document.getElementById('student-panel-wrap');
        if (!contentType || !contentType.includes('application/json')) {
            // Server returned HTML instead of JSON — render it directly as page update
            const html = await res.text();
            panelWrap.innerHTML = html;
            panelWrap.style.display = 'block';
        } else {
            // Server returned JSON with HTML payload
            const json = await res.json();
            if (json && json.html) {
                panelWrap.innerHTML = json.html;
                panelWrap.style.display = 'block';
            } else {
                alert('Unexpected response format');
            }
        }
    } catch (err) {
        console.error('Error loading student report:', err);
        alert('Tidak dapat memuatkan laporan pelajar: ' + err.message);
    }
});




/* ========= CLASS AJAX LOAD ========= */

const classReportSelect = document.getElementById('class-report-select');
const openClassBtn = document.getElementById('open-class');
const classPanelWrap = document.getElementById('class-panel-wrap');

function setClassDownloadLinks(cls) {
    const csv = document.getElementById('download-class-csv-bottom');
    const pdf = document.getElementById('download-class-pdf-bottom');
    if (csv) csv.href = `/reports/class/${encodeURIComponent(cls)}/export/csv`;
    if (pdf) pdf.href = `/reports/class/${encodeURIComponent(cls)}/export/pdf`;
}

openClassBtn.addEventListener('click', async function () {
    const cls = classReportSelect.value;
    if (!cls) return alert('Sila pilih kelas.');

    try {
        const res = await fetch(`/reports/class?class=${encodeURIComponent(cls)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const json = await res.json();
        classPanelWrap.innerHTML = json.html || 'Tiada maklumat.';
        setClassDownloadLinks(cls);
    } catch (error) {
        console.error(error);
        alert('Tidak dapat memuatkan ringkasan kelas.');
    }
});

/* ========= STATISTICS LOAD ========= */
let statsChart = null;
let trendChart = null;

async function loadStatistics() {
    const selectedClass = document.getElementById('stats-class-select').value;
    const dateRange = document.getElementById('stats-date-range').value;
    
    // If no class selected, don't load
    if (!selectedClass) {
        const tbody = document.getElementById('stats-tbody');
        tbody.innerHTML = '<tr><td colspan="5" style="padding:20px;text-align:center;color:var(--muted);">Sila pilih kelas untuk melihat data.</td></tr>';
        document.getElementById('stats-table-container').style.display = 'none';
        if (typeof Chart !== 'undefined') {
            if (statsChart) statsChart.destroy();
            if (trendChart) trendChart.destroy();
        }
        return;
    }
    
    // Show table container only if 'Semua Kelas' is selected
    const tableContainer = document.getElementById('stats-table-container');
    if (selectedClass === 'semua') {
        tableContainer.style.display = 'block';
    } else {
        tableContainer.style.display = 'none';
    }
    
    console.log('loadStatistics called with class:', selectedClass, 'range:', dateRange);
    
    const debugEl = document.getElementById('debug-info');
    if (debugEl) {
        debugEl.style.display = 'block';
        document.getElementById('debug-status').textContent = 'Mengemuanya data...';
    }

    try {
        const url = `/api/statistics?class=${encodeURIComponent(selectedClass)}&range=${dateRange}`;
        console.log('Fetching from:', url);
        
        const res = await fetch(url);
        console.log('Response status:', res.status);
        
        if (!res.ok) {
            throw new Error(`HTTP Error: ${res.status} ${res.statusText}`);
        }
        
        const data = await res.json();
        console.log('Received data:', data);
        
        if (debugEl) {
            document.getElementById('debug-status').textContent = 'Data loaded. Avg: ' + data.avgScore + ', Attempts: ' + data.totalAttempts;
        }

        // Update stat cards with default values
        document.getElementById('stat-avg-score').textContent = data.avgScore !== undefined ? data.avgScore : '0';
        document.getElementById('stat-total-attempts').textContent = data.totalAttempts !== undefined ? data.totalAttempts : '0';
        document.getElementById('stat-active-students').textContent = data.activeStudents !== undefined ? data.activeStudents : '0';
        document.getElementById('stat-success-rate').textContent = (data.successRate !== undefined ? data.successRate : '0') + '%';

        // Only draw charts if Chart.js is available
        if (typeof Chart !== 'undefined') {
            // Update topic chart
            if (data.topicData && data.topicData.labels && data.topicData.labels.length > 0) {
                updateTopicChart(data.topicData);
            } else {
                const ctx = document.getElementById('topicChart').getContext('2d');
                if (statsChart) statsChart.destroy();
                statsChart = new Chart(ctx, {
                    type: 'bar',
                    data: { labels: ['Tiada data'], datasets: [{ label: 'N/A', data: [0], backgroundColor: '#ccc' }] },
                    options: { responsive: true, maintainAspectRatio: true, indexAxis: 'y' }
                });
            }

            // Update trend chart
            if (data.trendData && data.trendData.dates && data.trendData.dates.length > 0) {
                updateTrendChart(data.trendData);
            } else {
                const ctx = document.getElementById('trendChart').getContext('2d');
                if (trendChart) trendChart.destroy();
                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: { labels: ['Tiada data'], datasets: [{ label: 'N/A', data: [0], borderColor: '#ccc' }] },
                    options: { responsive: true, maintainAspectRatio: true }
                });
            }
        } else {
            console.warn('Chart.js not loaded');
        }

        // Update stats table
        if (data.classStats && data.classStats.length > 0) {
            updateStatsTable(data.classStats);
        } else {
            const tbody = document.getElementById('stats-tbody');
            tbody.innerHTML = '<tr><td colspan="5" style="padding:20px;text-align:center;color:var(--muted);">Tiada data tersedia.</td></tr>';
        }
        
        if (debugEl) {
            document.getElementById('debug-status').textContent = 'Selesai dimuatkan!';
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
        if (debugEl) {
            document.getElementById('debug-status').textContent = 'Ralat: ' + error.message;
        }
        const tbody = document.getElementById('stats-tbody');
        tbody.innerHTML = '<tr><td colspan="5" style="padding:20px;text-align:center;color:red;">Ralat: ' + error.message + '</td></tr>';
    }
}

function updateTopicChart(data) {
    const ctx = document.getElementById('topicChart').getContext('2d');
    
    if (statsChart) statsChart.destroy();
    
    statsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels || [],
            datasets: [{
                label: 'Purata Skor',
                data: data.scores || [],
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true, max: 100 }
            }
        }
    });
}

function updateTrendChart(data) {
    const ctx = document.getElementById('trendChart').getContext('2d');
    
    if (trendChart) trendChart.destroy();
    
    // Handle both single class (object with class name key) and multiple classes (object with multiple keys)
    const datasets = [];
    const colors = [
        'rgba(102, 126, 234, 1)',
        'rgba(255, 159, 64, 1)',
        'rgba(255, 99, 132, 1)',
        'rgba(54, 162, 235, 1)',
        'rgba(75, 192, 192, 1)',
        'rgba(153, 102, 255, 1)',
        'rgba(255, 206, 86, 1)',
        'rgba(231, 233, 237, 1)'
    ];
    
    let colorIndex = 0;
    for (const [className, scores] of Object.entries(data.scores || {})) {
        const color = colors[colorIndex % colors.length];
        datasets.push({
            label: className,
            data: scores || [],
            borderColor: color,
            backgroundColor: color.replace('1)', '0.1)'),
            borderWidth: 2,
            fill: true,
            tension: 0.4
        });
        colorIndex++;
    }
    
    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.dates || [],
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
}

function updateStatsTable(classStats) {
    const tbody = document.getElementById('stats-tbody');
    tbody.innerHTML = '';
    
    if (!classStats || classStats.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="padding:20px;text-align:center;color:var(--muted);">Tiada data tersedia.</td></tr>';
        return;
    }
    
    classStats.forEach(cls => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.05);">${cls.name || 'N/A'}</td>
            <td style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.05);">${cls.avgScore || 'N/A'}</td>
            <td style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.05);">${cls.maxScore || 'N/A'}</td>
            <td style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.05);">${cls.minScore || 'N/A'}</td>
            <td style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.05);">${cls.highestQuiz || 'N/A'}</td>
            <td style="padding:10px 8px;border-bottom:1px solid rgba(0,0,0,0.05);">${cls.lowestQuiz || 'N/A'}</td>
        `;
        tbody.appendChild(row);
    });
}

// Event listeners for statistics
document.getElementById('refresh-stats-btn').addEventListener('click', loadStatistics);
document.getElementById('stats-class-select').addEventListener('change', loadStatistics);
document.getElementById('stats-date-range').addEventListener('change', loadStatistics);

// Export statistics
document.getElementById('export-stats-btn').addEventListener('click', function() {
    const selectedClass = document.getElementById('stats-class-select').value;
    if (!selectedClass) {
        alert('Sila pilih kelas terlebih dahulu.');
        return;
    }
    const dateRange = document.getElementById('stats-date-range').value;
    window.location.href = `/reports/export-statistics?class=${encodeURIComponent(selectedClass)}&range=${dateRange}`;
});

// Load statistics on page load
window.addEventListener('load', function() {
    console.log('Page loaded, Chart available:', typeof Chart !== 'undefined');
    loadStatistics();
});
</script>
@endsection
