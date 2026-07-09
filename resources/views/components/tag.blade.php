@props(['variant' => 'default'])

@php
    $variantClass = match ($variant) {
        'sale' => 'tag tag--sale',
        'new' => 'tag tag--new',
        default => 'tag',
    };
@endphp

<span {{ $attributes->merge(['class' => $variantClass]) }}>
    {{ $slot }}
</span>
