<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $ordersToday = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->count();

        $revenueToday = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        $pendingCount = Order::where('status', 'pending')->count();

        $doneToday = Order::whereDate('created_at', $today)
            ->where('status', 'done')
            ->count();

        $latestOrders = Order::latest()->limit(5)->get();

        $topMenus = OrderItem::select('menu_name')
            ->selectRaw('SUM(qty) as total_qty')
            ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
            ->groupBy('menu_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'ordersToday', 'revenueToday', 'pendingCount', 'doneToday', 'latestOrders', 'topMenus'
        ));
    }
}
