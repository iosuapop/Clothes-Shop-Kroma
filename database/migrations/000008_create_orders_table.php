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
            $table->string('reference')->unique(); // human-friendly order number, e.g. KRM-000123
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();

            $table->string('status')->default('pending'); // mirrors App\Enums\OrderStatus
            $table->unsignedInteger('subtotal_cents');
            $table->unsignedInteger('shipping_cents')->default(0);
            $table->unsignedInteger('total_cents');

            $table->string('shipping_address');
            $table->string('shipping_phone');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
