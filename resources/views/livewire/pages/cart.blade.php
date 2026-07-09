<?php

use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app', ['title' => 'Your cart — KROMA'])] class extends Component
{
    public function cart(): CartService
    {
        return new CartService(Auth::user(), session()->getId());
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $item = CartItem::findOrFail($itemId);
        $this->cart()->updateQuantity($item, $quantity);
        $this->dispatch('cart-updated');
    }

    public function remove(int $itemId): void
    {
        $this->cart()->remove(CartItem::findOrFail($itemId));
        $this->dispatch('cart-updated');
    }

    public function with(): array
    {
        return [
            'items' => $this->cart()->items(),
            'total' => $this->cart()->totalCents() / 100,
        ];
    }
}; ?>

<div>
    <section class="px-6 py-14">
        <div class="mx-auto max-w-3xl">
            <h1 class="font-display text-4xl mb-10">YOUR CART</h1>

            @if ($items->isEmpty())
                <p class="font-mono text-sm text-static-grey">Cart's empty. Go find something to add to it.</p>
                <a href="{{ route('home') }}" class="card-sticker inline-block bg-ink text-bone font-display px-6 py-3 mt-6">
                    KEEP SHOPPING
                </a>
            @else
                <div class="space-y-4">
                    @foreach ($items as $item)
                        <div class="card-sticker bg-white p-4 flex items-center gap-4">
                            <img src="{{ $item->variant->product->images->first()?->url }}"
                                 class="w-20 h-24 object-cover border-2 border-ink">

                            <div class="flex-1">
                                <p class="font-display">{{ $item->variant->product->name }}</p>
                                <p class="font-mono text-xs text-static-grey">SIZE {{ $item->variant->size }}</p>

                                <div class="flex items-center gap-2 mt-2">
                                    <input type="number" min="1" value="{{ $item->quantity }}"
                                        wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                        class="w-16 border-2 border-ink text-center font-mono text-sm">
                                    <button wire:click="remove({{ $item->id }})" class="font-mono text-xs underline text-flash-coral">
                                        REMOVE
                                    </button>
                                </div>
                            </div>

                            <x-tag>{{ number_format($item->lineTotalCents() / 100, 2) }} RON</x-tag>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center mt-10 font-display text-2xl">
                    <span>TOTAL</span>
                    <x-tag>{{ number_format($total, 2) }} RON</x-tag>
                </div>

                <a href="{{ route('checkout.show') }}" class="card-sticker block text-center bg-riot-yellow font-display py-4 mt-6 text-lg">
                    CHECKOUT
                </a>
            @endif
        </div>
    </section>
</div>
