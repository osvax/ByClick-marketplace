<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SearchBox extends Component
{
    /** Minimum query length before suggestions are shown. */
    private const MIN_QUERY = 2;

    /** Maximum number of product suggestions. */
    private const LIMIT = 7;

    /** Current search term. */
    public string $query = '';

    /** Whether the suggestions dropdown is open. */
    public bool $open = false;

    /**
     * React to typing: trim the query and open the dropdown when it is long enough.
     */
    public function updatedQuery(): void
    {
        $this->query = trim($this->query);
        $this->open = mb_strlen($this->query) >= self::MIN_QUERY;
    }

    /**
     * Reopen suggestions when the input gets focus.
     */
    public function openSuggestions(): void
    {
        $this->open = mb_strlen(trim($this->query)) >= self::MIN_QUERY;
    }

    public function closeSuggestions(): void
    {
        $this->open = false;
    }

    /**
     * Submit the search: the catalog page already supports the ?q= filter.
     */
    public function search(): void
    {
        $query = trim($this->query);
        $this->open = false;

        $this->redirect(
            $query === '' ? route('catalog') : route('catalog', ['q' => $query]),
            navigate: true,
        );
    }

    public function render(): View
    {
        $term = trim($this->query);
        $suggestions = collect();

        if ($this->open && mb_strlen($term) >= self::MIN_QUERY) {
            $like = '%'.mb_strtolower($term).'%';

            $suggestions = Product::query()
                ->active()
                ->with(['category.parent', 'mainImage'])
                ->where('search_text', 'like', $like)
                ->orderByDesc('sold_count')
                ->orderByDesc('rating')
                ->limit(self::LIMIT)
                ->get();
        }

        return view('livewire.search-box', [
            'term' => $term,
            'suggestions' => $suggestions,
        ]);
    }
}
