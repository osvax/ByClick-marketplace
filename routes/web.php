<?php

use App\Http\Controllers\CheckoutSuccessController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::view('/catalog', 'catalog')->name('catalog');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::view('/cart', 'cart')->name('cart');
Route::view('/favorites', 'favorites')->name('favorites');
Route::view('/checkout', 'checkout')->name('checkout');
Route::get('/checkout/success/{order:number}', [CheckoutSuccessController::class, 'show'])->name('checkout.success');

// Informational pages.
Route::get('/delivery', [InfoController::class, 'delivery'])->name('delivery');
Route::view('/help', 'help')->name('help');
Route::view('/about', 'about')->name('about');
Route::view('/seller', 'seller')->name('seller');

// Guests only: login, registration and password reset.
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth')->name('login');
    Route::view('/register', 'auth')->name('register');
    Route::view('/forgot-password', 'password-forgot')->name('password.request');
    Route::view('/reset-password/{token}', 'password-reset')->name('password.reset');
});

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// Authenticated customer area.
Route::middleware('auth')->group(function () {
    Route::view('/profile', 'profile')->name('profile');
    Route::view('/orders', 'orders')->name('orders');
});

// Seller cabinet.
Route::middleware(['auth', 'role:seller,admin'])
    ->prefix('seller')
    ->name('seller.')
    ->group(function () {
        Route::view('/dashboard', 'seller.dashboard')->name('dashboard');
        Route::view('/products', 'seller.products')->name('products');
        Route::view('/orders', 'seller.orders')->name('orders');
    });

// Administrator panel.
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::view('/', 'admin.dashboard')->name('dashboard');
        Route::view('/requests', 'admin.requests')->name('requests');
        Route::view('/shops', 'admin.shops')->name('shops');
        Route::view('/orders', 'admin.orders')->name('orders');
        Route::view('/products', 'admin.products')->name('products');
        Route::view('/categories', 'admin.categories')->name('categories');
        Route::view('/users', 'admin.users')->name('users');
        Route::view('/promocodes', 'admin.promocodes')->name('promocodes');
    });
