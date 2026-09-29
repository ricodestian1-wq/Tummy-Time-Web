const WA_NUMBER = '6285187408288'; // Ganti dengan nomor WA admin
let QRIS_IMAGE = null; // Otomatis terisi dari Admin Dashboard (menu Pengaturan → QRIS Pembayaran), tidak perlu diedit manual
let QRIS_MERCHANT_NAME = 'Tummy Time';

let menuData = [];
let categories = [];
let cart = {};
let isOpen = true;
let closedMessage = null; // null = pakai default dari kamus bahasa
let paymentProof = null;
let currentFilter = 'semua';
let selectedPayment = 'cash';

// ==========================================
// BAHASA (ID / EN)
// ==========================================
const TRANSLATIONS = {
  id: {
    nav_track: 'Lacak Pesanan',
    nav_history: 'Riwayat',
    nav_logout: 'Keluar',
    nav_login: 'Masuk',
    nav_cart: '🛒 Keranjang',

    hero_badge_open: 'Buka Sekarang',
    hero_badge_closed: 'Sedang Tutup',
    hero_title: 'Ayam Crispy Paling <em>Nendang</em><br>di Kotamu',
    hero_subtitle: 'Ayam Crispy, Fire Chicken, sampai Sambal Bawang — digoreng fresh begitu kamu order. Pesan online, tinggal ambil atau tunggu diantar!',
    stat_cat: 'Kategori',
    stat_menu: 'Menu Tersedia',
    stat_ready: 'Siap Saji',

    ticker_1: '⚡ Siap saji 15 menit',
    ticker_2: '🔥 Level pedas sesuai selera',
    ticker_3: '📦 Order online tanpa antri',
    ticker_4: '🍗 Digoreng fresh, bukan basi',

    closed_title: 'Toko Sedang Tutup',
    closed_default_msg: 'Maaf, kami sedang tutup. Silakan order lagi ya!',

    menu_eyebrow: 'Menu Kami',
    menu_title: 'Ayam Crispy Paling <em>Nendang</em> Sejagat',
    menu_subtitle: 'Klik menu buat lihat penjelasan lengkap & sisa stoknya sebelum kamu pesan.',
    filter_all: '🍽️ Semua',

    badge_habis: 'Habis',
    badge_limited: 'Terbatas',
    badge_spicy: '🔥 Pedas',
    stock_unlimited: '✅ Selalu tersedia',
    stock_low: (n) => `🔥 Sisa ${n} porsi!`,
    stock_ok: (n) => `📦 Stok: ${n}`,
    habis_label: 'Habis',

    detail_stock_unlimited: 'Selalu tersedia',
    detail_stock_low: (n) => `Stok tersisa ${n} porsi — buruan sebelum habis!`,
    detail_stock_ok: (n) => `Stok tersedia: ${n} porsi`,
    detail_qty: 'Jumlah',
    detail_add: '🛒 Tambah ke Keranjang',
    detail_add_disabled: '😔 Stok Tidak Cukup',
    detail_desc_fallback: 'Menu favorit yang wajib kamu coba!',
    toast_added: (name, qty) => `${name} ×${qty} ditambahkan ke keranjang`,
    toast_cart_full: (name) => `Stok ${name} sudah habis di keranjangmu`,
    toast_stock_left: (name, stock) => `Stok ${name} tinggal ${stock}`,

    cart_title: '🛒 Keranjang Belanja',
    cart_empty_title: 'Keranjang masih kosong',
    cart_empty_sub: 'Pilih menu yang kamu suka!',
    cart_total: 'Total',
    cart_checkout: '📦 Lanjut Pesan',
    float_cart_item: (n) => `${n} item`,
    float_cart_full: (n, total) => `${n} item • Rp ${total}`,

    checkout_title: '📋 Detail Pesanan',
    checkout_sub: 'Lengkapi data di bawah ini',
    f_name: 'Nama Lengkap *',
    f_name_ph: 'Masukkan nama kamu',
    f_phone: 'Nomor WhatsApp *',
    f_phone_ph: 'Contoh: 08123456789',
    f_address: 'Alamat Pembeli *',
    f_address_ph: 'Contoh: Jalan Mawar No. 12, Kelurahan Cempaka, Kota Bandung',
    f_notes: 'Catatan (opsional)',
    f_notes_ph: 'Contoh: tidak pedas, saus dipisah...',
    payment_method: 'Metode Pembayaran',
    pay_cash: 'Tunai',
    pay_qris: 'QRIS',
    cash_paid: 'Uang yang dibayar',
    change_label: '💰 Kembalian',
    proof_upload: '📤 Kirim Bukti Pembayaran',
    proof_attached: '✓ Bukti pembayaran terlampir',
    view_status: '📦 Lihat Status Pesanan',
    btn_cancel: 'Batal',
    btn_confirm: '✅ Konfirmasi Pesanan',
    qris_not_set: 'Gambar QRIS belum diatur.<br>Atur lewat Admin Dashboard → Pengaturan.',
    qris_total_label: 'Total Bayar',

    success_title: 'Pesanan Diterima!',
    success_msg: 'Terima kasih sudah memesan. Kami akan segera memproses pesananmu!',
    success_code_label: 'Kode Pesananmu',
    success_note: 'Simpan kode ini untuk melihat detail pesananmu nanti',
    success_done: '🎉 Pesanan Selesai',

    status_title: '📦 Status Pesanan',
    status_hint: 'Masukkan kode pesanan (contoh: TT123456) untuk melihat status terbaru.',
    status_ph: 'Kode Pesanan',
    status_check: 'Cek',
    status_searching: 'Mencari pesanan...',
    status_not_found: (code) => `Pesanan dengan kode <strong>${code}</strong> tidak ditemukan.<br>Pastikan kode sudah benar ya!`,
    status_cancelled: '❌ Pesanan ini dibatalkan',
    status_proof_received: '✓ Bukti pembayaran diterima',
    status_proof_missing: '⚠️ Bukti pembayaran belum dikirim',
    status_seller: 'Nomor penjual:',
    status_detail: 'Detail Pesanan',
    status_address: 'Alamat Pembeli',
    steps: [
      { key: 'pending',   label: 'Menunggu',    icon: '⏳' },
      { key: 'confirmed', label: 'Dikonfirmasi', icon: '✅' },
      { key: 'cooking',   label: 'Dimasak',     icon: '🔥' },
      { key: 'ready',     label: 'Siap Ambil',  icon: '📦' },
      { key: 'done',      label: 'Selesai',     icon: '🎉' },
    ],
    pay_label_map: { cash: 'Tunai', qris: 'QRIS' },

    history_title: '🧾 Riwayat Pesanan',
    history_sub: 'Pesanan dari akun kamu',
    history_loading: '⏳ Memuat riwayat pesanan...',
    history_error: 'Riwayat pesanan belum bisa dimuat.',
    history_empty: 'Belum ada pesanan. Yuk pilih menu favoritmu!',
    history_see_menu: 'Lihat menu',
    history_total_orders: 'Total pesanan',
    history_completed: 'Pesanan selesai',
    history_status_labels: {
      pending: 'Menunggu', confirmed: 'Dikonfirmasi', cooking: 'Sedang dimasak',
      ready: 'Siap diambil', done: 'Selesai', cancelled: 'Dibatalkan',
    },

    footer_line: 'Pesan via web, konfirmasi otomatis lewat',
    footer_wa: 'WhatsApp',
    footer_copyright: '© 2026 Tummy Time. All rights reserved.',

    alert_name_required: 'Nama wajib diisi!',
    alert_phone_required: 'Nomor WhatsApp wajib diisi!',
    alert_address_required: 'Alamat pembeli wajib diisi!',
    alert_pick_menu: 'Pilih menu dulu!',
    alert_cash_less: 'Uang tunai kurang dari total!',
    alert_file_image: 'File harus berupa gambar',
    alert_file_size: 'Ukuran gambar maksimal 3MB',
    alert_enter_code: 'Masukkan kode pesanan dulu',
  },

  en: {
    nav_track: 'Track Order',
    nav_history: 'History',
    nav_logout: 'Logout',
    nav_login: 'Login',
    nav_cart: '🛒 Cart',

    hero_badge_open: 'Open Now',
    hero_badge_closed: 'Currently Closed',
    hero_title: 'The Most Epic Crispy Chicken<br>in Your City',
    hero_subtitle: 'Crispy Chicken, Fire Chicken, to Sambal Bawang — freshly fried the moment you order. Order online, just pick up or wait for delivery!',
    stat_cat: 'Categories',
    stat_menu: 'Menu Available',
    stat_ready: 'Ready In',

    ticker_1: '⚡ Ready in 15 minutes',
    ticker_2: '🔥 Spice level to your taste',
    ticker_3: '📦 Order online, no queue',
    ticker_4: '🍗 Freshly fried, never stale',

    closed_title: 'Shop Is Currently Closed',
    closed_default_msg: "Sorry, we're closed right now. Please order again later!",

    menu_eyebrow: 'Our Menu',
    menu_title: 'The Most Epic Crispy Chicken in Town',
    menu_subtitle: 'Tap a menu to see the full description & remaining stock before you order.',
    filter_all: '🍽️ All',

    badge_habis: 'Sold Out',
    badge_limited: 'Limited',
    badge_spicy: '🔥 Spicy',
    stock_unlimited: '✅ Always available',
    stock_low: (n) => `🔥 Only ${n} left!`,
    stock_ok: (n) => `📦 Stock: ${n}`,
    habis_label: 'Sold Out',

    detail_stock_unlimited: 'Always available',
    detail_stock_low: (n) => `Only ${n} left — grab it before it's gone!`,
    detail_stock_ok: (n) => `In stock: ${n} servings`,
    detail_qty: 'Quantity',
    detail_add: '🛒 Add to Cart',
    detail_add_disabled: '😔 Not Enough Stock',
    detail_desc_fallback: 'A customer favorite you have to try!',
    toast_added: (name, qty) => `${name} ×${qty} added to cart`,
    toast_cart_full: (name) => `${name} stock is already maxed out in your cart`,
    toast_stock_left: (name, stock) => `Only ${stock} of ${name} left`,

    cart_title: '🛒 Shopping Cart',
    cart_empty_title: 'Your cart is empty',
    cart_empty_sub: 'Pick something you like!',
    cart_total: 'Total',
    cart_checkout: '📦 Continue to Order',
    float_cart_item: (n) => `${n} item${n === 1 ? '' : 's'}`,
    float_cart_full: (n, total) => `${n} item${n === 1 ? '' : 's'} • Rp ${total}`,

    checkout_title: '📋 Order Details',
    checkout_sub: 'Fill in the details below',
    f_name: 'Full Name *',
    f_name_ph: 'Enter your name',
    f_phone: 'WhatsApp Number *',
    f_phone_ph: 'e.g. 08123456789',
    f_address: 'Delivery Address *',
    f_address_ph: 'e.g. Jalan Mawar No. 12, Kelurahan Cempaka, Kota Bandung',
    f_notes: 'Notes (optional)',
    f_notes_ph: 'e.g. not spicy, sauce on the side...',
    payment_method: 'Payment Method',
    pay_cash: 'Cash',
    pay_qris: 'QRIS',
    cash_paid: 'Amount paid',
    change_label: '💰 Change',
    proof_upload: '📤 Upload Payment Proof',
    proof_attached: '✓ Payment proof attached',
    view_status: '📦 View Order Status',
    btn_cancel: 'Cancel',
    btn_confirm: '✅ Confirm Order',
    qris_not_set: 'QRIS image not set yet.<br>Set it up via Admin Dashboard → Settings.',
    qris_total_label: 'Total Due',

    success_title: 'Order Received!',
    success_msg: "Thanks for ordering. We'll start processing your order right away!",
    success_code_label: 'Your Order Code',
    success_note: 'Save this code to check your order details later',
    success_done: '🎉 Done',

    status_title: '📦 Order Status',
    status_hint: 'Enter your order code (e.g. TT123456) to see the latest status.',
    status_ph: 'Order Code',
    status_check: 'Check',
    status_searching: 'Looking up order...',
    status_not_found: (code) => `Order with code <strong>${code}</strong> was not found.<br>Please make sure the code is correct!`,
    status_cancelled: '❌ This order was cancelled',
    status_proof_received: '✓ Payment proof received',
    status_proof_missing: '⚠️ Payment proof not sent yet',
    status_seller: 'Seller number:',
    status_detail: 'Order Details',
    status_address: 'Delivery Address',
    steps: [
      { key: 'pending',   label: 'Pending',    icon: '⏳' },
      { key: 'confirmed', label: 'Confirmed', icon: '✅' },
      { key: 'cooking',   label: 'Cooking',     icon: '🔥' },
      { key: 'ready',     label: 'Ready',  icon: '📦' },
      { key: 'done',      label: 'Done',      icon: '🎉' },
    ],
    pay_label_map: { cash: 'Cash', qris: 'QRIS' },

    history_title: '🧾 Order History',
    history_sub: 'Orders from your account',
    history_loading: '⏳ Loading order history...',
    history_error: 'Order history could not be loaded.',
    history_empty: "No orders yet. Go pick your favorite menu!",
    history_see_menu: 'View menu',
    history_total_orders: 'Total orders',
    history_completed: 'Completed orders',
    history_status_labels: {
      pending: 'Pending', confirmed: 'Confirmed', cooking: 'Cooking',
      ready: 'Ready for pickup', done: 'Done', cancelled: 'Cancelled',
    },

    footer_line: 'Order via web, auto-confirmed through',
    footer_wa: 'WhatsApp',
    footer_copyright: '© 2026 Tummy Time. All rights reserved.',

    alert_name_required: 'Name is required!',
    alert_phone_required: 'WhatsApp number is required!',
    alert_address_required: 'Delivery address is required!',
    alert_pick_menu: 'Please pick a menu item first!',
    alert_cash_less: 'Cash amount is less than the total!',
    alert_file_image: 'File must be an image',
    alert_file_size: 'Max image size is 3MB',
    alert_enter_code: 'Please enter an order code first',
  },
};

let currentLang = localStorage.getItem('tt_lang') === 'en' ? 'en' : 'id';

function t(key) {
  const dict = TRANSLATIONS[currentLang] || TRANSLATIONS.id;
  return (key in dict) ? dict[key] : TRANSLATIONS.id[key];
}

function setLang(lang) {
  if (lang !== 'id' && lang !== 'en') return;
  currentLang = lang;
  localStorage.setItem('tt_lang', lang);
  applyStaticText();
  renderAll();
  updateCartUI();
  const langBtn = document.getElementById('langToggleBtn');
  if (langBtn) langBtn.textContent = lang === 'id' ? '🇬🇧 EN' : '🇮🇩 ID';
  // Kalau modal status/riwayat sedang terbuka, refresh isinya juga
  if (document.getElementById('history-modal')?.classList.contains('open')) loadOrderHistory();
  const codeInput = document.getElementById('status-code-input');
  if (document.getElementById('status-modal')?.classList.contains('open') && codeInput?.value) checkOrderStatus();
}

function toggleLang() {
  setLang(currentLang === 'id' ? 'en' : 'id');
}

// Set semua teks statis (elemen yang punya atribut data-i18n / data-i18n-placeholder / data-i18n-html)
function applyStaticText() {
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const val = t(el.getAttribute('data-i18n'));
    if (typeof val === 'string') el.textContent = val;
  });
  document.querySelectorAll('[data-i18n-html]').forEach(el => {
    const val = t(el.getAttribute('data-i18n-html'));
    if (typeof val === 'string') el.innerHTML = val;
  });
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const val = t(el.getAttribute('data-i18n-placeholder'));
    if (typeof val === 'string') el.setAttribute('placeholder', val);
  });
  document.documentElement.lang = currentLang;
}

const CAT_ICONS = [
  { keys: ['wings'], icon: '🍗' },
  { keys: ['fire', 'pedas', 'spicy'], icon: '🔥' },
  { keys: ['sambal'], icon: '🌶️' },
  { keys: ['ayam', 'chicken'], icon: '🍗' },
  { keys: ['paket', 'hemat', 'combo'], icon: '🎁' },
  { keys: ['add on', 'addon', 'tambahan'], icon: '➕' },
  { keys: ['carte', 'ala carte'], icon: '🍽️' },
  { keys: ['mie', 'noodle'], icon: '🍜' },
  { keys: ['nasi', 'rice'], icon: '🍚' },
  { keys: ['minum', 'drink', 'es', 'jus'], icon: '🥤' },
  { keys: ['dessert', 'manis', 'cake'], icon: '🍰' },
];

function getCatIcon(name) {
  const n = (name || '').toLowerCase();
  const found = CAT_ICONS.find(c => c.keys.some(k => n.includes(k)));
  return found ? found.icon : '🍴';
}

function fmtNum(n) { return Math.round(n).toLocaleString('id-ID'); }

// stock: null = tidak dibatasi (selalu ada selama is_available=1)
function isInStock(m) { return m.is_available == 1 && (m.stock === null || m.stock === undefined || m.stock > 0); }
function isLowStock(m) { return m.stock !== null && m.stock !== undefined && m.stock > 0 && m.stock <= 5; }
function isSpicy(m, catMap) {
  const text = ((m.name || '') + ' ' + (catMap[m.category_id] || '')).toLowerCase();
  return text.includes('fire') || text.includes('pedas') || text.includes('sambal');
}
function stockRowHtml(m) {
  if (m.stock === null || m.stock === undefined) return `<div class="menu-stock-row unlimited">${t('stock_unlimited')}</div>`;
  if (m.stock <= 0) return '';
  if (m.stock <= 5) return `<div class="menu-stock-row low">${t('stock_low')(m.stock)}</div>`;
  return `<div class="menu-stock-row ok">${t('stock_ok')(m.stock)}</div>`;
}

function handleMenuImageError(image, fallbackIcon) {
  const fallback = document.createElement('span');
  fallback.className = 'menu-icon-fallback';
  fallback.textContent = fallbackIcon || '🍴';
  image.replaceWith(fallback);
}

// STATIC FALLBACK DATA (dipakai jika api/menu.php tidak tersedia)
const STATIC_CATEGORIES = [
  { id: 1, name: 'Ayam Crispy' },
  { id: 2, name: 'Fire Chicken' },
  { id: 3, name: 'Fire Chicken Wings' },
  { id: 4, name: 'Ayam Sambal Bawang' },
  { id: 5, name: 'Ala Carte' },
  { id: 6, name: 'Add On' },
  { id: 7, name: 'Paket Hemat' },
];
const STATIC_MENUS = [
  { id: 1, category_id: 1, name: 'Ayam Crispy Tanpa Nasi', description: 'Ayam crispy digoreng garing, disajikan dengan saus sachet dan free nugget.', price: 8000, is_available: 1, stock: 25 },
  { id: 2, category_id: 1, name: 'Ayam Crispy + Nasi', description: 'Ayam crispy digoreng garing dengan nasi hangat, saus sachet, dan free nugget.', price: 10000, is_available: 1, stock: 25 },
  { id: 3, category_id: 1, name: 'Ayam Crispy + Mie', description: 'Ayam crispy digoreng garing dengan mie, saus sachet, dan free nugget.', price: 13000, is_available: 1, stock: 20 },
  { id: 4, category_id: 2, name: 'Fire Chicken Tanpa Nasi', description: 'Ayam crispy dibalur saus fire pedas nampol, saus bisa dipisah/dicampur, free nugget.', price: 11000, is_available: 1, stock: 20 },
  { id: 5, category_id: 2, name: 'Fire Chicken + Nasi', description: 'Ayam crispy dibalur saus fire pedas nampol dengan nasi hangat, free nugget.', price: 13000, is_available: 1, stock: 20 },
  { id: 6, category_id: 2, name: 'Fire Chicken + Mie', description: 'Ayam crispy dibalur saus fire pedas nampol dengan mie, free nugget.', price: 15000, is_available: 1, stock: 15 },
  { id: 7, category_id: 3, name: 'Fire Chicken Wings Tanpa Nasi', description: 'Sayap ayam crispy dibalur saus fire pedas, saus bisa dipisah/dicampur, free nugget.', price: 12000, is_available: 1, stock: 15 },
  { id: 8, category_id: 3, name: 'Fire Chicken Wings + Nasi', description: 'Sayap ayam crispy dibalur saus fire pedas dengan nasi hangat, free nugget.', price: 14000, is_available: 1, stock: 15 },
  { id: 9, category_id: 3, name: 'Fire Chicken Wings + Mie', description: 'Sayap ayam crispy dibalur saus fire pedas dengan mie, free nugget.', price: 16000, is_available: 1, stock: 10 },
  { id: 10, category_id: 4, name: 'Ayam Crispy Sambal Bawang Tanpa Nasi', description: 'Ayam crispy dengan siraman sambal bawang segar, free nugget.', price: 11000, is_available: 1, stock: 20 },
  { id: 11, category_id: 4, name: 'Ayam Crispy Sambal Bawang + Nasi', description: 'Ayam crispy dengan siraman sambal bawang segar dan nasi hangat, free nugget.', price: 13000, is_available: 1, stock: 20 },
  { id: 12, category_id: 4, name: 'Ayam Crispy Sambal Bawang + Mie', description: 'Ayam crispy dengan siraman sambal bawang segar dan mie, free nugget.', price: 15000, is_available: 1, stock: 15 },
  { id: 13, category_id: 5, name: 'Nasi', description: 'Nasi putih hangat pulen.', price: 3000, is_available: 1, stock: null },
  { id: 14, category_id: 5, name: 'Nugget (isi 4)', description: 'Nugget crispy isi 4 pcs, cocok jadi teman makan.', price: 5000, is_available: 1, stock: 30 },
  { id: 15, category_id: 5, name: 'Telur Ceplok', description: 'Telur ceplok digoreng matang sempurna.', price: 3500, is_available: 1, stock: 30 },
  { id: 16, category_id: 5, name: 'Mie + Telur + Sosis', description: 'Mie goreng dengan telur dan potongan sosis.', price: 8000, is_available: 1, stock: 15 },
  { id: 17, category_id: 5, name: 'Kerupuk Finna', description: 'Kerupuk renyah favorit semua orang.', price: 1500, is_available: 1, stock: null },
  { id: 18, category_id: 6, name: 'Sambal Bawang', description: 'Sambal bawang segar, level pedas nampol.', price: 2500, is_available: 1, stock: null },
  { id: 19, category_id: 6, name: 'Saus Fire', description: 'Saus fire pedas kental untuk cocolan ayam.', price: 2500, is_available: 1, stock: null },
  { id: 20, category_id: 7, name: 'Hemat 1', description: 'Paket spesial hemat untuk makan kenyang tanpa bikin kantong bolong.', price: 12000, is_available: 1, stock: 10 },
  { id: 21, category_id: 7, name: 'Hemat 2', description: 'Paket spesial hemat untuk makan kenyang tanpa bikin kantong bolong.', price: 15000, is_available: 1, stock: 10 },
  { id: 22, category_id: 7, name: 'Hemat 3', description: 'Paket spesial hemat untuk makan kenyang tanpa bikin kantong bolong.', price: 15000, is_available: 1, stock: 10 },
];

async function loadData() {
  // Data awal dirender langsung oleh Blade (lihat resources/views/home.blade.php),
  // jadi tidak perlu fetch API terpisah untuk load pertama kali.
  const d = window.APP_DATA || {};
  categories = d.categories || STATIC_CATEGORIES;
  menuData = d.menus || STATIC_MENUS;
  isOpen = !!d.settings?.is_open;
  closedMessage = d.settings?.closed_message || null;
  if (d.settings?.qris_image) QRIS_IMAGE = d.settings.qris_image;
  if (d.settings?.qris_merchant_name) QRIS_MERCHANT_NAME = d.settings.qris_merchant_name;
  else if (d.settings?.shop_name) QRIS_MERCHANT_NAME = d.settings.shop_name;

  document.getElementById('footer-wa-link').href = `https://wa.me/${WA_NUMBER}`;

  const langBtn = document.getElementById('langToggleBtn');
  if (langBtn) langBtn.textContent = currentLang === 'id' ? '🇬🇧 EN' : '🇮🇩 ID';

  applyStaticText();
  renderAll();
}

function renderAll() {
  const badge = document.getElementById('hero-badge');
  if (!isOpen) {
    badge.textContent = t('hero_badge_closed');
    badge.classList.add('closed');
    document.getElementById('closed-banner').style.display = 'block';
    document.getElementById('closed-msg').textContent = closedMessage || t('closed_default_msg');
    document.getElementById('filter-tabs').style.display = 'none';
    document.getElementById('menu-grid').innerHTML = '';
    return;
  }

  badge.textContent = t('hero_badge_open');
  badge.classList.remove('closed');
  document.getElementById('stat-cat').textContent = categories.length;
  document.getElementById('stat-menu').textContent = menuData.filter(m => m.is_available == 1).length;

  renderFilterTabs();
  renderMenuGrid();
}

function renderFilterTabs() {
  const wrap = document.getElementById('filter-tabs');
  let html = `<button class="filter-tab ${currentFilter === 'semua' ? 'active' : ''}" onclick="filterMenu('semua', this)">${t('filter_all')}</button>`;
  html += categories.map(c =>
    `<button class="filter-tab ${currentFilter == c.id ? 'active' : ''}" onclick="filterMenu(${c.id}, this)">${getCatIcon(c.name)} ${c.name}</button>`
  ).join('');
  wrap.innerHTML = html;
}

function filterMenu(tag, btn) {
  currentFilter = tag;
  document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderMenuGrid();
}

function renderMenuGrid() {
  const grid = document.getElementById('menu-grid');
  const catMap = Object.fromEntries(categories.map(c => [c.id, c.name]));
  const filtered = currentFilter === 'semua' ? menuData : menuData.filter(m => m.category_id == currentFilter);

  grid.innerHTML = filtered.map(m => {
    const available = isInStock(m);
    const qty = cart[m.id]?.qty || 0;
    const icon = getCatIcon(catMap[m.category_id] || '');
    const spicy = isSpicy(m, catMap);
    const cardClick = available ? `onclick="openMenuDetail(${m.id})"` : '';
    const imageMarkup = m.image_url
      ? `<img src="${m.image_url}" alt="${(m.name || '').replace(/"/g, '&quot;')}" loading="lazy" onerror="handleMenuImageError(this, '${icon}')">`
      : `<span class="menu-icon-fallback">${icon}</span>`;
    return `
      <div class="menu-card ${!available ? 'unavailable' : ''}" ${cardClick}>
        <div class="menu-img">
          ${imageMarkup}
          ${!available ? `<span class="badge-habis">${t('badge_habis')}</span>` : isLowStock(m) ? `<span class="badge-limited">${t('badge_limited')}</span>` : ''}
          ${available && spicy ? `<span class="badge-spicy">${t('badge_spicy')}</span>` : ''}
        </div>
        <div class="menu-body">
          <div class="menu-name">${m.name}</div>
          ${m.description ? `<div class="menu-desc">${m.description}</div>` : ''}
          ${available ? stockRowHtml(m) : ''}
          <div class="menu-footer">
            <div class="menu-price">Rp ${fmtNum(m.price)}</div>
            <div id="ctrl-${m.id}" onclick="event.stopPropagation()">
              ${!available
                ? `<span class="habis-label">${t('habis_label')}</span>`
                : qty === 0
                  ? `<button class="add-btn" onclick="addItem(${m.id})">+</button>`
                  : `<div class="qty-control">
                      <button class="qty-btn" onclick="removeItem(${m.id})">−</button>
                      <span class="qty-num">${qty}</span>
                      <button class="qty-btn" onclick="addItem(${m.id})">+</button>
                    </div>`
              }
            </div>
          </div>
        </div>
      </div>
    `;
  }).join('');
}

// ==========================================
// MENU DETAIL MODAL
// ==========================================
let detailMenuId = null;
let detailQty = 1;

function openMenuDetail(id) {
  const m = menuData.find(x => x.id === id);
  if (!m || !isInStock(m)) return;

  detailMenuId = id;
  detailQty = 1;

  const catMap = Object.fromEntries(categories.map(c => [c.id, c.name]));
  const icon = getCatIcon(catMap[m.category_id] || '');
  const detailImg = document.getElementById('detail-img');

  if (m.image_url) {
    detailImg.innerHTML = `<img src="${m.image_url}" alt="${(m.name || '').replace(/"/g, '&quot;')}" loading="lazy" onerror="handleMenuImageError(this, '${icon}')">`;
  } else {
    detailImg.innerHTML = icon;
  }

  document.getElementById('detail-name').textContent = m.name;
  document.getElementById('detail-price').textContent = 'Rp ' + fmtNum(m.price);
  document.getElementById('detail-desc').textContent = m.description || t('detail_desc_fallback');
  document.getElementById('detail-qty-num').textContent = detailQty;
  document.querySelector('.detail-qty-label').textContent = t('detail_qty');

  const box = document.getElementById('detail-stock-box');
  const text = document.getElementById('detail-stock-text');
  box.className = 'detail-stock-box';
  if (m.stock === null || m.stock === undefined) {
    box.classList.add('unlimited');
    text.textContent = t('detail_stock_unlimited');
  } else if (m.stock <= 5) {
    box.classList.add('low');
    text.textContent = t('detail_stock_low')(m.stock);
  } else {
    box.classList.add('ok');
    text.textContent = t('detail_stock_ok')(m.stock);
  }

  updateDetailAddBtn();

  document.getElementById('detailModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeMenuDetail() {
  document.getElementById('detailModal').classList.remove('open');
  document.body.style.overflow = '';
  detailMenuId = null;
}

function detailChangeQty(delta) {
  const m = menuData.find(x => x.id === detailMenuId);
  if (!m) return;
  const already = cart[m.id]?.qty || 0;
  const maxAddable = (m.stock === null || m.stock === undefined) ? Infinity : Math.max(0, m.stock - already);

  detailQty += delta;
  if (detailQty < 1) detailQty = 1;
  if (detailQty > maxAddable) detailQty = maxAddable || 1;

  document.getElementById('detail-qty-num').textContent = detailQty;
  updateDetailAddBtn();
}

function updateDetailAddBtn() {
  const m = menuData.find(x => x.id === detailMenuId);
  const btn = document.getElementById('detail-add-btn');
  if (!m) return;
  const already = cart[m.id]?.qty || 0;
  const maxAddable = (m.stock === null || m.stock === undefined) ? Infinity : Math.max(0, m.stock - already);

  if (maxAddable <= 0) {
    btn.disabled = true;
    btn.textContent = t('detail_add_disabled');
  } else {
    btn.disabled = false;
    btn.innerHTML = t('detail_add');
  }
}

function detailAddToCart() {
  const m = menuData.find(x => x.id === detailMenuId);
  if (!m || !isInStock(m)) return;

  const already = cart[m.id]?.qty || 0;
  const maxAddable = (m.stock === null || m.stock === undefined) ? Infinity : Math.max(0, m.stock - already);
  const addQty = Math.min(detailQty, maxAddable);
  if (addQty <= 0) { showToastMsg(t('toast_cart_full')(m.name)); return; }

  if (cart[m.id]) cart[m.id].qty += addQty;
  else cart[m.id] = { id: m.id, name: m.name, price: m.price, qty: addQty };

  renderMenuGrid();
  updateCartUI();
  showToastMsg(t('toast_added')(m.name, addQty));
  closeMenuDetail();
}

// ==========================================
// CART LOGIC
// ==========================================
function addItem(id) {
  const item = menuData.find(m => m.id === id);
  if (!item || !isInStock(item)) return;
  const currentQty = cart[id]?.qty || 0;
  if (item.stock !== null && item.stock !== undefined && currentQty >= item.stock) {
    showToastMsg(t('toast_stock_left')(item.name, item.stock));
    return;
  }
  if (cart[id]) cart[id].qty++;
  else cart[id] = { id, name: item.name, price: item.price, qty: 1 };
  renderMenuGrid();
  updateCartUI();
}

function showToastMsg(msg) {
  let el = document.getElementById('stockToast');
  if (!el) {
    el = document.createElement('div');
    el.id = 'stockToast';
    el.style.cssText = 'position:fixed;bottom:90px;left:50%;transform:translateX(-50%);background:var(--dark);color:white;padding:10px 18px;border-radius:20px;font-size:0.82rem;font-weight:700;z-index:3000;box-shadow:0 6px 20px rgba(0,0,0,0.3);transition:opacity 0.3s;';
    document.body.appendChild(el);
  }
  el.textContent = msg;
  el.style.opacity = '1';
  clearTimeout(el._t);
  el._t = setTimeout(() => { el.style.opacity = '0'; }, 2000);
}

function removeItem(id) {
  if (!cart[id]) return;
  cart[id].qty--;
  if (cart[id].qty <= 0) delete cart[id];
  renderMenuGrid();
  updateCartUI();
  renderCartItems();
}

function getCartTotal() { return Object.values(cart).reduce((s, i) => s + i.price * i.qty, 0); }
function getCartCount() { return Object.values(cart).reduce((s, i) => s + i.qty, 0); }

function updateCartUI() {
  const count = getCartCount();
  const total = getCartTotal();
  document.getElementById('cartCountNav').textContent = count;

  const floatBtn = document.getElementById('floatCartBtn');
  if (count > 0) {
    floatBtn.style.display = 'flex';
    document.getElementById('floatCartText').textContent = t('float_cart_full')(count, fmtNum(total));
  } else {
    floatBtn.style.display = 'none';
  }

  renderCartItems();
}

function renderCartItems() {
  const items = Object.values(cart);
  const cartItemsEl = document.getElementById('cartItems');
  const cartFooter = document.getElementById('cartFooter');
  const catMap = Object.fromEntries(categories.map(c => [c.id, c.name]));

  if (!items.length) {
    cartItemsEl.innerHTML = `<div class="cart-empty"><span class="cart-empty-icon">🛒</span><p>${t('cart_empty_title')}</p><p style="font-size:0.78rem;margin-top:4px;color:#aaa">${t('cart_empty_sub')}</p></div>`;
    cartFooter.style.display = 'none';
    return;
  }

  cartItemsEl.innerHTML = items.map(i => {
    const menuItem = menuData.find(m => m.id === i.id);
    const icon = getCatIcon(catMap[menuItem?.category_id] || '');
    return `
      <div class="cart-item">
        <div class="cart-item-icon">${icon}</div>
        <div class="cart-item-info">
          <div class="cart-item-name">${i.name}</div>
          <div class="cart-item-price">Rp ${fmtNum(i.price * i.qty)}</div>
        </div>
        <div class="cart-item-controls">
          <button class="qty-btn" onclick="removeItem(${i.id})">−</button>
          <span class="qty-num">${i.qty}</span>
          <button class="qty-btn" onclick="addItem(${i.id})">+</button>
        </div>
      </div>
    `;
  }).join('');
  cartFooter.style.display = 'block';
  document.getElementById('totalText').textContent = 'Rp ' + fmtNum(getCartTotal());
}

function openCart() {
  document.getElementById('cartOverlay').classList.add('open');
  document.getElementById('cartPanel').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeCart() {
  document.getElementById('cartOverlay').classList.remove('open');
  document.getElementById('cartPanel').classList.remove('open');
  document.body.style.overflow = '';
}

// ==========================================
// CHECKOUT
// ==========================================
function openCheckout() {
  if (!getCartCount()) return;
  closeCart();
  renderQuickCash();
  renderQrisBox();
  document.getElementById('checkoutModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeCheckout() {
  document.getElementById('checkoutModal').classList.remove('open');
  document.body.style.overflow = '';
}

function renderQuickCash() {
  const wrap = document.querySelector('.quick-cash');
  wrap.innerHTML = [5000,10000,15000,20000,50000,100000].map(v =>
    `<button class="quick-cash-btn" onclick="setCash(${v})">Rp ${fmtNum(v)}</button>`
  ).join('');
}

function renderQrisBox() {
  const total = getCartTotal();
  document.getElementById('qris-box-content').innerHTML = `
    ${QRIS_IMAGE
      ? `<img src="${QRIS_IMAGE}" alt="QRIS ${QRIS_MERCHANT_NAME}" onerror="this.replaceWith(qrisPlaceholderEl())">`
      : qrisPlaceholderHTML()}
    <div class="qris-merchant">${QRIS_MERCHANT_NAME}</div>
    <div class="qris-amount-row"><span>${t('qris_total_label')}</span><span>Rp ${fmtNum(total)}</span></div>
  `;
}

function qrisPlaceholderHTML() {
  return `<div class="qris-placeholder">
    <span class="qp-icon">📷</span>
    <span class="qp-text">${t('qris_not_set')}</span>
  </div>`;
}
function qrisPlaceholderEl() {
  const div = document.createElement('div');
  div.innerHTML = qrisPlaceholderHTML();
  return div.firstElementChild;
}

function selectPayment(method) {
  selectedPayment = method;
  ['cash','qris'].forEach(m => document.getElementById('pay-' + m)?.classList.remove('selected'));
  document.getElementById('pay-' + method)?.classList.add('selected');
  document.getElementById('cash-section').style.display = method === 'cash' ? 'block' : 'none';
  document.getElementById('qris-section').style.display = method === 'qris' ? 'block' : 'none';
}

function setCash(v) {
  document.getElementById('f-cash').value = v;
  calcChange();
}
function calcChange() {
  const cash = parseInt(document.getElementById('f-cash').value || 0);
  const total = getCartTotal();
  const change = cash - total;
  const display = document.getElementById('change-display');
  if (cash > 0) {
    display.style.display = 'flex';
    document.getElementById('change-val').textContent = 'Rp ' + fmtNum(Math.max(0, change));
  } else {
    display.style.display = 'none';
  }
}

// ==========================================
// UPLOAD BUKTI PEMBAYARAN
// ==========================================
function triggerProofUpload() { document.getElementById('proof-input')?.click(); }

function handleProofFile(e) {
  const file = e.target.files[0];
  if (!file) return;
  if (!file.type.startsWith('image/')) { alert(t('alert_file_image')); return; }
  if (file.size > 3 * 1024 * 1024) { alert(t('alert_file_size')); return; }

  const reader = new FileReader();
  reader.onload = ev => {
    paymentProof = ev.target.result;
    const preview = document.getElementById('proof-preview');
    preview.innerHTML = `<img src="${paymentProof}"><span class="proof-check">${t('proof_attached')}</span>`;
    preview.style.display = 'flex';
  };
  reader.readAsDataURL(file);
}

// ==========================================
// API HELPERS + LOCAL STORAGE FALLBACK
// ==========================================
async function apiPost(url, data) {
  try {
    const r = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': window.CSRF_TOKEN,
        'Accept': 'application/json',
      },
      body: JSON.stringify(data),
    });
    const json = await r.json().catch(() => null);
    if (!r.ok) return json;
    return json;
  } catch { return null; }
}
async function apiGet(url) {
  try {
    const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!r.ok) return null;
    return await r.json();
  } catch { return null; }
}
function getLocalOrders() { try { return JSON.parse(localStorage.getItem('tt_orders') || '[]'); } catch { return []; } }
function saveLocalOrders(orders) { localStorage.setItem('tt_orders', JSON.stringify(orders)); }

// ==========================================
// SUBMIT ORDER
// ==========================================
async function submitOrder() {
  const name = document.getElementById('f-name')?.value?.trim();
  if (!name) { alert(t('alert_name_required')); return; }
  const phone = document.getElementById('f-phone')?.value?.trim();
  if (!phone) { alert(t('alert_phone_required')); return; }
  const address = document.getElementById('f-address')?.value?.trim();
  if (!address) { alert(t('alert_address_required')); return; }

  const notes = document.getElementById('f-notes')?.value?.trim() || '-';
  const items = Object.values(cart);
  const total = getCartTotal();
  if (!items.length) { alert(t('alert_pick_menu')); return; }

  const cashAmount = parseInt(document.getElementById('f-cash')?.value || 0);
  const changeAmount = cashAmount - total;
  if (selectedPayment === 'cash' && cashAmount < total) { alert(t('alert_cash_less')); return; }

  const orderCode = 'TT' + Date.now().toString().slice(-6);

  const orderObj = {
    id: Date.now(),
    order_code: orderCode,
    customer_name: name,
    customer_phone: phone,
    customer_address: address,
    notes: notes === '-' ? '' : notes,
    items: items.map(i => ({ id: i.id, name: i.name, price: i.price, qty: i.qty, subtotal: i.price * i.qty })),
    total,
    payment_method: selectedPayment,
    cash_amount: selectedPayment === 'cash' ? cashAmount : 0,
    change_amount: selectedPayment === 'cash' ? Math.max(0, changeAmount) : 0,
    payment_proof: paymentProof || null,
    status: 'pending',
    created_at: new Date().toISOString()
  };

  const apiResult = await apiPost('/order', orderObj);
  if (!apiResult || !apiResult.success) {
    const orders = getLocalOrders();
    orders.unshift(orderObj);
    saveLocalOrders(orders);
  }

  localStorage.setItem('tt_last_order_code', orderCode);
  localStorage.setItem('tt_last_order_phone', phone);
  localStorage.setItem('tt_last_order_address', address);

  cart = {};
  paymentProof = null;
  document.getElementById('proof-preview').style.display = 'none';
  document.getElementById('f-cash').value = '';
  closeCheckout();
  renderMenuGrid();
  updateCartUI();

  document.getElementById('kodePesananDisplay').textContent = orderCode;
  document.getElementById('successModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeSuccess() {
  document.getElementById('successModal').classList.remove('open');
  document.body.style.overflow = '';
}

// ==========================================
// STATUS PESANAN
// ==========================================
function openStatusModal() {
  closeCart();
  document.getElementById('status-modal').classList.add('open');
  document.body.style.overflow = 'hidden';
  const input = document.getElementById('status-code-input');
  const lastCode = localStorage.getItem('tt_last_order_code');
  if (lastCode && !input.value) input.value = lastCode;
  document.getElementById('status-result').innerHTML = '';
  if (lastCode) checkOrderStatus();
}
function closeStatusModal() {
  document.getElementById('status-modal').classList.remove('open');
  document.body.style.overflow = '';
}

// ==========================================
// RIWAYAT PESANAN CUSTOMER
// ==========================================
function escapeHistoryHtml(value) {
  return String(value ?? '').replace(/[&<>'"]/g, char => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;',
  }[char]));
}

function openHistoryModal() {
  closeCart();
  document.getElementById('history-modal').classList.add('open');
  document.body.style.overflow = 'hidden';
  loadOrderHistory();
}

function closeHistoryModal() {
  document.getElementById('history-modal').classList.remove('open');
  document.body.style.overflow = '';
}

async function loadOrderHistory() {
  const result = document.getElementById('history-result');
  result.innerHTML = `<div class="history-loading">${t('history_loading')}</div>`;
  const response = await apiGet('/customer/orders');
  if (!response?.success) {
    result.innerHTML = `<div class="history-empty"><div class="history-empty-icon">⚠️</div><p>${t('history_error')}</p></div>`;
    return;
  }

  const orders = response.orders || [];
  const total = orders.reduce((sum, order) => sum + Number(order.total || 0), 0);
  const completed = orders.filter(order => order.status === 'done').length;
  const summary = `<div class="history-summary"><div class="history-summary-card"><div class="history-summary-label">${t('history_total_orders')}</div><div class="history-summary-value">${orders.length}</div></div><div class="history-summary-card"><div class="history-summary-label">${t('history_completed')}</div><div class="history-summary-value">${completed}</div></div></div>`;

  if (!orders.length) {
    result.innerHTML = `${summary}<div class="history-empty"><div class="history-empty-icon">🛒</div><p>${t('history_empty')}</p><a href="#menu" onclick="closeHistoryModal();document.getElementById('menu').scrollIntoView({behavior:'smooth'})">${t('history_see_menu')}</a></div>`;
    return;
  }

  const statusLabels = t('history_status_labels');
  const dateLocale = currentLang === 'en' ? 'en-US' : 'id-ID';
  const list = orders.map(order => {
    const items = (order.items || []).map(item => `<div class="history-item"><span><strong>${escapeHistoryHtml(item.qty)}x</strong> ${escapeHistoryHtml(item.name)}</span><span>Rp ${fmtNum(item.subtotal)}</span></div>`).join('');
    const date = new Date(order.created_at).toLocaleDateString(dateLocale, { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    const status = escapeHistoryHtml(order.status);
    return `<article class="history-order"><div class="history-order-top"><div><div class="history-code">${escapeHistoryHtml(order.order_code)}</div><div class="history-date">${date}</div></div><span class="history-status ${status}">${escapeHistoryHtml(statusLabels[order.status] || order.status)}</span></div>${items}<div class="history-total"><span>${t('cart_total')}</span><strong>Rp ${fmtNum(order.total)}</strong></div></article>`;
  }).join('');
  result.innerHTML = `${summary}<div class="history-list">${list}</div>`;
}

async function checkOrderStatus() {
  const code = document.getElementById('status-code-input')?.value?.trim().toUpperCase();
  const result = document.getElementById('status-result');
  if (!code) { alert(t('alert_enter_code')); return; }

  result.innerHTML = `<div class="status-empty"><div class="se-icon">⏳</div><p>${t('status_searching')}</p></div>`;

  const apiResult = await apiGet(`/order/status?code=${encodeURIComponent(code)}`);
  if (apiResult && apiResult.success) { renderStatusResult(apiResult.order, code); return; }

  const localOrder = getLocalOrders().find(o => o.order_code === code);
  if (localOrder) { renderStatusResult({ ...localOrder, has_payment_proof: !!localOrder.payment_proof }, code); return; }

  result.innerHTML = `<div class="status-empty"><div class="se-icon">🔍</div><p>${t('status_not_found')(code)}</p></div>`;
}

function renderStatusResult(order, code) {
  const result = document.getElementById('status-result');
  const isCancelled = order.status === 'cancelled';
  const steps = t('steps');
  const currentIdx = steps.findIndex(s => s.key === order.status);
  const fillPercent = isCancelled ? 0 : Math.max(0, currentIdx) / (steps.length - 1) * 100;
  const sellerPhone = '085187408288';
  const sellerHref = 'https://wa.me/6285187408288';
  const payLabelMap = t('pay_label_map');

  const itemsHTML = (order.items || []).map(i => `
    <div class="cart-item">
      <div class="cart-item-info">
        <div class="cart-item-name">${i.name}</div>
        <div class="cart-item-price">Rp ${fmtNum(i.price)} × ${i.qty}</div>
      </div>
      <div class="cart-item-price">Rp ${fmtNum(i.subtotal ?? i.price * i.qty)}</div>
    </div>
  `).join('');

  result.innerHTML = `
    <div class="status-order-code">${order.order_code || code}</div>
    <div class="status-order-sub">${order.customer_name || ''} &middot; Rp ${fmtNum(order.total || 0)} &middot; ${payLabelMap[order.payment_method] || order.payment_method}</div>
    <div class="status-seller-box" style="margin:8px 0 12px;padding:10px 12px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.02);border-radius:12px;font-size:0.85rem;color:#e5e5e5;">
      <strong>${t('status_seller')}</strong> <a href="${sellerHref}" target="_blank" rel="noopener" style="color:#fff;text-decoration:underline;">${sellerPhone}</a>
    </div>

    ${isCancelled ? `<div class="status-cancelled-banner">${t('status_cancelled')}</div>` : `
      <div class="status-timeline">
        <div class="st-fill" style="width:${fillPercent}%"></div>
        ${steps.map((s, i) => `
          <div class="status-step ${i < currentIdx ? 'done' : ''} ${i === currentIdx ? 'current' : ''}">
            <div class="status-dot">${s.icon}</div>
            <div class="status-step-label">${s.label}</div>
          </div>
        `).join('')}
      </div>
    `}

    <div style="text-align:center;margin-bottom:14px">
      ${order.has_payment_proof
        ? `<span class="status-proof-badge">${t('status_proof_received')}</span>`
        : order.payment_method === 'qris'
          ? `<span class="status-proof-badge" style="background:#fef3c7;border-color:#f59e0b;color:#92400e">${t('status_proof_missing')}</span>`
          : ''}
    </div>

    ${order.payment_method === 'qris' && !order.has_payment_proof ? `
      <input type="file" id="status-proof-input" accept="image/*" style="display:none" onchange="handleStatusProofFile(event,'${order.order_code || code}')">
      <button type="button" class="btn-outline-action" style="margin-bottom:14px" onclick="document.getElementById('status-proof-input').click()">${t('proof_upload')}</button>
    ` : ''}

    <div class="section-divider">${t('status_detail')}</div>
    ${itemsHTML}
    ${order.customer_address ? `<div class="section-divider" style="margin-top:12px">${t('status_address')}</div><div style="padding:10px 12px;border-radius:12px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);color:#e5e5e5;white-space:pre-line;">${order.customer_address}</div>` : ''}
  `;
}

async function handleStatusProofFile(e, orderCode) {
  const file = e.target.files[0];
  if (!file) return;
  if (!file.type.startsWith('image/')) { alert(t('alert_file_image')); return; }
  if (file.size > 3 * 1024 * 1024) { alert(t('alert_file_size')); return; }

  const reader = new FileReader();
  reader.onload = async ev => {
    const proofData = ev.target.result;
    const apiResult = await apiPost('/order/upload-proof', { order_code: orderCode, payment_proof: proofData });
    if (!apiResult || !apiResult.success) {
      const orders = getLocalOrders();
      const idx = orders.findIndex(o => o.order_code === orderCode);
      if (idx > -1) {
        orders[idx].payment_proof = proofData;
        if (orders[idx].status === 'pending') orders[idx].status = 'confirmed';
        saveLocalOrders(orders);
      }
    }
    checkOrderStatus();
  };
  reader.readAsDataURL(file);
}

// Close modal on overlay click
document.getElementById('checkoutModal').addEventListener('click', function(e) { if (e.target === this) closeCheckout(); });
document.getElementById('successModal').addEventListener('click', function(e) { if (e.target === this) closeSuccess(); });
document.getElementById('status-modal').addEventListener('click', function(e) { if (e.target === this) closeStatusModal(); });
document.getElementById('status-code-input').addEventListener('keydown', function(e) { if (e.key === 'Enter') checkOrderStatus(); });

// INIT
loadData();