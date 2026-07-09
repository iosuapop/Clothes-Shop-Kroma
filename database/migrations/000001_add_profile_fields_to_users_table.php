<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend the default Breeze/Laravel `users` table with the fields
     * our storefront actually needs: role-based access, contact info
     * and shipping address used at checkout.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Native enum column: cheaper than a role/permission package
            // for a two-role app (customer vs admin) and self-documenting.
            $table->enum('role', ['customer', 'admin'])->default('customer')->after('email');
            $table->string('phone')->nullable()->after('role');
            $table->string('address')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'address']);
        });
    }
};
