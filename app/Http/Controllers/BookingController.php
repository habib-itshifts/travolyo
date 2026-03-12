<?php

namespace App\Http\Controllers;

use App\Enums\BookingObjectModelEnum;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function show(string $code): View
    {
        $booking = Booking::where('code', $code)->firstOrFail();

        if ($booking->object_model === BookingObjectModelEnum::Flight->value) {
            $passengers = $booking->getJsonMeta('flight_passengers') ?: [];
            $orderRef   = $booking->getMeta('flight_pnr') ?: '';

            return view('flight::flights.confirmation', compact('booking', 'passengers', 'orderRef'));
        }

        return view('bookings.show', compact('booking'));
    }
}
