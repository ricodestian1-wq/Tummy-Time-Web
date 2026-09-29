@extends('layouts.admin')

@section('title', 'Pelanggan')

@section('content')
<div class="page active" id="page-customers">
  <div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div class="page-title">👥 Pelanggan Terdaftar</div>
      <div class="page-sub">Lihat pelanggan yang memiliki akun dan frekuensi pesanannya</div>
    </div>
    <div class="search-box">
      <span class="search-icon">🔍</span>
      <input type="search" placeholder="Cari pelanggan..." oninput="filterCustomerRows(this.value)">
    </div>
  </div>

  <div class="stats-grid" style="margin-bottom:20px">
    <div class="stat-card"><div class="stat-icon blue">👥</div><div><div class="stat-label">Total pelanggan</div><div class="stat-value">{{ $customers->count() }}</div></div></div>
    <div class="stat-card"><div class="stat-icon green">🛒</div><div><div class="stat-label">Total pesanan akun</div><div class="stat-value">{{ $customers->sum('orders_count') }}</div></div></div>
    <div class="stat-card"><div class="stat-icon orange">⭐</div><div><div class="stat-label">Pelanggan aktif</div><div class="stat-value">{{ $customers->where('orders_count', '>', 0)->count() }}</div></div></div>
  </div>

  <div class="table-card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Nama pelanggan</th><th>Email</th><th>No. HP</th><th>Terdaftar</th><th>Pesanan</th><th>Pesanan terakhir</th></tr>
        </thead>
        <tbody id="customer-tbody">
          @forelse($customers as $customer)
            <tr data-customer-search="{{ strtolower($customer->name . ' ' . $customer->email . ' ' . ($customer->phone ?? '')) }}">
              <td><strong>{{ $customer->name }}</strong></td>
              <td>{{ $customer->email }}</td>
              <td>{{ $customer->phone ?: '-' }}</td>
              <td>{{ $customer->created_at->format('d M Y') }}</td>
              <td><span class="customer-order-count">{{ $customer->orders_count }}x</span></td>
              <td>{{ $customer->orders_max_created_at ? \Illuminate\Support\Carbon::parse($customer->orders_max_created_at)->format('d M Y H:i') : 'Belum pernah pesan' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" style="text-align:center;padding:24px;color:#999">Belum ada pelanggan terdaftar.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function filterCustomerRows(query) {
    const normalized = query.toLowerCase().trim();
    document.querySelectorAll('#customer-tbody tr[data-customer-search]').forEach((row) => {
      row.style.display = row.dataset.customerSearch.includes(normalized) ? '' : 'none';
    });
  }
</script>
@endpush
