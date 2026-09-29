@extends('layouts.admin')

@section('title', 'Menu')

@section('content')
<div class="page active" id="page-menus">
  <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div class="page-title">Manajemen Menu</div>
      <div class="page-sub">Atur ketersediaan, stok, dan tambah menu baru</div>
    </div>
    <button class="btn btn-primary" onclick="openAddMenu()">+ Tambah Menu</button>
  </div>

  <div class="table-card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Nama Menu</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="menus-tbody">
          @foreach ($menus as $m)
            <tr data-menu-id="{{ $m->id }}">
              <td>
                <strong>{{ $m->name }}</strong>
                @if ($m->description)
                  <div style="font-size:11px;color:#999;margin-top:2px">{{ $m->description }}</div>
                @endif
              </td>
              <td>{{ $m->category->name ?? '-' }}</td>
              <td><strong>Rp {{ number_format($m->price, 0, ',', '.') }}</strong></td>
              <td>
                @if ($m->stock !== null)
                  <div style="display:flex;align-items:center;gap:6px">
                    <button class="btn btn-secondary btn-sm" style="padding:2px 8px" onclick="adjustStock({{ $m->id }},-1)">−</button>
                    <span class="badge {{ $m->stock <= 5 ? 'badge-unavailable' : 'badge-available' }}" style="min-width:38px;text-align:center" id="stock-val-{{ $m->id }}">{{ $m->stock }}</span>
                    <button class="btn btn-secondary btn-sm" style="padding:2px 8px" onclick="adjustStock({{ $m->id }},1)">+</button>
                  </div>
                @else
                  <span class="badge" style="background:#eef2f7;color:#64748b">∞ Tak Terbatas</span>
                @endif
              </td>
              <td>
                <label class="toggle-switch" title="Ubah ketersediaan">
                  <input type="checkbox" {{ $m->is_available ? 'checked' : '' }} onclick="toggleMenuAvail({{ $m->id }})">
                  <div class="toggle-track"><div class="toggle-thumb"></div></div>
                </label>
                <span class="badge {{ $m->is_available ? 'badge-available' : 'badge-unavailable' }}" style="margin-left:6px" id="avail-badge-{{ $m->id }}">
                  {{ $m->is_available ? 'Tersedia' : 'Habis' }}
                </span>
              </td>
              <td>
                <button class="btn btn-secondary btn-sm" onclick='editMenu(@json($m))'>✏️ Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteMenu({{ $m->id }})" style="margin-left:4px">🗑</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- MODAL: ADD/EDIT MENU -->
<div class="modal-overlay" id="menu-modal">
  <div class="modal-box">
    <div class="modal-box-header">
      <span class="modal-box-title" id="menu-modal-title">Tambah Menu</span>
      <button class="modal-box-close" onclick="closeModal('menu-modal')">✕</button>
    </div>
    <div class="modal-box-body">
      <input type="hidden" id="edit-menu-id">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Kategori</label>
          <select class="form-select" id="m-category">
            @foreach ($categories as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Tambah Kategori Baru</label>
        <div style="display:flex;gap:8px">
          <input class="form-input" id="new-category-name" type="text" placeholder="Contoh: Paket Hemat">
          <button class="btn btn-secondary btn-sm" onclick="addCategoryFromMenuModal()" style="white-space:nowrap">+ Baru</button>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Nama Menu</label>
        <input class="form-input" id="m-name" type="text" placeholder="Contoh: Ayam Crispy + Nasi">
      </div>
      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea class="form-input form-textarea" id="m-desc" placeholder="Deskripsi menu..."></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Gambar Menu</label>
        <input class="form-input" id="m-image" type="file" accept="image/jpeg,image/png,image/webp">
        <div id="m-image-current" style="font-size:0.75rem;color:var(--gray);margin-top:4px">Pilih foto produk (maks. 10 MB). Kosongkan saat edit untuk memakai gambar lama.</div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Harga (Rp)</label>
          <input class="form-input" id="m-price" type="number" placeholder="10000">
        </div>
        <div class="form-group">
          <label class="form-label">Status</label>
          <select class="form-select" id="m-available">
            <option value="1">✅ Tersedia</option>
            <option value="0">❌ Habis</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Stok (porsi)</label>
        <input class="form-input" id="m-stock" type="number" min="0" placeholder="Kosongkan = tidak dibatasi">
        <div style="font-size:0.75rem;color:var(--gray);margin-top:4px">Kosongkan kalau stok menu ini tidak perlu dibatasi/dilacak (misal: nasi, sambal, saus).</div>
      </div>
    </div>
    <div class="modal-box-footer">
      <button class="btn btn-secondary" onclick="closeModal('menu-modal')">Batal</button>
      <button class="btn btn-primary" onclick="saveMenu()">💾 Simpan</button>
    </div>
  </div>
</div>
@endsection
