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
        <section class="panel" style="margin-bottom:20px;">
            <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; line-height:1;">Statistik</h2>
            <div style="display:grid; grid-template-columns:1fr 1fr auto auto; gap:12px; align-items:flex-end; margin-bottom:25px;">
                <div>
                    <label style="font-size:12px;">Kelas</label>
                    <select id="stats-class-select" class="select" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;" required>
                        <option value="">-- Sila Pilih --</option>
                        <option value="semua">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size:12px;">Jangka Masa</label>
                    <select id="stats-date-range" class="select" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;" required>
                        <option value="">-- Sila Pilih --</option>
                        <option value="week">Minggu Ini</option>
                        <option value="month">Bulan Ini</option>
                        <option value="all">Semua Masa</option>
                    </select>
                </div>

                <button id="export-stats-btn" class="btn" style="padding:12px 24px; border-radius:8px; background:var(--success); color:#fff; border:none; font-weight:700; font-size:14px; cursor:pointer; height:40px;">
                    Export
                </button>
                <button id="refresh-stats-btn" class="btn" style="padding:12px 24px; border-radius:8px; background:linear-gradient(90deg,var(--accent),var(--accent-2)); color:#fff; border:none; font-weight:700; font-size:14px; cursor:pointer; height:40px;">
                    Muat Semula
                </button>
            </div>

            {{-- Statistics Cards --}}
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:20px;">
                <div style="background:linear-gradient(135deg,#f5f0ffff 0%,#ffffffff 100%);border-radius:16px;padding:24px;border:1px solid #e5e7eb;box-shadow:0 2px 12px rgba(2,6,23,0.12);transition:transform 0.12s ease,box-shadow 0.12s ease;text-align:center;cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 20px rgba(106,77,247,0.2)';this.style.borderColor='#6A4DF7';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(2,6,23,0.12)';this.style.borderColor='#e5e7eb';">
                    <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">Purata Skor</div>
                    <div id="stat-avg-score" style="font-size:28px;font-weight:700;color:#7c3aed;">0</div>
                </div>
                <div style="background:linear-gradient(135deg,#f5f0ffff 0%,#ffffffff 100%);border-radius:16px;padding:24px;border:1px solid #e5e7eb;box-shadow:0 2px 12px rgba(2,6,23,0.12);transition:transform 0.12s ease,box-shadow 0.12s ease;text-align:center;cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 20px rgba(106,77,247,0.2)';this.style.borderColor='#6A4DF7';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(2,6,23,0.12)';this.style.borderColor='#e5e7eb';">
                    <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">Gred Purata</div>
                    <div id="stat-avg-grade" style="font-size:28px;font-weight:700;color:#7c3aed;">N/A</div>
                </div>
                <div style="background:linear-gradient(135deg,#f5f0ffff 0%,#ffffffff 100%);border-radius:16px;padding:24px;border:1px solid #e5e7eb;box-shadow:0 2px 12px rgba(2,6,23,0.12);transition:transform 0.12s ease,box-shadow 0.12s ease;text-align:center;cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 20px rgba(106,77,247,0.2)';this.style.borderColor='#6A4DF7';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(2,6,23,0.12)';this.style.borderColor='#e5e7eb';">
                    <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">Jumlah Percubaan</div>
                    <div id="stat-total-attempts" style="font-size:28px;font-weight:700;color:#7c3aed;">0</div>
                </div>
                <div style="background:linear-gradient(135deg,#f5f0ffff 0%,#ffffffff 100%);border-radius:16px;padding:24px;border:1px solid #e5e7eb;box-shadow:0 2px 12px rgba(2,6,23,0.12);transition:transform 0.12s ease,box-shadow 0.12s ease;text-align:center;cursor:pointer;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 20px rgba(106,77,247,0.2)';this.style.borderColor='#6A4DF7';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(2,6,23,0.12)';this.style.borderColor='#e5e7eb';">
                    <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">Pelajar Aktif</div>
                    <div id="stat-active-students" style="font-size:28px;font-weight:700;color:#7c3aed;">0</div>
                </div>
            </div>

            {{-- Debug info --}}
            <div id="debug-info" style="background:#f0f0f0;padding:12px;border-radius:8px;margin-bottom:16px;font-size:12px;color:#666;display:none;">
                Status: <span id="debug-status">Menunggu...</span>
            </div>

            {{-- Charts Container --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div style="border:1px dotted rgba(0,0,0,0.2);border-radius:16px;padding:16px;transition:transform 0.12s ease,box-shadow 0.12s ease,border-color 0.12s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 20px rgba(106,77,247,0.2)';this.style.borderColor='#6A4DF7';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none';this.style.borderColor='rgba(0,0,0,0.2)';">
                    <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">Prestasi Mengikut Topik</div>
                    <div id="topicChartPlaceholder" style="display:flex;align-items:center;justify-content:center;height:250px;color:#999;font-size:14px;">Sila pilih kelas untuk melihat grafik</div>
                    <canvas id="topicChart" style="max-height:250px;display:none;"></canvas>
                </div>
                <div style="border:1px dotted rgba(0,0,0,0.2);border-radius:16px;padding:16px;transition:transform 0.12s ease,box-shadow 0.12s ease,border-color 0.12s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 20px rgba(106,77,247,0.2)';this.style.borderColor='#6A4DF7';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none';this.style.borderColor='rgba(0,0,0,0.2)';">
                    <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">Trend Prestasi</div>
                    <div id="trendChartPlaceholder" style="display:flex;align-items:center;justify-content:center;height:250px;color:#999;font-size:14px;">Sila pilih kelas untuk melihat grafik</div>
                    <canvas id="trendChart" style="max-height:250px;display:none;"></canvas>
                </div>
            </div>

            {{-- Performance Table --}}
            <div id="stats-table-container" style="border:1px solid rgba(0,0,0,0.1);border-radius:16px;padding:16px;display:none;transition:transform 0.12s ease,box-shadow 0.12s ease,border-color 0.12s ease;box-shadow:0 2px 12px rgba(2,6,23,0.12);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 20px rgba(106,77,247,0.2)';this.style.borderColor='#6A4DF7';" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 12px rgba(2,6,23,0.12)';this.style.borderColor='rgba(0,0,0,0.1)';">
                <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:8px;">Perbandingan Prestasi Kelas</div>
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
        </section>

        {{-- ===================== STUDENT REPORT ===================== --}}
        <section class="panel" style="margin-bottom:20px; margin-top:10px;">
            <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; line-height:1;">Laporan Pelajar</h2>
            <div style="display:flex;gap:0;align-items:flex-start;margin-bottom:12px;flex-wrap:wrap;">
                <div style="flex:1;min-width:200px;">
                    <label style="font-size:12px;">Kelas</label>
                    <select id="class-select" class="select" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;" required>
                        <option value="">{{ $classPlaceholder ?? '-- pilih kelas --' }}</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="flex:2;min-width:300px;margin-left:16px;">
                    <label style="font-size:12px;">Pelajar</label>
                    <select id="student-select" class="select" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;" required>
                        <option value="">{{ $studentPlaceholder ?? '-- pilih pelajar --' }}</option>
                    </select>
                </div>

                <button id="open-student" class="btn"
                    style="padding:8px 12px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;border:0;font-weight:700;margin-top:20px;margin-left:16px;cursor:pointer;">
                    Buka
                </button>
            </div>

            <div id="student-panel-wrap" style="display:none;">
            </div>


        </section>


        {{-- ===================== CLASS REPORT ===================== --}}
        <section class="panel" style="margin-bottom:20px; margin-top:10px;">
            <h2 style="margin:0 0 20px 0; font-size:18px; font-weight:700; line-height:1;">Laporan Kelas</h2>
            <div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:12px;flex-wrap:wrap;">
                <div style="flex:1;min-width:220px;">
                    <label style="font-size:12px;">Pilih Kelas</label>
                    <select id="class-report-select" class="select" style="height:40px; width:100%; padding:8px 12px; border-radius:8px; border:2px solid #d1d5db; box-sizing:border-box; font-size:12px;" required>
                        <option value="">{{ $classPlaceholder ?? '-- pilih kelas --' }}</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <button id="open-class" class="btn"
                    style="padding:8px 12px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;border:0;font-weight:700;margin-top:20px;cursor:pointer;">
                    Buka
                </button>
            </div>
        </section>
    </main>
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

// Load student panel in-place via AJAX (no redirect)
openStudentBtn.addEventListener('click', async () => {
    const classValue = classSelect.value;
    const studentValue = studentSelect.value;

    // Trigger HTML5 native validation
    if (!classSelect.reportValidity()) {
        return;
    }
    
    if (!studentSelect.reportValidity()) {
        return;
    }

    try {
        const res = await fetch(`/reports/student/${encodeURIComponent(studentValue)}`, {
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

    // Trigger HTML5 native validation
    if (!classReportSelect.reportValidity()) {
        return;
    }

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
    const statsClassSelect = document.getElementById('stats-class-select');
    const statsDateRangeSelect = document.getElementById('stats-date-range');
    const selectedClass = statsClassSelect.value;
    const dateRange = statsDateRangeSelect.value;
    
    // Check if both class and date range are selected
    if (!selectedClass || !dateRange) {
        // Show validation error on the empty field
        if (!selectedClass) {
            statsClassSelect.reportValidity();
        } else {
            statsDateRangeSelect.reportValidity();
        }
        
        const tbody = document.getElementById('stats-tbody');
        tbody.innerHTML = '<tr><td colspan="5" style="padding:20px;text-align:center;color:var(--muted);">Sila pilih kelas dan jangka masa untuk melihat data.</td></tr>';
        document.getElementById('stats-table-container').style.display = 'none';
        // Show placeholders and hide charts when no selection
        document.getElementById('topicChartPlaceholder').style.display = 'flex';
        document.getElementById('topicChart').style.display = 'none';
        document.getElementById('trendChartPlaceholder').style.display = 'flex';
        document.getElementById('trendChart').style.display = 'none';
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
        document.getElementById('stat-avg-grade').textContent = data.avgGrade !== undefined ? data.avgGrade : 'N/A';
        document.getElementById('stat-total-attempts').textContent = data.totalAttempts !== undefined ? data.totalAttempts : '0';
        document.getElementById('stat-active-students').textContent = data.activeStudents !== undefined ? data.activeStudents : '0';

        // Only draw charts if Chart.js is available
        if (typeof Chart !== 'undefined') {
            // Update topic chart
            if (data.topicData && data.topicData.labels && data.topicData.labels.length > 0) {
                updateTopicChart(data.topicData);
            } else {
                const ctx = document.getElementById('topicChart').getContext('2d');
                const placeholder = document.getElementById('topicChartPlaceholder');
                const canvas = document.getElementById('topicChart');
                const containerDiv = canvas.parentElement;
                if (statsChart) statsChart.destroy();
                statsChart = new Chart(ctx, {
                    type: 'bar',
                    data: { labels: ['Tiada data'], datasets: [{ label: 'N/A', data: [0], backgroundColor: '#ccc' }] },
                    options: { responsive: true, maintainAspectRatio: true, indexAxis: 'y' }
                });
                placeholder.style.display = 'none';
                canvas.style.display = 'block';
                containerDiv.style.borderStyle = 'solid';
            }

            // Update trend chart
            if (data.trendData && data.trendData.dates && data.trendData.dates.length > 0) {
                updateTrendChart(data.trendData);
            } else {
                const ctx = document.getElementById('trendChart').getContext('2d');
                const placeholder = document.getElementById('trendChartPlaceholder');
                const canvas = document.getElementById('trendChart');
                const containerDiv = canvas.parentElement;
                if (trendChart) trendChart.destroy();
                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: { labels: ['Tiada data'], datasets: [{ label: 'N/A', data: [0], borderColor: '#ccc' }] },
                    options: { responsive: true, maintainAspectRatio: true }
                });
                placeholder.style.display = 'none';
                canvas.style.display = 'block';
                containerDiv.style.borderStyle = 'solid';
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
    const placeholder = document.getElementById('topicChartPlaceholder');
    const canvas = document.getElementById('topicChart');
    const containerDiv = canvas.parentElement;
    
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
    
    placeholder.style.display = 'none';
    canvas.style.display = 'block';
    containerDiv.style.borderStyle = 'solid';
}

function updateTrendChart(data) {
    const ctx = document.getElementById('trendChart').getContext('2d');
    const placeholder = document.getElementById('trendChartPlaceholder');
    const canvas = document.getElementById('trendChart');
    const selectedClass = document.getElementById('stats-class-select').value;
    
    if (trendChart) trendChart.destroy();
    
    let datasets = [];
    let counts = null;
    
    // If "semua" is selected and we have per-class data, create multiple lines
    if (selectedClass === 'semua' && data.byClass && Object.keys(data.byClass).length > 0) {
        const colors = [
            { border: 'rgba(102, 126, 234, 1)', bg: 'rgba(102, 126, 234, 0.1)' },
            { border: 'rgba(244, 67, 54, 1)', bg: 'rgba(244, 67, 54, 0.1)' },
            { border: 'rgba(76, 175, 80, 1)', bg: 'rgba(76, 175, 80, 0.1)' },
            { border: 'rgba(255, 152, 0, 1)', bg: 'rgba(255, 152, 0, 0.1)' },
            { border: 'rgba(0, 150, 136, 1)', bg: 'rgba(0, 150, 136, 0.1)' }
        ];
        
        let colorIndex = 0;
        for (const [className, scores] of Object.entries(data.byClass)) {
            const color = colors[colorIndex % colors.length];
            datasets.push({
                label: className,
                data: scores || [],
                borderColor: color.border,
                backgroundColor: color.bg,
                borderWidth: 2,
                fill: false,
                tension: 0.4
            });
            colorIndex++;
        }
        // Get counts for tooltips
        counts = data.countsByClass;
    } else {
        // Single line for overall or specific class
        datasets.push({
            label: 'Prestasi Purata',
            data: data.scores || [],
            borderColor: 'rgba(102, 126, 234, 1)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        });
        // Get counts for tooltips
        counts = { 'Prestasi Purata': data.counts || [] };
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
                legend: { display: true },
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            if (!counts) return '';
                            const datasetLabel = context.dataset.label;
                            const dataIndex = context.dataIndex;
                            
                            if (counts[datasetLabel] && counts[datasetLabel][dataIndex] !== undefined) {
                                const studentCount = counts[datasetLabel][dataIndex];
                                return 'Pelajar: ' + studentCount;
                            }
                            return '';
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
    
    placeholder.style.display = 'none';
    canvas.style.display = 'block';
    containerDiv.style.borderStyle = 'solid';
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
    const statsClassSelect = document.getElementById('stats-class-select');
    const statsDateRangeSelect = document.getElementById('stats-date-range');
    const selectedClass = statsClassSelect.value;
    const dateRange = statsDateRangeSelect.value;
    
    // Check if both are selected
    if (!selectedClass) {
        statsClassSelect.reportValidity();
        return;
    }
    if (!dateRange) {
        statsDateRangeSelect.reportValidity();
        return;
    }
    
    window.location.href = `/reports/export-statistics?class=${encodeURIComponent(selectedClass)}&range=${dateRange}`;
});

// Load statistics on page load
window.addEventListener('load', function() {
    console.log('Page loaded, Chart available:', typeof Chart !== 'undefined');
    loadStatistics();
});
</script>
@endsection
