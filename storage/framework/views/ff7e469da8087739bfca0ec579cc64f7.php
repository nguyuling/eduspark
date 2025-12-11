


<?php $__env->startSection('title', 'Student Report'); ?>
<?php $__env->startSection('page_title', ($student->name ?? 'N/A') . ' • Report'); ?>
<?php $__env->startSection('page_sub', 'Individual performance summary'); ?>

<?php $__env->startSection('content'); ?>
<div class="panel card" style="margin-top:18px;">
  <div style="display:flex;justify-content:space-between;align-items:center;">
    <div>
      <strong style="font-size:18px;">Pelajar</strong>
      <div style="color:var(--muted);font-size:13px;">Prestasi pelajar terpilih</div>
    </div>

    <div style="text-align:right;">
      <?php if(!empty($student->id)): ?>
        <div style="font-weight:700"><?php echo e($student->name); ?></div>
        <div style="color:var(--muted);font-size:13px;">ID: <?php echo e($student->id); ?></div>
      <?php else: ?>
        <div style="font-weight:700">N/A</div>
        <div style="color:var(--muted);font-size:13px;">ID: —</div>
      <?php endif; ?>
    </div>
  </div>

  
  <div class="cards" style="margin-top:18px; grid-template-columns: repeat(3, 1fr); gap:16px;">
    <div class="card" style="padding:16px;text-align:center;min-height:110px;">
      <div class="label" style="font-size:13px;color:var(--muted);font-weight:700;">Purata</div>
      <div class="value" style="margin-top:10px;">
        <span class="badge-pill" style="background:linear-gradient(90deg,var(--accent),var(--accent-2)); padding:6px 12px; font-size:15px;">
          <?php echo e($stats['average_score'] ?? 'N/A'); ?>

        </span>
      </div>
    </div>

    <div class="card" style="padding:16px;text-align:center;min-height:110px;">
      <div class="label" style="font-size:13px;color:var(--muted);font-weight:700;">Tertinggi</div>
      <div class="value" style="margin-top:10px;">
        <span class="badge-pill" style="background:var(--success); padding:6px 12px; font-size:15px;">
          <?php echo e($stats['highest_score'] ?? 'N/A'); ?>

        </span>
      </div>
      <?php if(!empty($stats['highest_subject'])): ?>
        <div style="color:var(--muted);font-size:12px;margin-top:6px;"><?php echo e($stats['highest_subject']); ?></div>
      <?php endif; ?>
    </div>

    <div class="card" style="padding:16px;text-align:center;min-height:110px;">
      <div class="label" style="font-size:13px;color:var(--muted);font-weight:700;">Paling Lemah</div>
      <div class="value" style="margin-top:10px;">
        <span class="badge-pill" style="background:var(--danger); padding:6px 12px; font-size:15px;">
          <?php echo e($stats['weakest_score'] ?? 'N/A'); ?>

        </span>
      </div>
      <?php if(!empty($stats['weakest_subject'])): ?>
        <div style="color:var(--muted);font-size:12px;margin-top:6px;"><?php echo e($stats['weakest_subject']); ?></div>
      <?php endif; ?>
    </div>
  </div>

  <hr style="margin:16px 0;border:none;border-top:1px solid rgba(255,255,255,0.06)">

  <div style="font-weight:700;margin-bottom:8px;">Rekod Percubaan</div>

  <?php
    $attempts = $stats['attempts'] ?? [];
  ?>

  <?php if(empty($attempts) || count($attempts) === 0): ?>
    <div style="color:var(--muted);padding:8px 0;">Tiada rekod percubaan.</div>
  <?php else: ?>
    <div style="overflow:auto;">
      <table style="width:100%;border-collapse:collapse;">
        <thead style="text-align:left;color:var(--muted);font-size:13px;">
          <tr>
            <th style="padding:8px 6px;">Tarikh</th>
            <th style="padding:8px 6px;">Jenis</th>
            <th style="padding:8px 6px;">Topik</th>
            <th style="padding:8px 6px;">Skor</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $attempts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);"><?php echo e($row['date'] ?? $row->date ?? '-'); ?></td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);"><?php echo e(ucfirst($row['type'] ?? ($row->type ?? ''))); ?></td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);"><?php echo e($row['topic'] ?? $row->topic ?? '-'); ?></td>
              <td style="padding:8px 6px;border-top:1px solid rgba(0,0,0,0.04);"><?php echo e($row['score'] ?? $row->score ?? ''); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div style="display:flex;gap:10px;margin-top:12px;">
      <?php if(!empty($student->id)): ?>
        <a href="<?php echo e(route('reports.student.csv', $student->id)); ?>"
           class="btn"
           style="text-decoration:none;padding:8px 14px;border-radius:8px;background:#f5f5f7;color:#111;">CSV</a>

        <a href="<?php echo e(route('reports.student.print', $student->id)); ?>"
           class="btn"
           style="text-decoration:none;padding:8px 14px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:white;">PDF</a>
      <?php else: ?>
        <button disabled class="btn" style="padding:8px 14px;border-radius:8px;background:#ddd;color:#999;">CSV</button>
        <button disabled class="btn" style="padding:8px 14px;border-radius:8px;background:#ddd;color:#999;">PDF</button>
      <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\php\myapp\resources\views/reports/student.blade.php ENDPATH**/ ?>