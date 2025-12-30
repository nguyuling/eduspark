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
</script>
@endsection
