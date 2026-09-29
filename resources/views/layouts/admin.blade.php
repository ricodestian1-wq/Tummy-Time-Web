<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — Tummy Time Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
  @stack('styles')
</head>
<body>

<div class="toast" id="toast"></div>

<!-- SIDEBAR -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <h1>TUMMY TIME</h1>
    <p>Admin Panel</p>
  </div>

  <div style="padding:12px">
    @php($settings = \App\Models\Setting::current())
    <div class="shop-status {{ $settings->is_open ? 'open' : 'closed' }}" id="sidebar-shop-status">
      <span class="shop-status-label" id="shop-status-label">{{ $settings->is_open ? '🟢 BUKA' : '🔴 TUTUP' }}</span>
      <label class="toggle-switch" title="Buka/Tutup Toko">
        <input type="checkbox" id="toggle-shop" {{ $settings->is_open ? 'checked' : '' }} onchange="toggleShop()">
        <div class="toggle-track"><div class="toggle-thumb"></div></div>
      </label>
    </div>
  </div>

  <div class="nav-section">Menu Utama</div>
  <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
    <span class="nav-icon">📊</span> Dashboard
  </a>
  <a href="{{ route('admin.orders') }}" class="nav-item {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
    <span class="nav-icon">🛒</span> Pesanan
    <span id="pending-badge" style="background:var(--red);color:white;border-radius:999px;font-size:11px;padding:1px 7px;margin-left:auto;display:none">0</span>
  </a>
  <a href="{{ route('admin.customers') }}" class="nav-item {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
    <span class="nav-icon">👥</span> Pelanggan
  </a>
  <a href="{{ route('admin.menu') }}" class="nav-item {{ request()->routeIs('admin.menu') ? 'active' : '' }}">
    <span class="nav-icon">🍗</span> Menu
  </a>
  <a href="{{ route('admin.report') }}" class="nav-item {{ request()->routeIs('admin.report') ? 'active' : '' }}">
    <span class="nav-icon">📈</span> Laporan
  </a>
  <div class="nav-section">Pengaturan</div>
  <a href="{{ route('admin.settings') }}" class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
    <span class="nav-icon">⚙️</span> Pengaturan
  </a>
  <div style="flex:1"></div>
  <div style="padding:12px">
    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button type="submit" class="btn btn-secondary" style="width:100%">🚪 Logout</button>
    </form>
  </div>
</aside>

<!-- MOBILE NAV -->
<div class="mobile-nav">
  <a href="{{ route('admin.dashboard') }}" class="mob-nav-btn {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="mob-icon">📊</span>Dashboard</a>
  <a href="{{ route('admin.orders') }}" class="mob-nav-btn {{ request()->routeIs('admin.orders') ? 'active' : '' }}"><span class="mob-icon">🛒</span>Pesanan</a>
  <a href="{{ route('admin.customers') }}" class="mob-nav-btn {{ request()->routeIs('admin.customers') ? 'active' : '' }}"><span class="mob-icon">👥</span>Pelanggan</a>
  <a href="{{ route('admin.menu') }}" class="mob-nav-btn {{ request()->routeIs('admin.menu') ? 'active' : '' }}"><span class="mob-icon">🍗</span>Menu</a>
  <a href="{{ route('admin.report') }}" class="mob-nav-btn {{ request()->routeIs('admin.report') ? 'active' : '' }}"><span class="mob-icon">📈</span>Laporan</a>
  <a href="{{ route('admin.settings') }}" class="mob-nav-btn {{ request()->routeIs('admin.settings') ? 'active' : '' }}"><span class="mob-icon">⚙️</span>Setting</a>
</div>

<!-- MAIN CONTENT -->
<main class="main">
  @yield('content')
</main>

<script>
  window.CSRF_TOKEN = "{{ csrf_token() }}";
</script>
<script src="{{ asset('js/admin.js') }}?v={{ filemtime(public_path('js/admin.js')) }}"></script>
@stack('scripts')
</body>
</html>
