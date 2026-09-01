<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Ganti password ini lewat menu "Ganti Password" di dashboard admin setelah login pertama kali!
        Admin::firstOrCreate(
            ['username' => 'admin'],
            ['password' => 'tummytime123']
        );
    }
}
