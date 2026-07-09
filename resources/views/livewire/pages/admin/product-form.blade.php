<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('components.layouts.app', ['title' => 'Product form — Admin'])] class extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:255')]
    public string $shortDescription = '';

    #[Validate('required|string')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public string $price = '';

    #[Validate('nullable|numeric|min:0')]
    public string $compareAtPrice = '';

    #[Validate('required')]
    public string $categoryId = '';

    #[Validate('required|string|max:50')]
    public string $sku = '';

    public bool $isFeatured = false;

    /** @var array<string,int> size => stock, seeded with defaults so the form always shows all four */
    public array $stock = ['S' => 0, 'M' => 0, 'L' => 0, 'XL' => 0];

    /** @var array<\Livewire\Features\SupportFileUploads\TemporaryUploadedFile> */
    public array $newImages = [];

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product->load('variants');
            $this->name = $product->name;
            $this->shortDescription = $product->short_description;
            $this->description = $product->description;
            $this->price = (string) $product->price;
            $this->compareAtPrice = $product->compare_at_price_cents ? (string) ($product->compare_at_price_cents / 100) : '';
            $this->categoryId = (string) $product->category_id;
            $this->sku = $product->sku;
            $this->isFeatured = $product->is_featured;

            foreach ($product->variants as $variant) {
                $this->stock[$variant->size] = $variant->stock;
            }
        }
    }

    public function save(): void
    {
        $this->validate();

        $product = Product::updateOrCreate(
            ['id' => $this->product?->id],
            [
                'category_id' => $this->categoryId,
                'name' => $this->name,
                'slug' => Str::slug($this->name).'-'.Str::random(4),
                'short_description' => $this->shortDescription,
                'description' => $this->description,
                'price_cents' => (int) round(((float) $this->price) * 100),
                'compare_at_price_cents' => $this->compareAtPrice !== '' ? (int) round(((float) $this->compareAtPrice) * 100) : null,
                'sku' => $this->sku,
                'is_published' => true,
                'is_featured' => $this->isFeatured,
                'published_at' => now(),
            ]
        );

        foreach ($this->stock as $size => $quantity) {
            $product->variants()->updateOrCreate(['size' => $size], ['stock' => $quantity]);
        }

        foreach ($this->newImages as $index => $file) {
            $path = $file->store("products/manual/{$product->id}", 'public');

            $product->images()->create([
                'path' => $path,
                'alt_text' => $product->name,
                'sort_order' => $product->images()->count() + $index,
                'is_primary' => $product->images()->count() === 0,
            ]);
        }

        session()->flash('success', "\"{$product->name}\" saved.");
        $this->redirect(route('admin.products.index'), navigate: true);
    }

    public function with(): array
    {
        return ['categories' => Category::orderBy('sort_order')->get()];
    }
}; ?>

<div>
    <section class="px-6 py-10">
        <div class="mx-auto max-w-2xl">
            <h1 class="font-display text-4xl mb-10">{{ $product ? 'EDIT PRODUCT' : 'NEW PRODUCT' }}</h1>

            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="font-mono text-xs">NAME</label>
                    <input type="text" wire:model="name" class="w-full border-2 border-ink p-2 mt-1">
                    @error('name') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-mono text-xs">CATEGORY</label>
                    <select wire:model="categoryId" class="w-full border-2 border-ink p-2 mt-1">
                        <option value="">Select…</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-mono text-xs">SKU</label>
                    <input type="text" wire:model="sku" class="w-full border-2 border-ink p-2 mt-1">
                    @error('sku') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="font-mono text-xs">SHORT DESCRIPTION (shown on cards)</label>
                    <input type="text" wire:model="shortDescription" class="w-full border-2 border-ink p-2 mt-1">
                </div>

                <div>
                    <label class="font-mono text-xs">FULL DESCRIPTION</label>
                    <textarea wire:model="description" rows="4" class="w-full border-2 border-ink p-2 mt-1"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-mono text-xs">PRICE (RON)</label>
                        <input type="text" wire:model="price" class="w-full border-2 border-ink p-2 mt-1">
                        @error('price') <p class="text-flash-coral text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono text-xs">COMPARE-AT PRICE (optional, for sale badge)</label>
                        <input type="text" wire:model="compareAtPrice" class="w-full border-2 border-ink p-2 mt-1">
                    </div>
                </div>

                <div>
                    <label class="font-mono text-xs block mb-2">STOCK PER SIZE</label>
                    <div class="grid grid-cols-4 gap-3">
                        @foreach (array_keys($stock) as $size)
                            <div>
                                <span class="font-mono text-xs text-static-grey">{{ $size }}</span>
                                <input type="number" min="0" wire:model="stock.{{ $size }}" class="w-full border-2 border-ink p-2 mt-1">
                            </div>
                        @endforeach
                    </div>
                </div>

                <label class="flex items-center gap-2 font-mono text-xs">
                    <input type="checkbox" wire:model="isFeatured"> FEATURE ON HOMEPAGE
                </label>

                <div>
                    <label class="font-mono text-xs">ADD IMAGES</label>
                    <input type="file" wire:model="newImages" multiple class="w-full border-2 border-ink p-2 mt-1">
                    <div wire:loading wire:target="newImages" class="font-mono text-xs mt-1">Uploading…</div>

                    @if ($product?->images->isNotEmpty())
                        <div class="flex gap-2 mt-3">
                            @foreach ($product->images as $image)
                                <img src="{{ $image->url }}" class="w-16 h-16 object-cover border-2 border-ink">
                            @endforeach
                        </div>
                    @endif
                </div>

                <button type="submit" class="card-sticker bg-riot-yellow font-display w-full py-3 text-lg">
                    {{ $product ? 'SAVE CHANGES' : 'CREATE PRODUCT' }}
                </button>
            </form>
        </div>
    </section>
</div>
