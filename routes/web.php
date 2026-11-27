<?php

use App\Http\Controllers\CheckoutSuccessController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::view('/catalog', 'catalog')->name('catalog');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::view('/cart', 'cart')->name('cart');
Route::view('/checkout', 'checkout')->name('checkout');
Route::get('/checkout/success/{order:number}', [CheckoutSuccessController::class, 'show'])->name('checkout.success');
