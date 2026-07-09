<div class="relative" x-data @click.outside="$wire.open = false">
    <input
        type="text"
        wire:model.live.debounce.300ms="query"
        placeholder="SEARCH"
        class="border-2 border-ink px-3 py-1 font-mono text-xs w-32 md:w-48 bg-bone"
    >

    @if ($open)
        <div class="absolute top-full left-0 mt-2 w-72 card-sticker bg-white z-50">
            @forelse ($this->results as $product)
                <a href="{{ route('products.show', $product->slug) }}"
                   class="flex items-center gap-3 p-3 border-b border-static-grey/30 last:border-0">
                    <img src="{{ $product->images->first()?->url }}" class="w-10 h-12 object-cover border border-ink">
                    <div>
                        <p class="font-display text-sm">{{ $product->name }}</p>
                        <p class="font-mono text-xs text-static-grey">{{ number_format($product->price, 2) }} RON</p>
                    </div>
                </a>
            @empty
                <p class="p-3 font-mono text-xs text-static-grey">No matches.</p>
            @endforelse
        </div>
    @endif
</div>
