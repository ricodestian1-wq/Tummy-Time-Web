// ==========================================
// STATE
// ==========================================
let allOrders = [];
let filterStatus = 'all';
let searchQuery = '';
let editingMenuId = null;
let pendingQrisImage = null;

// ==========================================
// API HELPERS (semua panggil route Laravel + CSRF token)
// ==========================================
async function apiPost(url, data) {
  try {
    const r = await fetch(url, {
      method: 'POST',
      headers: {
        ...(data instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
        'X-CSRF-TOKEN': window.CSRF_TOKEN,
        'Accept': 'application/json',
      },
      body: data instanceof FormData ? data : JSON.stringify(data),
    });
    const json = await r.json().catch(() => null);
    if (!r.ok) {
      const validationError = json?.errors ? Object.values(json.errors).flat()[0] : null;
      return { success: false, error: json?.error || validationError || json?.message || `Server merespon status ${r.status}` };
    }
    return json;
  } catch (err) {
    return { success: false, error: 'Tidak bisa menghubungi server: ' + err.message };
  }
}

async function apiGet(url) {
  try {
    const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
    if (!r.ok) return null;
    return await r.json();
  } catch { return null; }
}

function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.classList.remove('show'), 2500);
}

function closeModal(id) {
  document.getElementById(id).classList.remove('show');
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
  });
});

// ==========================================
// UTILS
// ==========================================
function fmtNum(n) {
  return (n || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function payIcon(m) { return { cash: '💵', transfer: '🏦', qris: '📱' }[m] || '💳'; }
function payLabel(m) { return { cash: 'Tunai', transfer: 'Transfer', qris: 'QRIS' }[m] || m; }
function statusLabel(s) {
  return {
    pending: 'Menunggu', confirmed: 'Dikonfirmasi',
    cooking: 'Dimasak', ready: 'Siap Ambil',
    done: 'Selesai', cancelled: 'Dibatalkan',
  }[s] || s;
}
function timeAgo(dt) {
  const diff = Date.now() - new Date(dt).getTime();
  const mins = Math.floor(diff / 60000);
  if (mins < 1) return 'Baru saja';
  if (mins < 60) return `${mins} menit lalu`;
  const hrs = Math.floor(mins / 60);
  if (hrs < 24) return `${hrs} jam lalu`;
  return new Date(dt).toLocaleDateString('id-ID');
}

// ==========================================
// TOGGLE BUKA/TUTUP TOKO (sidebar, tersedia di semua halaman admin)
// ==========================================
async function toggleShop() {
  const toggle = document.getElementById('toggle-shop');
  const label = document.getElementById('shop-status-label');
  const statusEl = document.getElementById('sidebar-shop-status');
  const newState = toggle.checked;

  const result = await apiPost('/admin/settings/toggle-open', { is_open: newState });

  if (result && result.success) {
    label.textContent = newState ? '🟢 BUKA' : '🔴 TUTUP';
    statusEl.className = 'shop-status ' + (newState ? 'open' : 'closed');
    showToast(newState ? '🟢 Toko sekarang BUKA (tersimpan ke database)' : '🔴 Toko sekarang TUTUP (tersimpan ke database)');
    const openSelect = document.getElementById('cfg-open');
    if (openSelect) openSelect.value = newState ? '1' : '0';
  } else {
    toggle.checked = !newState;
    showToast('❌ Gagal simpan ke database: ' + (result?.error || 'tidak diketahui'));
  }
}

// ==========================================
// SETTINGS (halaman Pengaturan)
// ==========================================
async function saveSettings() {
  const payload = {
    shop_name: document.getElementById('cfg-name').value,
    wa_number: document.getElementById('cfg-wa').value,
    is_open: document.getElementById('cfg-open').value === '1',
    closed_message: document.getElementById('cfg-closed-msg').value,
  };

  const result = await apiPost('/admin/settings', payload);

  if (result && result.success) {
    showToast('✅ Pengaturan disimpan ke database!');
    const toggle = document.getElementById('toggle-shop');
    if (toggle) {
      toggle.checked = payload.is_open;
      document.getElementById('shop-status-label').textContent = payload.is_open ? '🟢 BUKA' : '🔴 TUTUP';
      document.getElementById('sidebar-shop-status').className = 'shop-status ' + (payload.is_open ? 'open' : 'closed');
    }
  } else {
    showToast('❌ Gagal simpan ke database: ' + (result?.error || 'tidak diketahui'));
  }
}

function handleQrisFile(e) {
  const file = e.target.files[0];
  if (!file) return;
  if (!file.type.startsWith('image/')) { showToast('❌ File harus berupa gambar'); return; }
  if (file.size > 3 * 1024 * 1024) { showToast('❌ Ukuran gambar maksimal 3MB'); return; }

  const reader = new FileReader();
  reader.onload = ev => {
    pendingQrisImage = ev.target.result;
    const img = document.getElementById('qris-preview-img');
    const empty = document.getElementById('qris-preview-empty');
    img.src = pendingQrisImage;
    img.style.display = 'block';
    if (empty) empty.style.display = 'none';
    showToast('✓ Gambar dipilih, klik "Simpan QRIS" untuk menyimpan');
  };
  reader.readAsDataURL(file);
}

async function saveQrisSettings() {
  const payload = {
    qris_merchant_name: document.getElementById('cfg-qris-merchant').value,
  };
  if (pendingQrisImage) payload.qris_image = pendingQrisImage;

  const result = await apiPost('/admin/settings/upload-qris', payload);

  if (result && result.success) {
    pendingQrisImage = null;
    showToast('✅ QRIS berhasil disimpan! Otomatis muncul di halaman pembeli.');
  } else {
    showToast('❌ Gagal simpan QRIS: ' + (result?.error || 'tidak diketahui'));
  }
}

async function changePassword() {
  const oldPass = document.getElementById('old-pass').value;
  const newPass = document.getElementById('new-pass').value;
  const confPass = document.getElementById('conf-pass').value;

  if (!oldPass || !newPass) { showToast('⚠️ Semua field wajib diisi!'); return; }
  if (newPass !== confPass) { showToast('❌ Konfirmasi password tidak sama!'); return; }
  if (newPass.length < 6) { showToast('⚠️ Password baru minimal 6 karakter'); return; }

  const result = await apiPost('/admin/change-password', {
    old_password: oldPass,
    new_password: newPass,
    new_password_confirmation: confPass,
  });

  if (result && result.success) {
    showToast('✅ Password berhasil diubah!');
    document.getElementById('old-pass').value = '';
    document.getElementById('new-pass').value = '';
    document.getElementById('conf-pass').value = '';
  } else {
    showToast('❌ Gagal ganti password: ' + (result?.error || 'periksa password lama'));
  }
}

async function addCategory() {
  const name = document.getElementById('new-cat').value.trim();
  if (!name) return;

  const result = await apiPost('/admin/category/save', { name });
  if (result && result.success) {
    showToast('✅ Kategori ditambahkan');
    location.reload();
  } else {
    showToast('❌ Gagal tambah kategori: ' + (result?.error || 'tidak diketahui'));
  }
}

async function addCategoryFromMenuModal() {
  const input = document.getElementById('new-category-name');
  const select = document.getElementById('m-category');
  const name = input.value.trim();

  if (!name) {
    showToast('⚠️ Isi nama kategori baru terlebih dahulu');
    return;
  }

  const result = await apiPost('/admin/category/save', { name });
  if (result && result.success) {
    const option = new Option(result.name, result.id);
    select.add(option);
    select.value = String(result.id);
    input.value = '';
    showToast('✅ Kategori ditambahkan');
  } else {
    showToast('❌ Gagal tambah kategori: ' + (result?.error || 'tidak diketahui'));
  }
}

async function deleteCategory(id) {
  if (!confirm('Hapus kategori ini? Menu di dalamnya juga akan ikut terhapus.')) return;
  const result = await apiPost('/admin/category/' + id + '/delete', {});
  if (result && result.success) {
    showToast('✅ Kategori dihapus');
    location.reload();
  } else {
    showToast('❌ Gagal hapus kategori: ' + (result?.error || 'tidak diketahui'));
  }
}

// ==========================================
// ORDERS (halaman Pesanan)
// ==========================================
async function loadOrders() {
  const result = await apiGet('/admin/orders/list');
  allOrders = (result && result.success) ? result.orders : [];
  renderOrdersTable();
  updatePendingBadge();
}

function updatePendingBadge() {
  const pending = allOrders.filter(o => o.status === 'pending').length;
  const badge = document.getElementById('pending-badge');
  if (!badge) return;
  badge.style.display = pending > 0 ? 'block' : 'none';
  badge.textContent = pending;
}

function renderOrdersTable() {
  const tbody = document.getElementById('orders-tbody');
  if (!tbody) return;

  let filtered = [...allOrders];
  if (filterStatus !== 'all') filtered = filtered.filter(o => o.status === filterStatus);
  if (searchQuery) {
    const q = searchQuery.toLowerCase();
    filtered = filtered.filter(o =>
      o.order_code.toLowerCase().includes(q) || o.customer_name.toLowerCase().includes(q)
    );
  }

  if (!filtered.length) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:24px;color:#999">Tidak ada pesanan</td></tr>';
    return;
  }

  tbody.innerHTML = filtered.map(o => `
    <tr>
      <td><strong>${o.order_code}</strong></td>
      <td>${o.customer_name}</td>
      <td><strong>Rp ${fmtNum(o.total)}</strong></td>
      <td>${payIcon(o.payment_method)} ${payLabel(o.payment_method)}</td>
      <td><span class="badge badge-${o.status}">${statusLabel(o.status)}</span></td>
      <td style="font-size:12px;color:#999">${timeAgo(o.created_at)}</td>
      <td>
        <button class="btn btn-secondary btn-sm" onclick="viewOrder(${o.id})">👁</button>
        <select class="form-select" style="display:inline;width:auto;padding:4px 8px;font-size:12px;margin-left:4px" onchange="updateOrderStatus(${o.id}, this.value)">
          ${['pending', 'confirmed', 'cooking', 'ready', 'done', 'cancelled'].map(s =>
            `<option value="${s}" ${o.status === s ? 'selected' : ''}>${statusLabel(s)}</option>`
          ).join('')}
        </select>
      </td>
    </tr>
  `).join('');
}

function filterOrders(q) {
  searchQuery = q;
  renderOrdersTable();
}

function filterOrderStatus(status, btn) {
  filterStatus = status;
  document.querySelectorAll('#page-orders .tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderOrdersTable();
}

async function updateOrderStatus(orderId, newStatus) {
  const order = allOrders.find(o => o.id === orderId);
  if (!order) return;

  const result = await apiPost('/admin/orders/update-status', { id: orderId, status: newStatus });
  if (result && result.success) {
    order.status = newStatus;
    renderOrdersTable();
    updatePendingBadge();
    showToast('✅ Status pesanan diperbarui');
  } else {
    showToast('❌ Gagal update status: ' + (result?.error || 'tidak diketahui'));
    renderOrdersTable(); // reset dropdown ke status lama
  }
}

function viewOrder(orderId) {
  const o = allOrders.find(o => o.id === orderId);
  if (!o) return;

  const body = document.getElementById('order-detail-body');
  body.innerHTML = `
    <div style="background:#f8f4f0;border-radius:10px;padding:14px;margin-bottom:14px">
      <div style="display:flex;justify-content:space-between;margin-bottom:8px">
        <strong style="font-size:16px">${o.order_code}</strong>
        <span class="badge badge-${o.status}">${statusLabel(o.status)}</span>
      </div>
      <div style="font-size:13px;color:#666;display:grid;gap:4px">
        <div>👤 <strong>${o.customer_name}</strong></div>
        <div>📞 ${o.customer_phone || '-'}</div>
        <div>🕐 ${new Date(o.created_at).toLocaleString('id-ID')}</div>
        ${o.notes ? `<div>📝 ${o.notes}</div>` : ''}
      </div>
    </div>

    <div class="order-items-list">
      <strong style="font-size:13px;color:#999;letter-spacing:1px">ITEM PESANAN</strong>
      ${(o.items || []).map(item => `
        <div class="order-item-row">
          <span>${item.menu_name || item.name} <span style="color:#999">×${item.qty}</span></span>
          <strong>Rp ${fmtNum(item.subtotal)}</strong>
        </div>
      `).join('')}
      <div class="order-total-row">
        <span>TOTAL</span>
        <span style="color:var(--red)">Rp ${fmtNum(o.total)}</span>
      </div>
    </div>

    <div style="background:#f0f9ff;border-radius:10px;padding:12px;margin-top:14px;font-size:13px">
      <div style="display:flex;justify-content:space-between;margin-bottom:4px">
        <span>💳 Pembayaran</span>
        <strong>${payIcon(o.payment_method)} ${payLabel(o.payment_method)}</strong>
      </div>
      ${o.payment_method === 'cash' ? `
        <div style="display:flex;justify-content:space-between;margin-bottom:4px">
          <span>💵 Uang Dibayar</span>
          <strong>Rp ${fmtNum(o.cash_amount)}</strong>
        </div>
        <div style="display:flex;justify-content:space-between;color:#166534;font-weight:800">
          <span>🔄 Kembalian</span>
          <span>Rp ${fmtNum(Math.max(0, o.change_amount))}</span>
        </div>
      ` : `
        <div style="display:flex;justify-content:space-between;align-items:center">
          <span>📤 Bukti Pembayaran</span>
          <strong style="color:${o.has_payment_proof ? '#166534' : '#b91c1c'}">
            ${o.has_payment_proof ? '✓ Sudah dikirim' : '✗ Belum dikirim'}
          </strong>
        </div>
      `}
    </div>

    <div style="margin-top:14px">
      <label style="font-weight:700;font-size:12px;display:block;margin-bottom:6px">Update Status:</label>
      <div style="display:flex;gap:6px;flex-wrap:wrap">
        ${['confirmed', 'cooking', 'ready', 'done', 'cancelled'].map(s => `
          <button class="btn btn-sm ${s === 'cancelled' ? 'btn-danger' : s === 'done' ? 'btn-success' : 'btn-primary'}"
            onclick="updateOrderStatus(${o.id},'${s}');closeModal('order-modal')"
            ${o.status === s ? 'disabled' : ''}>
            ${statusLabel(s)}
          </button>
        `).join('')}
      </div>
    </div>
  `;

  document.getElementById('order-modal').classList.add('show');
}

// ==========================================
// MENUS (halaman Menu) — tabel sudah dirender server-side oleh Blade,
// JS di sini hanya menangani aksi CRUD + update tampilan tanpa reload penuh.
// ==========================================
function openAddMenu() {
  editingMenuId = null;
  document.getElementById('menu-modal-title').textContent = 'Tambah Menu Baru';
  document.getElementById('edit-menu-id').value = '';
  document.getElementById('m-name').value = '';
  document.getElementById('m-desc').value = '';
  document.getElementById('m-image').value = '';
  document.getElementById('m-image-current').textContent = 'Pilih foto produk (maks. 5 MB).';
  document.getElementById('m-price').value = '';
  document.getElementById('m-available').value = '1';
  document.getElementById('m-stock').value = '';
  document.getElementById('menu-modal').classList.add('show');
}

function editMenu(m) {
  editingMenuId = m.id;
  document.getElementById('menu-modal-title').textContent = 'Edit Menu';
  document.getElementById('edit-menu-id').value = m.id;
  document.getElementById('m-name').value = m.name;
  document.getElementById('m-desc').value = m.description || '';
  document.getElementById('m-image').value = '';
  document.getElementById('m-image-current').textContent = m.image_url
    ? 'Gambar saat ini tersimpan. Pilih file baru jika ingin menggantinya.'
    : 'Belum ada gambar. Pilih foto produk (maks. 5 MB).';
  document.getElementById('m-price').value = m.price;
  document.getElementById('m-category').value = m.category_id;
  document.getElementById('m-available').value = m.is_available ? '1' : '0';
  document.getElementById('m-stock').value = (m.stock === null || m.stock === undefined) ? '' : m.stock;
  document.getElementById('menu-modal').classList.add('show');
}

async function saveMenu() {
  const name = document.getElementById('m-name').value.trim();
  const price = parseInt(document.getElementById('m-price').value);
  const catId = parseInt(document.getElementById('m-category').value);
  const desc = document.getElementById('m-desc').value.trim();
  const avail = document.getElementById('m-available').value === '1';
  const stockRaw = document.getElementById('m-stock').value.trim();
  const stock = stockRaw === '' ? null : Math.max(0, parseInt(stockRaw));

  if (!name || !price) { showToast('⚠️ Nama dan harga wajib diisi!'); return; }

  const formData = new FormData();
  formData.append('id', editingMenuId || '');
  formData.append('category_id', catId);
  formData.append('name', name);
  formData.append('description', desc);
  formData.append('price', price);
  formData.append('is_available', avail ? '1' : '0');
  if (stock !== null) formData.append('stock', stock);
  const image = document.getElementById('m-image').files[0];
  if (image) formData.append('image', image);

  const result = await apiPost('/admin/menu/save', formData);

  if (result && result.success) {
    showToast(editingMenuId ? '✅ Menu berhasil diperbarui' : '✅ Menu baru berhasil ditambahkan');
    closeModal('menu-modal');
    location.reload();
  } else {
    showToast('❌ Gagal simpan menu: ' + (result?.error || 'tidak diketahui'));
  }
}

async function deleteMenu(menuId) {
  if (!confirm('Yakin ingin menghapus menu ini?')) return;
  const result = await apiPost('/admin/menu/' + menuId + '/delete', {});
  if (result && result.success) {
    showToast('✅ Menu dihapus');
    location.reload();
  } else {
    showToast('❌ Gagal hapus menu: ' + (result?.error || 'tidak diketahui'));
  }
}

async function toggleMenuAvail(menuId) {
  const result = await apiPost(`/admin/menu/${menuId}/toggle-availability`, {});
  if (result && result.success) {
    const badge = document.getElementById('avail-badge-' + menuId);
    badge.textContent = result.is_available ? 'Tersedia' : 'Habis';
    badge.className = 'badge ' + (result.is_available ? 'badge-available' : 'badge-unavailable');
    showToast('✅ Status ketersediaan diperbarui');
  } else {
    showToast('❌ Gagal update status: ' + (result?.error || 'tidak diketahui'));
  }
}

async function adjustStock(menuId, delta) {
  const result = await apiPost(`/admin/menu/${menuId}/adjust-stock`, { delta });
  if (result && result.success) {
    const el = document.getElementById('stock-val-' + menuId);
    if (el) {
      el.textContent = result.stock;
      el.className = 'badge ' + (result.stock <= 5 ? 'badge-unavailable' : 'badge-available');
    }
  } else {
    showToast('⚠️ ' + (result?.error || 'Gagal ubah stok'));
  }
}

// ==========================================
// LAPORAN (halaman Laporan)
// ==========================================
async function loadReport() {
  const result = await apiGet('/admin/report/data?period=7');
  if (!result || !result.success) {
    showToast('❌ Gagal memuat laporan');
    return;
  }

  const tbody = document.getElementById('report-tbody');
  if (tbody) {
    tbody.innerHTML = (result.daily && result.daily.length)
      ? result.daily.map(d => `
          <tr>
            <td>${new Date(d.tanggal).toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' })}</td>
            <td>${d.jumlah_pesanan}</td>
            <td><strong>Rp ${fmtNum(d.total_pendapatan)}</strong></td>
          </tr>
        `).join('')
      : '<tr><td colspan="3" style="text-align:center;padding:24px;color:#999">Belum ada data penjualan</td></tr>';
  }

  const payEl = document.getElementById('payment-stats');
  if (payEl) {
    payEl.innerHTML = (result.payment_stats && result.payment_stats.length)
      ? result.payment_stats.map(p => `
          <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--cream-dark)">
            <span>${payIcon(p.payment_method)} ${payLabel(p.payment_method)} (${p.jumlah}x)</span>
            <strong>Rp ${fmtNum(p.pendapatan)}</strong>
          </div>
        `).join('')
      : '<p style="color:#999">Belum ada data.</p>';
  }

  const menuEl = document.getElementById('menu-stats');
  if (menuEl) {
    menuEl.innerHTML = (result.menu_sales && result.menu_sales.length)
      ? result.menu_sales.slice(0, 8).map(m => `
          <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--cream-dark)">
            <span>${m.menu_name}</span>
            <strong>${m.total_terjual} porsi</strong>
          </div>
        `).join('')
      : '<p style="color:#999">Belum ada data.</p>';
  }
}
