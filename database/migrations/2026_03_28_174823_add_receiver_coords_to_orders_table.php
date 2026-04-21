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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('dropship_receiver_lat', 10, 8)->nullable()->after('dropship_receiver_city');
            $table->decimal('dropship_receiver_lng', 11, 8)->nullable()->after('dropship_receiver_lat');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['dropship_receiver_lat', 'dropship_receiver_lng']);
        });
    }
};
