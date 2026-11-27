<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPoint;
use Illuminate\Contracts\View\View;

class InfoController extends Controller
{
    /**
     * Delivery information with live pickup points from the database.
     */
    public function delivery(): View
    {
        $points = DeliveryPoint::query()
            ->active()
            ->orderBy('city')
            ->orderBy('address')
            ->get();

        return view('delivery', [
            'points' => $points,
        ]);
    }
}
