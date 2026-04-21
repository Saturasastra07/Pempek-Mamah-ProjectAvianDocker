<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('dropship_receiver_name')->nullable()->after('dropship_phone');
            $table->string('dropship_receiver_phone')->nullable()->after('dropship_receiver_name');
            $table->text('dropship_receiver_address')->nullable()->after('dropship_receiver_phone');
            $table->string('dropship_receiver_district')->nullable()->after('dropship_receiver_address');
            $table->string('dropship_receiver_city')->nullable()->after('dropship_receiver_district');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'dropship_receiver_name',
                'dropship_receiver_phone',
                'dropship_receiver_address',
                'dropship_receiver_district',
                'dropship_receiver_city',
            ]);
        });
    }
};