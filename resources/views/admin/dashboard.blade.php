@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page active" id="page-dashboard">
  <div class="page-header">
    <div class="page-title">Dashboard</div>
    <div class="page-sub">{{ now()->translatedFormat('l, d F Y') }}</div>
  </div>

  <div class="stats-grid" id="stats-grid">
    <div class="stat-card"><span class="stat-icon">🛒</span><div class="stat-value">{{ $ordersToday }}</div><div class="stat-label">Pesanan Hari Ini</div></div>
    <div class="stat-card"><span class="stat-icon">💰</span><div class="stat-value">Rp {{ number_format($revenueToday, 0, ',', '.') }}</div><div class="stat-label">Pendapatan Hari Ini</div></div>
    <div class="stat-card"><span class="stat-icon">⏳</span><div class="stat-value">{{ $pendingCount }}</div><div class="stat-label">Menunggu Konfirmasi</div></div>
    <div class="stat-card"><span class="stat-icon">✅</span><div class="stat-value">{{ $doneToday }}</div><div class="stat-label">Selesai Hari Ini</div></div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px" id="mini-grids">
    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">📦 Pesanan Terbaru</span></div>
      <div style="padding:12px">
        @forelse ($latestOrders as $o)
          <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 4px;border-bottom:1px solid var(--cream-dark)">
            <div>
              <strong>{{ $o->order_code }}</strong>
              <span style="color:#999;margin-left:6px">{{ $o->customer_name }}</span>
            </div>
            <div style="text-align:right">
              <div><strong>Rp {{ number_format($o->total, 0, ',', '.') }}</strong></div>
              <span class="badge {{ $o->status === 'done' ? 'badge-available' : 'badge-unavailable' }}">{{ strtoupper($o->status) }}</span>
            </div>
          </div>
        @empty
          <p style="color:#999">Belum ada pesanan.</p>
        @endforelse
      </div>
    </div>
    <div class="table-card">
      <div class="table-card-header"><span class="table-card-title">🏆 Menu Terlaris</span></div>
      <div style="padding:12px">
        @forelse ($topMenus as $m)
          <div style="display:flex;justify-content:space-between;padding:8px 4px;border-bottom:1px solid var(--cream-dark)">
            <span>{{ $m->menu_name }}</span>
            <strong>{{ $m->total_qty }} porsi</strong>
          </div>
        @empty
          <p style="color:#999">Belum ada data penjualan.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
