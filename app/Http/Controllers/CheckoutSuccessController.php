<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;

class CheckoutSuccessController extends Controller
{
    /**
     * Show the success page of a placed order.
     *
     * Guests can only open the page of an order they placed in this session;
     * authenticated customers can reopen their own orders by number.
     */
    public function show(Order $order): View
    {
        abort_unless(
            (auth()->check() && $order->user_id === auth()->id())
            || session()->has('placed_order_'.$order->id),
            404,
        );

        $order->load(['items', 'deliveryPoint', 'promoCode']);

        return view('checkout-success', [
            'order' => $order,
        ]);
    }
}
