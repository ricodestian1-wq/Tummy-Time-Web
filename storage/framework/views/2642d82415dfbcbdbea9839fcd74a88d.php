<?php $__env->startSection('content'); ?>


<!-- HERO -->
<header class="hero">
  <nav class="navbar">
    <div class="logo">
      <div class="logo-icon">🍗</div>
      <div class="logo-text">
        TUMMY TIME
        <span>Fried Chicken • Order Online</span>
      </div>
    </div>
    <div class="nav-links">
      <button class="nav-link-btn" onclick="document.getElementById('menu').scrollIntoView({behavior:'smooth'})">Menu</button>
      <button class="nav-link-btn" onclick="openStatusModal()">📦 Lacak Pesanan</button>
      <button class="nav-link-btn cart-btn" onclick="openCart()">
        🛒 Keranjang <span class="cart-count" id="cartCountNav">0</span>
      </button>
    </div>
  </nav>

  <div class="hero-content">
    <div class="hero-text">
      <div class="badge-open" id="hero-badge">Buka Sekarang</div>
      <h1 class="hero-title">Ayam Crispy Paling <em>Nendang</em><br>di Kotamu</h1>
      <p class="hero-subtitle">Ayam Crispy, Fire Chicken, sampai Sambal Bawang — digoreng fresh begitu kamu order. Pesan online, tinggal ambil atau tunggu diantar!</p>
      <div class="hero-stats">
        <div class="stat">
          <div class="stat-num" id="stat-cat">0</div>
          <div class="stat-label">Kategori</div>
        </div>
        <div class="stat">
          <div class="stat-num" id="stat-menu">0</div>
          <div class="stat-label">Menu Tersedia</div>
        </div>
        <div class="stat">
          <div class="stat-num">⚡15'</div>
          <div class="stat-label">Siap Saji</div>
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
    <span>⚡ Siap saji 15 menit</span>
    <span>🔥 Level pedas sesuai selera</span>
    <span>📦 Order online tanpa antri</span>
    <span>🍗 Digoreng fresh, bukan basi</span>
    <span>⚡ Siap saji 15 menit</span>
    <span>🔥 Level pedas sesuai selera</span>
    <span>📦 Order online tanpa antri</span>
    <span>🍗 Digoreng fresh, bukan basi</span>
  </div>
</div>

<!-- CLOSED BANNER -->
<div id="closed-banner">
  <div class="closed-icon">🔒</div>
  <h3>Toko Sedang Tutup</h3>
  <p id="closed-msg">Maaf, kami sedang tutup. Silakan order lagi ya!</p>
</div>

<!-- MAIN -->
<main class="main" id="menu">
  <div class="section-header">
    <div class="section-eyebrow">🍗 Menu Kami</div>
    <h2 class="section-title">Ayam Crispy Paling <em>Nendang</em> Sejagat</h2>
    <p class="section-subtitle">Klik menu buat lihat penjelasan lengkap & sisa stoknya sebelum kamu pesan.</p>
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
        <span class="detail-qty-label">Jumlah</span>
        <div class="detail-qty-control">
          <button class="detail-qty-btn" onclick="detailChangeQty(-1)">−</button>
          <span class="detail-qty-num" id="detail-qty-num">1</span>
          <button class="detail-qty-btn" onclick="detailChangeQty(1)">+</button>
        </div>
      </div>
      <button class="detail-add-btn" id="detail-add-btn" onclick="detailAddToCart()">🛒 Tambah ke Keranjang</button>
    </div>
  </div>
</div>

<!-- CART OVERLAY + PANEL -->
<div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>
<div class="cart-panel" id="cartPanel">
  <div class="cart-header">
    <div class="cart-title">🛒 Keranjang Belanja</div>
    <button class="cart-close" onclick="closeCart()">✕</button>
  </div>
  <div class="cart-items" id="cartItems">
    <div class="cart-empty">
      <span class="cart-empty-icon">🛒</span>
      <p>Keranjang masih kosong</p>
      <p style="font-size:0.78rem;margin-top:4px;color:#aaa">Pilih menu yang kamu suka!</p>
    </div>
  </div>
  <div class="cart-footer" id="cartFooter" style="display:none">
    <div class="cart-summary">
      <div class="cart-total"><span>Total</span><span id="totalText">Rp 0</span></div>
    </div>
    <button class="checkout-btn" onclick="openCheckout()">📦 Lanjut Pesan</button>
  </div>
</div>

<!-- CHECKOUT MODAL -->
<div class="modal-overlay" id="checkoutModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">📋 Detail Pesanan</div>
      <p class="modal-sub">Lengkapi data di bawah ini</p>
    </div>
    <div class="modal-body">
      <div class="form-group">
        <label class="form-label">Nama Lengkap *</label>
        <input class="form-input" id="f-name" type="text" placeholder="Masukkan nama kamu">
      </div>
      <div class="form-group">
        <label class="form-label">Nomor WhatsApp *</label>
        <input class="form-input" id="f-phone" type="tel" placeholder="Contoh: 08123456789">
      </div>
      <div class="form-group">
        <label class="form-label">Alamat Pembeli *</label>
        <textarea class="form-textarea" id="f-address" placeholder="Contoh: Jalan Mawar No. 12, Kelurahan Cempaka, Kota Bandung"></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Catatan (opsional)</label>
        <textarea class="form-textarea" id="f-notes" placeholder="Contoh: tidak pedas, saus dipisah..."></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Metode Pembayaran</label>
        <div class="payment-options">
          <div class="payment-opt selected" id="pay-cash" onclick="selectPayment('cash')">
            <span class="payment-opt-icon">💵</span>
            <span class="payment-opt-name">Tunai</span>
          </div>
          <div class="payment-opt" id="pay-qris" onclick="selectPayment('qris')">
            <span class="payment-opt-icon">📱</span>
            <span class="payment-opt-name">QRIS</span>
          </div>
        </div>

        <div id="cash-section">
          <div class="form-group" style="margin-top:12px;margin-bottom:4px">
            <label class="form-label">Uang yang dibayar</label>
            <div class="cash-input-wrap">
              <span class="cash-prefix">Rp</span>
              <input class="form-input cash-input" id="f-cash" type="number" placeholder="0" oninput="calcChange()" onchange="calcChange()">
            </div>
          </div>
          <div class="quick-cash"></div>
          <div class="change-display" id="change-display" style="display:none">
            <span class="change-label">💰 Kembalian</span>
            <span class="change-amount" id="change-val">Rp 0</span>
          </div>
        </div>

        <div id="qris-section">
          <div class="qris-box" id="qris-box-content"></div>
          <div class="payment-action-btns">
            <input type="file" id="proof-input" accept="image/*" style="display:none" onchange="handleProofFile(event)">
            <button type="button" class="btn-outline-action" onclick="triggerProofUpload()">📤 Kirim Bukti Pembayaran</button>
            <div class="proof-preview" id="proof-preview"></div>
            <button type="button" class="btn-outline-action secondary" onclick="openStatusModal()">📦 Lihat Status Pesanan</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeCheckout()">Batal</button>
      <button class="btn-submit" onclick="submitOrder()">✅ Konfirmasi Pesanan</button>
    </div>
  </div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal-overlay" id="successModal">
  <div class="modal">
    <div class="success-content">
      <div class="success-icon">✅</div>
      <h2 class="success-title">Pesanan Diterima!</h2>
      <p style="color:var(--gray);line-height:1.7">Terima kasih sudah memesan. Kami akan segera memproses pesananmu!</p>
      <div class="success-kode">
        <div class="kode-label">Kode Pesananmu</div>
        <div class="kode-value" id="kodePesananDisplay">—</div>
      </div>
      <p style="font-size:0.8rem;color:var(--gray);margin-bottom:20px">Simpan kode ini untuk melacak pesananmu</p>
      <button class="checkout-btn" onclick="closeSuccess()" style="margin-bottom:10px">🎉 Oke, Terima Kasih!</button>
      <button class="btn-outline-action secondary" onclick="closeSuccess();openStatusModal()">📦 Lacak Pesanan Sekarang</button>
    </div>
  </div>
</div>

<!-- STATUS PESANAN MODAL -->
<div class="modal-overlay" id="status-modal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">📦 Status Pesanan</div>
      <button class="cart-close" onclick="closeStatusModal()" style="position:absolute;top:18px;right:18px;background:var(--cream-dark);color:var(--dark)">✕</button>
    </div>
    <div class="modal-body">
      <div class="status-hint">Masukkan kode pesanan (contoh: TT123456) untuk melihat status terbaru.</div>
      <div class="status-search">
        <input class="form-input" id="status-code-input" type="text" placeholder="Kode Pesanan">
        <button class="status-search-btn" onclick="checkOrderStatus()">Cek</button>
      </div>
      <div id="status-result"></div>
    </div>
  </div>
</div>

<!-- FLOATING CART BUTTON -->
<button class="float-cart" onclick="openCart()" id="floatCartBtn">
  🛒 <span id="floatCartText">0 item</span>
</button>

<!-- FOOTER -->
<footer>
  <div class="footer-logo">🍗 Tummy Time</div>
  <p>Pesan via web, konfirmasi otomatis lewat <a href="#" id="footer-wa-link">WhatsApp</a></p>
  <p style="margin-top:6px;font-size:0.74rem;opacity:0.6">© 2026 Tummy Time. All rights reserved.</p>
</footer>

<?php $__env->stopSection(); ?>

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