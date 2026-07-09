<?php

use App\Models\Product;
use App\Models\Review;
use App\Models\Wishlist;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app', ['title' => 'Product — KROMA'])] class extends Component
{
    public Product $product;

    public ?int $selectedVariantId = null;

    public string $activeImage = '';

    public int $reviewRating = 5;

    public string $reviewComment = '';

    public function mount(): void
    {
        $this->product->load(['images', 'variants', 'reviews.user']);
        $this->activeImage = $this->product->images->first()?->url ?? '';
        $this->selectedVariantId = $this->product->variants->firstWhere('stock', '>', 0)?->id;
    }

    #[Computed]
    public function userHasReviewed(): bool
    {
        return Auth::check() && $this->product->reviews->contains('user_id', Auth::id());
    }

    #[Computed]
    public function isWishlisted(): bool
    {
        return Auth::check() && Wishlist::where('user_id', Auth::id())
            ->where('product_id', $this->product->id)
            ->exists();
    }

    public function toggleWishlist(): void
    {
        if (! Auth::check()) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        Wishlist::where('user_id', Auth::id())->where('product_id', $this->product->id)->exists()
            ? Wishlist::where('user_id', Auth::id())->where('product_id', $this->product->id)->delete()
            : Wishlist::create(['user_id' => Auth::id(), 'product_id' => $this->product->id]);

        unset($this->isWishlisted); // bust the computed cache so the heart icon updates
    }

    public function submitReview(): void
    {
        $this->validate([
            'reviewRating' => 'required|integer|min:1|max:5',
            'reviewComment' => 'nullable|string|max:1000',
        ]);

        Review::create([
            'product_id' => $this->product->id,
            'user_id' => Auth::id(),
            'rating' => $this->reviewRating,
            'comment' => $this->reviewComment,
        ]);

        $this->product->load('reviews.user');
        $this->reviewComment = '';
        session()->flash('success', 'Thanks for the review!');
    }

    #[Computed]
    public function relatedProducts()
    {
        return Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->where('is_published', true)
            ->with('images')
            ->take(4)
            ->get();
    }

    public function addToCart(): void
    {
        $this->validate([
            'selectedVariantId' => 'required|exists:product_variants,id',
        ], [], ['selectedVariantId' => 'size']);

        $variant = $this->product->variants->firstWhere('id', $this->selectedVariantId);

        if (! $variant || $variant->stock < 1) {
            $this->addError('selectedVariantId', 'That size just sold out.');

            return;
        }

        $cart = new CartService(Auth::user(), session()->getId());
        $cart->add($variant);

        // Tells the navbar's CartCounter widget (a separate Livewire
        // component) to refresh, without either component knowing
        // about the other directly.
        $this->dispatch('cart-updated');

        session()->flash('success', "{$this->product->name} added to your cart.");
    }
}; ?>

<div>
    <section class="px-6 py-10">
        <div class="mx-auto max-w-6xl grid md:grid-cols-2 gap-10">

            {{-- Gallery --}}
            <div>
                <div class="card-sticker bg-white overflow-hidden">
                    <img src="{{ $activeImage }}" alt="{{ $product->name }}" class="w-full aspect-[3/4] object-cover">
                </div>
                <div class="flex gap-3 mt-4">
                    @foreach ($product->images as $image)
                        <button wire:click="$set('activeImage', '{{ $image->url }}')"
                                class="w-16 h-16 border-2 border-ink overflow-hidden {{ $activeImage === $image->url ? 'ring-2 ring-electric' : '' }}">
                            <img src="{{ $image->url }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Details --}}
            <div>
                <p class="font-mono text-xs text-static-grey uppercase">{{ $product->category->name }}</p>
                <h1 class="font-display text-4xl mt-1">{{ strtoupper($product->name) }}</h1>

                <div class="flex items-center gap-3 mt-4">
                    <x-tag :variant="$product->isOnSale ? 'sale' : 'default'">
                        {{ number_format($product->price, 2) }} RON
                    </x-tag>
                    @if ($product->isOnSale)
                        <span class="line-through text-static-grey font-mono text-sm">
                            {{ number_format($product->compare_at_price_cents / 100, 2) }} RON
                        </span>
                    @endif
                </div>

                <p class="mt-6 text-sm leading-relaxed">{{ $product->description }}</p>

                <div class="mt-8">
                    <p class="font-mono text-xs mb-2">SIZE</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($product->variants as $variant)
                            <button
                                wire:click="$set('selectedVariantId', {{ $variant->id }})"
                                @disabled($variant->stock < 1)
                                class="tag !rotate-0 !shadow-none relative
                    {{ $selectedVariantId === $variant->id ? '!bg-ink !text-bone' : '' }}
                    {{ $variant->stock < 1 ? 'opacity-30 cursor-not-allowed' : '' }}"
                            >
                                {{ $variant->size }}
                                <span class="font-mono text-[10px] {{ $selectedVariantId === $variant->id ? 'text-bone/70' : 'text-static-grey' }} ml-1">
                    ({{ $variant->stock }})
                </span>
                            </button>
                        @endforeach
                    </div>
                    @error('selectedVariantId') <p class="text-flash-coral text-xs mt-2">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 mt-8">
                    <button wire:click="addToCart" class="card-sticker bg-riot-yellow font-display flex-1 py-4 text-lg">
                        ADD TO CART
                    </button>
                    <button wire:click="toggleWishlist" class="card-sticker bg-bone px-5 text-xl">
                        {{ $this->isWishlisted ? '♥' : '♡' }}
                    </button>
                </div>

                {{-- Reviews --}}
                <div class="mt-12">
                    <h2 class="font-display text-2xl mb-4">
                        REVIEWS ({{ $product->reviews->count() }}) &middot; {{ $product->averageRating }}★
                    </h2>
                    @forelse ($product->reviews as $review)
                        <div class="border-t-2 border-ink py-3">
                            <p class="font-mono text-xs">{{ $review->user->name }} &middot; {{ str_repeat('★', $review->rating) }}</p>
                            <p class="text-sm mt-1">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="font-mono text-xs text-static-grey">No reviews yet — be the first.</p>
                    @endforelse

                    @auth
                        @if (! $this->userHasReviewed)
                            <form wire:submit="submitReview" class="mt-6 border-t-2 border-ink pt-4">
                                <label class="font-mono text-xs block mb-2">YOUR RATING</label>
                                <div class="flex gap-1 mb-3">
                                    @foreach ([1, 2, 3, 4, 5] as $star)
                                        <button type="button" wire:click="$set('reviewRating', {{ $star }})"
                                                class="text-xl {{ $star <= $reviewRating ? '' : 'opacity-30' }}">★</button>
                                    @endforeach
                                </div>
                                <textarea wire:model="reviewComment" rows="2" placeholder="Optional comment"
                                          class="w-full border-2 border-ink p-2 text-sm"></textarea>
                                <button type="submit" class="card-sticker bg-ink text-bone font-mono text-xs px-4 py-2 mt-3">
                                    SUBMIT REVIEW
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        {{-- Related products --}}
        @if ($this->relatedProducts->isNotEmpty())
            <div class="mx-auto max-w-6xl mt-20">
                <h2 class="font-display text-3xl mb-6">YOU MIGHT ALSO LIKE</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach ($this->relatedProducts as $related)
                        <a href="{{ route('products.show', $related->slug) }}" class="card-sticker bg-white block">
                            <img src="{{ $related->images->first()?->url }}" class="w-full aspect-[3/4] object-cover border-b-3 border-ink">
                            <p class="font-display p-3 text-sm">{{ $related->name }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</div>
