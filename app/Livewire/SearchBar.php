<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

/**
 * Full-text search box, live in the navbar on every page. Uses Scout's
 * search() instead of a LIKE query — swapping SCOUT_DRIVER from
 * "database" to "meilisearch" later needs zero changes here.
 */
class SearchBar extends Component
{
    public string $query = '';

    public bool $open = false;

    public function updatedQuery(): void
    {
        $this->open = strlen($this->query) >= 2;
    }

    public function getResultsProperty()
    {
        if (! $this->open) {
            return collect();
        }

        // Scout's database engine doesn't eager-load relations, so without
        // load() here every result triggers its own query just to fetch
        // the thumbnail — very noticeable while typing.
        return Product::search($this->query)->take(6)->get()->load('images');
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}
