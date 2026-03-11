<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    /**
     * Render the flight results page.
     * Actual search is handled by Api\FlightController@search (shared with mobile).
     * The view calls /api/flights/search via AJAX on load when params are present.
     */
    public function index(Request $request): View
    {
        $params = $request->only([
            'origin', 'destination', 'departure_date', 'return_date',
            'trip_type', 'adults', 'children', 'infants', 'cabin_class', 'provider',
        ]);

        

        return view('flights.index', compact('params'));
    }
}
