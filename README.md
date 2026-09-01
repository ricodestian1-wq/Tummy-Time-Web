# Tummy Time — Laravel Edition

Migrasi penuh dari versi PHP native (`index.html` + `admin.html` + `menu.php`/`orders.php`/`settings.php`) ke Laravel (Blade + Eloquent).

## Yang berubah dari versi lama

- **Backend**: PDO raw SQL → Eloquent ORM, dengan migration & seeder
- **Routing**: `api/menu.php?action=...` → route Laravel yang jelas (`routes/web.php`)
- **Login admin**: password hardcoded di JavaScript + localStorage → login sungguhan pakai Laravel session (`guard: admin`), password di-hash pakai bcrypt
- **Views**: HTML statis → Blade template (`resources/views`), data awal dirender server-side
- **Keamanan**: validasi request via Laravel Form Validation, CSRF protection otomatis di semua form/fetch

## Instalasi (di komputer kamu, bukan di sini)

Karena proses ini butuh `composer install` yang mengunduh banyak package dari internet, jalankan langkah berikut **di komputer kamu sendiri**:

### 1. Pastikan sudah terinstall
- PHP 8.3+
- Composer ([getcomposer.org](https://getcomposer.org))
- MySQL (XAMPP sudah cukup, tapi Laravel TIDAK dijalankan lewat Apache XAMPP)

### 2. Ekstrak project ini
Ekstrak folder `tummy-time-laravel` ke lokasi manapun **di luar** `htdocs` XAMPP, misalnya langsung di `D:\tummy-time-laravel` atau `C:\Users\namamu\tummy-time-laravel`.

### 3. Install dependency
```bash
cd tummy-time-laravel
composer install
```

### 4. Setup environment
```bash
copy .env.example .env        # Windows
# atau: cp .env.example .env  # Mac/Linux

php artisan key:generate
```

Buka file `.env`, sesuaikan bagian database kalau perlu (defaultnya sudah diarahkan ke `tummytime_db`, user `root`, password kosong — sama seperti setup XAMPP kamu):
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tummytime_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5A. Kalau mau mulai dari database KOSONG (paling aman/direkomendasikan)
```bash
php artisan migrate --seed
```
Ini akan membuat semua tabel dari nol + mengisi kategori, menu, dan akun admin default.

### 5B. Kalau mau PAKAI database `tummytime_db` yang sudah ada (dengan data pesanan lama)
**Backup dulu databasenya**, lalu cukup tambahkan tabel `admins` yang belum ada:
```bash
php artisan migrate --path=database/migrations/2024_01_01_000000_create_admins_table.php
php artisan db:seed --class=AdminSeeder
```
Tabel `categories`, `menus`, `orders`, `order_items`, `settings` kamu yang sudah ada **tidak perlu diubah** — strukturnya sudah didesain identik dengan migration di sini (termasuk kolom `stock` yang sudah kamu tambahkan sebelumnya).

### 6. Jalankan server
```bash
php artisan serve
```
Buka **http://localhost:8000** untuk halaman pemesanan, dan **http://localhost:8000/admin/login** untuk admin.

## Login admin default

```
Username: admin
Password: tummytime123
```

⚠️ **Ganti password ini lewat menu Pengaturan → Ganti Password setelah login pertama kali.**

## Struktur penting

```
app/
  Models/                    → Category, Menu, Order, OrderItem, Setting, Admin
  Http/Controllers/          → HomeController, OrderController (publik)
  Http/Controllers/Admin/    → Auth, Dashboard, Menu, Order, Setting (admin)
  Http/Middleware/EnsureAdminIsAuthenticated.php  → proteksi halaman admin

database/
  migrations/   → skema tabel (setara database.sql versi lama)
  seeders/      → data awal kategori, menu, akun admin

resources/views/
  home.blade.php               → halaman pemesanan customer
  layouts/admin.blade.php      → sidebar + layout admin
  admin/                       → dashboard, menu, orders, settings, report, login

routes/web.php   → semua endpoint (pengganti menu.php/orders.php/settings.php)

public/css/app.css, admin.css   → styling (sama seperti versi lama)
public/js/app.js, admin.js      → interaktivitas (route Laravel + CSRF, tanpa localStorage fallback)
```

## Kenapa admin.js sekarang tidak ada localStorage fallback lagi?

Versi PHP native lama menyimpan cadangan data ke `localStorage` browser kalau koneksi ke backend gagal — ini justru sumber masalah "toggle tutup toko kelihatan sukses tapi database tidak berubah" yang kamu alami sebelumnya. Di versi Laravel ini, kalau simpan ke database gagal, admin akan **langsung diberi tahu jujur** dan perubahan di layar akan otomatis dibatalkan (dikembalikan ke kondisi semula), bukan pura-pura sukses.

## Kalau nanti mau deploy ke hosting

Pastikan document root server diarahkan ke folder **`public/`**, bukan ke root project. Ini beda dengan versi PHP native lama yang semua file ada langsung di root `htdocs`.

## Kalau ada error saat migrate/composer install

Kirim pesan errornya lengkap — banyak error di tahap ini biasanya soal versi PHP yang kurang baru, extension PHP yang belum aktif (pdo_mysql, mbstring, dll di `php.ini`), atau kredensial database yang belum sesuai di `.env`.
