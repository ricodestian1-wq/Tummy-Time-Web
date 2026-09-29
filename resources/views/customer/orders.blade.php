<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Pesanan - Tummy Time</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/customer-orders.css') }}">
</head>
<body class="customer-orders-page">
  <main class="orders-shell">
    <header class="orders-topbar">
      <a class="orders-brand" href="{{ route('home') }}"><span class="orders-brand-mark">🍗</span><span>TUMMY TIME</span></a>
      <div class="orders-actions">
        <a class="orders-link" href="{{ route('home') }}">&larr; Pesan lagi</a>
        <form method="POST" action="{{ route('customer.logout') }}">@csrf<button class="orders-logout" type="submit">Keluar</button></form>
      </div>
    </header>

    <section class="orders-hero">
      <div>
        <div class="orders-eyebrow">Akun kamu</div>
        <h1 class="orders-title">Riwayat Pesanan</h1>
        <p class="orders-subtitle">Semua pesanan yang pernah kamu buat tersimpan di sini. Pantau detail dan statusnya dengan mudah.</p>
      </div>
      <div class="orders-welcome">Halo, {{ auth('customer')->user()->name }}!</div>
    </section>

    <section class="orders-summary" aria-label="Ringkasan pesanan">
      <div class="summary-card"><div class="summary-icon">🧾</div><div><div class="summary-label">Total pesanan</div><div class="summary-value">{{ $orders->count() }}</div></div></div>
      <div class="summary-card"><div class="summary-icon">💰</div><div><div class="summary-label">Total belanja</div><div class="summary-value">Rp {{ number_format($orders->sum('total'), 0, ',', '.') }}</div></div></div>
      <div class="summary-card"><div class="summary-icon">⭐</div><div><div class="summary-label">Pesanan selesai</div><div class="summary-value">{{ $orders->where('status', 'done')->count() }}</div></div></div>
    </section>

    <section class="orders-list">
      @forelse($orders as $order)
        @php
          $statusLabels = ['pending' => 'Menunggu', 'confirmed' => 'Dikonfirmasi', 'cooking' => 'Sedang dimasak', 'ready' => 'Siap diambil', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'];
        @endphp
        <article class="order-card">
          <header class="order-card-head">
            <div><div class="order-code">{{ $order->order_code }}</div><div class="order-date">{{ $order->created_at->format('d M Y, H:i') }}</div></div>
            <span class="order-status status-{{ $order->status }}">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span>
          </header>
          <div class="order-card-body">
            <div class="order-items">
              @foreach($order->items as $item)
                <div class="order-item"><span class="order-item-name">{{ $item->qty }}x {{ $item->menu_name }}</span><span class="order-item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span></div>
              @endforeach
            </div>
            <div class="order-total"><div class="order-total-label">Total pembayaran</div><div class="order-total-value">Rp {{ number_format($order->total, 0, ',', '.') }}</div></div>
          </div>
        </article>
      @empty
        <div class="orders-empty"><div class="orders-empty-icon">🛒</div><h2>Belum ada pesanan</h2><p>Pesanan pertamamu akan muncul di halaman ini.</p><a class="orders-primary" href="{{ route('home') }}">Lihat Menu</a></div>
      @endforelse
    </section>
  </main>
</body>
</html>