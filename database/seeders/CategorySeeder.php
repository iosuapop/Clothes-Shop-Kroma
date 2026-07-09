<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        collect(['Women', 'Men', 'Accessories'])->each(function (string $name, int $index) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'sort_order' => $index]
            );
        });
    }
}
