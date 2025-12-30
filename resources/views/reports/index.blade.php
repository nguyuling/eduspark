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

    {{-- ===================== STUDENT REPORT ===================== --}}
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
        <div style="display:flex; gap:8px; align-items:center;">
          <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Laporan Pelajar</h2>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:0.6fr 1.4fr;gap:16px;margin-bottom:12px;">
        <div style="display:flex;flex-direction:column;gap:6px;">
          <label style="font-weight:700;color:var(--muted);">Kelas</label>
          <select id="class-select" class="select" style="padding:8px;border-radius:8px;border:2px solid #d1d5db;background:transparent;color:inherit;height:40px;box-sizing:border-box;">
            <option value="">{{ $classPlaceholder ?? '-- pilih kelas --' }}</option>
            @foreach($classes as $c)
              <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
          </select>
        </div>

        <div style="display:flex;flex-direction:column;gap:6px;">
          <label style="font-weight:700;color:var(--muted);">Pelajar</label>
          <div style="display:flex; gap:8px; align-items:center;">
            <select id="student-select" class="select" style="flex:1; padding:8px;border-radius:8px;border:2px solid #d1d5db;background:transparent;color:inherit;height:40px;box-sizing:border-box;">
              <option value="">{{ $studentPlaceholder ?? '-- pilih pelajar --' }}</option>
            </select>
            <button type="button" onclick="clearBothSelects()" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; border:none; box-shadow:none; color:var(--accent); padding:8px; cursor:pointer; font-size:20px; transition:opacity .2s ease; height:40px; width:40px;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Ulang Pilihan">
              <i class="bi bi-arrow-repeat"></i>
            </button>
          </div>
        </div>
      </div>

      <div id="student-panel-wrap" style="display:none;">
        @php
          $dummyStudent = (object)['id'=>null,'name'=>'Tiada'];
          $dummyStats = [
            'average_score'=>'Tiada',
            'highest_score'=>'Tiada',
            'weakest_score'=>'Tiada',
            'highest_subject'=>null,
            'weakest_subject'=>null,
            'attempts'=>[]
          ];
        @endphp
        @include('reports.partials.student_panel', [
          'student' => $dummyStudent,
          'stats' => $dummyStats
        ])
      </div>
    </section>

    {{-- ===================== STATISTICS ===================== --}}
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
        <div style="display:flex; gap:8px; align-items:center;">
          <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Statistik Prestasi</h2>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:16px;margin-bottom:20px;">
        <div style="display:flex;flex-direction:column;gap:6px;">
          <label style="font-weight:700;color:var(--muted);">Kelas</label>
          <select id="stats-class-select" class="select" style="padding:8px;border-radius:8px;border:2px solid #d1d5db;background:transparent;color:inherit;height:40px;box-sizing:border-box;">
            <option value="">--Sila Pilih--</option>
            <option value="semua">Semua Kelas</option>
            @foreach($classes as $c)
              <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
          </select>
        </div>

        <div style="display:flex;flex-direction:column;gap:6px;">
          <label style="font-weight:700;color:var(--muted);">Jangka Masa</label>
          <select id="stats-date-range" class="select" style="padding:8px;border-radius:8px;border:2px solid #d1d5db;background:transparent;color:inherit;height:40px;box-sizing:border-box;">
            <option value="">--Sila Pilih--</option>
            <option value="week">Minggu Ini</option>
            <option value="month">Bulan Ini</option>
            <option value="quarter">Suku Tahun Ini</option>
            <option value="all">Semua Masa</option>
          </select>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(4, 1fr);gap:12px;margin-bottom:20px;" id="stats-cards">
        <div style="padding:16px;border-radius:8px;background:var(--surface);border:1px solid #e5e7eb;">
          <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Purata Skor</div>
          <div style="font-size:24px;font-weight:700;" id="avg-score">-</div>
          <div style="font-size:11px;color:var(--muted);margin-top:4px;">%</div>
        </div>
        <div style="padding:16px;border-radius:8px;background:var(--surface);border:1px solid #e5e7eb;">
          <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Jumlah Percubaan</div>
          <div style="font-size:24px;font-weight:700;" id="total-attempts">-</div>
        </div>
        <div style="padding:16px;border-radius:8px;background:var(--surface);border:1px solid #e5e7eb;">
          <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Pelajar Aktif</div>
          <div style="font-size:24px;font-weight:700;" id="active-students">-</div>
        </div>
        <div style="padding:16px;border-radius:8px;background:var(--surface);border:1px solid #e5e7eb;">
          <div style="font-size:12px;color:var(--muted);margin-bottom:6px;">Kadar Kejayaan</div>
          <div style="font-size:24px;font-weight:700;" id="success-rate">-</div>
          <div style="font-size:11px;color:var(--muted);margin-top:4px;">%</div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:16px;margin-bottom:20px;">
        <div style="padding:16px;border-radius:8px;background:var(--surface);border:1px solid #e5e7eb;">
          <h3 style="margin:0 0 12px 0;font-size:14px;font-weight:700;">Prestasi Mengikut Topik</h3>
          <canvas id="topicChart" height="100"></canvas>
        </div>
        <div style="padding:16px;border-radius:8px;background:var(--surface);border:1px solid #e5e7eb;">
          <h3 style="margin:0 0 12px 0;font-size:14px;font-weight:700;">Tren Prestasi</h3>
          <canvas id="trendChart" height="100"></canvas>
        </div>
      </div>

      <div style="display:flex;gap:8px;margin-bottom:20px;">
        <button onclick="reloadStatistics()" style="padding:8px 16px;border-radius:6px;background:var(--accent);color:white;border:none;cursor:pointer;font-weight:600;">Muat Semula</button>
        <button onclick="exportStatistics()" style="padding:8px 16px;border-radius:6px;background:var(--muted);color:white;border:none;cursor:pointer;font-weight:600;">Eksport PDF</button>
      </div>

      <div id="stats-table-container" style="display:none;overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;">
          <thead>
            <tr style="border-bottom:2px solid #e5e7eb;">
              <th style="padding:12px;text-align:left;font-weight:700;">Kelas</th>
              <th style="padding:12px;text-align:center;font-weight:700;">Purata Skor</th>
              <th style="padding:12px;text-align:center;font-weight:700;">Tertinggi</th>
              <th style="padding:12px;text-align:center;font-weight:700;">Terendah</th>
              <th style="padding:12px;text-align:left;font-weight:700;">Kuiz Tertinggi</th>
              <th style="padding:12px;text-align:left;font-weight:700;">Kuiz Terendah</th>
            </tr>
          </thead>
          <tbody id="stats-table-body">
          </tbody>
        </table>
      </div>
    </section>

    {{-- ===================== CLASS REPORT ===================== --}}
    <section class="panel" style="margin-bottom:20px; margin-top:10px;">
      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
        <div style="display:flex; gap:8px; align-items:center;">
          <h2 style="margin:0; padding:0; font-size:18px; font-weight:700; line-height:1;">Laporan Kelas</h2>
        </div>
      </div>

      <div style="display:grid; width:350px;gap:16px;margin-bottom:12px;">
        <div style="display:flex;flex-direction:column;gap:6px;">
          <label style="font-weight:700;color:var(--muted);">Kelas</label>
          <div style="display:flex; gap:8px; align-items:center;">
            <select id="class-report-select" class="select" style="flex:1; padding:8px;border-radius:8px;border:2px solid #d1d5db;background:transparent;color:inherit;height:40px;box-sizing:border-box;">
              <option value="">{{ $classPlaceholder ?? '-- pilih kelas --' }}</option>
              @foreach($classes as $c)
                <option value="{{ $c }}">{{ $c }}</option>
              @endforeach
            </select>
            <button type="button" onclick="clearClassSelect()" style="display:inline-flex; align-items:center; justify-content:center; background:transparent; box-shadow:none; border:none; color:var(--accent); padding:8px; cursor:pointer; font-size:20px; transition:opacity .2s ease; height:40px; width:40px;" onmouseover="this.style.opacity='0.7';" onmouseout="this.style.opacity='1';" title="Ulang Pilihan">
              <i class="bi bi-arrow-repeat"></i>
            </button>
            
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

@endsection


@section('scripts')
<script>
// Clear both select fields and hide panel
function clearBothSelects() {
    document.getElementById('class-select').value = '';
    document.getElementById('student-select').innerHTML = '<option value="">{{ $studentPlaceholder ?? "-- pilih pelajar --" }}</option>';
    document.getElementById('student-panel-wrap').style.display = 'none';
}

// Clear class select and hide class panel
function clearClassSelect() {
    document.getElementById('class-report-select').value = '';
    document.getElementById('class-panel-wrap').innerHTML = 'Pilih kelas untuk melihat ringkasan kelas.';
    document.getElementById('class-panel-wrap').style.color = 'var(--muted)';
}

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

// Function to load student panel
async function loadStudentPanel(id) {
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
}

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

// Auto-load panel when student is selected
studentSelect && studentSelect.addEventListener('change', function() {
    const id = this.value;
    if (id) {
        loadStudentPanel(id);
    } else {
        document.getElementById('student-panel-wrap').style.display = 'none';
    }
});

// Button click also loads panel (backup option)
openStudentBtn.addEventListener('click', async () => {
    const id = studentSelect.value;
    if (!id) return alert('Sila pilih pelajar.');
    loadStudentPanel(id);
});




/* ========= CLASS AJAX LOAD ========= */

const classReportSelect = document.getElementById('class-report-select');
const classPanelWrap = document.getElementById('class-panel-wrap');

function setClassDownloadLinks(cls) {
    const csv = document.getElementById('download-class-csv-bottom');
    const pdf = document.getElementById('download-class-pdf-bottom');
    if (csv) csv.href = `/reports/class/${encodeURIComponent(cls)}/export/csv`;
    if (pdf) pdf.href = `/reports/class/${encodeURIComponent(cls)}/export/pdf`;
}

async function loadClassPanel(cls) {
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
}

// Auto-load class panel when class is selected
classReportSelect && classReportSelect.addEventListener('change', function() {
    const cls = this.value;
    if (cls) {
        loadClassPanel(cls);
    } else {
        classPanelWrap.innerHTML = 'Pilih kelas untuk melihat ringkasan kelas.';
        classPanelWrap.style.color = 'var(--muted)';
    }
});


/* ========= STATISTICS ========= */
let topicChart = null;
let trendChart = null;

const statsClassSelect = document.getElementById('stats-class-select');
const statsDateRange = document.getElementById('stats-date-range');

statsClassSelect && statsClassSelect.addEventListener('change', loadStatistics);
statsDateRange && statsDateRange.addEventListener('change', loadStatistics);

async function loadStatistics() {
    const selectedClass = statsClassSelect.value;
    
    if (!selectedClass) {
        document.getElementById('stats-table-container').style.display = 'none';
        return;
    }

    try {
        const params = new URLSearchParams({
            class: selectedClass,
            range: statsDateRange.value || ''
        });

        const res = await fetch(`/api/statistics?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const data = await res.json();
        updateStatsCards(data);
        updateCharts(data);

        if (selectedClass === 'semua') {
            updateStatsTable(data);
            document.getElementById('stats-table-container').style.display = 'block';
        } else {
            document.getElementById('stats-table-container').style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
        alert('Tidak dapat memuatkan statistik: ' + error.message);
    }
}

function updateStatsCards(data) {
    document.getElementById('avg-score').textContent = (data.avgScore || 0).toFixed(2);
    document.getElementById('total-attempts').textContent = data.totalAttempts || 0;
    document.getElementById('active-students').textContent = data.activeStudents || 0;
    document.getElementById('success-rate').textContent = (data.successRate || 0).toFixed(2);
}

function updateCharts(data) {
    // Topic Chart
    const topicCtx = document.getElementById('topicChart').getContext('2d');
    if (topicChart) topicChart.destroy();
    topicChart = new Chart(topicCtx, {
        type: 'bar',
        data: {
            labels: data.topicData?.labels || [],
            datasets: [{
                label: 'Purata Skor (%)',
                data: data.topicData?.scores || [],
                backgroundColor: '#3b82f6'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Trend Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    if (trendChart) trendChart.destroy();
    trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: data.trendData?.dates || [],
            datasets: [{
                label: 'Purata Skor (%)',
                data: data.trendData?.scores || [],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.3
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
}

function updateStatsTable(data) {
    const tbody = document.getElementById('stats-table-body');
    tbody.innerHTML = '';

    if (!data.classStats || !Array.isArray(data.classStats)) return;

    data.classStats.forEach(stat => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td style="padding:12px;border-bottom:1px solid #e5e7eb;">${stat.name}</td>
            <td style="padding:12px;text-align:center;border-bottom:1px solid #e5e7eb;">${(stat.avgScore || 0).toFixed(2)}%</td>
            <td style="padding:12px;text-align:center;border-bottom:1px solid #e5e7eb;">${(stat.maxScore || 0).toFixed(2)}%</td>
            <td style="padding:12px;text-align:center;border-bottom:1px solid #e5e7eb;">${(stat.minScore || 0).toFixed(2)}%</td>
            <td style="padding:12px;border-bottom:1px solid #e5e7eb;">${stat.highestQuiz || '-'}</td>
            <td style="padding:12px;border-bottom:1px solid #e5e7eb;">${stat.lowestQuiz || '-'}</td>
        `;
    });
}

function reloadStatistics() {
    loadStatistics();
}

async function exportStatistics() {
    const selectedClass = statsClassSelect.value;
    if (!selectedClass) {
        alert('Sila pilih kelas.');
        return;
    }

    const params = new URLSearchParams({
        class: selectedClass,
        range: statsDateRange.value || ''
    });

    window.location.href = `/reports/export-statistics?${params}`;
}
</script>
@endsection
