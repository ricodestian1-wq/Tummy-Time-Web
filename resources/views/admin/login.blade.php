<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin - Tummy Time</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@400;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-admin-page">
  <section class="admin-brand-panel">
    <div class="brand-eyebrow">Back of House Access</div>
    <h1 class="brand-logo">TUMMY<br>TIME<span>.</span></h1>
    <p class="brand-copy">Dari dapur ke pelanggan, secepat mungkin. Masuk untuk kelola menu, pantau pesanan masuk, dan jaga dapur tetap ngebut.</p>
    <div class="ticket-stack">
      <div class="chip chip-3"><div class="chip-label">Dapur</div><div class="chip-text">Stok ayam ✓ aman</div></div>
      <div class="chip chip-2"><div class="chip-label">Menu</div><div class="chip-text">Update harga &amp; foto</div></div>
      <div class="chip chip-1"><div class="chip-label">Pesanan</div><div class="chip-text">Pantau status realtime</div></div>
    </div>
  </section>
  <section class="admin-form-panel">
    <a href="{{ route('home') }}" class="back-link">&larr; Kembali ke Tummy Time</a>
    <div class="ticket-card">
      <div class="ticket-head"><span class="ticket-serial">TICKET NO. ADM-0001</span><span class="ticket-tag">ADMIN ONLY</span></div>
      <div class="perforation"></div>
      <div class="ticket-body">
        <h2 class="ticket-form-title">Masuk Dashboard</h2>
        <p class="ticket-form-sub">Khusus staf Tummy Time yang terdaftar.</p>
        @if ($errors->any())<div class="ticket-error">⚠ {{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('admin.login.submit') }}">@csrf
          <div class="ticket-group"><label class="ticket-label" for="admin-username">Username</label><div class="input-wrap"><span class="input-icon">👤</span><input class="ticket-input" id="admin-username" name="username" type="text" value="{{ old('username') }}" placeholder="admin" autocomplete="username" required autofocus></div></div>
          <div class="ticket-group"><label class="ticket-label" for="admin-password">Password</label><div class="input-wrap"><span class="input-icon">🔒</span><input class="ticket-input" id="admin-password" name="password" type="password" placeholder="••••••••" autocomplete="current-password" required><button class="toggle-eye" type="button" onclick="togglePassword()">LIHAT</button></div></div>
          <button class="ticket-login-btn" type="submit">Masuk Dashboard &rarr;</button>
        </form>
      </div>
    </div>
  </section>
  <script>function togglePassword(){const input=document.getElementById('admin-password');const button=document.querySelector('.toggle-eye');input.type=input.type==='password'?'text':'password';button.textContent=input.type==='password'?'LIHAT':'SEMBUNYIKAN'}</script>
</body>
</html>
