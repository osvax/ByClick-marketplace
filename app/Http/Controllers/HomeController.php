<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Show the marketplace home page.
     */
    public function __invoke(): View
    {
        $products = Product::query()
            ->active()
            ->with(['category.parent', 'mainImage']);

        $saleProducts = (clone $products)
            ->whereNotNull('old_price')
            ->whereColumn('old_price', '>', 'price')
            ->orderByDesc('sold_count')
            ->limit(4)
            ->get();

        $hitProducts = (clone $products)
            ->orderByDesc('sold_count')
            ->limit(4)
            ->get();

        $recommended = (clone $products)
            ->orderByDesc('rating')
            ->orderByDesc('sold_count')
            ->limit(4)
            ->get();

        $categories = Category::query()
            ->roots()
            ->active()
            ->ordered()
            ->with(['children' => fn ($query) => $query->where('is_active', true)])
            ->get();

        return view('home', [
            'saleProducts' => $saleProducts,
            'hitProducts' => $hitProducts,
            'recommended' => $recommended,
            'categories' => $categories,
            'categoryCounts' => Category::activeProductCounts(),
        ]);
    }
}
