<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete(); // don't silently orphan products if a category is removed

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable(); // shown on listing cards
            $table->text('description')->nullable();          // full copy on the product page

            // Money is stored as an integer number of cents/bani to avoid
            // float rounding errors creeping into totals at checkout.
            $table->unsignedInteger('price_cents');
            $table->unsignedInteger('compare_at_price_cents')->nullable(); // for "was/now" pricing

            $table->string('sku')->unique();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
            $table->softDeletes(); // keep order history intact even if a product is discontinued

            $table->index(['is_published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
