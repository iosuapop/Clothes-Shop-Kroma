<?php

use App\Actions\PlaceOrder;
use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CartService;

beforeEach(function () {
    $category = Category::create(['name' => 'Men', 'slug' => 'men']);

    $this->product = Product::create([
        'category_id' => $category->id,
        'name' => 'Test Jacket',
        'slug' => 'test-jacket',
        'short_description' => 'A jacket.',
        'description' => 'A jacket for tests.',
        'price_cents' => 15000,
        'sku' => 'TEST-JKT-001',
    ]);

    $this->variant = ProductVariant::create([
        'product_id' => $this->product->id,
        'size' => 'L',
        'stock' => 5,
    ]);

    $this->user = User::factory()->create();
});

it('turns a cart into an order and clears the cart', function () {
    $cart = new CartService($this->user, 'session-x');
    $cart->add($this->variant, 2);

    $order = (new PlaceOrder($cart))($this->user, '123 Main St', '0700000000');

    expect($order->total_cents)->toBe(30000);
    expect($order->items)->toHaveCount(1);
    expect($order->status)->toBe(OrderStatus::Pending);
    expect($cart->items())->toHaveCount(0);
});

it('decrements variant stock by the ordered quantity', function () {
    $cart = new CartService($this->user, 'session-y');
    $cart->add($this->variant, 3);

    (new PlaceOrder($cart))($this->user, '123 Main St', '0700000000');

    expect($this->variant->fresh()->stock)->toBe(2);
});

it('snapshots the product name and price on the order item', function () {
    $cart = new CartService($this->user, 'session-z');
    $cart->add($this->variant, 1);

    $order = (new PlaceOrder($cart))($this->user, '123 Main St', '0700000000');

    expect($order->items->first()->product_name)->toBe('Test Jacket');
    expect($order->items->first()->unit_price_cents)->toBe(15000);
});
