<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — Tummy Time</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>

<div class="login-overlay" id="login-overlay" style="display:flex">
  <div class="login-box">
    <div class="login-logo">TUMMY TIME</div>
    <div class="login-sub">🔐 Admin Dashboard</div>

    @if ($errors->any())
      <div class="login-error" style="display:block">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Username</label>
        <input class="form-input" name="username" type="text" placeholder="admin" autocomplete="username" value="{{ old('username') }}" autofocus>
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
