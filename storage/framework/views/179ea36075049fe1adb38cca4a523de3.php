<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — Tummy Time</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(asset('css/admin.css')); ?>">
</head>
<body>

<div class="login-overlay" id="login-overlay" style="display:flex">
  <div class="login-box">
    <div class="login-logo">TUMMY TIME</div>
    <div class="login-sub">🔐 Admin Dashboard</div>

    <?php if($errors->any()): ?>
      <div class="login-error" style="display:block"><?php echo e($errors->first()); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.login.submit')); ?>">
      <?php echo csrf_field(); ?>
      <div class="form-group">
        <label class="form-label">Username</label>
        <input class="form-input" name="username" type="text" placeholder="admin" autocomplete="username" value="<?php echo e(old('username')); ?>" autofocus>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input class="form-input" name="password" type="password" placeholder="••••••••" autocomplete="current-password">
      </div>
      <button class="login-btn" type="submit">Masuk</button>
    </form>
  </div>
</div>

</body>
</html>
<?php /**PATH C:\laragon\www\tummy-time-laravel\resources\views/admin/login.blade.php ENDPATH**/ ?>