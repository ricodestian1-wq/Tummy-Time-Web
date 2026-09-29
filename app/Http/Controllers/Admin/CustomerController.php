<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('orders')
            ->withMax('orders', 'created_at')
            ->latest()
            ->get();

        return view('admin.customers', compact('customers'));
    }
}
