<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laundry_orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('customer_id')
                  ->constrained('customers', 'customer_id')
                  ->onDelete('cascade');
            $table->date('pickup_date')->index();
            $table->enum('laundry_status', ['Pending', 'Processing', 'Completed', 'Cancelled'])
                  ->default('Pending');
            $table->decimal('grand_total', 10, 2)->default(0);
            $table->enum('payment_status', ['Unpaid', 'Paid'])->default('Unpaid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_orders');
    }
};