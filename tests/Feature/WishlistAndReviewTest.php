<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Wishlist;
use Livewire\Volt\Volt;

beforeEach(function () {
    $category = Category::create(['name' => 'Women', 'slug' => 'women']);

    $this->product = Product::create([
        'category_id' => $category->id,
        'name' => 'Test Skirt',
        'slug' => 'test-skirt',
        'short_description' => 'A skirt.',
        'description' => 'A skirt for tests.',
        'price_cents' => 12000,
        'sku' => 'TEST-SKT-001',
        'is_published' => true,
    ]);

    ProductVariant::create(['product_id' => $this->product->id, 'size' => 'M', 'stock' => 5]);

    $this->user = User::factory()->create();
});

it('adds and removes a product from the wishlist', function () {
    $component = Volt::actingAs($this->user)->test('pages.product', ['product' => $this->product]);

    $component->call('toggleWishlist');
    expect(Wishlist::where('user_id', $this->user->id)->where('product_id', $this->product->id)->exists())->toBeTrue();

    $component->call('toggleWishlist');
    expect(Wishlist::where('user_id', $this->user->id)->where('product_id', $this->product->id)->exists())->toBeFalse();
});

it('lets a logged-in user submit a review', function () {
    Volt::actingAs($this->user)->test('pages.product', ['product' => $this->product])
        ->set('reviewRating', 4)
        ->set('reviewComment', 'Pretty good, runs slightly small.')
        ->call('submitReview');

    $this->assertDatabaseHas('reviews', [
        'product_id' => $this->product->id,
        'user_id' => $this->user->id,
        'rating' => 4,
    ]);
});

it('does not let the same user review a product twice', function () {
    $this->product->reviews()->create(['user_id' => $this->user->id, 'rating' => 5]);

    $component = Volt::actingAs($this->user)->test('pages.product', ['product' => $this->product]);

    expect($component->get('userHasReviewed'))->toBeTrue();
});
