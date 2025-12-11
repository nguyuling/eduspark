

<?php $__env->startSection('page_title','Papan Laporan'); ?>
<?php $__env->startSection('page_sub',''); ?>

<?php $__env->startSection('content'); ?>


<div class="panel" style="padding:18px;border-radius:12px;">
    <h3 style="margin-top:0;">Laporan Pelajar</h3>

    <div style="display:flex;gap:8px;align-items:center;margin-bottom:12px;flex-wrap:wrap;">
        <label style="font-weight:700;color:var(--muted);">Kelas</label>
        <select id="class-select" class="select" style="padding:8px;border-radius:8px;min-width:160px;">
            <option value=""><?php echo e($classPlaceholder ?? '-- pilih kelas --'); ?></option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c); ?>"><?php echo e($c); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <label style="font-weight:700;color:var(--muted);">Pelajar</label>
        <select id="student-select" class="select" style="padding:8px;border-radius:8px;min-width:280px;">
            <option value=""><?php echo e($studentPlaceholder ?? '-- pilih pelajar --'); ?></option>
        </select>

        <button id="open-student" class="btn"
            style="padding:8px 12px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;border:0;font-weight:700;">
            Buka
        </button>
    </div>

    <div id="student-panel-wrap">
        <?php
            $dummyStudent = (object)['id'=>null,'name'=>'Tiada'];
            $dummyStats = [
                'average_score'=>'Tiada',
                'highest_score'=>'Tiada',
                'weakest_score'=>'Tiada',
                'highest_subject'=>null,
                'weakest_subject'=>null,
                'attempts'=>[]
            ];
        ?>
        <?php echo $__env->make('reports.partials.student_panel', [
            'student' => $dummyStudent,
            'stats' => $dummyStats
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>



<div class="panel" style="padding:18px;border-radius:12px;margin-top:16px;">
    <h3 style="margin-top:0;">Laporan Kelas</h3>

    <div style="display:flex;gap:8px;align-items:center;margin-bottom:12px;flex-wrap:wrap;">
        <label style="font-weight:700;color:var(--muted);">Pilih kelas</label>

        <select id="class-report-select" class="select" style="padding:8px;border-radius:8px;min-width:160px;">
            <option value=""><?php echo e($classPlaceholder ?? '-- pilih kelas --'); ?></option>
            <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c); ?>"><?php echo e($c); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
<script>
function fillStudentSelect(selectEl, items) {
    selectEl.innerHTML = '<option value=""><?php echo e($studentPlaceholder ?? "-- pilih pelajar --"); ?></option>';
    items.forEach(it => {
        const opt = document.createElement('option');
        opt.value = it.id;
        opt.textContent = it.name;
        selectEl.appendChild(opt);
    });
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
        alert('Tidak dapat memuatkan senarai pelajar.');
    }
});

// NEW: load student panel in-place via AJAX (no redirect)
openStudentBtn.addEventListener('click', async () => {
    const id = studentSelect.value;
    if (!id) return alert('Sila pilih pelajar.');

    try {
        const res = await fetch(`/reports/student/${encodeURIComponent(id)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const json = await res.json();
        // server returns { html: '...' } when AJAX
        if (json && json.html) {
            document.getElementById('student-panel-wrap').innerHTML = json.html;
        } else {
            // fallback — if server returned full page (not expected), redirect
            window.location.href = `/reports/student/${id}`;
        }
    } catch (err) {
        console.error(err);
        alert('Tidak dapat memuatkan laporan pelajar.');
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\php\myapp\resources\views/reports/index.blade.php ENDPATH**/ ?>