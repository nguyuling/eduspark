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

    <div id="student-panel-wrap">
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
        if (!contentType || !contentType.includes('application/json')) {
            // Server returned HTML instead of JSON — render it directly as page update
            const html = await res.text();
            document.getElementById('student-panel-wrap').innerHTML = html;
        } else {
            // Server returned JSON with HTML payload
            const json = await res.json();
            if (json && json.html) {
                document.getElementById('student-panel-wrap').innerHTML = json.html;
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
</script>
@endsection
