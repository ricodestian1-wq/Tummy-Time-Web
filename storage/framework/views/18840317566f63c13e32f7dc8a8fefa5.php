<?php $__env->startSection('title', 'Laporan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page active" id="page-report">
  <div class="page-header">
    <div class="page-title">📈 Laporan Penjualan</div>
    <div class="page-sub">Ringkasan pendapatan dan analisis penjualan (7 hari terakhir)</div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">💳 Metode Pembayaran</span></div>
      <div style="padding:16px" id="payment-stats">Memuat...</div>
    </div>
    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">🏆 Menu Terlaris</span></div>
      <div style="padding:16px" id="menu-stats">Memuat...</div>
    </div>
  </div>

  <div class="table-card" style="margin-top:14px">
    <div class="table-card-header">
      <span class="table-card-title">📋 Rincian Penjualan Harian</span>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Jumlah Pesanan</th>
            <th>Total Pendapatan</th>
          </tr>
        </thead>
        <tbody id="report-tbody">
          <tr><td colspan="3" style="text-align:center;padding:24px;color:#999">Memuat laporan...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>document.addEventListener('DOMContentLoaded', loadReport);</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tummy-time-laravel\resources\views/admin/report.blade.php ENDPATH**/ ?>