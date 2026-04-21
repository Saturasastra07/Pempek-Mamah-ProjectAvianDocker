<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Kategori (misal: Pempek Satuan, Paket Hemat)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Tabel Produk dengan Fitur Harga Coret
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->integer('discount_price')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // Tabel Order untuk mencatat pesanan masuk
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->integer('total_amount');
            $table->enum('status', ['pending', 'cooking', 'on_delivery', 'completed', 'cancelled'])->default('pending');
            $table->text('delivery_address');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toko_pempek_schema');
    }
};
