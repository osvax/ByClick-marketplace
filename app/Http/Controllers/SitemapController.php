<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * XML sitemap with public pages, categories and active products.
     */
    public function __invoke(): Response
    {
        $staticPages = collect([
            ['loc' => url('/'), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => route('catalog'), 'changefreq' => 'daily', 'priority' => '0.9'],
            ['loc' => route('delivery'), 'changefreq' => 'monthly', 'priority' => '0.6'],
            ['loc' => route('help'), 'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => route('about'), 'changefreq' => 'monthly', 'priority' => '0.4'],
            ['loc' => route('seller'), 'changefreq' => 'monthly', 'priority' => '0.4'],
        ]);

        $categories = Category::query()
            ->active()
            ->select(['slug', 'updated_at'])
            ->orderBy('slug')
            ->get()
            ->map(fn (Category $category): array => [
                'loc' => url('/catalog?category='.$category->slug),
                'lastmod' => $category->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ]);

        $products = Product::query()
            ->active()
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (Product $product): array => [
                'loc' => route('product.show', $product),
                'lastmod' => $product->updated_at?->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ]);

        $xml = view('sitemap', [
            'urls' => $staticPages->concat($categories)->concat($products),
        ])->render();

        return response('<?xml version="1.0" encoding="UTF-8"?>'."\n".$xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
