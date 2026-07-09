<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // restrictOnDelete: a product can't be hard-deleted while it
            // still appears in historical orders (soft delete handles this instead).
            $table->foreignId('product_variant_id')->constrained()->restrictOnDelete();

            // Snapshot the name/price/size at time of purchase. If the
            // product is later renamed or re-priced, past invoices must
            // still show what the customer actually paid.
            $table->string('product_name');
            $table->string('size');
            $table->unsignedInteger('unit_price_cents');
            $table->unsignedInteger('quantity');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
