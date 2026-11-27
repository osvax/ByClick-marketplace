<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryMethod;
use App\Models\DeliveryPoint;
use App\Services\DeliveryCalculator;
use Illuminate\Contracts\View\View;

class InfoController extends Controller
{
    /**
     * Delivery information with live pickup points from the database.
     */
    public function delivery(DeliveryCalculator $calculator): View
    {
        $points = DeliveryPoint::query()
            ->active()
            ->orderBy('city')
            ->orderBy('address')
            ->get();

        $zones = collect($calculator->zones())
            ->map(fn (array $zone, string $city): array => [
                'city' => $city,
                'price' => $calculator->courierPriceForCity($city),
                'label' => $calculator->estimateLabel([
                    'price' => $calculator->courierPriceForCity($city),
                    'min_days' => (int) $zone['min_days'],
                    'max_days' => (int) $zone['max_days'],
                ]),
            ]);

        return view('delivery', [
            'points' => $points,
            'zones' => $zones,
            'freeFrom' => (int) config('marketplace.delivery.free_from', 10000),
            'pickupLabel' => $calculator->estimateLabel(
                $calculator->quote(DeliveryMethod::Pickup, null, 1),
            ),
            'postamatLabel' => $calculator->estimateLabel(
                $calculator->quote(DeliveryMethod::Postamat, null, 1),
            ),
        ]);
    }
}
