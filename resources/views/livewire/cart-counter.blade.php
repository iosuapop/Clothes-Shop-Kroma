<a href="{{ route('cart.show') }}" class="relative font-mono text-sm">
    CART
    @if ($count > 0)
        <span class="tag ml-1 !rotate-0 !py-0 !px-2 !shadow-none !border-2 text-xs">{{ $count }}</span>
    @endif
</a>
