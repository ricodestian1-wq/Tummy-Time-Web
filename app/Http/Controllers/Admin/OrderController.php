<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.orders');
    }

    public function list()
    {
        $orders = Order::with('items')->latest()->get()->map(function (Order $order) {
            $data = $order->toArray();
            $data['has_payment_proof'] = ! empty($order->payment_proof);
            unset($data['payment_proof']);

            return $data;
        });

        return response()->json(['success' => true, 'orders' => $orders]);
    }

    public function show(Order $order)
    {
        return response()->json(['success' => true, 'order' => $order->load('items')]);
    }

    /**
     * Update status pesanan. Kalau dibatalkan, stok menu yang tadinya dikurangi dikembalikan.
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'order_code' => 'nullable|string',
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cooking', 'ready', 'done', 'cancelled'])],
        ]);

        $order = Order::where('id', $validated['id'] ?? 0)
            ->orWhere('order_code', $validated['order_code'] ?? '')
            ->first();

        if (! $order) {
            return response()->json(['success' => false, 'error' => 'Pesanan tidak ditemukan'], 404);
        }

        DB::transaction(function () use ($order, $validated) {
            $wasCancelled = $order->status === 'cancelled';
            $order->update(['status' => $validated['status']]);

            // Jika pesanan baru saja dibatalkan, kembalikan stok menu yang tadinya dikurangi
            if ($validated['status'] === 'cancelled' && ! $wasCancelled) {
                foreach ($order->items()->whereNotNull('menu_id')->get() as $item) {
                    Menu::where('id', $item->menu_id)
                        ->whereNotNull('stock')
                        ->increment('stock', $item->qty);
                }
            }
        });

        return response()->json(['success' => true]);
    }
}
