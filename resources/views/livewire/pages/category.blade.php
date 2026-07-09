<?php

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('components.layouts.app', ['title' => 'Shop — KROMA'])] class extends Component
{
    use WithPagination;

    public Category $category;

    public string $size = '';

    // Resetting the page whenever the filter changes avoids landing on
    // an empty "page 3" after the result set shrinks.
    public function updatedSize(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $products = Product::query()
            ->with(['images', 'variants'])
            ->where('category_id', $this->category->id)
            ->where('is_published', true)
            ->when($this->size, fn ($q) => $q->whereHas(
                'variants',
                fn ($v) => $v->where('size', $this->size)->where('stock', '>', 0)
            ))
            ->latest('published_at')
            ->paginate(12);

        return ['products' => $products];
    }
}; ?>

<div>
    <section class="bg-electric border-b-4 border-ink px-6 py-14">
        <h1 class="font-display text-5xl text-bone">{{ strtoupper($category->name) }}</h1>
    </section>

    <section class="px-6 py-10">
        <div class="mx-auto max-w-6xl">
            <div class="flex flex-wrap gap-2 mb-8 font-mono text-xs">
                <button wire:click="$set('size', '')"
                        class="tag {{ $size === '' ? '!bg-ink !text-bone' : '' }}">ALL</button>
                @foreach (['S', 'M', 'L', 'XL'] as $option)
                    <button wire:click="$set('size', '{{ $option }}')"
                            class="tag {{ $size === $option ? '!bg-ink !text-bone' : '' }}">{{ $option }}</button>
                @endforeach
            </div>

            @if ($products->isEmpty())
                <p class="font-mono text-sm text-static-grey">No pieces match that size right now — check back after the next drop.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach ($products as $product)
                        <a href="{{ route('products.show', $product->slug) }}" class="card-sticker bg-white block relative">
                            @if ($product->isOnSale)
                                <x-tag variant="sale" class="absolute -top-3 -left-3 z-10">SALE</x-tag>
                            @endif

                            <img src="{{ $product->images->first()?->url }}"
                                 alt="{{ $product->name }}"
                                 class="w-full aspect-[3/4] object-cover border-b-3 border-ink">

                            <div class="p-4">
                                <p class="font-display text-lg">{{ $product->name }}</p>
                                <x-tag class="mt-2">{{ number_format($product->price, 2) }} RON</x-tag>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
