<?php

namespace App\Http\Controllers;

class ComingSoonController extends Controller
{
    private const PRODUCTS = [
        'flights' => 'Flights',
        'trains' => 'Trains',
        'buses-cabs' => 'Buses & Cabs',
    ];

    public function show(string $product)
    {
        abort_unless(array_key_exists($product, self::PRODUCTS), 404);

        return view('coming_soon', ['productLabel' => self::PRODUCTS[$product]]);
    }
}
