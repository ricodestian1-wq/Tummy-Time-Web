<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title><?php echo $__env->yieldContent('title', 'Tummy Time — Pesan Sekarang'); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800;900&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
  <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
  <?php echo $__env->yieldContent('content'); ?>

  <script>
    window.CSRF_TOKEN = "<?php echo e(csrf_token()); ?>";
  </script>
  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\tummy-time-laravel\resources\views\layouts\app.blade.php ENDPATH**/ ?>