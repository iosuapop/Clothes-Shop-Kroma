<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app', ['title' => 'Your wishlist — KROMA'])] class extends Component
{
    public function remove(int $productId): void
    {
        Auth::user()->wishlist()->where('product_id', $productId)->delete();
    }

    public function with(): array
    {
        return [
            'items' => Auth::user()->wishlist()->with('product.images')->get(),
        ];
    }
}; ?>

<div>
    <section class="px-6 py-14">
        <div class="mx-auto max-w-4xl">
            <h1 class="font-display text-4xl mb-10">YOUR WISHLIST</h1>

            @if ($items->isEmpty())
                <p class="font-mono text-sm text-static-grey">Nothing saved yet. Tap the ♡ on any product to add it here.</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($items as $item)
                        <div class="card-sticker bg-white relative">
                            <button wire:click="remove({{ $item->product->id }})"
                                    class="absolute top-2 right-2 z-10 bg-bone border-2 border-ink w-7 h-7 rounded-full">♥</button>
                            <a href="{{ route('products.show', $item->product->slug) }}">
                                <img src="{{ $item->product->images->first()?->url }}" class="w-full aspect-[3/4] object-cover border-b-3 border-ink">
                                <div class="p-3">
                                    <p class="font-display text-sm">{{ $item->product->name }}</p>
                                    <x-tag class="mt-2">{{ number_format($item->product->price, 2) }} RON</x-tag>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
