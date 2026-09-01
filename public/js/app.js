
const WA_NUMBER = '6285187408288'; // Ganti dengan nomor WA admin
let QRIS_IMAGE = null; // Otomatis terisi dari Admin Dashboard (menu Pengaturan → QRIS Pembayaran), tidak perlu diedit manual
let QRIS_MERCHANT_NAME = 'Tummy Time';

let menuData = [];
let categories = [];
let cart = {};
let isOpen = true;
let closedMessage = 'Maaf, kami sedang tutup. Silakan order lagi ya!';
let paymentProof = null;
let currentFilter = 'semua';
let selectedPayment = 'cash';

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
  if (m.stock === null || m.stock === undefined) return `<div class="menu-stock-row unlimited">✅ Selalu tersedia</div>`;
  if (m.stock <= 0) return '';
  if (m.stock <= 5) return `<div class="menu-stock-row low">🔥 Sisa ${m.stock} porsi!</div>`;
  return `<div class="menu-stock-row ok">📦 Stok: ${m.stock}</div>`;
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
  closedMessage = d.settings?.closed_message || closedMessage;
  if (d.settings?.qris_image) QRIS_IMAGE = d.settings.qris_image;
  if (d.settings?.qris_merchant_name) QRIS_MERCHANT_NAME = d.settings.qris_merchant_name;
  else if (d.settings?.shop_name) QRIS_MERCHANT_NAME = d.settings.shop_name;

  document.getElementById('footer-wa-link').href = `https://wa.me/${WA_NUMBER}`;
  renderAll();
}

function renderAll() {
  const badge = document.getElementById('hero-badge');
  if (!isOpen) {
    badge.textContent = 'Sedang Tutup';
    badge.classList.add('closed');
    document.getElementById('closed-banner').style.display = 'block';
    document.getElementById('closed-msg').textContent = closedMessage;
    document.getElementById('filter-tabs').style.display = 'none';
    document.getElementById('menu-grid').innerHTML = '';
    return;
  }

  badge.textContent = 'Buka Sekarang';
  badge.classList.remove('closed');
  document.getElementById('stat-cat').textContent = categories.length;
  document.getElementById('stat-menu').textContent = menuData.filter(m => m.is_available == 1).length;

  renderFilterTabs();
  renderMenuGrid();
}

function renderFilterTabs() {
  const wrap = document.getElementById('filter-tabs');
  let html = `<button class="filter-tab ${currentFilter === 'semua' ? 'active' : ''}" onclick="filterMenu('semua', this)">🍽️ Semua</button>`;
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
      ? `<img src="${m.image_url}" alt="${(m.name || '').replace(/"/g, '&quot;')}" loading="lazy">`
      : `<span class="menu-icon-fallback">${icon}</span>`;
    return `
      <div class="menu-card ${!available ? 'unavailable' : ''}" ${cardClick}>
        <div class="menu-img">
          ${imageMarkup}
          ${!available ? '<span class="badge-habis">Habis</span>' : isLowStock(m) ? '<span class="badge-limited">Terbatas</span>' : ''}
          ${available && spicy ? '<span class="badge-spicy">🔥 Pedas</span>' : ''}
        </div>
        <div class="menu-body">
          <div class="menu-name">${m.name}</div>
          ${m.description ? `<div class="menu-desc">${m.description}</div>` : ''}
          ${available ? stockRowHtml(m) : ''}
          <div class="menu-footer">
            <div class="menu-price">Rp ${fmtNum(m.price)}</div>
            <div id="ctrl-${m.id}" onclick="event.stopPropagation()">
              ${!available
                ? `<span class="habis-label">Habis</span>`
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
    detailImg.innerHTML = `<img src="${m.image_url}" alt="${(m.name || '').replace(/"/g, '&quot;')}" loading="lazy">`;
  } else {
    detailImg.innerHTML = icon;
  }

  document.getElementById('detail-name').textContent = m.name;
  document.getElementById('detail-price').textContent = 'Rp ' + fmtNum(m.price);
  document.getElementById('detail-desc').textContent = m.description || 'Menu favorit yang wajib kamu coba!';
  document.getElementById('detail-qty-num').textContent = detailQty;

  const box = document.getElementById('detail-stock-box');
  const text = document.getElementById('detail-stock-text');
  box.className = 'detail-stock-box';
  if (m.stock === null || m.stock === undefined) {
    box.classList.add('unlimited');
    text.textContent = 'Selalu tersedia';
  } else if (m.stock <= 5) {
    box.classList.add('low');
    text.textContent = `Stok tersisa ${m.stock} porsi — buruan sebelum habis!`;
  } else {
    box.classList.add('ok');
    text.textContent = `Stok tersedia: ${m.stock} porsi`;
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
    btn.textContent = '😔 Stok Tidak Cukup';
  } else {
    btn.disabled = false;
    btn.innerHTML = '🛒 Tambah ke Keranjang';
  }
}

function detailAddToCart() {
  const m = menuData.find(x => x.id === detailMenuId);
  if (!m || !isInStock(m)) return;

  const already = cart[m.id]?.qty || 0;
  const maxAddable = (m.stock === null || m.stock === undefined) ? Infinity : Math.max(0, m.stock - already);
  const addQty = Math.min(detailQty, maxAddable);
  if (addQty <= 0) { showToastMsg(`Stok ${m.name} sudah habis di keranjangmu`); return; }

  if (cart[m.id]) cart[m.id].qty += addQty;
  else cart[m.id] = { id: m.id, name: m.name, price: m.price, qty: addQty };

  renderMenuGrid();
  updateCartUI();
  showToastMsg(`${m.name} ×${addQty} ditambahkan ke keranjang`);
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
    showToastMsg(`Stok ${item.name} tinggal ${item.stock}`);
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
    document.getElementById('floatCartText').textContent = `${count} item • Rp ${fmtNum(total)}`;
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
    cartItemsEl.innerHTML = `<div class="cart-empty"><span class="cart-empty-icon">🛒</span><p>Keranjang masih kosong</p><p style="font-size:0.78rem;margin-top:4px;color:#aaa">Pilih menu yang kamu suka!</p></div>`;
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
    <div class="qris-amount-row"><span>Total Bayar</span><span>Rp ${fmtNum(total)}</span></div>
  `;
}

function qrisPlaceholderHTML() {
  return `<div class="qris-placeholder">
    <span class="qp-icon">📷</span>
    <span class="qp-text">Gambar QRIS belum diatur.<br>Atur lewat Admin Dashboard → Pengaturan.</span>
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
  if (!file.type.startsWith('image/')) { alert('File harus berupa gambar'); return; }
  if (file.size > 3 * 1024 * 1024) { alert('Ukuran gambar maksimal 3MB'); return; }

  const reader = new FileReader();
  reader.onload = ev => {
    paymentProof = ev.target.result;
    const preview = document.getElementById('proof-preview');
    preview.innerHTML = `<img src="${paymentProof}"><span class="proof-check">✓ Bukti pembayaran terlampir</span>`;
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
    if (!r.ok) return json; // tetap kembalikan payload error (mis. {success:false, error:'...'}) biar bisa ditampilkan
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
  if (!name) { alert('Nama wajib diisi!'); return; }
  const phone = document.getElementById('f-phone')?.value?.trim();
  if (!phone) { alert('Nomor WhatsApp wajib diisi!'); return; }
  const address = document.getElementById('f-address')?.value?.trim();
  if (!address) { alert('Alamat pembeli wajib diisi!'); return; }

  const notes = document.getElementById('f-notes')?.value?.trim() || '-';
  const items = Object.values(cart);
  const total = getCartTotal();
  if (!items.length) { alert('Pilih menu dulu!'); return; }

  const cashAmount = parseInt(document.getElementById('f-cash')?.value || 0);
  const changeAmount = cashAmount - total;
  if (selectedPayment === 'cash' && cashAmount < total) { alert('Uang tunai kurang dari total!'); return; }

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

  setTimeout(() => {
    closeSuccess();
    openStatusModal();
  }, 800);
}

function closeSuccess() {
  document.getElementById('successModal').classList.remove('open');
  document.body.style.overflow = '';
}

// ==========================================
// STATUS PESANAN
// ==========================================
const STATUS_STEPS = [
  { key: 'pending',   label: 'Menunggu',    icon: '⏳' },
  { key: 'confirmed', label: 'Dikonfirmasi', icon: '✅' },
  { key: 'cooking',   label: 'Dimasak',     icon: '🔥' },
  { key: 'ready',     label: 'Siap Ambil',  icon: '📦' },
  { key: 'done',      label: 'Selesai',     icon: '🎉' },
];

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

async function checkOrderStatus() {
  const code = document.getElementById('status-code-input')?.value?.trim().toUpperCase();
  const result = document.getElementById('status-result');
  if (!code) { alert('Masukkan kode pesanan dulu'); return; }

  result.innerHTML = `<div class="status-empty"><div class="se-icon">⏳</div><p>Mencari pesanan...</p></div>`;

  const apiResult = await apiGet(`/order/status?code=${encodeURIComponent(code)}`);
  if (apiResult && apiResult.success) { renderStatusResult(apiResult.order, code); return; }

  const localOrder = getLocalOrders().find(o => o.order_code === code);
  if (localOrder) { renderStatusResult({ ...localOrder, has_payment_proof: !!localOrder.payment_proof }, code); return; }

  result.innerHTML = `<div class="status-empty"><div class="se-icon">🔍</div><p>Pesanan dengan kode <strong>${code}</strong> tidak ditemukan.<br>Pastikan kode sudah benar ya!</p></div>`;
}

function renderStatusResult(order, code) {
  const result = document.getElementById('status-result');
  const isCancelled = order.status === 'cancelled';
  const currentIdx = STATUS_STEPS.findIndex(s => s.key === order.status);
  const fillPercent = isCancelled ? 0 : Math.max(0, currentIdx) / (STATUS_STEPS.length - 1) * 100;
  const sellerPhone = '085187408288';
  const sellerHref = 'https://wa.me/6285187408288';

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
    <div class="status-order-sub">${order.customer_name || ''} &middot; Rp ${fmtNum(order.total || 0)} &middot; ${{cash:'Tunai',qris:'QRIS'}[order.payment_method] || order.payment_method}</div>
    <div class="status-seller-box" style="margin:8px 0 12px;padding:10px 12px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.02);border-radius:12px;font-size:0.85rem;color:#e5e5e5;">
      <strong>Nomor penjual:</strong> <a href="${sellerHref}" target="_blank" rel="noopener" style="color:#fff;text-decoration:underline;">${sellerPhone}</a>
    </div>

    ${isCancelled ? `<div class="status-cancelled-banner">❌ Pesanan ini dibatalkan</div>` : `
      <div class="status-timeline">
        <div class="st-fill" style="width:${fillPercent}%"></div>
        ${STATUS_STEPS.map((s, i) => `
          <div class="status-step ${i < currentIdx ? 'done' : ''} ${i === currentIdx ? 'current' : ''}">
            <div class="status-dot">${s.icon}</div>
            <div class="status-step-label">${s.label}</div>
          </div>
        `).join('')}
      </div>
    `}

    <div style="text-align:center;margin-bottom:14px">
      ${order.has_payment_proof
        ? `<span class="status-proof-badge">✓ Bukti pembayaran diterima</span>`
        : order.payment_method === 'qris'
          ? `<span class="status-proof-badge" style="background:#fef3c7;border-color:#f59e0b;color:#92400e">⚠️ Bukti pembayaran belum dikirim</span>`
          : ''}
    </div>

    ${order.payment_method === 'qris' && !order.has_payment_proof ? `
      <input type="file" id="status-proof-input" accept="image/*" style="display:none" onchange="handleStatusProofFile(event,'${order.order_code || code}')">
      <button type="button" class="btn-outline-action" style="margin-bottom:14px" onclick="document.getElementById('status-proof-input').click()">📤 Kirim Bukti Pembayaran</button>
    ` : ''}

    <div class="section-divider">Detail Pesanan</div>
    ${itemsHTML}
    ${order.customer_address ? `<div class="section-divider" style="margin-top:12px">Alamat Pembeli</div><div style="padding:10px 12px;border-radius:12px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.08);color:#e5e5e5;white-space:pre-line;">${order.customer_address}</div>` : ''}
  `;
}

async function handleStatusProofFile(e, orderCode) {
  const file = e.target.files[0];
  if (!file) return;
  if (!file.type.startsWith('image/')) { alert('File harus berupa gambar'); return; }
  if (file.size > 3 * 1024 * 1024) { alert('Ukuran gambar maksimal 3MB'); return; }

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
