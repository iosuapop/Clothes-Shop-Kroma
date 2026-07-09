<?php

namespace App\Livewire;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Small, persistent widget shown in the navbar on every page. Kept as a
 * classic class-based Livewire component (rather than Volt) since it's
 * reused across many pages and benefits from being registered once as
 * a named component: <livewire:cart-counter />.
 */
class CartCounter extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    // Any page that mutates the cart dispatches this browser event,
    // so the navbar badge updates without a full page reload.
    #[On('cart-updated')]
    public function refreshCount(): void
    {
        $cart = new CartService(Auth::user(), session()->getId());
        $this->count = $cart->items()->sum('quantity');
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
