<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kroma.test'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'role' => UserRole::Admin]
        );

        User::updateOrCreate(
            ['email' => 'customer@kroma.test'],
            ['name' => 'Test Customer', 'password' => bcrypt('password'), 'role' => UserRole::Customer]
        );

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
