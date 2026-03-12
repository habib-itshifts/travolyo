<?php

namespace Modules\Flight\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

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

        return view('flight::flights.index', compact('params'));
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $token = $request->get('token');
        $fc    = $token ? Cache::get('flight_checkout_' . $token) : null;

        if (! $fc) {
            return redirect()->route('flights.index');
        }

        // Store in session so the view and any subsequent web requests can read it
        session(['flight_checkout' => $fc]);

        return view('flight::flights.checkout');
    }
}