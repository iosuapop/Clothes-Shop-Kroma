<?php

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app', ['title' => 'Products — Admin'])] class extends Component
{
    use WithPagination;

    public function togglePublish(Product $product): void
    {
        $product->update(['is_published' => ! $product->is_published]);
    }

    public function delete(Product $product): void
    {
        // Soft delete (see migration) — keeps past order_items intact
        // since order_items.product_variant_id can't reference a
        // hard-deleted product's variants.
        $product->delete();
    }

    public function with(): array
    {
        return [
            // Eager-load variants too — totalStock() below reads
            // $product->variants, and without this it fires one extra
            // query per row (15 rows = 15 extra queries just to load a page).
            'products' => Product::with(['category', 'variants'])->latest()->paginate(15),
        ];
    }
}; ?>

<div>
    <section class="px-6 py-10">
        <div class="mx-auto max-w-6xl">
            <div class="flex items-center justify-between mb-10">
                <h1 class="font-display text-4xl">PRODUCTS</h1>
                <a href="{{ route('admin.products.create') }}" class="card-sticker bg-riot-yellow font-display px-6 py-2">
                    + NEW PRODUCT
                </a>
            </div>

            <div class="card-sticker bg-white overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left font-mono text-xs text-static-grey border-b-2 border-ink">
                            <th class="p-3">NAME</th>
                            <th>CATEGORY</th>
                            <th>PRICE</th>
                            <th>STOCK</th>
                            <th>STATUS</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="border-b border-static-grey/30">
                                <td class="p-3">{{ $product->name }}</td>
                                <td>{{ $product->category->name }}</td>
                                <td class="font-mono">{{ number_format($product->price, 2) }} RON</td>
                                <td class="font-mono">{{ $product->totalStock() }}</td>
                                <td>
                                    <button wire:click="togglePublish({{ $product->id }})">
                                        <x-tag :variant="$product->is_published ? 'new' : 'default'" class="!py-0 !px-2 !text-xs !shadow-none">
                                            {{ $product->is_published ? 'LIVE' : 'HIDDEN' }}
                                        </x-tag>
                                    </button>
                                </td>
                                <td class="p-3 text-right">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="font-mono text-xs underline mr-3">EDIT</a>
                                    <button wire:click="delete({{ $product->id }})"
                                            wire:confirm="Delete this product?"
                                            class="font-mono text-xs underline text-flash-coral">DELETE</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $products->links() }}</div>
        </div>
    </section>
</div>
