<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $settings = Setting::current();
        $categories = Category::orderBy('sort_order')->get();
        $menus = Menu::orderBy('sort_order')->get();

        return view('home', compact('settings', 'categories', 'menus'));
    }
}
