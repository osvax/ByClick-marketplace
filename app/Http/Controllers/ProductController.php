<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class ProductController extends Controller
{
    /**
     * Show a single product with reviews, questions and recommendations.
     */
    public function show(Product $product): View
    {
        // Hidden products should behave like missing pages.
        abort_unless($product->is_active, 404);

        $product->load([
            'shop',
            'category.parent',
            'images',
            'reviews' => fn ($query) => $query->published()->with('user')->latest(),
            'questions' => fn ($query) => $query->published()->with('user')->latest(),
        ]);

        $reviews = $product->reviews;
        $reviewCount = $reviews->count();

        // Rating distribution for the "5/4/3/2/1" bars in the summary block.
        $ratingBreakdown = collect([5, 4, 3, 2, 1])
            ->mapWithKeys(fn (int $star): array => [$star => $reviews->where('rating', $star)->count()]);

        $recommendedPercent = $reviewCount > 0
            ? (int) round($reviews->where('rating', '>=', 4)->count() / $reviewCount * 100)
            : 0;

        $related = $this->relatedProducts($product);
        $bundleProducts = $related->take(2);

        // Bundle discount is shown for the whole set, like in the template.
        $bundleDiscount = max(0, ($product->old_price ?? $product->price) - $product->price)
            + $bundleProducts->sum(fn (Product $item): int => max(0, ($item->old_price ?? $item->price) - $item->price));

        $gallery = $product->images
            ->map(fn ($image): array => [
                'url' => $image->url(),
                'alt' => $image->alt ?: $product->name,
            ])
            ->values();

        if ($gallery->isEmpty()) {
            $gallery = collect([
                ['url' => asset('images/products/placeholder.svg'), 'alt' => $product->name],
            ]);
        }

        return view('product', [
            'product' => $product,
            'gallery' => $gallery,
            'reviews' => $reviews,
            'reviewCount' => $reviewCount,
            'ratingBreakdown' => $ratingBreakdown,
            'recommendedPercent' => $recommendedPercent,
            'related' => $related,
            'bundleProducts' => $bundleProducts,
            'bundleDiscount' => $bundleDiscount,
            'breadcrumbs' => $this->breadcrumbs($product),
            'isFavorite' => Favorite::isFavorited($product->id),
        ]);
    }

    /**
     * Products from the same category, topped up with other active products.
     *
     * @return Collection<int, Product>
     */
    protected function relatedProducts(Product $product): Collection
    {
        $related = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->with(['category.parent', 'mainImage'])
            ->orderByDesc('sold_count')
            ->limit(4)
            ->get();

        if ($related->count() < 4) {
            $related = $related->concat(
                Product::query()
                    ->active()
                    ->whereKeyNot($product->getKey())
                    ->whereNotIn('id', $related->pluck('id'))
                    ->with(['category.parent', 'mainImage'])
                    ->orderByDesc('sold_count')
                    ->limit(4 - $related->count())
                    ->get(),
            );
        }

        return $related;
    }

    /**
     * Breadcrumbs: root category, subcategory and the product itself.
     *
     * @return array<int, array{label: string, url: string|null}>
     */
    protected function breadcrumbs(Product $product): array
    {
        $category = $product->category;

        if ($category === null) {
            return [['label' => $product->name, 'url' => null]];
        }

        $root = $category->parent;
        $items = [];

        if ($root !== null) {
            $items[] = [
                'label' => $root->name,
                'url' => url('/catalog?category='.$root->slug),
            ];

            $items[] = [
                'label' => $category->name,
                'url' => url('/catalog?category='.$root->slug.'&sub='.$category->slug),
            ];
        } else {
            $items[] = [
                'label' => $category->name,
                'url' => url('/catalog?category='.$category->slug),
            ];
        }

        $items[] = ['label' => $product->name, 'url' => null];

        return $items;
    }
}
