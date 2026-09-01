<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('description', 255)->default('');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_available')->default(true);
            // stock: NULL = stok tidak dibatasi (selalu tersedia selama is_available=true)
            $table->integer('stock')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
