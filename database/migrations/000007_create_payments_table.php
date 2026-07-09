<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('amount_cents');
            $table->string('currency', 3)->default('ron');
            $table->string('method')->default('card');
            $table->string('status')->default('pending'); // mirrors App\Enums\PaymentStatus
            $table->string('stripe_payment_intent_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
