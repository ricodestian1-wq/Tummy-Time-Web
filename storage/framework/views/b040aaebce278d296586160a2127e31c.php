

<?php $__env->startSection('title', 'Pelanggan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page active" id="page-customers">
  <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div class="page-title">👥 Pelanggan Terdaftar</div>
      <div class="page-sub">Lihat pelanggan yang memiliki akun dan frekuensi pesanannya</div>
    </div>
    <div class="search-box">
      <span class="search-icon">🔍</span>
      <input type="search" placeholder="Cari pelanggan..." oninput="filterCustomerRows(this.value)">
    </div>
  </div>

  <div class="stats-grid" style="margin-bottom:20px">
    <div class="stat-card"><div class="stat-icon blue">👥</div><div><div class="stat-label">Total pelanggan</div><div class="stat-value"><?php echo e($customers->count()); ?></div></div></div>
    <div class="stat-card"><div class="stat-icon green">🛒</div><div><div class="stat-label">Total pesanan akun</div><div class="stat-value"><?php echo e($customers->sum('orders_count')); ?></div></div></div>
    <div class="stat-card"><div class="stat-icon orange">⭐</div><div><div class="stat-label">Pelanggan aktif</div><div class="stat-value"><?php echo e($customers->where('orders_count', '>', 0)->count()); ?></div></div></div>
  </div>

  <div class="table-card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Nama pelanggan</th><th>Email</th><th>No. HP</th><th>Terdaftar</th><th>Pesanan</th><th>Pesanan terakhir</th></tr>
        </thead>
        <tbody id="customer-tbody">
          <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr data-customer-search="<?php echo e(strtolower($customer->name . ' ' . $customer->email . ' ' . ($customer->phone ?? ''))); ?>">
              <td><strong><?php echo e($customer->name); ?></strong></td>
              <td><?php echo e($customer->email); ?></td>
              <td><?php echo e($customer->phone ?: '-'); ?></td>
              <td><?php echo e($customer->created_at->format('d M Y')); ?></td>
              <td><span class="customer-order-count"><?php echo e($customer->orders_count); ?>x</span></td>
              <td><?php echo e($customer->orders_max_created_at ? \Illuminate\Support\Carbon::parse($customer->orders_max_created_at)->format('d M Y H:i') : 'Belum pernah pesan'); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" style="text-align:center;padding:24px;color:#999">Belum ada pelanggan terdaftar.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  function filterCustomerRows(query) {
    const normalized = query.toLowerCase().trim();
    document.querySelectorAll('#customer-tbody tr[data-customer-search]').forEach((row) => {
      row.style.display = row.dataset.customerSearch.includes(normalized) ? '' : 'none';
    });
  }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tummy-time-laravel\resources\views/admin/customers.blade.php ENDPATH**/ ?>