<?php $__env->startSection('title', 'Pesanan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page active" id="page-orders">
  <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div class="page-title">🛒 Manajemen Pesanan</div>
      <div class="page-sub">Lihat dan kelola semua pesanan masuk</div>
    </div>
    <div class="search-box">
      <span class="search-icon">🔍</span>
      <input type="text" id="order-search" placeholder="Cari pesanan..." oninput="filterOrders(this.value)">
    </div>
  </div>

  <div class="tab-bar">
    <button class="tab-btn active" onclick="filterOrderStatus('all', this)">Semua</button>
    <button class="tab-btn" onclick="filterOrderStatus('pending', this)">Pending</button>
    <button class="tab-btn" onclick="filterOrderStatus('confirmed', this)">Dikonfirmasi</button>
    <button class="tab-btn" onclick="filterOrderStatus('cooking', this)">Dimasak</button>
    <button class="tab-btn" onclick="filterOrderStatus('ready', this)">Siap</button>
    <button class="tab-btn" onclick="filterOrderStatus('done', this)">Selesai</button>
  </div>

  <div class="table-card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Total</th>
            <th>Pembayaran</th>
            <th>Status</th>
            <th>Waktu</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="orders-tbody">
          <tr><td colspan="7" style="text-align:center;padding:24px;color:#999">Memuat data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL: ORDER DETAIL -->
<div class="modal-overlay" id="order-modal">
  <div class="modal-box" style="max-width:520px">
    <div class="modal-box-header">
      <span class="modal-box-title">Detail Pesanan</span>
      <button class="modal-box-close" onclick="closeModal('order-modal')">✕</button>
    </div>
    <div class="modal-box-body" id="order-detail-body"></div>
    <div class="modal-box-footer">
      <button class="btn btn-secondary" onclick="closeModal('order-modal')">Tutup</button>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>document.addEventListener('DOMContentLoaded', loadOrders);</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tummy-time-laravel\resources\views/admin/orders.blade.php ENDPATH**/ ?>