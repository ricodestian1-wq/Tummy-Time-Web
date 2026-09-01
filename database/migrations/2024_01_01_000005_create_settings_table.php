<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name', 100)->default('Tummy Time');
            $table->string('wa_number', 20)->default('6285187408288');
            $table->boolean('is_open')->default(true);
            $table->string('closed_message', 255)->default('Maaf, kami sedang tutup. Silakan order lagi ya!');
            $table->text('qris_image')->nullable(); // URL/path atau base64 data-URI gambar QRIS
            $table->string('qris_merchant_name', 100)->default('Tummy Time');
            $table->timestamp('updated_at')->nullable();
        });

        // Selalu ada tepat 1 baris pengaturan (id = 1)
        DB::table('settings')->insert([
            'id' => 1,
            'shop_name' => 'Tummy Time',
            'wa_number' => '6285187408288',
            'is_open' => true,
            'closed_message' => 'Maaf, kami sedang tutup. Silakan order lagi ya!',
            'qris_image' => null,
            'qris_merchant_name' => 'Tummy Time',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
