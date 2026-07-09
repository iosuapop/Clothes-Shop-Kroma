<?php

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\CartService;

beforeEach(function () {
    $category = Category::create(['name' => 'Men', 'slug' => 'men']);

    $this->product = Product::create([
        'category_id' => $category->id,
        'name' => 'Test Hoodie',
        'slug' => 'test-hoodie',
        'short_description' => 'A hoodie.',
        'description' => 'A hoodie for tests.',
        'price_cents' => 10000,
        'sku' => 'TEST-001',
        'is_published' => true,
    ]);

    $this->variant = ProductVariant::create([
        'product_id' => $this->product->id,
        'size' => 'M',
        'stock' => 10,
    ]);
});

it('adds a new item to an empty cart', function () {
    $cart = new CartService(null, 'guest-session-1');

    $cart->add($this->variant, 2);

    expect($cart->items())->toHaveCount(1);
    expect($cart->totalCents())->toBe(20000);
});

it('increments quantity instead of duplicating the row when adding the same variant twice', function () {
    $cart = new CartService(null, 'guest-session-2');

    $cart->add($this->variant, 1);
    $cart->add($this->variant, 2);

    expect($cart->items())->toHaveCount(1);
    expect($cart->items()->first()->quantity)->toBe(3);
});

it('merges a guest cart into the user cart on login', function () {
    $user = User::factory()->create(['role' => UserRole::Customer]);
    $guestCart = new CartService(null, 'guest-session-3');
    $guestCart->add($this->variant, 1);

    $guestCart->mergeGuestCartInto($user);

    $userCart = new CartService($user, 'guest-session-3');
    expect($userCart->items())->toHaveCount(1);
    expect($userCart->items()->first()->user_id)->toBe($user->id);
});

it('removes the item entirely when quantity is updated to zero', function () {
    $cart = new CartService(null, 'guest-session-4');
    $item = $cart->add($this->variant, 1);

    $cart->updateQuantity($item, 0);

    expect($cart->items())->toHaveCount(0);
});
