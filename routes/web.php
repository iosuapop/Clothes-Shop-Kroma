<?php

use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public storefront — each route maps straight to a Volt single-file
// component in resources/views/livewire/pages, no separate controller
// needed for pages that are "fetch data, render a view".
Volt::route('/', 'pages.home')->name('home');
Volt::route('/category/{category:slug}', 'pages.category')->name('category.show');
Volt::route('/products/{product:slug}', 'pages.product')->name('products.show');
Volt::route('/cart', 'pages.cart')->name('cart.show');

// Called by Stripe's servers, not a browser — must stay outside the
// `web` group's CSRF check (excluded in bootstrap/app.php, see SETUP.md).
Route::post('/stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::middleware('auth')->group(function () {
    Volt::route('/checkout', 'pages.checkout')->name('checkout.show');
    Volt::route('/account/orders', 'pages.account.orders')->name('account.orders');
    Volt::route('/account/wishlist', 'pages.account.wishlist')->name('account.wishlist');
});

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Volt::route('/dashboard', 'pages.admin.dashboard')->name('dashboard');
    Volt::route('/products', 'pages.admin.products')->name('products.index');
    Volt::route('/products/create', 'pages.admin.product-form')->name('products.create');
    Volt::route('/products/{product}/edit', 'pages.admin.product-form')->name('products.edit');
    Volt::route('/orders', 'pages.admin.orders')->name('orders.index');
});

require __DIR__.'/auth.php';

