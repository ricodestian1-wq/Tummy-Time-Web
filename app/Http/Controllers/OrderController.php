<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Buat pesanan baru dari keranjang customer.
     * Mengecek & mengunci stok tiap menu dalam transaksi supaya tidak minus / rebutan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:30',
            'customer_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:255',
            'payment_method' => ['nullable', Rule::in(['cash', 'qris'])],
            'cash_amount' => 'nullable|numeric',
            'change_amount' => 'nullable|numeric',
            'payment_proof' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                // --- Cek & kunci stok tiap menu yang dipesan (mencegah stok minus / rebutan) ---
                foreach ($validated['items'] as $item) {
                    $menuId = $item['id'] ?? null;
                    if (! $menuId) {
                        continue;
                    }

                    $menu = Menu::where('id', $menuId)->lockForUpdate()->first();
                    if (! $menu) {
                        continue; // menu custom/lama yang sudah dihapus, biarkan lewat
                    }

                    if (! $menu->is_available) {
                        abort(409, "{$menu->name} sedang habis, silakan hapus dari keranjang.");
                    }

                    if ($menu->stock !== null && $menu->stock < $item['qty']) {
                        abort(409, "Stok {$menu->name} tinggal {$menu->stock}, kurangi jumlahnya ya.");
                    }
                }

                $total = collect($validated['items'])->sum(fn ($it) => $it['price'] * $it['qty']);

                $order = Order::create([
                    'order_code' => Order::generateOrderCode(),
                    'customer_id' => Auth::guard('customer')->id(),
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'] ?? '-',
                    'customer_address' => $validated['customer_address'],
                    'notes' => $validated['notes'] ?? '',
                    'total' => $total,
                    'payment_method' => $validated['payment_method'] ?? 'cash',
                    'cash_amount' => $validated['cash_amount'] ?? 0,
                    'change_amount' => $validated['change_amount'] ?? 0,
                    'payment_proof' => $validated['payment_proof'] ?? null,
                    'status' => 'pending',
                ]);

                foreach ($validated['items'] as $item) {
                    $order->items()->create([
                        'menu_id' => $item['id'] ?? null,
                        'menu_name' => $item['name'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['price'] * $item['qty'],
                    ]);

                    if (! empty($item['id'])) {
                        Menu::where('id', $item['id'])
                            ->whereNotNull('stock')
                            ->decrement('stock', $item['qty']);
                    }
                }

                return $order;
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], $e->getStatusCode());
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'total' => (float) $order->total,
        ]);
    }

    /**
     * Kirim/lampirkan bukti pembayaran QRIS.
     */
    public function uploadProof(Request $request)
    {
        $validated = $request->validate([
            'order_code' => 'required|string',
            'payment_proof' => 'required|string',
        ]);

        $order = Order::where('order_code', $validated['order_code'])->first();

        if (! $order) {
            return response()->json(['success' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }

        $newStatus = $order->status === 'pending' ? 'confirmed' : $order->status;

        $order->update([
            'payment_proof' => $validated['payment_proof'],
            'payment_proof_at' => now(),
            'status' => $newStatus,
        ]);

        return response()->json(['success' => true, 'status' => $newStatus]);
    }

    /**
     * Cek status pesanan berdasarkan kode (dipakai modal "Cek Status Pesanan" customer).
     */
    public function status(Request $request)
    {
        $code = trim($request->query('code', ''));

        if ($code === '') {
            return response()->json(['success' => false, 'error' => 'Kode pesanan wajib diisi'], 400);
        }

        $order = Order::with('items')->where('order_code', $code)->first();

        if (! $order) {
            return response()->json(['success' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }

        $payload = $order->toArray();
        $payload['has_payment_proof'] = ! empty($order->payment_proof);
        unset($payload['payment_proof']);

        return response()->json(['success' => true, 'order' => $payload]);
    }
}
