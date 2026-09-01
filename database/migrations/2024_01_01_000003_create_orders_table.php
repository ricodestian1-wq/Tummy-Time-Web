<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 20)->unique();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 30)->default('-');
            $table->string('notes', 255)->default('');
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'qris'])->default('cash');
            $table->decimal('cash_amount', 10, 2)->default(0);
            $table->decimal('change_amount', 10, 2)->default(0);
            $table->longText('payment_proof')->nullable(); // base64 data-URI atau path file bukti bayar
            $table->timestamp('payment_proof_at')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cooking', 'ready', 'done', 'cancelled'])
                  ->default('pending');
            $table->timestamps();

            $table->index('order_code');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
