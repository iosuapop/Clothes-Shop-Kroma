<?php

use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;

/**
 * "Smoke tests": hit every route and assert it doesn't blow up. Cheap to
 * write, and they catch exactly the class of bug that broke this app
 * during development — a syntax error or a Livewire root-element
 * violation on a specific page. `php artisan test` would have failed
 * loudly on the very page that was broken, instead of that being
 * discovered by clicking around in a browser.
 */
beforeEach(function () {
    $category = Category::create(['name' => 'Men', 'slug' => 'men']);

    $this->product = Product::create([
        'category_id' => $category->id,
        'name' => 'Smoke Test Hoodie',
        'slug' => 'smoke-test-hoodie',
        'short_description' => 'A hoodie.',
        'description' => 'A hoodie for tests.',
        'price_cents' => 10000,
        'sku' => 'SMOKE-001',
        'is_published' => true,
        'is_featured' => true,
        'published_at' => now(),
    ]);

    ProductVariant::create(['product_id' => $this->product->id, 'size' => 'M', 'stock' => 10]);

    $this->category = $category;
    $this->user = User::factory()->create(['role' => UserRole::Customer]);
    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
});

it('loads the homepage', function () {
    $this->get('/')->assertOk()->assertSee('KROMA');
});

it('loads a category page', function () {
    $this->get(route('category.show', $this->category->slug))->assertOk();
});

it('loads a product page', function () {
    $this->get(route('products.show', $this->product->slug))->assertOk()
        ->assertSee($this->product->name);
});

it('loads the cart page', function () {
    $this->get(route('cart.show'))->assertOk();
});

it('loads the login page', function () {
    $this->get(route('login'))->assertOk();
});

it('loads the register page', function () {
    $this->get(route('register'))->assertOk();
});

it('loads the forgot-password page', function () {
    $this->get(route('password.request'))->assertOk();
});

it('redirects guests away from checkout to login', function () {
    $this->get(route('checkout.show'))->assertRedirect(route('login'));
});

it('lets a logged-in customer load checkout, account orders and wishlist', function () {
    $this->actingAs($this->user);

    $this->get(route('account.orders'))->assertOk();
    $this->get(route('account.wishlist'))->assertOk();
});

it('blocks a regular customer from the admin dashboard', function () {
    $this->actingAs($this->user);

    $this->get(route('admin.dashboard'))->assertForbidden();
});

it('lets an admin load every admin page', function () {
    $this->actingAs($this->admin);

    $this->get(route('admin.dashboard'))->assertOk();
    $this->get(route('admin.products.index'))->assertOk();
    $this->get(route('admin.products.create'))->assertOk();
    $this->get(route('admin.products.edit', $this->product))->assertOk();
    $this->get(route('admin.orders.index'))->assertOk();
});

it('returns the custom 404 page for a made-up product slug', function () {
    $this->get('/products/this-does-not-exist')->assertNotFound();
});
