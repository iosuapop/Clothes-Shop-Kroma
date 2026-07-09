<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

/**
 * All cart read/write logic lives here instead of scattered across
 * Livewire components. This is what makes the cart testable without
 * booting a browser, and reusable from both the storefront and any
 * future API endpoint.
 */
class CartService
{
    public function __construct(private readonly ?Authenticatable $user, private readonly string $sessionId) {}

    public function items(): Collection
    {
        return CartItem::query()
            ->with('variant.product.images')
            ->when($this->user, fn ($q) => $q->where('user_id', $this->user->id))
            ->when(! $this->user, fn ($q) => $q->where('session_id', $this->sessionId))
            ->get();
    }

    public function add(ProductVariant $variant, int $quantity = 1): CartItem
    {
        $item = CartItem::query()
            ->where('product_variant_id', $variant->id)
            ->when($this->user, fn ($q) => $q->where('user_id', $this->user->id))
            ->when(! $this->user, fn ($q) => $q->where('session_id', $this->sessionId))
            ->first();

        if ($item) {
            $item->increment('quantity', $quantity);

            return $item;
        }

        return CartItem::create([
            'user_id' => $this->user?->id,
            'session_id' => $this->user ? null : $this->sessionId,
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
        ]);
    }

    public function updateQuantity(CartItem $item, int $quantity): void
    {
        $quantity > 0 ? $item->update(['quantity' => $quantity]) : $item->delete();
    }

    public function remove(CartItem $item): void
    {
        $item->delete();
    }

    public function totalCents(): int
    {
        return $this->items()->sum(fn (CartItem $item) => $item->lineTotalCents());
    }

    /**
     * Called right after login: whatever the guest added to their
     * session cart before creating an account now belongs to them.
     */
    public function mergeGuestCartInto(Authenticatable $user): void
    {
        CartItem::where('session_id', $this->sessionId)->update([
            'user_id' => $user->id,
            'session_id' => null,
        ]);
    }
}
