<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Pelanggan - Tummy Time</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(asset('css/auth.css')); ?>">
</head>
<body class="auth-customer">
  <section class="hero-panel">
    <div class="auth-visual-content">
      <span class="badge-open">Buka sekarang - 10.00-22.00</span>
      <div class="hero-avatar"><img src="https://i.pinimg.com/1200x/77/a5/9f/77a59fbdefff59e775c80a5d961cc2e3.jpg" alt="Ayam Crispy Tummy Time"></div>
      <h1 class="hero-title">Lapar lagi? <em>Kami tunggu</em> pesananmu.</h1>
      <p class="hero-subtitle">Masuk untuk lihat riwayat pesanan, simpan alamat favorit, dan checkout lebih cepat tiap kali lapar menyerang.</p>
    </div>
  </section>
  <section class="form-panel">
    <div class="tag-wrap">
      <div class="tag-string"></div>
      <div class="tag-card">
        <div class="tag-hole"></div>
        <section class="auth-login">
          <span class="tag-pill">Member Tummy Time</span>
          <h2 class="form-title">Selamat Datang Kembali</h2>
          <p class="form-sub">Masuk pakai nomor HP atau email kamu.</p>
          <?php if($errors->any()): ?><div class="login-error">⚠ <?php echo e($errors->first()); ?></div><?php endif; ?>
          <form method="POST" action="<?php echo e(route('customer.login.submit')); ?>"><?php echo csrf_field(); ?>
            <div class="form-group"><label class="form-label" for="login-email">No. HP atau Email</label><input class="form-input" id="login-email" name="email" type="email" value="<?php echo e(old('email')); ?>" placeholder="email@kamu.com" autocomplete="username" required autofocus></div>
            <div class="form-group"><label class="form-label" for="login-password">Password</label><input class="form-input" id="login-password" name="password" type="password" placeholder="••••••••" autocomplete="current-password" required></div>
            <div class="form-extra"><label><input type="checkbox" name="remember"> Ingat saya</label><a href="#">Lupa password?</a></div>
            <button class="login-btn" type="submit">Masuk &amp; Pesan &rarr;</button>
          </form>
          <p class="signup-note">Belum punya akun? <a href="#register" onclick="showRegister(event)">Daftar di sini</a></p>
        </section>
        <section class="auth-register">
          <span class="tag-pill">Daftar Member</span>
          <h2 class="form-title">Buat Akun Baru</h2>
          <p class="form-sub">Simpan data untuk checkout lebih cepat.</p>
          <?php if($errors->any()): ?><div class="login-error">⚠ <?php echo e($errors->first()); ?></div><?php endif; ?>
          <form method="POST" action="<?php echo e(route('customer.register')); ?>"><?php echo csrf_field(); ?>
            <div class="form-group"><label class="form-label" for="register-name">Nama Lengkap</label><input class="form-input" id="register-name" name="name" value="<?php echo e(old('name')); ?>" placeholder="Nama kamu" required autofocus></div>
            <div class="form-group"><label class="form-label" for="register-email">Email</label><input class="form-input" id="register-email" name="email" type="email" value="<?php echo e(old('email')); ?>" placeholder="email@kamu.com" required></div>
            <div class="form-group"><label class="form-label" for="register-phone">Nomor HP</label><input class="form-input" id="register-phone" name="phone" type="tel" value="<?php echo e(old('phone')); ?>" placeholder="0812xxxxxx"></div>
            <div class="form-group"><label class="form-label" for="register-password">Password</label><input class="form-input" id="register-password" name="password" type="password" placeholder="••••••••" required></div>
            <div class="form-group"><label class="form-label" for="register-confirmation">Ulangi Password</label><input class="form-input" id="register-confirmation" name="password_confirmation" type="password" placeholder="••••••••" required></div>
            <button class="login-btn" type="submit">Daftar &amp; Pesan &rarr;</button>
          </form>
          <p class="signup-note">Sudah punya akun? <a href="#login" onclick="showLogin(event)">Masuk di sini</a></p>
        </section>
      </div>
    </div>
  </section>
  <script>function showRegister(event){event.preventDefault();document.body.classList.add('is-register')}function showLogin(event){event.preventDefault();document.body.classList.remove('is-register')}</script>
</body>
</html>
<?php /**PATH C:\laragon\www\tummy-time-laravel\resources\views\customer\login.blade.php ENDPATH**/ ?>