<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::current();
        $categories = \App\Models\Category::orderBy('sort_order')->get();

        return view('admin.settings', compact('settings', 'categories'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required|string|max:100',
            'wa_number' => 'required|string|max:20',
            'is_open' => 'required|boolean',
            'closed_message' => 'nullable|string|max:255',
        ]);

        $settings = Setting::current();
        $settings->update($validated);

        return response()->json(['success' => true, 'settings' => $settings->fresh()]);
    }

    /**
     * Toggle cepat buka/tutup toko dari sidebar (tidak perlu kirim semua field settings).
     */
    public function toggleOpen(Request $request)
    {
        $validated = $request->validate(['is_open' => 'required|boolean']);

        $settings = Setting::current();
        $settings->update(['is_open' => $validated['is_open']]);

        return response()->json(['success' => true, 'is_open' => $settings->is_open]);
    }

    /**
     * Simpan gambar QRIS (dikirim sebagai base64 data-URI dari halaman admin).
     */
    public function uploadQris(Request $request)
    {
        $validated = $request->validate([
            'qris_image' => 'nullable|string',
            'qris_merchant_name' => 'nullable|string|max:100',
        ]);

        $settings = Setting::current();
        $settings->update($validated);

        return response()->json(['success' => true]);
    }

    public function reportPage()
    {
        return view('admin.report');
    }

    public function report(Request $request)
    {
        $period = (int) $request->query('period', 7);
        $from = now()->subDays($period)->toDateString();

        $daily = Order::selectRaw('DATE(created_at) as tanggal, COUNT(*) as jumlah_pesanan, SUM(total) as total_pendapatan')
            ->where('status', '!=', 'cancelled')
            ->whereDate('created_at', '>=', $from)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        $menuSales = OrderItem::select('menu_name')
            ->selectRaw('SUM(qty) as total_terjual, SUM(subtotal) as total_pendapatan')
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->groupBy('menu_name')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->get();

        $paymentStats = Order::select('payment_method')
            ->selectRaw('COUNT(*) as jumlah, SUM(total) as pendapatan')
            ->where('status', '!=', 'cancelled')
            ->groupBy('payment_method')
            ->get();

        return response()->json([
            'success' => true,
            'daily' => $daily,
            'menu_sales' => $menuSales,
            'payment_stats' => $paymentStats,
        ]);
    }
}
