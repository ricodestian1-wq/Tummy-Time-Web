<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page active" id="page-dashboard">
  <div class="page-header">
    <div class="page-title">📊 Dashboard</div>
    <div class="page-sub"><?php echo e(now()->translatedFormat('l, d F Y')); ?></div>
  </div>

  <div class="stats-grid" id="stats-grid">
    <div class="stat-card"><span class="stat-icon">🛒</span><div class="stat-value"><?php echo e($ordersToday); ?></div><div class="stat-label">Pesanan Hari Ini</div></div>
    <div class="stat-card"><span class="stat-icon">💰</span><div class="stat-value">Rp <?php echo e(number_format($revenueToday, 0, ',', '.')); ?></div><div class="stat-label">Pendapatan Hari Ini</div></div>
    <div class="stat-card"><span class="stat-icon">⏳</span><div class="stat-value"><?php echo e($pendingCount); ?></div><div class="stat-label">Menunggu Konfirmasi</div></div>
    <div class="stat-card"><span class="stat-icon">✅</span><div class="stat-value"><?php echo e($doneToday); ?></div><div class="stat-label">Selesai Hari Ini</div></div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px" id="mini-grids">
    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">📦 Pesanan Terbaru</span></div>
      <div style="padding:12px">
        <?php $__empty_1 = true; $__currentLoopData = $latestOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 4px;border-bottom:1px solid var(--cream-dark)">
            <div>
              <strong><?php echo e($o->order_code); ?></strong>
              <span style="color:#999;margin-left:6px"><?php echo e($o->customer_name); ?></span>
            </div>
            <div style="text-align:right">
              <div><strong>Rp <?php echo e(number_format($o->total, 0, ',', '.')); ?></strong></div>
              <span class="badge <?php echo e($o->status === 'done' ? 'badge-available' : 'badge-unavailable'); ?>"><?php echo e(strtoupper($o->status)); ?></span>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <p style="color:#999">Belum ada pesanan.</p>
        <?php endif; ?>
      </div>
    </div>
    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">🏆 Menu Terlaris</span></div>
      <div style="padding:12px">
        <?php $__empty_1 = true; $__currentLoopData = $topMenus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div style="display:flex;justify-content:space-between;padding:8px 4px;border-bottom:1px solid var(--cream-dark)">
            <span><?php echo e($m->menu_name); ?></span>
            <strong><?php echo e($m->total_qty); ?> porsi</strong>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <p style="color:#999">Belum ada data penjualan.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tummy-time-laravel\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>