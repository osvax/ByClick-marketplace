<?php

use Database\Seeders\ProductSeeder;

beforeEach(function () {
    $this->seed(ProductSeeder::class);
});

it('publishes an xml sitemap with products and categories', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertSee('<urlset', false)
        ->assertSee(route('product.show', 'naushniki-aura-pro'), false)
        ->assertSee('changefreq', false);
});

it('publishes robots.txt with sitemap and private areas', function () {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap: '.url('/sitemap.xml'))
        ->assertSee('Disallow: /admin');
});

it('renders open graph and product structured data on product pages', function () {
    $this->get('/product/naushniki-aura-pro')
        ->assertOk()
        ->assertSee('og:type', false)
        ->assertSee('application/ld+json', false)
        ->assertSee('"@type":"Product"', false)
        ->assertSee('"@type":"Offer"', false);
});

it('renders default organization structured data on the home page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('application/ld+json', false)
        ->assertSee('"@type":"Organization"', false);
});
