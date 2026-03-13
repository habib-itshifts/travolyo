<?php

namespace Modules\Hotel\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HotelController extends Controller
{
    /**
     * Hotel search/listing page.
     * Actual search is handled by Api\HotelController@search via AJAX.
     */
    public function index(Request $request): View
    {
        $params = $request->only([
            'city', 'check_in', 'check_out', 'adults', 'children', 'rooms', 'provider',
        ]);

        return view('hotel::hotels.index', compact('params'));
    }

    /**
     * Hotel checkout page — loads prebook data from cache via token.
     */
    public function checkout(Request $request): View|RedirectResponse
    {
        $token = $request->get('token');
        $hc    = $token ? Cache::get('hotel_checkout_' . $token) : null;

        if (! $hc) {
            return redirect()->route('hotels.index');
        }

        return view('hotel::hotels.checkout', ['hc' => $hc, 'checkout_token' => $token]);
    }

    /**
     * Hotel booking confirmation page.
     */
    public function confirmation(string $code): View|RedirectResponse
    {
        $booking = Booking::where('code', $code)
            ->where('object_model', 'hotel')
            ->first();

        if (! $booking) {
            return redirect()->route('hotels.index');
        }

        $hotelDetails = $booking->getJsonMeta('hotel_details') ?? [];
        $gateway      = $booking->getMeta('payment_gateway') ?? '—';

        return view('hotel::hotels.confirmation', compact('booking', 'hotelDetails', 'gateway'));
    }
}
