<?php

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Single-purpose "Action" class: one public entry point (__invoke),
 * one job — turn a cart into an order. Keeps this critical, multi-step
 * operation out of a Livewire component and easy to unit test in isolation.
 */
class PlaceOrder
{
    public function __construct(private readonly CartService $cart) {}

    public function __invoke(User $user, string $address, string $phone, ?Payment $payment = null): Order
    {
        return DB::transaction(function () use ($user, $address, $phone, $payment) {
            $items = $this->cart->items();
            $subtotal = $items->sum(fn ($item) => $item->lineTotalCents());

            $order = Order::create([
                'reference' => 'KRM-'.strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'payment_id' => $payment?->id,
                'status' => $payment ? OrderStatus::Paid : OrderStatus::Pending,
                'subtotal_cents' => $subtotal,
                'shipping_cents' => 0,
                'total_cents' => $subtotal,
                'shipping_address' => $address,
                'shipping_phone' => $phone,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->variant->product->name,
                    'size' => $item->variant->size,
                    'unit_price_cents' => $item->variant->product->price_cents,
                    'quantity' => $item->quantity,
                ]);

                // Decrement stock atomically to avoid overselling under concurrent checkouts.
                $item->variant->decrement('stock', $item->quantity);
                $item->delete();
            }

            OrderPlaced::dispatch($order);

            return $order;
        });
    }
}
