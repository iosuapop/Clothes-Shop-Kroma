<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app', ['title' => 'Your orders — KROMA'])] class extends Component
{
    public ?string $confirmed = null;

    public function mount(): void
    {
        $this->confirmed = request()->query('confirmed');
    }

    public function with(): array
    {
        return [
            'orders' => Auth::user()->orders()->with('items')->latest()->get(),
        ];
    }
}; ?>

<div>
    <section class="px-6 py-14">
        <div class="mx-auto max-w-3xl">
            @if ($confirmed)
                <div class="card-sticker bg-riot-yellow p-6 mb-10">
                    <p class="font-display text-2xl">ORDER CONFIRMED</p>
                    <p class="font-mono text-sm mt-1">Reference {{ $confirmed }} — we'll email you when it ships.</p>
                </div>
            @endif

            <h1 class="font-display text-4xl mb-10">YOUR ORDERS</h1>

            @forelse ($orders as $order)
                <div class="card-sticker bg-white p-5 mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-display">{{ $order->reference }}</p>
                            <p class="font-mono text-xs text-static-grey">{{ $order->created_at->format('d M Y') }} &middot; {{ $order->items->count() }} items</p>
                        </div>
                        <x-tag :class="'!bg-'.$order->status->color()">{{ strtoupper($order->status->label()) }}</x-tag>
                    </div>
                    <p class="font-mono text-sm mt-3">{{ number_format($order->total_cents / 100, 2) }} RON</p>
                </div>
            @empty
                <p class="font-mono text-sm text-static-grey">No orders yet.</p>
            @endforelse
        </div>
    </section>
</div>
