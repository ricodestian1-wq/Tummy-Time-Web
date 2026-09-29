<?php $__env->startSection('content'); ?>


<!-- HERO -->
<header class="hero">
  <nav class="navbar">
    <div class="logo">
      <div class="logo-text">
        TUMMY TIME
        <span>Fried Chicken • Order Online</span>
      </div>
    </div>
    <div class="nav-links">
      <button class="nav-link-btn" id="langToggleBtn" onclick="toggleLang()" title="Switch language / Ganti bahasa">🇬🇧 EN</button>
      <button class="nav-link-btn" data-i18n="nav_track" onclick="openStatusModal()">Lacak Pesanan</button>
      <?php if(auth()->guard('customer')->check()): ?>
        <button class="nav-link-btn" data-i18n="nav_history" onclick="openHistoryModal()">Riwayat</button>
        <form method="POST" action="<?php echo e(route('customer.logout')); ?>" style="display:inline"><?php echo csrf_field(); ?><button class="nav-link-btn" data-i18n="nav_logout" type="submit">Keluar</button></form>
      <?php else: ?>
        <a class="nav-link-btn" data-i18n="nav_login" href="<?php echo e(route('customer.login')); ?>">Masuk</a>
      <?php endif; ?>
      <button class="nav-link-btn cart-btn" onclick="openCart()">
        <span data-i18n="nav_cart">🛒 Keranjang</span> <span class="cart-count" id="cartCountNav">0</span>
      </button>
    </div>
  </nav>

  <div class="hero-content">
    <div class="hero-text">
      <div class="badge-open" id="hero-badge">Buka Sekarang</div>
      <h1 class="hero-title" data-i18n-html="hero_title">Ayam Crispy Paling <em>Nendang</em><br>di Kotamu</h1>
      <p class="hero-subtitle" data-i18n="hero_subtitle">Ayam Crispy, Fire Chicken, sampai Sambal Bawang — digoreng fresh begitu kamu order. Pesan online, tinggal ambil atau tunggu diantar!</p>
      <div class="hero-stats">
        <div class="stat">
          <div class="stat-num" id="stat-cat">0</div>
          <div class="stat-label" data-i18n="stat_cat">Kategori</div>
        </div>
        <div class="stat">
          <div class="stat-num" id="stat-menu">0</div>
          <div class="stat-label" data-i18n="stat_menu">Menu Tersedia</div>
        </div>
        <div class="stat">
          <div class="stat-num">⚡15'</div>
          <div class="stat-label" data-i18n="stat_ready">Siap Saji</div>
        </div>
      </div>
    </div>
    <div class="hero-image">
      <!-- Ganti URL di bawah ini kapan saja untuk mengganti foto banner -->
      <img id="hero-food-img"
           src="https://i.pinimg.com/1200x/77/a5/9f/77a59fbdefff59e775c80a5d961cc2e3.jpg"
           alt="Ayam Crispy Tummy Time">
    </div>
  </div>
</header>

<!-- TICKER STRIP -->
<div class="ticker-strip">
  <div class="ticker-track">
    <span data-i18n="ticker_1">⚡ Siap saji 15 menit</span>
    <span data-i18n="ticker_2">🔥 Level pedas sesuai selera</span>
    <span data-i18n="ticker_3">📦 Order online tanpa antri</span>
    <span data-i18n="ticker_4">🍗 Digoreng fresh, bukan basi</span>
    <span data-i18n="ticker_1">⚡ Siap saji 15 menit</span>
    <span data-i18n="ticker_2">🔥 Level pedas sesuai selera</span>
    <span data-i18n="ticker_3">📦 Order online tanpa antri</span>
    <span data-i18n="ticker_4">🍗 Digoreng fresh, bukan basi</span>
  </div>
</div>

<!-- CLOSED BANNER -->
<div id="closed-banner">
  <div class="closed-icon">🔒</div>
  <h3 data-i18n="closed_title">Toko Sedang Tutup</h3>
  <p id="closed-msg">Maaf, kami sedang tutup. Silakan order lagi ya!</p>
</div>

<!-- MAIN -->
<main class="main" id="menu">
  <div class="section-header">
    <div class="section-eyebrow" data-i18n="menu_eyebrow">Menu Kami</div>
    <h2 class="section-title" data-i18n-html="menu_title">Ayam Crispy Paling <em>Nendang</em> Sejagat</h2>
    <p class="section-subtitle" data-i18n="menu_subtitle">Klik menu buat lihat penjelasan lengkap & sisa stoknya sebelum kamu pesan.</p>
  </div>

  <div class="filter-tabs" id="filter-tabs"></div>

  <div class="menu-grid" id="menu-grid"></div>
</main>

<!-- MENU DETAIL MODAL -->
<div class="modal-overlay" id="detailModal" onclick="if(event.target===this) closeMenuDetail()">
  <div class="modal" style="max-width:420px">
    <button class="detail-modal-close" onclick="closeMenuDetail()">✕</button>
    <div class="detail-img" id="detail-img">🍗</div>
    <div class="detail-body">
      <div class="detail-name" id="detail-name">—</div>
      <div class="detail-price" id="detail-price">Rp 0</div>
      <p class="detail-desc" id="detail-desc"></p>
      <div class="detail-stock-box" id="detail-stock-box">
        <span class="detail-stock-icon">📦</span>
        <span id="detail-stock-text">—</span>
      </div>
      <div class="detail-qty-row" id="detail-qty-row">
        <span class="detail-qty-label" data-i18n="detail_qty">Jumlah</span>
        <div class="detail-qty-control">
          <button class="detail-qty-btn" onclick="detailChangeQty(-1)">−</button>
          <span class="detail-qty-num" id="detail-qty-num">1</span>
          <button class="detail-qty-btn" onclick="detailChangeQty(1)">+</button>
        </div>
      </div>
      <button class="detail-add-btn" id="detail-add-btn" data-i18n="detail_add" onclick="detailAddToCart()">🛒 Tambah ke Keranjang</button>
    </div>
  </div>
</div>

<!-- CART OVERLAY + PANEL -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-panel" id="cartPanel">
  <div class="cart-header">
    <div class="cart-title" data-i18n="cart_title">🛒 Keranjang Belanja</div>
    <button class="cart-close" onclick="closeCart()">✕</button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="cart-empty">
      <span class="cart-empty-icon">🛒</span>
      <p data-i18n="cart_empty_title">Keranjang masih kosong</p>
      <p style="font-size:0.78rem;margin-top:4px;color:#aaa" data-i18n="cart_empty_sub">Pilih menu yang kamu suka!</p>
    </div>
  </div>
  <div class="cart-footer" id="cartFooter" style="display:none">
    <div class="cart-summary">
      <div class="cart-total"><span data-i18n="cart_total">Total</span><span id="totalText">Rp 0</span></div>
    </div>
    <button class="checkout-btn" data-i18n="cart_checkout" onclick="openCheckout()">📦 Lanjut Pesan</button>
  </div>
</div>

<!-- CHECKOUT MODAL -->
<div class="modal-overlay" id="checkoutModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" data-i18n="checkout_title">📋 Detail Pesanan</div>
      <p class="modal-sub" data-i18n="checkout_sub">Lengkapi data di bawah ini</p>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label" data-i18n="f_name">Nama Lengkap *</label>
        <input class="form-input" id="f-name" type="text" data-i18n-placeholder="f_name_ph" placeholder="Masukkan nama kamu">
      </div>
      <div class="form-group">
        <label class="form-label" data-i18n="f_phone">Nomor WhatsApp *</label>
        <input class="form-input" id="f-phone" type="tel" data-i18n-placeholder="f_phone_ph" placeholder="Contoh: 08123456789">
      </div>
      <div class="form-group">
        <label class="form-label" data-i18n="f_address">Alamat Pembeli *</label>
        <textarea class="form-textarea" id="f-address" data-i18n-placeholder="f_address_ph" placeholder="Contoh: Jalan Mawar No. 12, Kelurahan Cempaka, Kota Bandung"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label" data-i18n="f_notes">Catatan (opsional)</label>
        <textarea class="form-textarea" id="f-notes" data-i18n-placeholder="f_notes_ph" placeholder="Contoh: tidak pedas, saus dipisah..."></textarea>
      </div>

      <div class="form-group">
        <label class="form-label" data-i18n="payment_method">Metode Pembayaran</label>
        <div class="payment-options">
          <div class="payment-opt selected" id="pay-cash" onclick="selectPayment('cash')">
            <span class="payment-opt-icon">💵</span>
            <span class="payment-opt-name" data-i18n="pay_cash">Tunai</span>
          </div>
          <div class="payment-opt" id="pay-qris" onclick="selectPayment('qris')">
            <span class="payment-opt-icon">📱</span>
            <span class="payment-opt-name" data-i18n="pay_qris">QRIS</span>
          </div>
        </div>

        <div id="cash-section">
          <div class="form-group" style="margin-top:12px;margin-bottom:4px">
            <label class="form-label" data-i18n="cash_paid">Uang yang dibayar</label>
            <div class="cash-input-wrap">
              <span class="cash-prefix">Rp</span>
              <input class="form-input cash-input" id="f-cash" type="number" placeholder="0" oninput="calcChange()" onchange="calcChange()">
            </div>
          </div>
          <div class="quick-cash"></div>
          <div class="change-display" id="change-display" style="display:none">
            <span class="change-label" data-i18n="change_label">💰 Kembalian</span>
            <span class="change-amount" id="change-val">Rp 0</span>
          </div>
        </div>

        <div id="qris-section">
          <div class="qris-box" id="qris-box-content"></div>
          <div class="payment-action-btns">
            <input type="file" id="proof-input" accept="image/*" style="display:none" onchange="handleProofFile(event)">
            <button type="button" class="btn-outline-action" data-i18n="proof_upload" onclick="triggerProofUpload()">📤 Kirim Bukti Pembayaran</button>
            <div class="proof-preview" id="proof-preview"></div>
            <button type="button" class="btn-outline-action secondary" data-i18n="view_status" onclick="openStatusModal()">📦 Lihat Status Pesanan</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" data-i18n="btn_cancel" onclick="closeCheckout()">Batal</button>
      <button class="btn-submit" data-i18n="btn_confirm" onclick="submitOrder()">✅ Konfirmasi Pesanan</button>
    </div>
  </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal-overlay" id="successModal">
  <div class="modal">
    <div class="success-content">
      <div class="success-icon">✅</div>
      <h2 class="success-title" data-i18n="success_title">Pesanan Diterima!</h2>
      <p style="color:var(--gray);line-height:1.7" data-i18n="success_msg">Terima kasih sudah memesan. Kami akan segera memproses pesananmu!</p>
      <div class="success-kode">
        <div class="kode-label" data-i18n="success_code_label">Kode Pesananmu</div>
        <div class="kode-value" id="kodePesananDisplay">—</div>
      </div>
      <p style="font-size:0.8rem;color:var(--gray);margin-bottom:20px" data-i18n="success_note">Simpan kode ini untuk melihat detail pesananmu nanti</p>
      <button class="checkout-btn" data-i18n="success_done" onclick="closeSuccess()">🎉 Pesanan Selesai</button>
    </div>
  </div>
</div>

<!-- STATUS PESANAN MODAL -->
<div class="modal-overlay" id="status-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title" data-i18n="status_title">📦 Status Pesanan</div>
      <button class="cart-close" onclick="closeStatusModal()" style="position:absolute;top:18px;right:18px;background:var(--cream-dark);color:var(--dark)">✕</button>
    </div>
    <div class="modal-body">
      <div class="status-hint" data-i18n="status_hint">Masukkan kode pesanan (contoh: TT123456) untuk melihat status terbaru.</div>
      <div class="status-search">
        <input class="form-input" id="status-code-input" type="text" data-i18n-placeholder="status_ph" placeholder="Kode Pesanan">
        <button class="status-search-btn" data-i18n="status_check" onclick="checkOrderStatus()">Cek</button>
      </div>
      <div id="status-result"></div>
    </div>
  </div>
</div>

<!-- RIWAYAT PESANAN MODAL -->
<div class="modal-overlay" id="history-modal" onclick="if(event.target===this) closeHistoryModal()">
  <div class="modal history-modal">
    <div class="modal-header history-modal-header">
      <div><div class="modal-title" data-i18n="history_title">🧾 Riwayat Pesanan</div><div class="modal-sub" data-i18n="history_sub">Pesanan dari akun kamu</div></div>
      <button class="cart-close" onclick="closeHistoryModal()" style="background:var(--cream-dark);color:var(--dark)">✕</button>
    </div>
    <div class="modal-body" id="history-result"><div class="history-loading">⏳ Memuat riwayat pesanan...</div></div>
  </div>
</div>

<!-- FLOATING CART BUTTON -->
<button class="float-cart" onclick="openCart()" id="floatCartBtn">
  🛒 <span id="floatCartText">0 item</span>
</button>

<!-- FOOTER -->
<footer>
  <div class="footer-logo">Tummy Time</div>
  <p><span data-i18n="footer_line">Pesan via web, konfirmasi otomatis lewat</span> <a href="#" id="footer-wa-link" data-i18n="footer_wa">WhatsApp</a></p>
  <p style="margin-top:6px;font-size:0.74rem;opacity:0.6" data-i18n="footer_copyright">© 2026 Tummy Time. All rights reserved.</p>
</footer>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
  .history-modal{max-width:620px}.history-modal-header{display:flex;align-items:flex-start;justify-content:space-between}.history-loading,.history-empty{text-align:center;color:var(--gray);padding:28px 8px}.history-empty-icon{font-size:2.5rem;margin-bottom:8px}.history-empty p{margin:5px 0 16px}.history-empty a{display:inline-block;padding:10px 15px;border-radius:10px;background:var(--red);color:#fff;font-weight:800;text-decoration:none}.history-summary{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:15px}.history-summary-card{padding:12px;border:1px solid var(--cream-dark);border-radius:10px;background:var(--cream)}.history-summary-label{color:var(--gray);font-size:.72rem;font-weight:700}.history-summary-value{margin-top:3px;color:var(--red);font-size:1.05rem;font-weight:900}.history-list{display:grid;gap:10px}.history-order{padding:14px;border:1px solid var(--cream-dark);border-radius:12px;background:#fff}.history-order-top{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:10px}.history-code{font-weight:900}.history-date{margin-top:3px;color:var(--gray);font-size:.72rem}.history-status{padding:4px 8px;border-radius:999px;font-size:.68rem;font-weight:800}.history-status.pending{background:#fff2cc;color:#996b00}.history-status.confirmed,.history-status.cooking{background:#e2edff;color:#245ab4}.history-status.ready,.history-status.done{background:#dff5e8;color:#167443}.history-status.cancelled{background:#fde1e1;color:#a52b2b}.history-item{display:flex;justify-content:space-between;gap:12px;padding:4px 0;color:var(--gray);font-size:.78rem}.history-item strong{color:var(--dark)}.history-total{display:flex;justify-content:space-between;margin-top:9px;padding-top:9px;border-top:1px dashed var(--cream-dark);font-weight:900}.history-total strong{color:var(--red)}@media(max-width:520px){.history-modal{max-height:86vh}.history-summary{grid-template-columns:1fr 1fr}.history-order-top{display:block}.history-status{display:inline-block;margin-top:7px}}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  window.APP_DATA = {
    categories: <?php echo json_encode($categories, 15, 512) ?>,
    menus: <?php echo json_encode($menus, 15, 512) ?>,
    settings: <?php echo json_encode($settings, 15, 512) ?>,
  };
</script>
<script src="<?php echo e(asset('js/app.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\tummy-time-laravel\resources\views/home.blade.php ENDPATH**/ ?>