@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<div class="page active" id="page-settings">
  <div class="page-header">
    <div class="page-title">⚙️ Pengaturan</div>
    <div class="page-sub">Konfigurasi toko dan sistem</div>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px">
    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">🏪 Pengaturan Toko</span></div>
      <div style="padding:20px">
        <div class="form-group">
          <label class="form-label">Nama Toko</label>
          <input class="form-input" id="cfg-name" type="text" value="{{ $settings->shop_name }}">
        </div>
        <div class="form-group">
          <label class="form-label">Nomor WhatsApp Admin</label>
          <input class="form-input" id="cfg-wa" type="text" value="{{ $settings->wa_number }}" placeholder="628xxxxxxxxxx">
        </div>
        <div class="form-group">
          <label class="form-label">Status Toko</label>
          <select class="form-select" id="cfg-open">
            <option value="1" {{ $settings->is_open ? 'selected' : '' }}>🟢 Buka</option>
            <option value="0" {{ !$settings->is_open ? 'selected' : '' }}>🔴 Tutup</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Pesan saat Tutup</label>
          <textarea class="form-input form-textarea" id="cfg-closed-msg" placeholder="Pesan yang muncul saat toko tutup...">{{ $settings->closed_message }}</textarea>
        </div>
        <button class="btn btn-primary" onclick="saveSettings()">💾 Simpan Pengaturan</button>
      </div>
    </div>

    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">📱 QRIS Pembayaran</span></div>
      <div style="padding:20px">
        <div class="form-group">
          <label class="form-label">Gambar QR Code QRIS</label>
          <div style="display:flex;align-items:center;gap:14px;margin-bottom:10px">
            <div id="qris-preview-box" style="width:90px;height:90px;border-radius:10px;border:2px dashed var(--border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;background:#fafafa">
              @if ($settings->qris_image)
                <img id="qris-preview-img" src="{{ $settings->qris_image }}" style="width:100%;height:100%;object-fit:contain" />
              @else
                <span id="qris-preview-empty" style="font-size:11px;color:#999;text-align:center;padding:4px">Belum ada QR</span>
                <img id="qris-preview-img" style="display:none;width:100%;height:100%;object-fit:contain" />
              @endif
            </div>
            <div style="flex:1">
              <input type="file" id="qris-file-input" accept="image/*" style="display:none" onchange="handleQrisFile(event)">
              <button class="btn btn-secondary" style="width:100%" onclick="document.getElementById('qris-file-input').click()">📤 Pilih Gambar QRIS</button>
            </div>
          </div>
          <p style="font-size:11px;color:#999">Upload foto/screenshot QR code QRIS toko Anda. Otomatis tampil di halaman pemesanan pembeli, tanpa perlu edit kode.</p>
        </div>
        <div class="form-group">
          <label class="form-label">Nama Merchant (tampil di bawah QR)</label>
          <input class="form-input" id="cfg-qris-merchant" type="text" value="{{ $settings->qris_merchant_name }}" placeholder="Tummy Time">
        </div>
        <button class="btn btn-primary" onclick="saveQrisSettings()">💾 Simpan QRIS</button>
      </div>
    </div>

    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">🔐 Ganti Password Admin</span></div>
      <div style="padding:20px">
        <div class="form-group">
          <label class="form-label">Password Lama</label>
          <input class="form-input" id="old-pass" type="password" placeholder="Password saat ini">
        </div>
        <div class="form-group">
          <label class="form-label">Password Baru</label>
          <input class="form-input" id="new-pass" type="password" placeholder="Password baru">
        </div>
        <div class="form-group">
          <label class="form-label">Konfirmasi Password Baru</label>
          <input class="form-input" id="conf-pass" type="password" placeholder="Ulangi password baru">
        </div>
        <button class="btn btn-warning" onclick="changePassword()">🔑 Ganti Password</button>
      </div>
    </div>

    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">📁 Kelola Kategori</span></div>
      <div style="padding:20px">
        <div id="cat-list">
          @foreach ($categories ?? [] as $c)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--cream-dark)">
              <span>{{ $c->name }}</span>
              <button class="btn btn-danger btn-sm" onclick="deleteCategory({{ $c->id }})">🗑</button>
            </div>
          @endforeach
        </div>
        <div style="display:flex;gap:8px;margin-top:14px">
          <input class="form-input" id="new-cat" type="text" placeholder="Nama kategori baru">
          <button class="btn btn-primary" onclick="addCategory()" style="white-space:nowrap">+ Tambah</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
