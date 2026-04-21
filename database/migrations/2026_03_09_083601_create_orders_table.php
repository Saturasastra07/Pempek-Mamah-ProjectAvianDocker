<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_code')->unique(); // e.g. PMD-20260309-001
            $table->enum('status', [
                'pending',      // baru dibuat
                'confirmed',    // dikonfirmasi admin
                'preparing',    // sedang dimasak
                'on_delivery',  // kurir jalan
                'delivered',    // sampai
                'cancelled'     // dibatalkan
            ])->default('pending');
            $table->enum('delivery_mode', ['delivery', 'pickup'])->default('delivery');
            $table->foreignId('address_id')->nullable()->constrained('addresses');
            $table->string('payment_method')->default('cod');
            $table->integer('subtotal');
            $table->integer('shipping_cost')->default(0);
            $table->integer('discount')->default(0);
            $table->integer('grand_total');
            $table->json('voucher_ids')->nullable();
            $table->boolean('is_dropship')->default(false);
            $table->string('dropship_name')->nullable();
            $table->string('dropship_phone')->nullable();
            $table->string('notes')->nullable();
            // Tracking kurir
            $table->decimal('courier_lat', 10, 8)->nullable();
            $table->decimal('courier_lng', 11, 8)->nullable();
            $table->timestamp('courier_last_update')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();
            $table->string('product_name'); // snapshot nama produk
            $table->integer('price');       // snapshot harga saat order
            $table->integer('quantity');
            $table->json('addons')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
