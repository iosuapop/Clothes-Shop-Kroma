<?php

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app', ['title' => 'KROMA — Wear the noise'])] class extends Component
{
    public function with(): array
    {
        return [
            'categories' => Category::visible()->orderBy('sort_order')->get(),
            'featured' => Product::query()
                ->with(['images', 'category'])
                ->where('is_published', true)
                ->where('is_featured', true)
                ->latest('published_at')
                ->take(8)
                ->get(),
        ];
    }
}; ?>

<div>
    {{-- Promo ticker: drop-culture streetwear vernacular, not a generic banner --}}
    <div class="marquee py-2 font-mono text-sm">
        <div class="marquee__track">
            <span class="mx-6">NEW DROP EVERY FRIDAY</span>
            <span class="mx-6">FREE SHIPPING OVER 250 RON</span>
            <span class="mx-6">NEW DROP EVERY FRIDAY</span>
            <span class="mx-6">FREE SHIPPING OVER 250 RON</span>
        </div>
    </div>

    {{-- Hero: color-blocked, big display type, one bold claim --}}
    <section class="bg-riot-yellow border-b-4 border-ink px-6 py-20 md:py-28">
        <div class="mx-auto max-w-6xl">
            <h1 class="font-display text-6xl md:text-8xl leading-[0.9]">
                WEAR<br>THE&nbsp;NOISE
            </h1>
            <p class="mt-6 max-w-md font-mono text-sm">
                Streetwear made for standing out, not blending in. New drop every Friday, limited stock, no restocks.
            </p>
            <a href="#featured" class="card-sticker inline-block mt-8 bg-ink text-bone font-display px-8 py-3 text-lg">
                SHOP THE DROP
            </a>
        </div>
    </section>

    {{-- Category tiles --}}
    <section class="px-6 py-16">
        <div class="mx-auto max-w-6xl grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($categories as $category)
                <a href="{{ route('category.show', $category->slug) }}"
                   class="card-sticker bg-white p-6 flex flex-col justify-between h-40">
                    <span class="font-mono text-xs text-static-grey">CATEGORY</span>
                    <span class="font-display text-3xl">{{ strtoupper($category->name) }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured products --}}
    <section id="featured" class="px-6 py-16 bg-ink">
        <div class="mx-auto max-w-6xl">
            <h2 class="font-display text-4xl text-bone mb-10">FEATURED THIS WEEK</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($featured as $product)
                    <a href="{{ route('products.show', $product->slug) }}" class="card-sticker bg-bone block relative">
                        @if ($product->isOnSale)
                            <x-tag variant="sale" class="absolute -top-3 -left-3 z-10">SALE</x-tag>
                        @endif

                        <img
                            src="{{ $product->images->first()?->url }}"
                            alt="{{ $product->images->first()?->alt_text ?? $product->name }}"
                            class="w-full aspect-[3/4] object-cover border-b-3 border-ink"
                        >

                        <div class="p-4">
                            <p class="font-mono text-xs text-static-grey uppercase">{{ $product->category->name }}</p>
                            <p class="font-display text-lg">{{ $product->name }}</p>
                            <x-tag class="mt-2">{{ number_format($product->price, 2) }} RON</x-tag>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</div>
