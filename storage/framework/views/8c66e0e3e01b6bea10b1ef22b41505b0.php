<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Pesanan - Tummy Time</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(asset('css/customer-orders.css')); ?>">
</head>
<body class="customer-orders-page">
  <main class="orders-shell">
    <header class="orders-topbar">
      <a class="orders-brand" href="<?php echo e(route('home')); ?>"><span class="orders-brand-mark">🍗</span><span>TUMMY TIME</span></a>
      <div class="orders-actions">
        <a class="orders-link" href="<?php echo e(route('home')); ?>">&larr; Pesan lagi</a>
        <form method="POST" action="<?php echo e(route('customer.logout')); ?>"><?php echo csrf_field(); ?><button class="orders-logout" type="submit">Keluar</button></form>
      </div>
    </header>

    <section class="orders-hero">
      <div>
        <div class="orders-eyebrow">Akun kamu</div>
        <h1 class="orders-title">Riwayat Pesanan</h1>
        <p class="orders-subtitle">Semua pesanan yang pernah kamu buat tersimpan di sini. Pantau detail dan statusnya dengan mudah.</p>
      </div>
      <div class="orders-welcome">Halo, <?php echo e(auth('customer')->user()->name); ?>!</div>
    </section>

    <section class="orders-summary" aria-label="Ringkasan pesanan">
      <div class="summary-card"><div class="summary-icon">🧾</div><div><div class="summary-label">Total pesanan</div><div class="summary-value"><?php echo e($orders->count()); ?></div></div></div>
      <div class="summary-card"><div class="summary-icon">💰</div><div><div class="summary-label">Total belanja</div><div class="summary-value">Rp <?php echo e(number_format($orders->sum('total'), 0, ',', '.')); ?></div></div></div>
      <div class="summary-card"><div class="summary-icon">⭐</div><div><div class="summary-label">Pesanan selesai</div><div class="summary-value"><?php echo e($orders->where('status', 'done')->count()); ?></div></div></div>
    </section>

    <section class="orders-list">
      <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $statusLabels = ['pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'cooking' => 'Sedang dimasak', 'ready' => 'Siap diambil', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'];
        ?>
        <article class="order-card">
          <header class="order-card-head">
            <div><div class="order-code"><?php echo e($order->order_code); ?></div><div class="order-date"><?php echo e($order->created_at->format('d M Y, H:i')); ?></div></div>
            <span class="order-status status-<?php echo e($order->status); ?>"><?php echo e($statusLabels[$order->status] ?? ucfirst($order->status)); ?></span>
          </header>
          <div class="order-card-body">
            <div class="order-items">
              <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="order-item"><span class="order-item-name"><?php echo e($item->qty); ?>x <?php echo e($item->menu_name); ?></span><span class="order-item-price">Rp <?php echo e(number_format($item->subtotal, 0, ',', '.')); ?></span></div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="order-total"><div class="order-total-label">Total pembayaran</div><div class="order-total-value">Rp <?php echo e(number_format($order->total, 0, ',', '.')); ?></div></div>
          </div>
        </article>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="orders-empty"><div class="orders-empty-icon">🛒</div><h2>Belum ada pesanan</h2><p>Pesanan pertamamu akan muncul di halaman ini.</p><a class="orders-primary" href="<?php echo e(route('home')); ?>">Lihat Menu</a></div>
      <?php endif; ?>
    </section>
  </main>
</body>
</html><?php /**PATH C:\laragon\www\tummy-time-laravel\resources\views\customer\orders.blade.php ENDPATH**/ ?>