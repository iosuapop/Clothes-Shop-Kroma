<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A "variant" is a purchasable size of a product (the thing that
     * actually has stock). Renamed from the old `size_quantities` table
     * for clarity — this is standard e-commerce vocabulary.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size'); // XS, S, M, L, XL, XXL or numeric (e.g. "42")
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            // A product can't have the same size listed twice.
            $table->unique(['product_id', 'size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
