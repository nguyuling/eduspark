
<div class="card" style="padding:18px;border-radius:12px;">
    <h3 style="margin-top:0;">Ringkasan Kelas: <?php echo e($selectedClass); ?></h3>

    <div style="display:flex;gap:12px;margin-top:12px;">
        <div style="background:white;padding:16px;border-radius:10px;flex:1;">
            <strong>Bilangan Pelajar</strong>
            <div style="margin-top:6px;color:var(--muted);">
                <?php echo e($classStats['student_count'] ?? 0); ?>

            </div>
        </div>

        <div style="background:white;padding:16px;border-radius:10px;flex:1;">
            <strong>Purata Skor Kelas</strong>
            <div style="margin-top:6px;color:var(--muted);">
                <?php echo e($classStats['avg_score'] ?? 'N/A'); ?>

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
            <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td style="padding:6px;border-top:1px solid #eee;"><?php echo e($s->id); ?></td>
                    <td style="padding:6px;border-top:1px solid #eee;"><?php echo e($s->name); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="2" style="padding:10px;text-align:center;color:var(--muted);">
                        Tiada rekod pelajar.
                    </td>
                </tr>
            <?php endif; ?>
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
<?php /**PATH C:\xampp\php\myapp\resources\views/reports/partials/class_panel.blade.php ENDPATH**/ ?>