<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundry_orders', function (Blueprint $table) {
            $table->date('pickup_date')->nullable();
            $table->boolean('express_service')->default(false);
            $table->decimal('express_fee', 10, 2)->default(0);
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->date('delivery_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('delivery_date');
        });

        Schema::table('laundry_orders', function (Blueprint $table) {
            $table->dropColumn(['pickup_date', 'express_service', 'express_fee']);
        });
    }
};
