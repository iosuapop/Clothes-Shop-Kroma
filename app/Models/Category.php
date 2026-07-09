<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'sort_order'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /** Only categories that currently have at least one published product — keeps the nav clean. */
    public function scopeVisible($query)
    {
        return $query->whereHas('products', fn ($q) => $q->where('is_published', true));
    }
}
