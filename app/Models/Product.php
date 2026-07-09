<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use SoftDeletes, Searchable;

    protected $fillable = [
        'category_id', 'name', 'slug', 'short_description', 'description',
        'price_cents', 'compare_at_price_cents', 'sku',
        'is_published', 'is_featured', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        // Sizes are stored as plain strings, so a default/alphabetical sort
        // gives L, M, S, XL, XS, XXL. This orders them the way a shopper
        // expects (XS, S, M, L, XL, XXL), with anything else (e.g. numeric
        // sizes) falling back to a plain alphabetical/numeric sort after.
        return $this->hasMany(ProductVariant::class)
            ->orderByRaw("CASE size
                WHEN 'XS' THEN 1
                WHEN 'S' THEN 2
                WHEN 'M' THEN 3
                WHEN 'L' THEN 4
                WHEN 'XL' THEN 5
                WHEN 'XXL' THEN 6
                ELSE 7
            END")
            ->orderBy('size');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * Prices are stored in cents (see migration comment). This accessor is
     * the single place that turns raw cents into a display value, so
     * every Blade/Livewire view formats money identically.
     */
    protected function price(): Attribute
    {
        return Attribute::get(fn () => $this->price_cents / 100);
    }

    protected function isOnSale(): Attribute
    {
        return Attribute::get(fn () => $this->compare_at_price_cents !== null
            && $this->compare_at_price_cents > $this->price_cents);
    }

    protected function averageRating(): Attribute
    {
        return Attribute::get(fn () => round($this->reviews()->avg('rating') ?? 0, 1));
    }

    public function totalStock(): int
    {
        return $this->variants->sum('stock');
    }

    /**
     * Data indexed by Laravel Scout for full-text product search.
     *
     * With SCOUT_DRIVER=database, Scout runs a WHERE ... LIKE against every
     * key returned here, so every key must be a real column on the
     * `products` table. Don't put relation values (e.g. category name)
     * here — that produces a "column not found" SQL error.
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'short_description' => $this->short_description,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->is_published;
    }
}
