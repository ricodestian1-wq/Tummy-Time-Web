<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('home');
        }

        return view('customer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:customers,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = Customer::create($validated);
        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function orders()
    {
        $orders = Auth::guard('customer')->user()->orders()->with('items')->latest()->get();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'orders' => $orders->map(function ($order) {
                    return [
                        'order_code' => $order->order_code,
                        'status' => $order->status,
                        'total' => (float) $order->total,
                        'created_at' => $order->created_at,
                        'items' => $order->items->map(fn ($item) => [
                            'name' => $item->menu_name,
                            'qty' => $item->qty,
                            'subtotal' => (float) $item->subtotal,
                        ]),
                    ];
                }),
            ]);
        }

        return view('customer.orders', compact('orders'));
    }
}