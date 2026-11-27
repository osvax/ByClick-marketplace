<?php

use App\Http\Controllers\CheckoutSuccessController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('/catalog', 'catalog')->name('catalog');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::view('/cart', 'cart')->name('cart');
Route::view('/favorites', 'favorites')->name('favorites');
Route::view('/checkout', 'checkout')->name('checkout');
Route::get('/checkout/success/{order:number}', [CheckoutSuccessController::class, 'show'])->name('checkout.success');

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
